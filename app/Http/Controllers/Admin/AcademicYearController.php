<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AcademicYearController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search', $request->input('q', '')));
        return view('admin.academic-years.index', [
            'academicYears' => AcademicYear::query()
                ->when($search !== '', fn ($query) => $query->where('name', 'like', "%{$search}%"))
                ->orderByDesc('is_active')
                ->orderByDesc('start_date')
                ->paginate(10)
                ->withQueryString(),
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return view('admin.academic-years.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);

        DB::transaction(function () use ($data): void {
            if ($data['is_active']) {
                AcademicYear::query()->update(['is_active' => false]);
            }

            AcademicYear::create($data);
        });

        return redirect()
            ->route('admin.academic-years.index')
            ->with('success', 'Tahun ajaran berhasil dibuat.');
    }

    public function edit(AcademicYear $academicYear): View
    {
        return view('admin.academic-years.edit', [
            'academicYear' => $academicYear,
        ]);
    }

    public function update(Request $request, AcademicYear $academicYear): RedirectResponse
    {
        $data = $this->validatedData($request, $academicYear);

        DB::transaction(function () use ($academicYear, $data): void {
            if ($data['is_active']) {
                AcademicYear::query()
                    ->whereKeyNot($academicYear->id)
                    ->update(['is_active' => false]);
            }

            $academicYear->update($data);
        });

        return redirect()
            ->route('admin.academic-years.index')
            ->with('success', 'Tahun ajaran berhasil diperbarui.');
    }

    public function destroy(AcademicYear $academicYear): RedirectResponse
    {
        if ($academicYear->classes()->exists() || $academicYear->schedules()->exists()) {
            return back()->withErrors([
                'delete' => 'Tahun ajaran tidak bisa dihapus karena sudah punya kelas atau jadwal.',
            ]);
        }

        $academicYear->delete();

        return redirect()
            ->route('admin.academic-years.index')
            ->with('success', 'Tahun ajaran berhasil dihapus.');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'academic_year_ids' => ['required', 'array', 'min:1'],
            'academic_year_ids.*' => ['integer', 'exists:academic_years,id'],
        ]);

        $years = AcademicYear::query()->whereIn('id', array_unique($data['academic_year_ids']))->get();
        $deletable = $years->filter(fn (AcademicYear $year): bool => ! $year->classes()->exists() && ! $year->schedules()->exists());
        $skipped = $years->count() - $deletable->count();
        $deletable->each->delete();

        $redirect = redirect()->route('admin.academic-years.index');
        if ($deletable->isNotEmpty()) {
            $redirect->with('success', $deletable->count() . ' tahun ajaran berhasil dihapus.');
        }
        if ($skipped > 0) {
            $redirect->with('warning', $skipped . ' tahun ajaran dilewati karena masih memiliki kelas atau jadwal.');
        }

        return $redirect;
    }

    private function validatedData(Request $request, ?AcademicYear $academicYear = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'semester' => [
                'required',
                'integer',
                Rule::in([1, 2]),
                Rule::unique('academic_years', 'semester')
                    ->where('name', $request->input('name'))
                    ->ignore($academicYear?->id),
            ],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'is_active' => ['nullable', 'boolean'],
        ]) + [
            'is_active' => false,
        ];
    }
}
