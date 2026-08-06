<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'totalStudents' => Student::where('is_active', true)->count(),
            'totalTeachers' => User::where('role', 'guru')->where('is_active', true)->count(),
            'totalClasses' => SchoolClass::count(),
            'totalAttendances' => Attendance::count(),
        ]);
    }
}
