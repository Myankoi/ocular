<x-layouts.app title="Jadwal Mengajar - Ocular">
    <a href="{{ route('guru.dashboard') }}" class="mb-4 inline-flex min-h-10 items-center gap-2 text-xs font-bold uppercase tracking-wider text-ocular-teal hover:text-ocular-teal-dark"><span class="text-base">←</span> Dashboard</a>
    <x-ui.page eyebrow="Jadwal Guru" title="Semua jadwal" description="Jadwal mengajar Anda pada tahun ajaran aktif.">
        <x-ui.card padding="p-4" class="border border-ocular-teal/15">
            <form method="GET" class="flex items-center gap-2">
                <label class="sr-only" for="schedule-search">Cari jadwal</label>
                <div class="relative flex-1">
                    <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-ocular-accent">⌕</span>
                    <input id="schedule-search" name="q" value="{{ $search }}" placeholder="Cari kelas atau mata pelajaran..." class="min-h-12 w-full border border-slate-300 bg-white pl-10 pr-4 text-sm outline-none focus:border-ocular-teal focus:ring-2 focus:ring-ocular-teal/20">
                </div>
                <div class="flex shrink-0 gap-2">
                    <x-ui.button variant="teal">Cari</x-ui.button>
                    @if ($search !== '')
                        <x-ui.link-button :href="route('guru.schedules.index')" variant="muted">Reset</x-ui.link-button>
                    @endif
                </div>
            </form>
        </x-ui.card>

        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="text-sm font-black uppercase tracking-widest text-ocular-teal">Jadwal mengajar</h2>
                @if ($activeYear)
                    <p class="mt-1 text-xs text-ocular-copy/60">{{ $activeYear->name }} · {{ $activeYear->semester === 1 ? 'Ganjil' : 'Genap' }}</p>
                @endif
            </div>
            <span class="font-mono text-[10px] font-bold text-ocular-accent">{{ $schedules->total() }} jadwal</span>
        </div>

        <div class="space-y-4">
            @forelse ($schedules as $schedule)
                <x-ui.card class="relative overflow-hidden border-l-4 border-ocular-accent">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="bg-ocular-teal/10 px-2 py-1 text-[10px] font-black uppercase tracking-wider text-ocular-teal">{{ $days[$schedule->day_of_week] ?? '-' }}</span>
                                <span class="font-mono text-xs font-bold text-ocular-copy">{{ substr($schedule->start_time, 0, 5) }} – {{ substr($schedule->end_time, 0, 5) }}</span>
                            </div>
                            <h3 class="mt-3 text-xl font-black leading-tight text-ocular-teal">{{ $schedule->schoolClass->name }}</h3>
                            <p class="mt-1 text-sm font-medium text-ocular-copy">{{ $schedule->subject->name }}</p>
                        </div>
                        <div class="text-left sm:text-right">
                            <p class="text-xs font-semibold text-ocular-copy/70">{{ $schedule->academicYear->name }} · {{ $schedule->academicYear->semester === 1 ? 'Ganjil' : 'Genap' }}</p>
                        </div>
                    </div>
                </x-ui.card>
            @empty
                <div class="border-2 border-dashed border-ocular-accent/20 px-5 py-10 text-center text-sm text-ocular-copy/60">Belum ada jadwal mengajar pada tahun ajaran aktif.</div>
            @endforelse
        </div>

        <x-ui.pagination :paginator="$schedules" />
    </x-ui.page>
</x-layouts.app>
