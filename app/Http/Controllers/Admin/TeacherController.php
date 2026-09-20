<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TeacherController extends Controller
{
    public function resetPassword(Request $request, User $teacher): RedirectResponse
    {
        abort_unless($teacher->role === 'guru', 404);

        $data = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $teacher->update(['password' => Hash::make($data['password'])]);

        return back()->with('success', 'Password guru berhasil direset.');
    }

    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search', $request->input('q', '')));

        return view('admin.teachers.index', [
            'teachers' => User::query()
                ->where('role', 'guru')
                ->with('subjects')
                ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('nip', 'like', "%{$search}%");
                }))
                ->orderBy('name')
                ->paginate(10)
                ->withQueryString(),
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return view('admin.teachers.create', [
            'subjects' => Subject::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $subjectIds = $data['subject_ids'] ?? [];

        unset($data['subject_ids']);

        $teacher = User::create([
            ...$data,
            'role' => 'guru',
            'password' => Hash::make($data['password']),
            'is_active' => $request->boolean('is_active'),
        ]);

        $teacher->subjects()->sync($subjectIds);

        return redirect()
            ->route('admin.teachers.index')
            ->with('success', 'Guru berhasil dibuat.');
    }

    public function edit(User $teacher): View
    {
        abort_unless($teacher->role === 'guru', 404);

        return view('admin.teachers.edit', [
            'teacher' => $teacher->load('subjects'),
            'subjects' => Subject::query()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, User $teacher): RedirectResponse
    {
        abort_unless($teacher->role === 'guru', 404);

        $data = $this->validatedData($request, $teacher);
        $subjectIds = $data['subject_ids'] ?? [];

        unset($data['subject_ids']);
        unset($data['password']);

        $teacher->update([
            ...$data,
            'is_active' => $request->boolean('is_active'),
        ]);

        if ($request->filled('password')) {
            $teacher->update(['password' => Hash::make($request->string('password')->toString())]);
        }

        $teacher->subjects()->sync($subjectIds);

        return redirect()
            ->route('admin.teachers.index')
            ->with('success', 'Guru berhasil diperbarui.');
    }

    public function destroy(User $teacher): RedirectResponse
    {
        abort_unless($teacher->role === 'guru', 404);

        if ($teacher->schedules()->exists()) {
            $archived = $teacher->schedules()->whereNull('archived_at')->update(['archived_at' => now()]);
            $teacher->subjects()->detach();
            $teacher->update(['is_active' => false]);

            return redirect()
                ->route('admin.teachers.index')
                ->with('success', "Guru dinonaktifkan dan {$archived} jadwal aktifnya diarsipkan.");
        }

        $teacher->subjects()->detach();
        $teacher->delete();

        return redirect()
            ->route('admin.teachers.index')
            ->with('success', 'Guru berhasil dihapus.');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'teacher_ids' => ['required', 'array', 'min:1'],
            'teacher_ids.*' => ['integer', 'exists:users,id'],
        ]);

        $ids = array_values(array_unique($data['teacher_ids']));
        $teachers = User::query()
            ->whereIn('id', $ids)
            ->where('role', 'guru')
            ->get();
        $deletable = $teachers->filter(fn (User $teacher): bool => ! $teacher->schedules()->exists());
        $archivable = $teachers->filter(fn (User $teacher): bool => $teacher->schedules()->exists());

        $deletable->each(function (User $teacher): void {
            $teacher->subjects()->detach();
            $teacher->delete();
        });

        $archivedSchedules = 0;
        $archivable->each(function (User $teacher) use (&$archivedSchedules): void {
            $archivedSchedules += $teacher->schedules()->whereNull('archived_at')->update(['archived_at' => now()]);
            $teacher->subjects()->detach();
            $teacher->update(['is_active' => false]);
        });

        $redirect = redirect()->route('admin.teachers.index');
        $messages = [];
        if ($deletable->isNotEmpty()) {
            $messages[] = $deletable->count() . ' guru dihapus';
        }
        if ($archivable->isNotEmpty()) {
            $messages[] = $archivable->count() . " guru dinonaktifkan dan {$archivedSchedules} jadwal diarsipkan";
        }
        if ($messages) {
            $redirect->with('success', implode(', ', $messages) . '.');
        }

        return $redirect;
    }

    private function validatedData(Request $request, ?User $teacher = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($teacher?->id),
            ],
            'nip' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('users', 'nip')->ignore($teacher?->id),
            ],
            'password' => [$teacher ? 'nullable' : 'required', 'string', 'min:8'],
            'photo' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
            'subject_ids' => ['nullable', 'array'],
            'subject_ids.*' => ['integer', 'exists:subjects,id'],
        ]);
    }
}
