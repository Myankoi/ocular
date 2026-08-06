<x-layouts.app title="Guru Dashboard - Ocular">
    <div class="space-y-6">
        <div>
            <p class="text-sm font-medium uppercase tracking-wide text-slate-500">Guru</p>
            <h1 class="text-2xl font-semibold tracking-tight">Dashboard Guru</h1>
        </div>

        <div class="rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4">
                <h2 class="font-semibold">Jadwal Hari Ini</h2>
            </div>

            <div class="divide-y divide-slate-200">
                @forelse ($todaySchedules as $schedule)
                    <div class="flex flex-col gap-1 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="font-medium">{{ $schedule->subject->name }}</p>
                            <p class="text-sm text-slate-500">{{ $schedule->schoolClass->name }}</p>
                        </div>
                        <p class="text-sm text-slate-600">
                            {{ substr($schedule->start_time, 0, 5) }} - {{ substr($schedule->end_time, 0, 5) }}
                        </p>
                    </div>
                @empty
                    <div class="px-5 py-8 text-sm text-slate-500">
                        Tidak ada jadwal RPL hari ini.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.app>
