<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(Request $request): View
    {
        $classes = SchoolClass::query()
            ->with('academicYear')
            ->orderBy('grade_level')
            ->orderBy('name')
            ->get();

        return view('admin.students.index', [
            'students' => Student::query()
                ->with('schoolClass.academicYear')
                ->when($request->filled('class_id'), fn ($query) => $query->where('class_id', $request->integer('class_id')))
                ->when($request->filled('search'), function ($query) use ($request): void {
                    $search = $request->string('search');

                    $query->where(function ($query) use ($search): void {
                        $query->where('name', 'like', "%{$search}%")
                            ->orWhere('nis', 'like', "%{$search}%")
                            ->orWhere('nisn', 'like', "%{$search}%");
                    });
                })
                ->orderBy('name')
                ->paginate(15)
                ->withQueryString(),
            'classes' => $classes,
            'selectedClassId' => $request->input('class_id'),
            'search' => $request->input('search'),
        ]);
    }

    public function create(): View
    {
        return view('admin.students.create', [
            'classes' => $this->classOptions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Student::create($this->validatedData($request));

        return redirect()
            ->route('admin.students.index')
            ->with('success', 'Siswa berhasil dibuat.');
    }

    public function edit(Student $student): View
    {
        return view('admin.students.edit', [
            'student' => $student,
            'classes' => $this->classOptions(),
        ]);
    }

    public function update(Request $request, Student $student): RedirectResponse
    {
        $data = $this->validatedData($request, $student);
        $oldPhoto = $student->photo;

        if ($request->hasFile('photo_upload')) {
            $data['photo'] = $request->file('photo_upload')->store('students/idcard', 'local');
        }

        unset($data['photo_upload']);
        $student->update($data);

        if ($request->hasFile('photo_upload') && $oldPhoto && Storage::disk('local')->exists($oldPhoto)) {
            Storage::disk('local')->delete($oldPhoto);
        }

        return redirect()
            ->route('admin.students.index')
            ->with('success', 'Siswa berhasil diperbarui.');
    }

    public function destroy(Student $student): RedirectResponse
    {
        if ($student->attendances()->exists()) {
            return back()->withErrors([
                'delete' => 'Siswa tidak bisa dihapus karena sudah punya histori absensi. Nonaktifkan siswa sebagai gantinya.',
            ]);
        }

        $student->delete();

        return redirect()
            ->route('admin.students.index')
            ->with('success', 'Siswa berhasil dihapus.');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'student_ids' => ['required', 'array', 'min:1'],
            'student_ids.*' => ['integer', 'distinct', 'exists:students,id'],
        ]);

        $ids = array_values(array_unique($data['student_ids']));
        $blocked = Student::query()
            ->whereIn('id', $ids)
            ->whereHas('attendances')
            ->pluck('name');
        $deleted = Student::query()
            ->whereIn('id', $ids)
            ->whereDoesntHave('attendances')
            ->delete();

        $response = back()->with('success', "{$deleted} siswa berhasil dihapus.");
        if ($blocked->isNotEmpty()) {
            $response->with('warning', $blocked->count().' siswa dilewati karena sudah memiliki histori absensi.');
        }

        return $response;
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate(['file' => ['required', 'file', 'mimes:csv,txt', 'max:2048']]);
        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');
        $headers = array_map(fn ($header) => strtolower(trim((string) $header)), fgetcsv($handle) ?: []);
        $required = ['nis', 'nisn', 'name', 'class_id'];
        $missing = array_diff($required, $headers);

        if ($missing) {
            fclose($handle);
            return back()->withErrors(['file' => 'Header CSV wajib: '.implode(', ', $required)]);
        }

        $success = 0;
        $errors = [];
        $rowNumber = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;
            if (count(array_filter($row, fn ($value) => trim((string) $value) !== '')) === 0) continue;
            $data = array_combine($headers, array_pad($row, count($headers), null));
            $validator = Validator::make($data, [
                'nis' => ['required', 'string', 'max:255', 'unique:students,nis'],
                'nisn' => ['required', 'string', 'max:255', 'unique:students,nisn'],
                'name' => ['required', 'string', 'max:255'],
                'class_id' => ['required', 'integer', 'exists:classes,id'],
                'is_active' => ['nullable', 'boolean'],
            ]);

            if ($validator->fails()) {
                $errors[] = "Baris {$rowNumber}: ".implode(' ', $validator->errors()->all());
                continue;
            }

            Student::create([
                'nis' => $data['nis'], 'nisn' => $data['nisn'], 'name' => $data['name'],
                'class_id' => $data['class_id'], 'is_active' => filter_var($data['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN),
            ]);
            $success++;
        }

        fclose($handle);
        $message = "Import selesai: {$success} baris berhasil.";
        if ($errors) $message .= ' '.count($errors).' baris gagal. '.implode(' | ', array_slice($errors, 0, 3));

        return redirect()->route('admin.students.index')->with($errors ? 'warning' : 'success', $message);
    }

    public function promotion(): View
    {
        return view('admin.students.promotion', ['classes' => $this->classOptions()]);
    }

    public function promote(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'from_class_id' => ['required', 'different:to_class_id', 'exists:classes,id'],
            'to_class_id' => ['required', 'exists:classes,id'],
        ]);

        $count = Student::where('class_id', $data['from_class_id'])->where('is_active', true)->update(['class_id' => $data['to_class_id']]);

        return redirect()->route('admin.students.promotion')->with('success', "{$count} siswa berhasil dipindahkan.");
    }

    private function validatedData(Request $request, ?Student $student = null): array
    {
        return $request->validate([
            'nis' => [
                'required',
                'string',
                'max:255',
                Rule::unique('students', 'nis')->ignore($student?->id),
            ],
            'nisn' => [
                'required',
                'string',
                'max:255',
                Rule::unique('students', 'nisn')->ignore($student?->id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'class_id' => ['required', 'integer', 'exists:classes,id'],
            'photo' => ['nullable', 'string', 'max:255'],
            'photo_upload' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'is_active' => ['nullable', 'boolean'],
        ]) + [
            'is_active' => false,
        ];
    }

    private function classOptions()
    {
        return SchoolClass::query()
            ->with('academicYear')
            ->orderBy('grade_level')
            ->orderBy('name')
            ->get();
    }
}
