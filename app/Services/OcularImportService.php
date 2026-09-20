<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\Schedule;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use ZipArchive;

class OcularImportService
{
    public function preview(string $workbookPath, ?string $photosZipPath = null): array
    {
        $spreadsheet = IOFactory::load($workbookPath);
        $photoEntries = $this->photoEntries($photosZipPath);
        $sheets = [];
        $errors = [];

        foreach (['subjects', 'teachers', 'students', 'schedules'] as $sheetName) {
            $worksheet = $this->findWorksheet($spreadsheet, $sheetName);

            if (! $worksheet) {
                if ($sheetName !== 'subjects') {
                    $errors[] = "Sheet {$sheetName} tidak ditemukan.";
                }
                $sheets[$sheetName] = [];
                continue;
            }

            $rows = $this->readRows($worksheet);
            $sheets[$sheetName] = match ($sheetName) {
                'subjects' => $this->previewSubjects($rows),
                'teachers' => $this->previewTeachers($rows, $photoEntries, $sheets['subjects'] ?? []),
                'students' => $this->previewStudents($rows, $photoEntries),
                'schedules' => $this->previewSchedules($rows, $sheets['teachers'] ?? [], $sheets['subjects'] ?? []),
            };
        }

        return [
            'errors' => $errors,
            'sheets' => $sheets,
            'summary' => $this->summary($sheets, $errors),
            'photos_zip' => $photosZipPath,
        ];
    }

    public function commit(array $preview, array $selected, bool $resetFirst = false): array
    {
        $selected = array_fill_keys($selected, true);
        $counts = ['subjects' => 0, 'teachers' => 0, 'students' => 0, 'schedules' => 0, 'photos' => 0, 'reset' => 0];

        DB::transaction(function () use ($preview, $selected, $resetFirst, &$counts): void {
            if ($resetFirst) {
                $counts['reset'] = $this->resetImportData();
            }

            foreach ($preview['sheets'] as $sheet => $items) {
                foreach ($items as $index => $item) {
                    if (($item['status'] ?? 'error') === 'error' || ! isset($selected["{$sheet}:{$index}"])) {
                        continue;
                    }

                    match ($sheet) {
                        'subjects' => $this->commitSubject($item, $counts),
                        'teachers' => $this->commitTeacher($item, $preview['photos_zip'] ?? null, $counts),
                        'students' => $this->commitStudent($item, $preview['photos_zip'] ?? null, $counts),
                        'schedules' => $this->commitSchedule($item, $counts),
                    };
                }
            }
        });

        return $counts;
    }

    private function previewSubjects(array $rows): array
    {
        $items = [];

        foreach ($rows as $row) {
            $errors = [];
            $name = trim($this->value($row, ['name', 'subject', 'mapel', 'mata_pelajaran']));
            $code = trim($this->value($row, ['code', 'subject_code', 'kode']));

            if ($name === '') $errors[] = 'Nama mapel wajib diisi.';
            if ($code === '') $errors[] = 'Kode mapel wajib diisi.';

            $byCode = $code !== '' ? Subject::query()->where('code', $code)->first() : null;
            $byName = $name !== '' ? Subject::query()->where('name', $name)->first() : null;
            $existing = $byCode ?: $byName;
            if ($byCode && $byName && $byCode->id !== $byName->id) {
                $errors[] = 'Kode dan nama mapel mengarah ke data berbeda.';
            }

            $items[] = $this->item(
                $row,
                $existing ? 'update' : 'new',
                $errors,
                [
                    'id' => $existing?->id,
                    'name' => $name,
                    'code' => $code,
                ],
                $name.' · '.$code,
            );
        }

        return $items;
    }

    private function previewTeachers(array $rows, array $photoEntries, array $subjectItems = []): array
    {
        $items = [];
        $pendingSubjects = $this->pendingSubjects($subjectItems);

        foreach ($rows as $row) {
            $errors = [];
            $email = strtolower(trim($this->value($row, ['email'])));
            $nip = trim($this->value($row, ['nip']));
            $name = trim($this->value($row, ['name', 'nama']));
            $password = trim($this->value($row, ['password']));
            $subjectCodes = $this->splitValues($this->value($row, ['subject_codes', 'subjects', 'mapel']));

            if ($name === '') $errors[] = 'Nama wajib diisi.';
            if (! filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email tidak valid.';
            if ($nip === '') $errors[] = 'NIP wajib diisi.';

            $byEmail = $email !== '' ? User::query()->where('email', $email)->first() : null;
            $byNip = $nip !== '' ? User::query()->where('nip', $nip)->first() : null;
            $existing = $byEmail ?: $byNip;

            if ($byEmail && $byNip && $byEmail->id !== $byNip->id) {
                $errors[] = 'Email dan NIP mengarah ke akun yang berbeda.';
            }
            if ($existing && $existing->role !== 'guru') {
                $errors[] = 'Email atau NIP sudah dipakai akun admin.';
            }
            if (! $existing && $password === '') {
                $errors[] = 'Password wajib diisi untuk guru baru.';
            }

            $subjectIds = [];
            $subjectKeys = [];
            foreach ($subjectCodes as $code) {
                $subject = Subject::query()->where('code', $code)->orWhere('name', $code)->first();
                if (! $subject) {
                    $pendingSubject = $pendingSubjects[strtolower(trim($code))] ?? null;
                    if (! $pendingSubject) {
                        $errors[] = "Mapel {$code} tidak ditemukan.";
                    } else {
                        $subjectKeys[] = $code;
                    }
                } else {
                    $subjectIds[] = $subject->id;
                    $subjectKeys[] = $code;
                }
            }

            $photo = $this->findPhoto($photoEntries, $nip);
            $items[] = $this->item(
                $row,
                $existing ? 'update' : 'new',
                $errors,
                [
                    'id' => $existing?->id,
                    'name' => $name,
                    'email' => $email,
                    'nip' => $nip,
                    'password' => $password,
                    'is_active' => $this->booleanValue($this->value($row, ['is_active', 'active']), true),
                    'subject_ids' => array_values(array_unique($subjectIds)),
                    'subject_keys' => array_values(array_unique($subjectKeys)),
                    'photo_entry' => $photo,
                ],
                $name.' · '.$email,
                $photo,
            );
        }

        return $items;
    }

    private function previewStudents(array $rows, array $photoEntries): array
    {
        $items = [];

        foreach ($rows as $row) {
            $errors = [];
            $nis = trim($this->value($row, ['nis']));
            $nisn = trim($this->value($row, ['nisn']));
            $name = trim($this->value($row, ['name', 'nama']));
            $className = trim($this->value($row, ['class_name', 'class', 'kelas']));
            $academicYearName = trim($this->value($row, ['academic_year', 'academic_year_name', 'tahun_ajaran']));
            $semester = (int) ($this->value($row, ['semester']) ?: 1);

            if ($nis === '') $errors[] = 'NIS wajib diisi.';
            if ($nisn === '') $errors[] = 'NISN wajib diisi.';
            if ($name === '') $errors[] = 'Nama wajib diisi.';
            if ($className === '') $errors[] = 'Nama kelas wajib diisi.';
            if ($academicYearName === '') $errors[] = 'Tahun ajaran wajib diisi.';
            if (! in_array($semester, [1, 2], true)) $errors[] = 'Semester harus 1 atau 2.';

            $academicYear = AcademicYear::query()->where('name', $academicYearName)->where('semester', $semester)->first();
            $class = $academicYear ? SchoolClass::query()->where('academic_year_id', $academicYear->id)->where('name', $className)->first() : null;
            if (! $academicYear) $errors[] = 'Tahun ajaran tidak ditemukan.';
            if ($academicYear && ! $class) $errors[] = 'Kelas tidak ditemukan pada tahun ajaran tersebut.';

            $byNis = $nis !== '' ? Student::query()->where('nis', $nis)->first() : null;
            $byNisn = $nisn !== '' ? Student::query()->where('nisn', $nisn)->first() : null;
            $existing = $byNis ?: $byNisn;
            if ($byNis && $byNisn && $byNis->id !== $byNisn->id) $errors[] = 'NIS dan NISN mengarah ke siswa yang berbeda.';

            $photoKey = $this->value($row, ['photo_filename', 'photo', 'photo_path']);
            $photo = $this->findPhoto($photoEntries, $photoKey !== '' ? $photoKey : $nisn);
            $items[] = $this->item(
                $row,
                $existing ? 'update' : 'new',
                $errors,
                [
                    'id' => $existing?->id,
                    'nis' => $nis,
                    'nisn' => $nisn,
                    'name' => $name,
                    'class_id' => $class?->id,
                    'is_active' => $this->booleanValue($this->value($row, ['is_active', 'active']), true),
                    'photo_entry' => $photo,
                ],
                $name.' · '.$nisn.' · '.$className,
                $photo,
            );
        }

        return $items;
    }

    private function previewSchedules(array $rows, array $teacherItems = [], array $subjectItems = []): array
    {
        $rows = $this->mergeScheduleRows($rows);
        $items = [];
        $pendingTeachers = [];
        $pendingSubjects = $this->pendingSubjects($subjectItems);

        foreach ($teacherItems as $teacherItem) {
            if (($teacherItem['status'] ?? 'error') === 'error') continue;
            $data = $teacherItem['data'] ?? [];
            foreach ([$data['nip'] ?? null, $data['email'] ?? null, $data['name'] ?? null] as $key) {
                if ($key !== null && trim((string) $key) !== '') {
                    $pendingTeachers[strtolower(trim((string) $key))] = $data;
                }
            }
        }

        foreach ($rows as $row) {
            $errors = [];
            $yearName = trim($this->value($row, ['academic_year', 'academic_year_name', 'tahun_ajaran']));
            $semester = (int) ($this->value($row, ['semester']) ?: 1);
            $teacherKey = trim($this->value($row, ['teacher_nip', 'nip', 'teacher_email', 'email']));
            $subjectKey = trim($this->value($row, ['subject_code', 'subject', 'mapel']));
            $className = trim($this->value($row, ['class_name', 'class', 'kelas']));
            $day = $this->dayValue($this->value($row, ['day', 'day_of_week', 'hari']));
            $start = $this->timeValue($this->value($row, ['start_time', 'start', 'mulai']));
            $end = $this->timeValue($this->value($row, ['end_time', 'end', 'selesai']));

            $academicYear = AcademicYear::query()->where('name', $yearName)->where('semester', $semester)->first();
            $teacher = User::query()->where('role', 'guru')->where(function ($query) use ($teacherKey): void {
                $query->where('nip', $teacherKey)->orWhere('email', $teacherKey)->orWhere('name', $teacherKey);
            })->first();
            $pendingTeacher = $pendingTeachers[strtolower($teacherKey)] ?? null;
            $subject = Subject::query()->where('code', $subjectKey)->orWhere('name', $subjectKey)->first();
            $pendingSubject = ! $subject ? ($pendingSubjects[strtolower($subjectKey)] ?? null) : null;
            $class = $academicYear ? SchoolClass::query()->where('academic_year_id', $academicYear->id)->where('name', $className)->first() : null;

            if (! $academicYear) $errors[] = 'Tahun ajaran tidak ditemukan.';
            if (! $teacher && ! $pendingTeacher) $errors[] = 'Guru tidak ditemukan.';
            if (! $subject && ! $pendingSubject) $errors[] = 'Mapel tidak ditemukan.';
            if ($academicYear && ! $class) $errors[] = 'Kelas tidak ditemukan pada tahun ajaran tersebut.';
            if (! $day) $errors[] = 'Hari tidak valid.';
            if (! $start || ! $end || $start >= $end) $errors[] = 'Jam mulai/selesai tidak valid.';
            if ($subject || $pendingSubject) {
                $hasSubject = $teacher
                    ? ($subject
                        ? $teacher->subjects()->whereKey($subject->id)->exists()
                        : in_array($subjectKey, $teacher->subjects()->pluck('code')->merge($teacher->subjects()->pluck('name'))->all(), true))
                        || in_array($subject?->id, $pendingTeacher['subject_ids'] ?? [], true)
                        || in_array($subjectKey, $pendingTeacher['subject_keys'] ?? [], true)
                    : in_array($subject?->id, $pendingTeacher['subject_ids'] ?? [], true)
                        || in_array($subjectKey, $pendingTeacher['subject_keys'] ?? [], true);
                if (! $hasSubject) $errors[] = 'Mapel belum di-assign ke guru.';
            }

            $existing = null;
            if ($academicYear && $class && $day && $start) {
                $existing = Schedule::query()->where([
                    'academic_year_id' => $academicYear->id,
                    'class_id' => $class->id,
                    'day_of_week' => $day,
                    'start_time' => $start,
                ])->first();

                if ($teacher && $start && $end && Schedule::query()
                    ->where('academic_year_id', $academicYear->id)
                    ->where('day_of_week', $day)
                    ->where(function ($query) use ($class, $teacher): void {
                        $query->where('class_id', $class->id)->orWhere('user_id', $teacher->id);
                    })
                    ->where('start_time', '<', $end)
                    ->where('end_time', '>', $start)
                    ->when($existing, fn ($query) => $query->whereKeyNot($existing->id))
                    ->exists()) {
                    $errors[] = 'Jadwal bentrok dengan kelas atau guru.';
                }
            }

            $items[] = $this->item(
                $row,
                $existing ? 'update' : 'new',
                $errors,
                [
                    'id' => $existing?->id,
                    'academic_year_id' => $academicYear?->id,
                    'user_id' => $teacher?->id,
                    'teacher_key' => $teacherKey,
                    'subject_id' => $subject?->id,
                    'subject_key' => $subjectKey,
                    'class_id' => $class?->id,
                    'day_of_week' => $day,
                    'start_time' => $start,
                    'end_time' => $end,
                ],
                $className.' · '.$subjectKey.' · '.$teacherKey,
            );
        }

        return $items;
    }

    private function pendingSubjects(array $subjectItems): array
    {
        $pending = [];

        foreach ($subjectItems as $subjectItem) {
            if (($subjectItem['status'] ?? 'error') === 'error') continue;
            $data = $subjectItem['data'] ?? [];
            foreach ([$data['code'] ?? null, $data['name'] ?? null] as $key) {
                if ($key !== null && trim((string) $key) !== '') {
                    $pending[strtolower(trim((string) $key))] = $data;
                }
            }
        }

        return $pending;
    }

    private function mergeScheduleRows(array $rows): array
    {
        $merged = [];

        foreach ($rows as $row) {
            $key = $this->scheduleMergeKey($row);
            $start = $this->timeValue($this->value($row, ['start_time', 'start', 'mulai']));
            $end = $this->timeValue($this->value($row, ['end_time', 'end', 'selesai']));
            $lastIndex = array_key_last($merged);
            $last = $lastIndex !== null ? $merged[$lastIndex] : null;

            if (
                $last
                && ($last['_schedule_merge_key'] ?? null) === $key
                && $start
                && $end
                && ($last['_schedule_merge_end'] ?? null) === $start
            ) {
                $merged[$lastIndex]['end_time'] = $end;
                $merged[$lastIndex]['_schedule_merge_end'] = $end;
                $merged[$lastIndex]['_row'] = ($merged[$lastIndex]['_row'] ?? '?').'-'.($row['_row'] ?? '?');
                continue;
            }

            $row['_schedule_merge_key'] = $key;
            $row['_schedule_merge_end'] = $end;
            if ($start) $row['start_time'] = $start;
            if ($end) $row['end_time'] = $end;
            $merged[] = $row;
        }

        return array_map(function (array $row): array {
            unset($row['_schedule_merge_key'], $row['_schedule_merge_end']);
            return $row;
        }, $merged);
    }

    private function scheduleMergeKey(array $row): string
    {
        $parts = [
            $this->value($row, ['academic_year', 'academic_year_name', 'tahun_ajaran']),
            $this->value($row, ['semester']),
            $this->value($row, ['teacher_nip', 'nip', 'teacher_email', 'email']),
            $this->value($row, ['subject_code', 'subject', 'mapel']),
            $this->value($row, ['class_name', 'class', 'kelas']),
            (string) $this->dayValue($this->value($row, ['day', 'day_of_week', 'hari'])),
        ];

        return implode('|', array_map(fn (string $part): string => strtolower(trim($part)), $parts));
    }

    private function commitSubject(array $item, array &$counts): void
    {
        $data = $item['data'];
        $subject = $data['id'] ? Subject::query()->find($data['id']) : null;
        $subject ??= new Subject();
        $subject->fill([
            'name' => $data['name'],
            'code' => $data['code'],
        ]);
        $subject->save();
        $counts['subjects']++;
    }

    private function commitTeacher(array $item, ?string $photosZipPath, array &$counts): void
    {
        $data = $item['data'];
        $teacher = $data['id'] ? User::query()->find($data['id']) : null;
        $teacher ??= new User();
        $subjectIds = array_values(array_unique(array_filter([
            ...($data['subject_ids'] ?? []),
            ...array_map(fn (string $key) => Subject::query()
                ->where('code', $key)
                ->orWhere('name', $key)
                ->value('id'), $data['subject_keys'] ?? []),
        ])));
        $teacher->fill([
            'name' => $data['name'],
            'email' => $data['email'],
            'nip' => $data['nip'],
            'role' => 'guru',
            'is_active' => $data['is_active'],
        ]);
        if ($data['password'] !== '') $teacher->password = Hash::make($data['password']);
        $photoStored = $this->storePhoto($photosZipPath, $data['photo_entry'] ?? null, 'teachers/idcard', $data['nip'], $teacher);
        $teacher->save();
        $teacher->subjects()->sync($subjectIds);
        if ($photoStored) $counts['photos']++;
        $counts['teachers']++;
    }

    private function commitStudent(array $item, ?string $photosZipPath, array &$counts): void
    {
        $data = $item['data'];
        $student = $data['id'] ? Student::query()->find($data['id']) : null;
        $student ??= new Student();
        $student->fill([
            'nis' => $data['nis'],
            'nisn' => $data['nisn'],
            'name' => $data['name'],
            'class_id' => $data['class_id'],
            'is_active' => $data['is_active'],
        ]);
        if ($this->storePhoto($photosZipPath, $data['photo_entry'] ?? null, 'students/idcard', $data['nisn'], $student)) $counts['photos']++;
        $student->save();
        $counts['students']++;
    }

    private function commitSchedule(array $item, array &$counts): void
    {
        $data = collect($item['data'])->except(['id', 'teacher_key', 'subject_key'])->all();
        if (! $data['user_id'] && ! empty($item['data']['teacher_key'])) {
            $data['user_id'] = User::query()->where('role', 'guru')->where(function ($query) use ($item): void {
                $query->where('nip', $item['data']['teacher_key'])
                    ->orWhere('email', $item['data']['teacher_key'])
                    ->orWhere('name', $item['data']['teacher_key']);
            })->value('id');
        }
        if (! $data['subject_id'] && ! empty($item['data']['subject_key'])) {
            $data['subject_id'] = Subject::query()
                ->where('code', $item['data']['subject_key'])
                ->orWhere('name', $item['data']['subject_key'])
                ->value('id');
        }
        if (! $data['user_id'] || ! $data['subject_id']) return;
        $schedule = $item['data']['id'] ? Schedule::query()->find($item['data']['id']) : null;
        $schedule ? $schedule->update($data) : Schedule::create($data);
        $counts['schedules']++;
    }

    private function resetImportData(): int
    {
        $deleted = 0;
        $tables = [
            'attendance_logs',
            'attendances',
            'attendance_sessions',
            'schedules',
            'students',
            'teacher_subject',
            'subjects',
        ];

        foreach ($tables as $table) {
            $deleted += DB::table($table)->delete();
        }

        $deleted += DB::table('users')->where('role', 'guru')->delete();

        return $deleted;
    }

    private function storePhoto(?string $zipPath, ?string $entry, string $folder, string $key, object $model): bool
    {
        if (! $entry) return false;

        if (is_file($entry)) {
            $contents = file_get_contents($entry);
            $extension = strtolower(pathinfo($entry, PATHINFO_EXTENSION) ?: 'jpg');
        } else {
            if (! $zipPath) return false;

            $zip = new ZipArchive();
            if ($zip->open($zipPath) !== true) return false;
            $contents = $zip->getFromName($entry);
            $extension = strtolower(pathinfo($entry, PATHINFO_EXTENSION) ?: 'jpg');
            $zip->close();
        }

        if ($contents === false) return false;

        $safeKey = preg_replace('/[^A-Za-z0-9_-]+/', '_', $key) ?: 'photo';
        $path = "{$folder}/{$safeKey}.{$extension}";
        Storage::disk('local')->put($path, $contents);
        $model->photo = $path;
        return true;
    }

    private function item(array $row, string $type, array $errors, array $data, string $label, ?string $photo = null): array
    {
        return [
            'row' => $row['_row'] ?? null,
            'type' => $type,
            'status' => $errors ? 'error' : $type,
            'errors' => array_values($errors),
            'label' => $label,
            'photo' => $photo,
            'data' => $data,
        ];
    }

    private function summary(array $sheets, array $errors): array
    {
        $all = collect($sheets)->flatten(1);
        return [
            'total' => $all->count(),
            'valid' => $all->whereIn('status', ['new', 'update'])->count(),
            'errors' => $all->where('status', 'error')->count() + count($errors),
            'new' => $all->where('status', 'new')->count(),
            'update' => $all->where('status', 'update')->count(),
        ];
    }

    private function readRows($worksheet): array
    {
        $rows = $worksheet->toArray(null, true, true, false);
        $headers = array_map(fn ($header) => $this->normalize((string) $header), array_shift($rows) ?: []);
        $result = [];
        foreach ($rows as $index => $values) {
            $values = array_pad($values, count($headers), null);
            if (count(array_filter($values, fn ($value) => trim((string) $value) !== '')) === 0) continue;
            $row = array_combine($headers, array_slice($values, 0, count($headers)));
            $row['_row'] = $index + 2;
            $result[] = $row;
        }
        return $result;
    }

    private function findWorksheet($spreadsheet, string $name)
    {
        foreach ($spreadsheet->getWorksheetIterator() as $worksheet) {
            if ($this->normalize($worksheet->getTitle()) === $this->normalize($name)) return $worksheet;
        }
        return null;
    }

    private function photoEntries(?string $zipPath): array
    {
        if (! $zipPath || ! is_file($zipPath)) return [];
        $zip = new ZipArchive();
        if ($zip->open($zipPath) !== true) return [];
        $entries = [];
        for ($index = 0; $index < $zip->numFiles; $index++) {
            $name = $zip->getNameIndex($index);
            if (! preg_match('/\.(jpe?g|png|webp)$/i', $name)) continue;
            $base = strtolower(pathinfo($name, PATHINFO_FILENAME));
            $entries[$base] ??= $name;
            $entries[strtolower($name)] ??= $name;
            $entries[strtolower(basename($name))] ??= $name;
        }
        $zip->close();
        return $entries;
    }

    private function findPhoto(array $entries, string $key): ?string
    {
        $key = trim(str_replace('\\', '/', $key));
        if ($key === '') return null;
        if (is_file($key)) return $key;

        $normalized = strtolower($key);
        $base = strtolower(pathinfo($key, PATHINFO_FILENAME));
        return $entries[$normalized] ?? $entries[strtolower(basename($key))] ?? $entries[$base] ?? null;
    }

    private function value(array $row, array $keys): string
    {
        foreach ($keys as $key) {
            $key = $this->normalize($key);
            if (array_key_exists($key, $row) && trim((string) $row[$key]) !== '') return trim((string) $row[$key]);
        }
        return '';
    }

    private function normalize(string $value): string
    {
        return trim((string) preg_replace('/[^a-z0-9]+/i', '_', strtolower($value)), '_');
    }

    private function splitValues(string $value): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/[,;|]+/', $value) ?: [])));
    }

    private function booleanValue(string $value, bool $default): bool
    {
        if ($value === '') return $default;
        return in_array(strtolower($value), ['1', 'true', 'yes', 'ya', 'aktif'], true);
    }

    private function dayValue(string $value): ?int
    {
        if (is_numeric($value)) return ((int) $value >= 1 && (int) $value <= 6) ? (int) $value : null;
        return match (strtolower(trim($value))) {
            'senin', 'monday' => 1,
            'selasa', 'tuesday' => 2,
            'rabu', 'wednesday' => 3,
            'kamis', 'thursday' => 4,
            'jumat', 'jum\'at', 'friday' => 5,
            'sabtu', 'saturday' => 6,
            default => null,
        };
    }

    private function timeValue(string $value): ?string
    {
        if ($value === '') return null;
        if (is_numeric($value) && (float) $value < 1) {
            $minutes = (int) round(((float) $value) * 24 * 60);
            return sprintf('%02d:%02d', intdiv($minutes, 60) % 24, $minutes % 60);
        }
        $value = str_replace('.', ':', trim($value));
        if (preg_match('/^(\d{1,2}):(\d{2})/', $value, $matches)) return sprintf('%02d:%02d', $matches[1], $matches[2]);
        try { return Carbon::parse($value)->format('H:i'); } catch (\Throwable) { return null; }
    }
}
