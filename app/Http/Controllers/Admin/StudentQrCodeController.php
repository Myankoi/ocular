<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use ZipArchive;

class StudentQrCodeController extends Controller
{
    public function index(Request $request): View
    {
        $classes = SchoolClass::query()
            ->with('academicYear')
            ->orderBy('grade_level')
            ->orderBy('name')
            ->get();

        $students = Student::query()
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
            ->withQueryString();

        return view('admin.students.qr.index', [
            'classes' => $classes,
            'students' => $students,
            'selectedClassId' => $request->input('class_id'),
            'search' => $request->input('search'),
        ]);
    }

    public function show(Student $student)
    {
        return Response::make($this->qrPng($student), 200, [
            'Content-Type' => 'image/png',
        ]);
    }

    public function download(Student $student)
    {
        return Response::make($this->qrPng($student), 200, [
            'Content-Type' => 'image/png',
            'Content-Disposition' => 'attachment; filename="' . $this->fileName($student) . '"',
        ]);
    }

    public function downloadClass(SchoolClass $schoolClass)
    {
        $students = $schoolClass->students()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        if ($students->isEmpty()) {
            return back()->withErrors([
                'download' => 'Kelas ini belum punya siswa aktif.',
            ]);
        }

        $zipPath = storage_path('app/qr-codes-' . $schoolClass->id . '-' . now()->format('YmdHis') . '.zip');

        $zip = new ZipArchive();

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return back()->withErrors([
                'download' => 'Gagal membuat file ZIP QR Code.',
            ]);
        }

        foreach ($students as $student) {
            $zip->addFromString($this->fileName($student), $this->qrPng($student));
        }

        $zip->close();

        return response()
            ->download($zipPath, 'qr-codes-' . str($schoolClass->name)->slug() . '.zip')
            ->deleteFileAfterSend(true);
    }

    private function qrPng(Student $student): string
    {
        return QrCode::format('png')
            ->size(512)
            ->margin(2)
            ->generate($student->nisn);
    }

    private function fileName(Student $student): string
    {
        return str($student->nisn . '-' . $student->name)->slug() . '.png';
    }
}
