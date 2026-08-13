<x-layouts.app title="Guru Dashboard - Ocular">
    <x-ui.page class="-mt-2 space-y-7 lg:mt-0" eyebrow="Dashboard Guru" title="Jadwal hari ini" description="Kelola sesi absensi kelas yang Anda ampu.">
        <x-ui.flash :message="session('success')" />
        @if ($errors->any())
            <x-ui.flash type="error" :message="$errors->first()" />
        @endif

        <x-slot:actions>
            @if ($activeYear)
                <span class="inline-flex min-h-11 items-center gap-2 border border-ocular-teal/20 bg-white px-3 py-2 text-[10px] font-bold uppercase tracking-wider text-ocular-teal">
                    <span class="text-ocular-orange">▣</span>
                    {{ $activeYear->name }} · {{ $activeYear->semester === 1 ? 'Ganjil' : 'Genap' }}
                </span>
            @endif
        </x-slot:actions>

        <section class="grid grid-cols-2 gap-3 sm:grid-cols-4" aria-label="Ringkasan kehadiran hari ini">
            <x-ui.stat-card label="Hadir" :value="$todayPresent" tone="success" />
            <x-ui.stat-card label="Sakit / Izin" :value="$todayExcused" tone="warning" />
            <x-ui.stat-card label="Alpha" :value="$todayAlpha" tone="danger" />
            <x-ui.stat-card label="Rate hari ini" :value="$todayRate . '%'" tone="teal" />
        </section>

        <section aria-labelledby="schedule-heading">
            <div class="mb-4 flex items-center justify-between gap-3">
                <h2 id="schedule-heading" class="text-sm font-black uppercase tracking-widest text-ocular-teal">Jadwal hari ini</h2>
                <a href="{{ route('guru.schedules.index') }}" class="text-[10px] font-black uppercase tracking-wider text-ocular-teal underline decoration-ocular-orange underline-offset-4 hover:text-ocular-teal-dark">Lihat semua jadwal →</a>
            </div>

            <div class="space-y-4">
                @forelse ($todaySchedules as $schedule)
                    @php
                        $session = $schedule->attendanceSessions->first();
                        $isOpen = $session?->status === 'open';
                    @endphp
                    <x-ui.card class="relative overflow-hidden border-l-4 {{ $isOpen ? 'border-ocular-orange' : 'border-ocular-accent' }} {{ ! $isOpen && $session ? 'opacity-75' : '' }}">
                        @if ($isOpen)
                            <span class="absolute right-4 top-4 bg-ocular-orange/10 px-2 py-1 text-[9px] font-black uppercase tracking-wider text-ocular-orange">Sedang berlangsung</span>
                        @elseif ($session)
                            <span class="absolute right-4 top-4"><x-ui.status-badge :status="$session->status" /></span>
                        @endif

                        <div class="pr-28">
                            <h3 class="text-xl font-black leading-tight text-ocular-teal">{{ $schedule->schoolClass->name }}</h3>
                            <p class="mt-1 text-sm font-medium text-ocular-copy">{{ $schedule->subject->name }}</p>
                        </div>

                        <div class="mt-5 flex flex-wrap items-center gap-4 text-ocular-copy">
                            <span class="flex items-center gap-2 font-mono text-xs font-bold"><span class="text-ocular-accent">◷</span>{{ substr($schedule->start_time, 0, 5) }} – {{ substr($schedule->end_time, 0, 5) }}</span>
                            <span class="flex items-center gap-2 font-mono text-xs font-bold"><span class="text-ocular-accent">⌖</span>Ruang kelas</span>
                        </div>

                        <div class="mt-5">
                            @if ($session)
                                <x-ui.link-button :href="route('guru.sessions.show', $session)" :variant="$isOpen ? 'primary' : 'teal'" class="w-full sm:w-auto">
                                    {{ $isOpen ? 'Lanjutkan absensi' : 'Lihat hasil absensi' }}
                                </x-ui.link-button>
                            @else
                                <form method="POST" action="{{ route('guru.sessions.store', $schedule) }}">
                                    @csrf
                                    <x-ui.button class="w-full sm:w-auto">Mulai sesi absensi</x-ui.button>
                                </form>
                            @endif
                        </div>
                    </x-ui.card>
                @empty
                    <div class="border-2 border-dashed border-ocular-accent/20 px-5 py-10 text-center">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-ocular-accent">Tidak ada jadwal hari ini</p>
                        <p class="mt-2 text-sm text-ocular-copy/60">Jadwal aktif dari admin akan muncul di sini.</p>
                    </div>
                @endforelse
            </div>
        </section>

        <section aria-labelledby="quick-actions-heading">
            <h2 id="quick-actions-heading" class="mb-4 text-sm font-black uppercase tracking-widest text-ocular-teal">Akses cepat</h2>
            <div class="grid grid-cols-2 gap-3">
                <x-ui.link-button :href="route('guru.attendances.index')" variant="muted" class="justify-start text-left">
                    <span class="text-lg text-ocular-teal">▤</span>
                    <span>Rekap absensi</span>
                </x-ui.link-button>
                <x-ui.link-button :href="route('guru.schedules.index')" variant="muted" class="justify-start text-left">
                    <span class="text-lg text-ocular-teal">▧</span>
                    <span>Jadwal mengajar</span>
                </x-ui.link-button>
            </div>
        </section>
    </x-ui.page>
</x-layouts.app>
