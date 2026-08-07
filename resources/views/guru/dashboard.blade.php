<div class="divide-y divide-slate-200">
    @forelse ($todaySchedules as $schedule)
        @php
            $session = $schedule->attendanceSessions->first();
        @endphp

        <div class="flex flex-col gap-3 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="font-medium">{{ $schedule->subject->name }}</p>
                <p class="text-sm text-slate-500">
                    {{ $schedule->schoolClass->name }} · {{ substr($schedule->start_time, 0, 5) }} - {{ substr($schedule->end_time, 0, 5) }}
                </p>
            </div>

            <div>
                @if ($session)
                    <a href="{{ route('guru.sessions.show', $session) }}" class="inline-flex rounded-md bg-slate-900 px-3 py-2 text-sm text-white">
                        Buka Sesi
                    </a>
                @else
                    <form method="POST" action="{{ route('guru.sessions.store', $schedule) }}">
                        @csrf
                        <button class="rounded-md bg-slate-900 px-3 py-2 text-sm text-white">
                            Mulai Absensi
                        </button>
                    </form>
                @endif
            </div>
        </div>
    @empty
        <div class="px-5 py-8 text-sm text-slate-500">
            Tidak ada jadwal RPL hari ini.
        </div>
    @endforelse
</div>
