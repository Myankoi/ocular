<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SchoolClassController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim($request->string('q')->toString());
        return view('admin.classes.index', [
            'classes' => SchoolClass::query()
                ->with('academicYear')
                ->when($search !== '', fn ($query) => $query->where('name', 'like', "%{$search}%"))
                ->orderBy('grade_level')
                ->orderBy('name')
                ->paginate(10)
                ->withQueryString(),
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return view('admin.classes.create', [
            'academicYears' => AcademicYear::query()
                ->orderByDesc('is_active')
                ->orderByDesc('start_date')
                ->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        SchoolClass::create($this->validatedData($request));

        return redirect()
            ->route('admin.classes.index')
            ->with('success', 'Kelas berhasil dibuat.');
    }

    public function edit(SchoolClass $schoolClass): View
    {
        return view('admin.classes.edit', [
            'class' => $schoolClass,
            'academicYears' => AcademicYear::query()
                ->orderByDesc('is_active')
                ->orderByDesc('start_date')
                ->get(),
        ]);
    }

    public function update(Request $request, SchoolClass $schoolClass): RedirectResponse
    {
        $schoolClass->update($this->validatedData($request, $schoolClass));

        return redirect()
            ->route('admin.classes.index')
            ->with('success', 'Kelas berhasil diperbarui.');
    }

    public function destroy(SchoolClass $schoolClass): RedirectResponse
    {
        if ($schoolClass->students()->exists() || $schoolClass->schedules()->exists()) {
            return back()->withErrors([
                'delete' => 'Kelas tidak bisa dihapus karena sudah punya siswa atau jadwal.',
            ]);
        }

        $schoolClass->delete();

        return redirect()
            ->route('admin.classes.index')
            ->with('success', 'Kelas berhasil dihapus.');
    }

    private function validatedData(Request $request, ?SchoolClass $schoolClass = null): array
    {
        return $request->validate([
            'academic_year_id' => ['required', 'integer', 'exists:academic_years,id'],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('classes', 'name')
                    ->where('academic_year_id', $request->input('academic_year_id'))
                    ->ignore($schoolClass?->id),
            ],
            'grade_level' => ['required', 'integer', Rule::in([10, 11, 12])],
        ]);
    }
}
