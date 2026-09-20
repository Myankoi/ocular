<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StudentPhotoController extends Controller
{
    public function show(Request $request, Student $student)
    {
        abort_unless($student->photo && Storage::disk('local')->exists($student->photo), 404);

        return response()->file(Storage::disk('local')->path($student->photo), [
            'Cache-Control' => 'private, max-age=600',
        ]);
    }
}
