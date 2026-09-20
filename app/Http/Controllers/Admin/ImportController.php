<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\OcularImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Throwable;

class ImportController extends Controller
{
    public function __construct(private readonly OcularImportService $imports) {}

    public function create(): View
    {
        return view('admin.imports.create');
    }

    public function preview(Request $request): View
    {
        $request->validate([
            'workbook' => ['required', 'file', 'mimes:xlsx,xls', 'max:10240'],
            'photos' => ['nullable', 'file', 'mimes:zip', 'max:51200'],
        ]);

        $token = (string) Str::uuid();
        $directory = storage_path("app/private/imports/{$token}");
        File::makeDirectory($directory, 0755, true);
        $workbookPath = $directory.'/data.xlsx';
        $request->file('workbook')->move($directory, 'data.xlsx');
        $photosPath = null;
        if ($request->hasFile('photos')) {
            $request->file('photos')->move($directory, 'photos.zip');
            $photosPath = $directory.'/photos.zip';
        }

        try {
            $preview = $this->imports->preview($workbookPath, $photosPath);
        } catch (Throwable $exception) {
            File::deleteDirectory($directory);

            report($exception);

            return redirect()
                ->route('admin.imports.create')
                ->withErrors(['workbook' => 'File Excel tidak valid atau rusak. Download ulang template Ocular lalu simpan kembali sebagai .xlsx sebelum di-upload.']);
        }
        File::put($directory.'/preview.json', json_encode($preview, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return view('admin.imports.create', compact('preview', 'token'));
    }

    public function commit(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'token' => ['required', 'uuid'],
            'selected' => ['nullable', 'array'],
            'selected.*' => ['string'],
            'reset_first' => ['nullable', 'boolean'],
        ]);
        $directory = storage_path("app/private/imports/{$data['token']}");
        $previewPath = $directory.'/preview.json';
        abort_unless(is_file($previewPath), 404);
        $preview = json_decode(File::get($previewPath), true, 512, JSON_THROW_ON_ERROR);
        abort_if(($preview['summary']['errors'] ?? 0) > 0, 422, 'Perbaiki semua error pada preview import sebelum melanjutkan.');

        $counts = $this->imports->commit($preview, $data['selected'] ?? [], $request->boolean('reset_first'));
        File::deleteDirectory($directory);

        return redirect()->route('admin.imports.create')->with('success', sprintf(
            'Import selesai: %d mapel, %d guru, %d siswa, %d jadwal, %d foto%s.',
            $counts['subjects'], $counts['teachers'], $counts['students'], $counts['schedules'], $counts['photos'],
            $counts['reset'] > 0 ? sprintf(' setelah reset %d data lama', $counts['reset']) : '',
        ));
    }

    public function template()
    {
        $spreadsheet = new Spreadsheet();
        $sheets = [
            'subjects' => ['name', 'code'],
            'teachers' => ['name', 'email', 'nip', 'password', 'subject_codes', 'is_active'],
            'students' => ['nis', 'nisn', 'name', 'class_name', 'academic_year', 'semester', 'is_active', 'photo_filename'],
            'schedules' => ['academic_year', 'semester', 'teacher_nip', 'subject_code', 'class_name', 'day', 'start_time', 'end_time'],
        ];
        $examples = [
            'subjects' => ['Pemrograman Web', 'PWEB'],
            'teachers' => ['Rian Pioriandana', 'rian@example.sch.id', '198001012010011001', 'password123', 'PWEB,BD', 1],
            'students' => ['10795', '0093469870', 'Abbiyu Dhimas Palestin', 'XI RPL 2', '2026/2027', 1, 1, '0093469870.jpg'],
            'schedules' => ['2026/2027', 1, '198001012010011001', 'PWEB', 'XI RPL 2', 'Senin', '07:15', '09:30'],
        ];
        foreach ($sheets as $index => $headers) {
            $sheet = $index === 'teachers' ? $spreadsheet->getActiveSheet() : $spreadsheet->createSheet();
            $sheet->setTitle($index);
            $sheet->fromArray([$headers], null, 'A1');
            $sheet->fromArray([$examples[$index]], null, 'A2');
            $sheet->getStyle('A1:'.chr(64 + count($headers)).'1')->getFont()->setBold(true);
            $sheet->freezePane('A2');
            foreach (range('A', chr(64 + count($headers))) as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }
        }

        $path = storage_path('app/private/ocular-import-template.xlsx');
        (new Xlsx($spreadsheet))->save($path);

        return response()->download($path, 'ocular-import-template.xlsx')->deleteFileAfterSend(true);
    }
}
