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
    public function index(): View
    {
        return view('admin.teachers.index', [
            'teachers' => User::query()
                ->where('role', 'guru')
                ->with('subjects')
                ->orderBy('name')
                ->paginate(10),
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

        $teacher->update([
            ...$data,
            'is_active' => $request->boolean('is_active'),
            ...($data['password'] ? ['password' => Hash::make($data['password'])] : []),
        ]);

        $teacher->subjects()->sync($subjectIds);

        return redirect()
            ->route('admin.teachers.index')
            ->with('success', 'Guru berhasil diperbarui.');
    }

    public function destroy(User $teacher): RedirectResponse
    {
        abort_unless($teacher->role === 'guru', 404);

        if ($teacher->schedules()->exists()) {
            return back()->withErrors([
                'delete' => 'Guru tidak bisa dihapus karena sudah punya jadwal.',
            ]);
        }

        $teacher->subjects()->detach();
        $teacher->delete();

        return redirect()
            ->route('admin.teachers.index')
            ->with('success', 'Guru berhasil dihapus.');
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
