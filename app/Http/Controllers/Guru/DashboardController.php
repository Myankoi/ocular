<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $today = now()->dayOfWeekIso;
        $date = now()->toDateString();

        return view('guru.dashboard', [
            'todaySchedules' => Schedule::query()
                ->with([
                    'subject',
                    'schoolClass',
                    'attendanceSessions' => fn ($query) => $query->whereDate('date', $date),
                ])
                ->where('user_id', $request->user()->id)
                ->where('day_of_week', $today)
                ->orderBy('start_time')
                ->get(),
        ]);
    }
}
