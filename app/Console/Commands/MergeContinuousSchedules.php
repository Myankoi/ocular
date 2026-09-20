<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\Schedule;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MergeContinuousSchedules extends Command
{
    protected $signature = 'schedules:merge-continuous {--dry-run : Show changes without writing to the database}';

    protected $description = 'Merge active schedule rows that are split into consecutive time blocks.';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $groups = $this->groups();
        $mergedGroups = 0;
        $deletedSchedules = 0;
        $movedSessions = 0;
        $movedAttendances = 0;

        DB::transaction(function () use ($groups, $dryRun, &$mergedGroups, &$deletedSchedules, &$movedSessions, &$movedAttendances): void {
            foreach ($groups as $group) {
                foreach ($this->segments($group) as $segment) {
                    if ($segment->count() < 2) {
                        continue;
                    }

                    $primary = $segment->first();
                    $duplicates = $segment->slice(1)->values();
                    $mergedGroups++;
                    $deletedSchedules += $duplicates->count();

                    if ($dryRun) {
                        $this->line(sprintf(
                            'Would merge schedules %s into #%d (%s-%s).',
                            $duplicates->pluck('id')->join(', '),
                            $primary->id,
                            substr((string) $primary->start_time, 0, 5),
                            substr((string) $segment->last()->end_time, 0, 5),
                        ));
                        continue;
                    }

                    $primary->update(['end_time' => $segment->last()->end_time]);

                    foreach ($duplicates as $duplicate) {
                        [$sessions, $attendances] = $this->moveSessions($duplicate, $primary);
                        $movedSessions += $sessions;
                        $movedAttendances += $attendances;
                        $duplicate->delete();
                    }
                }
            }
        });

        $this->info(sprintf(
            '%s %d schedule groups, %d duplicate schedules, %d sessions, %d attendances.',
            $dryRun ? 'Checked' : 'Merged',
            $mergedGroups,
            $deletedSchedules,
            $movedSessions,
            $movedAttendances,
        ));

        return self::SUCCESS;
    }

    private function groups()
    {
        return Schedule::query()
            ->whereNull('archived_at')
            ->orderBy('academic_year_id')
            ->orderBy('user_id')
            ->orderBy('subject_id')
            ->orderBy('class_id')
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get()
            ->groupBy(fn (Schedule $schedule): string => implode('|', [
                $schedule->academic_year_id,
                $schedule->user_id,
                $schedule->subject_id,
                $schedule->class_id,
                $schedule->day_of_week,
            ]));
    }

    private function segments($group): array
    {
        $segments = [];
        $current = collect();

        foreach ($group->sortBy('start_time')->values() as $schedule) {
            $last = $current->last();
            $lastEnd = $last ? substr((string) $last->end_time, 0, 5) : null;
            $start = substr((string) $schedule->start_time, 0, 5);

            if ($last && $lastEnd !== $start) {
                $segments[] = $current;
                $current = collect();
            }

            $current->push($schedule);
        }

        if ($current->isNotEmpty()) {
            $segments[] = $current;
        }

        return $segments;
    }

    private function moveSessions(Schedule $from, Schedule $to): array
    {
        $movedSessions = 0;
        $movedAttendances = 0;

        foreach ($from->attendanceSessions()->with('attendances')->get() as $session) {
            $target = AttendanceSession::query()
                ->where('schedule_id', $to->id)
                ->whereDate('date', $session->date)
                ->first();

            if (! $target) {
                $session->update(['schedule_id' => $to->id]);
                $movedSessions++;
                continue;
            }

            foreach ($session->attendances as $attendance) {
                $exists = Attendance::query()
                    ->where('session_id', $target->id)
                    ->where('student_id', $attendance->student_id)
                    ->exists();

                if ($exists) {
                    $attendance->delete();
                    continue;
                }

                $attendance->update(['session_id' => $target->id]);
                $movedAttendances++;
            }

            $session->delete();
        }

        return [$movedSessions, $movedAttendances];
    }
}
