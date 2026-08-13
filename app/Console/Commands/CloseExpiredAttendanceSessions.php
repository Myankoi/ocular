<?php

namespace App\Console\Commands;

use App\Models\AttendanceSession;
use Illuminate\Console\Command;

class CloseExpiredAttendanceSessions extends Command
{
    protected $signature = 'attendance:close-expired';

    protected $description = 'Close open attendance sessions after their scheduled end time';

    public function handle(): int
    {
        $closed = 0;

        AttendanceSession::query()
            ->with('schedule')
            ->where('status', 'open')
            ->whereDate('date', '<=', today())
            ->get()
            ->each(function (AttendanceSession $session) use (&$closed): void {
                $end = $session->date->copy()->setTimeFromTimeString($session->schedule->end_time);

                if (now()->greaterThanOrEqualTo($end)) {
                    $session->update(['status' => 'closed', 'closed_at' => $end]);
                    $closed++;
                }
            });

        $this->info("Closed {$closed} expired session(s).");

        return self::SUCCESS;
    }
}
