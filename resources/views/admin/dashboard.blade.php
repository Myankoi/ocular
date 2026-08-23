<x-layouts.app title="Admin Dashboard - Ocular">
    <x-ui.page title="Dashboard admin">
        <x-slot:actions>
            <div
                class="inline-flex items-center gap-2 border border-ocular-teal/15 bg-white px-3 py-2 text-xs text-ocular-copy/70 shadow-[var(--shadow-card)]">
                {{ $activeAcademicYear?->name ?? 'Tahun ajaran belum diatur' }} @if ($activeAcademicYear)
                    · {{ $activeAcademicYear->semester === 1 ? 'Ganjil' : 'Genap' }}
                @endif
            </div>
        </x-slot:actions>

        <section class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4" aria-label="Statistik utama">
            <x-ui.stat-card label="Siswa aktif" :value="number_format($totalStudents)" meta="terdata" />
            <x-ui.stat-card label="Guru aktif" :value="number_format($totalTeachers)" meta="pengajar" />
            <x-ui.stat-card label="Kelas aktif" :value="number_format($totalClasses)" meta="tahun ajaran" />
            <x-ui.stat-card label="Record absensi" :value="number_format($totalAttendances)" tone="orange" meta="tersimpan" />
        </section>

        <section class="grid gap-4 lg:grid-cols-[1.35fr_0.65fr]">
            <x-ui.card class="relative !overflow-hidden border border-ocular-teal/15 !bg-ocular-teal !text-white">
                <div class="absolute -right-12 -top-16 size-48 rounded-full border-[24px] border-white/10"></div>
                <div class="relative">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-white/60">Kehadiran hari
                                ini</p>
                            <h2 class="mt-2 text-4xl font-black tracking-tight">{{ $todayPercentage }}<span
                                    class="text-xl text-white/60">%</span></h2>
                        </div><span class="grid size-11 place-items-center rounded-full bg-white/10"><i data-lucide="check" class="size-5"></i></span>
                    </div>
                    <div class="mt-6 h-2 overflow-hidden rounded-full bg-white/15">
                        <div class="h-full rounded-full bg-ocular-orange transition-all"
                            style="width: {{ min(100, $todayPercentage) }}%"></div>
                    </div>
                    <div class="mt-5 grid grid-cols-3 gap-3 border-t border-white/15 pt-4 text-center">
                        <div>
                            <p class="font-mono text-lg font-bold">{{ $todayPresent }}</p>
                            <p class="text-[10px] uppercase tracking-wider text-white/60">Hadir</p>
                        </div>
                        <div>
                            <p class="font-mono text-lg font-bold">{{ $todayAbsent }}</p>
                            <p class="text-[10px] uppercase tracking-wider text-white/60">Tidak hadir</p>
                        </div>
                        <div>
                            <p class="font-mono text-lg font-bold">{{ $todayTotal }}</p>
                            <p class="text-[10px] uppercase tracking-wider text-white/60">Total</p>
                        </div>
                    </div>
                </div>
            </x-ui.card>
            <x-ui.card class="border border-ocular-orange/25 bg-white">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-ocular-orange">Aksi cepat</p>
                <h2 class="mt-2 text-lg font-black text-ocular-teal">Aksi data</h2>
                <div class="mt-5 grid gap-2"><x-ui.link-button :href="route('admin.students.index')">Siswa</x-ui.link-button><x-ui.link-button :href="route('admin.schedules.index')" variant="outline">Jadwal</x-ui.link-button><x-ui.link-button :href="route('admin.attendances.index')" variant="muted">Laporan
                        absensi</x-ui.link-button></div>
            </x-ui.card>
        </section>

        <x-ui.card padding="p-0" class="overflow-hidden border border-ocular-teal/15">
            <div class="flex items-center justify-between gap-4 border-b border-slate-100 px-4 py-5 sm:px-6">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-ocular-orange">Monitoring kelas</p>
                    <h2 class="mt-1 text-lg font-black text-ocular-teal">Kehadiran per kelas</h2>
                </div><span class="font-mono text-[10px] text-ocular-copy/50">{{ $classAttendance->count() }}
                    kelas</span>
            </div>
            <div class="hidden overflow-x-auto md:block">
                <table class="w-full min-w-[620px] text-left text-sm">
                    <thead class="bg-ocular-surface text-[10px] uppercase tracking-widest text-ocular-accent">
                        <tr>
                            <th class="px-6 py-3">Kelas</th>
                            <th class="px-6 py-3">Siswa</th>
                            <th class="px-6 py-3">Hadir</th>
                            <th class="px-6 py-3">Persentase</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($classAttendance as $class)
                            <tr class="transition hover:bg-ocular-surface">
                                <td class="px-6 py-4 font-bold text-ocular-teal">{{ $class['name'] }}</td>
                                <td class="px-6 py-4">{{ $class['students'] }}</td>
                                <td class="px-6 py-4 font-mono">{{ $class['present'] }} <span
                                        class="text-xs text-ocular-copy/40">/ {{ $class['total'] }}</span></td>
                                <td class="px-6 py-4">
                                    @if ($class['percentage'] === null)
                                    <span class="text-xs text-ocular-copy/40">Belum ada sesi</span>@else<div
                                            class="flex items-center gap-3">
                                            <div class="h-1.5 w-24 overflow-hidden rounded-full bg-slate-100">
                                                <div class="h-full rounded-full {{ $class['percentage'] >= 75 ? 'bg-emerald-500' : 'bg-amber-500' }}"
                                                    style="width: {{ min(100, $class['percentage']) }}%"></div>
                                            </div><span
                                                class="font-mono text-xs font-bold {{ $class['percentage'] >= 75 ? 'text-emerald-600' : 'text-amber-600' }}">{{ $class['percentage'] }}%</span>
                                        </div>
                                    @endif
                                </td>
                        </tr>@empty<tr>
                                <td colspan="4" class="px-6 py-12 text-center text-sm text-ocular-copy/60">Belum ada
                                    kelas pada tahun ajaran aktif.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="divide-y divide-slate-100 md:hidden">
                @forelse ($classAttendance as $class)
                    <article class="flex items-center justify-between gap-3 px-4 py-4">
                        <div>
                            <h3 class="font-bold text-ocular-teal">{{ $class['name'] }}</h3>
                            <p class="mt-1 text-xs text-ocular-copy/60">{{ $class['present'] }} dari
                                {{ $class['total'] }} hadir</p>
                        </div><span
                            class="font-mono text-sm font-bold {{ $class['percentage'] !== null && $class['percentage'] >= 75 ? 'text-emerald-600' : 'text-amber-600' }}">{{ $class['percentage'] !== null ? $class['percentage'] . '%' : '—' }}</span>
                </article>@empty<p class="px-4 py-10 text-center text-sm text-ocular-copy/60">Belum ada kelas pada
                        tahun ajaran aktif.</p>
                @endforelse
            </div>
        </x-ui.card>
    </x-ui.page>
</x-layouts.app>
