<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SubjectController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim($request->string('q')->toString());
        return view('admin.subjects.index', [
            'subjects' => Subject::query()
                ->withCount(['teachers', 'schedules'])
                ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")->orWhere('code', 'like', "%{$search}%");
                }))
                ->orderBy('name')
                ->paginate(10)
                ->withQueryString(),
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return view('admin.subjects.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Subject::create($this->validatedData($request));

        return redirect()
            ->route('admin.subjects.index')
            ->with('success', 'Mata pelajaran berhasil dibuat.');
    }

    public function edit(Subject $subject): View
    {
        return view('admin.subjects.edit', [
            'subject' => $subject,
        ]);
    }

    public function update(Request $request, Subject $subject): RedirectResponse
    {
        $subject->update($this->validatedData($request, $subject));

        return redirect()
            ->route('admin.subjects.index')
            ->with('success', 'Mata pelajaran berhasil diperbarui.');
    }

    public function destroy(Subject $subject): RedirectResponse
    {
        if ($subject->teachers()->exists() || $subject->schedules()->exists()) {
            return back()->withErrors([
                'delete' => 'Mata pelajaran tidak bisa dihapus karena sudah dipakai guru atau jadwal.',
            ]);
        }

        $subject->delete();

        return redirect()
            ->route('admin.subjects.index')
            ->with('success', 'Mata pelajaran berhasil dihapus.');
    }

    private function validatedData(Request $request, ?Subject $subject = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('subjects', 'code')->ignore($subject?->id),
            ],
        ]);
    }
}
