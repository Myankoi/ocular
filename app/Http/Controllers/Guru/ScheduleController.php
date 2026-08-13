<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    public function index(Request $request): View
    {
        $activeYear = AcademicYear::query()->where('is_active', true)->first();
        $search = trim($request->string('q')->toString());

        $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
        ]);

        return view('guru.schedules.index', [
            'activeYear' => $activeYear,
            'days' => [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu'],
            'schedules' => Schedule::query()
                ->with(['academicYear', 'subject', 'schoolClass'])
                ->where('user_id', $request->user()->id)
                ->when($activeYear, fn ($query) => $query->where('academic_year_id', $activeYear->id))
                ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search): void {
                    $query->whereHas('schoolClass', fn ($class) => $class->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('subject', fn ($subject) => $subject->where('name', 'like', "%{$search}%"));
                }))
                ->orderBy('day_of_week')
                ->orderBy('start_time')
                ->paginate(15)
                ->withQueryString(),
            'search' => $search,
        ]);
    }
}
