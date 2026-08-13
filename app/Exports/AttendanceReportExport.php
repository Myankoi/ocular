<?php

namespace App\Exports;

use App\Models\Attendance;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AttendanceReportExport implements FromCollection, WithHeadings
{
    public function __construct(private readonly array $filters) {}

    public function collection(): Collection
    {
        return $this->query()->get()->map(fn (Attendance $attendance): array => [
            $attendance->session->date->format('Y-m-d'),
            $attendance->session->schedule->schoolClass->name,
            $attendance->session->schedule->subject->name,
            $attendance->session->schedule->teacher->name,
            $attendance->student->nis,
            $attendance->student->nisn,
            $attendance->student->name,
            strtoupper($attendance->status),
            $attendance->updatedBy?->name ?? '-',
        ]);
    }

    public function headings(): array
    {
        return ['Tanggal', 'Kelas', 'Mata Pelajaran', 'Guru', 'NIS', 'NISN', 'Nama Siswa', 'Status', 'Diubah Oleh'];
    }

    private function query()
    {
        return Attendance::query()
            ->with([
                'student',
                'updatedBy',
                'session.schedule.schoolClass',
                'session.schedule.subject',
                'session.schedule.teacher',
            ])
            ->when($this->filters['academic_year_id'] ?? null, fn ($query, $value) => $query->whereHas('session.schedule', fn ($schedule) => $schedule->where('academic_year_id', $value)))
            ->when($this->filters['class_id'] ?? null, fn ($query, $value) => $query->whereHas('session.schedule', fn ($schedule) => $schedule->where('class_id', $value)))
            ->when($this->filters['subject_id'] ?? null, fn ($query, $value) => $query->whereHas('session.schedule', fn ($schedule) => $schedule->where('subject_id', $value)))
            ->when($this->filters['user_id'] ?? null, fn ($query, $value) => $query->whereHas('session.schedule', fn ($schedule) => $schedule->where('user_id', $value)))
            ->when($this->filters['date_from'] ?? null, fn ($query, $value) => $query->whereHas('session', fn ($session) => $session->whereDate('date', '>=', $value)))
            ->when($this->filters['date_to'] ?? null, fn ($query, $value) => $query->whereHas('session', fn ($session) => $session->whereDate('date', '<=', $value)))
            ->latest('id');
    }
}
