<x-layouts.app title="Sesi Absensi - Ocular">
    <x-ui.page eyebrow="Sesi Guru" title="Sesi absensi hari ini" description="Akses cepat untuk membuka atau melanjutkan scan absensi tanpa lewat dashboard.">
        <x-slot:actions>
            <x-ui.link-button :href="route('guru.schedules.index')" variant="outline">
                <i data-lucide="calendar-clock" class="size-4"></i>
                Jadwal
            </x-ui.link-button>
        </x-slot:actions>

        <div class="grid gap-4 sm:grid-cols-3">
            <x-ui.stat-card label="Jadwal hari ini" :value="$schedules->count()" tone="teal" />
            <x-ui.stat-card label="Sesi terbuka" :value="$schedules->filter(fn ($schedule) => $schedule->attendanceSessions->first()?->status === 'open')->count()" tone="success" />
            <x-ui.stat-card label="Sesi belum dibuka" :value="$schedules->filter(fn ($schedule) => ! $schedule->attendanceSessions->first())->count()" tone="warning" />
        </div>

        <x-ui.card padding="p-0" class="overflow-hidden border border-slate-200">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-4 py-4 sm:px-5">
                <div>
                    <h2 class="text-sm font-black uppercase tracking-widest text-ocular-teal">Jadwal {{ $date->translatedFormat('l') }}</h2>
                    <p class="mt-1 font-mono text-[10px] font-bold uppercase text-ocular-accent">{{ $date->format('d/m/Y') }}</p>
                </div>
                @if ($activeYear)
                    <span class="border border-ocular-teal/15 bg-ocular-teal/5 px-3 py-2 text-[10px] font-black uppercase tracking-wider text-ocular-teal">{{ $activeYear->name }} · {{ $activeYear->semester === 1 ? 'Ganjil' : 'Genap' }}</span>
                @endif
            </div>

            <div class="divide-y divide-slate-100">
                @forelse ($schedules as $schedule)
                    @php
                        $session = $schedule->attendanceSessions->first();
                        $total = $session?->attendances_count ?? 0;
                        $present = $session?->hadir_count ?? 0;
                        $rate = $total > 0 ? round($present / $total * 100) : 0;
                    @endphp
                    <article class="grid gap-4 p-4 sm:grid-cols-[minmax(0,1fr)_auto] sm:items-center sm:p-5">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="font-mono text-xs font-bold text-ocular-copy">{{ substr($schedule->start_time, 0, 5) }} – {{ substr($schedule->end_time, 0, 5) }}</span>
                                <x-ui.status-badge :status="$session?->status ?? 'closed'" :label="$session ? ($session->status === 'open' ? 'Terbuka' : 'Ditutup') : 'Belum dibuka'" />
                            </div>
                            <h3 class="mt-3 text-xl font-black leading-tight text-ocular-teal">{{ $schedule->schoolClass->name }}</h3>
                            <p class="mt-1 text-sm text-ocular-copy/70">{{ $schedule->subject->name }}</p>
                            @if ($session)
                                <p class="mt-3 font-mono text-[10px] font-bold uppercase tracking-wider text-ocular-accent">{{ $present }}/{{ $total }} hadir · {{ $rate }}%</p>
                            @endif
                        </div>

                        <div class="flex flex-wrap gap-2 sm:justify-end">
                            @if ($session)
                                <x-ui.link-button :href="route('guru.sessions.show', $session)" variant="{{ $session->status === 'open' ? 'primary' : 'outline' }}">
                                    <i data-lucide="scan-line" class="size-4"></i>
                                    {{ $session->status === 'open' ? 'Lanjut scan' : 'Lihat sesi' }}
                                </x-ui.link-button>
                            @else
                                <form method="POST" action="{{ route('guru.sessions.store', $schedule) }}">
                                    @csrf
                                    <x-ui.button variant="primary">
                                        <i data-lucide="scan-line" class="size-4"></i>
                                        Buka sesi
                                    </x-ui.button>
                                </form>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="px-5 py-12 text-center">
                        <p class="text-sm font-semibold text-ocular-teal">Tidak ada jadwal hari ini.</p>
                        <p class="mt-1 text-sm text-ocular-copy/60">Cek menu Jadwal Mengajar untuk melihat jadwal hari lain.</p>
                    </div>
                @endforelse
            </div>
        </x-ui.card>
    </x-ui.page>
</x-layouts.app>
