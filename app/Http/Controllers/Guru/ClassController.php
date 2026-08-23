<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClassController extends Controller
{
    public function __invoke(Request $request): View
    {
        $activeYear = AcademicYear::query()->where('is_active', true)->first();

        $classes = SchoolClass::query()
            ->with([
                'academicYear',
                'schedules' => fn ($query) => $query
                    ->where('user_id', $request->user()->id)
                    ->whereNull('archived_at')
                    ->with('subject')
                    ->orderBy('day_of_week')
                    ->orderBy('start_time'),
            ])
            ->withCount(['students as active_students_count' => fn ($query) => $query->where('is_active', true)])
            ->whereHas('schedules', fn ($query) => $query
                ->where('user_id', $request->user()->id)
                ->whereNull('archived_at')
                ->when($activeYear, fn ($query) => $query->where('academic_year_id', $activeYear->id)))
            ->when($activeYear, fn ($query) => $query->where('academic_year_id', $activeYear->id))
            ->orderBy('grade_level')
            ->orderBy('name')
            ->get();

        $selectedClass = null;
        $students = null;
        $selectedClassId = $request->integer('class_id');

        if ($selectedClassId) {
            $selectedClass = $classes->firstWhere('id', $selectedClassId);
        }

        if (! $selectedClass && $classes->isNotEmpty()) {
            $selectedClass = $classes->first();
        }

        if ($selectedClass) {
            $students = $selectedClass->students()
                ->where('is_active', true)
                ->orderBy('name')
                ->paginate(20, ['*'], 'student_page')
                ->withQueryString();
        }

        return view('guru.classes.index', [
            'activeYear' => $activeYear,
            'classes' => $classes,
            'selectedClass' => $selectedClass,
            'students' => $students,
        ]);
    }
}
