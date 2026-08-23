<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\AttendanceSession;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StudentPhotoController extends Controller
{
    public function show(Request $request, AttendanceSession $attendanceSession, Student $student)
    {
        $attendanceSession->load('schedule');

        abort_unless($attendanceSession->schedule?->user_id === $request->user()->id, 403);
        abort_unless($attendanceSession->schedule?->class_id === $student->class_id, 404);
        abort_unless($student->photo && Storage::disk('local')->exists($student->photo), 404);

        return response()->file(Storage::disk('local')->path($student->photo), [
            'Cache-Control' => 'private, max-age=600',
        ]);
    }
}
