<x-layouts.app title="Kelas Saya - Ocular">
    <x-ui.page eyebrow="Kelas Guru" title="Kelas saya">
        <x-slot:actions>
            <x-ui.link-button :href="route('guru.schedules.index')" variant="outline">
                <i data-lucide="calendar-clock" class="size-4"></i>
                Peta jadwal
            </x-ui.link-button>
        </x-slot:actions>

        <div class="grid gap-4 sm:grid-cols-3">
            <x-ui.stat-card label="Kelas diampu" :value="$classes->count()" tone="teal" />
            <x-ui.stat-card label="Total siswa aktif" :value="$classes->sum('active_students_count')" tone="success" />
            <x-ui.stat-card label="Tahun ajaran" :value="$activeYear ? ($activeYear->semester === 1 ? 'Ganjil' : 'Genap') : '-'" tone="warning" :meta="$activeYear?->name" />
        </div>

        <div class="grid gap-5 lg:grid-cols-[20rem_minmax(0,1fr)]">
            <x-ui.card padding="p-0" class="overflow-hidden border border-slate-200">
                <div class="border-b border-slate-200 px-4 py-4">
                    <h2 class="text-sm font-black uppercase tracking-widest text-ocular-teal">Daftar kelas</h2>
                    <p class="mt-1 text-xs text-ocular-copy/60">{{ $classes->count() }} kelas aktif</p>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse ($classes as $class)
                        <a href="{{ route('guru.classes.index', ['class_id' => $class->id]) }}" class="block p-4 transition hover:bg-ocular-teal/5 {{ $selectedClass?->id === $class->id ? 'border-l-4 border-ocular-orange bg-ocular-teal/5' : 'border-l-4 border-transparent' }}">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h3 class="font-black text-ocular-teal">{{ $class->name }}</h3>
                                    <p class="mt-1 font-mono text-[10px] font-bold uppercase text-ocular-accent">{{ $class->active_students_count }} siswa</p>
                                </div>
                                <span class="text-xs font-bold text-ocular-copy/40">›</span>
                            </div>
                            <p class="mt-3 line-clamp-2 text-xs text-ocular-copy/60">{{ $class->schedules->pluck('subject.name')->unique()->join(', ') ?: 'Belum ada mapel' }}</p>
                        </a>
                    @empty
                        <p class="px-4 py-10 text-center text-sm text-ocular-copy/60">Belum ada kelas.</p>
                    @endforelse
                </div>
            </x-ui.card>

            <x-ui.card padding="p-0" class="overflow-hidden border border-slate-200">
                @if ($selectedClass)
                    <div class="flex flex-col gap-3 border-b border-slate-200 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-5">
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-ocular-orange">Detail kelas</p>
                            <h2 class="mt-1 text-2xl font-black text-ocular-teal">{{ $selectedClass->name }}</h2>
                            <p class="mt-1 text-sm text-ocular-copy/60">{{ $selectedClass->active_students_count }} siswa aktif</p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($selectedClass->schedules->take(4) as $schedule)
                                <span class="border border-ocular-teal/15 bg-ocular-teal/5 px-3 py-2 text-[10px] font-black uppercase tracking-wider text-ocular-teal">{{ $schedule->subject->code ?? $schedule->subject->name }}</span>
                            @endforeach
                        </div>
                    </div>

                    <div class="hidden overflow-x-auto md:block">
                        <table class="w-full min-w-[680px] text-left text-sm">
                            <thead class="bg-ocular-surface text-[10px] uppercase tracking-widest text-ocular-accent">
                                <tr>
                                    <th class="px-5 py-3">Nama siswa</th>
                                    <th class="px-5 py-3">NIS</th>
                                    <th class="px-5 py-3">NISN</th>
                                    <th class="px-5 py-3">Foto ID card</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse ($students as $student)
                                    <tr class="hover:bg-ocular-surface/60">
                                        <td class="px-5 py-3 font-semibold text-ocular-teal">{{ $student->name }}</td>
                                        <td class="px-5 py-3 font-mono text-xs">{{ $student->nis ?? '-' }}</td>
                                        <td class="px-5 py-3 font-mono text-xs">{{ $student->nisn }}</td>
                                        <td class="px-5 py-3"><x-ui.status-badge :status="$student->photo ? 'open' : 'closed'" :label="$student->photo ? 'Ada' : 'Kosong'" /></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="px-5 py-12 text-center text-sm text-ocular-copy/60">Belum ada siswa aktif.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="divide-y divide-slate-100 md:hidden">
                        @forelse ($students as $student)
                            <article class="space-y-3 p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <h3 class="truncate font-semibold text-ocular-teal">{{ $student->name }}</h3>
                                        <p class="mt-1 font-mono text-[10px] text-ocular-copy/60">NISN {{ $student->nisn }}</p>
                                    </div>
                                    <x-ui.status-badge :status="$student->photo ? 'open' : 'closed'" :label="$student->photo ? 'Foto ada' : 'Foto kosong'" />
                                </div>
                                <dl class="grid grid-cols-2 gap-3 border-t border-slate-100 pt-3 text-xs">
                                    <div><dt class="text-ocular-copy/50">NIS</dt><dd class="mt-1 font-mono">{{ $student->nis ?? '-' }}</dd></div>
                                    <div><dt class="text-ocular-copy/50">Kelas</dt><dd class="mt-1 font-semibold">{{ $selectedClass->name }}</dd></div>
                                </dl>
                            </article>
                        @empty
                            <p class="px-4 py-12 text-center text-sm text-ocular-copy/60">Belum ada siswa aktif.</p>
                        @endforelse
                    </div>

                    <div class="px-4 pb-4 sm:px-5"><x-ui.pagination :paginator="$students" /></div>
                @else
                    <div class="px-5 py-12 text-center">
                        <p class="text-sm font-semibold text-ocular-teal">Belum ada kelas.</p>
                        <p class="mt-1 text-sm text-ocular-copy/60">Jadwal belum tersedia.</p>
                    </div>
                @endif
            </x-ui.card>
        </div>
    </x-ui.page>
</x-layouts.app>
