<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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
        $student->update($this->validatedData($request, $student));

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
