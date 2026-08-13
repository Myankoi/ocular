<x-layouts.app title="Jadwal - Ocular">
    @php
        $teacherColors = [
            ['bg' => 'bg-sky-50', 'border' => 'border-sky-300', 'text' => 'text-sky-800'],
            ['bg' => 'bg-emerald-50', 'border' => 'border-emerald-300', 'text' => 'text-emerald-800'],
            ['bg' => 'bg-amber-50', 'border' => 'border-amber-300', 'text' => 'text-amber-800'],
            ['bg' => 'bg-rose-50', 'border' => 'border-rose-300', 'text' => 'text-rose-800'],
            ['bg' => 'bg-violet-50', 'border' => 'border-violet-300', 'text' => 'text-violet-800'],
            ['bg' => 'bg-cyan-50', 'border' => 'border-cyan-300', 'text' => 'text-cyan-800'],
            ['bg' => 'bg-orange-50', 'border' => 'border-orange-300', 'text' => 'text-orange-800'],
        ];
        $activeDayLabel = $days[$selectedDay] ?? 'Senin';
    @endphp

    <x-ui.page eyebrow="Data akademik" title="Jadwal" description="Atur jadwal mengajar dalam blok waktu yang mudah dibaca.">
        <x-slot:actions>
            <x-ui.link-button :href="route('admin.schedules.create', ['day_of_week' => $selectedDay])">Tambah jadwal</x-ui.link-button>
        </x-slot:actions>

        <x-ui.flash :message="session('success')" />
        @if ($errors->any())
            <x-ui.flash type="error" :message="$errors->first()" />
        @endif

        <x-ui.card class="border border-ocular-teal/15">
            <form method="GET" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                <label class="block text-xs font-bold uppercase tracking-wider text-ocular-teal">
                    Cari
                    <input name="q" value="{{ $search }}" placeholder="Kelas, mapel, atau guru..." class="mt-2 min-h-11 w-full border border-slate-300 px-3 text-sm focus:border-ocular-teal focus:outline-none focus:ring-2 focus:ring-ocular-teal/20">
                </label>
                <label class="block text-xs font-bold uppercase tracking-wider text-ocular-teal">
                    Tahun ajaran
                    <select name="academic_year_id" class="mt-2 min-h-11 w-full border border-slate-300 bg-white px-3 text-sm">
                        <option value="">Semua</option>
                        @foreach ($academicYears as $academicYear)
                            <option value="{{ $academicYear->id }}" @selected($selectedAcademicYearId == $academicYear->id)>{{ $academicYear->name }} · {{ $academicYear->semester === 1 ? 'Ganjil' : 'Genap' }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="block text-xs font-bold uppercase tracking-wider text-ocular-teal">
                    Kelas
                    <select name="class_id" class="mt-2 min-h-11 w-full border border-slate-300 bg-white px-3 text-sm">
                        <option value="">Semua</option>
                        @foreach ($classes as $class)
                            <option value="{{ $class->id }}" @selected($selectedClassId == $class->id)>{{ $class->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="block text-xs font-bold uppercase tracking-wider text-ocular-teal">
                    Guru
                    <select name="user_id" class="mt-2 min-h-11 w-full border border-slate-300 bg-white px-3 text-sm">
                        <option value="">Semua</option>
                        @foreach ($teachers as $teacher)
                            <option value="{{ $teacher->id }}" @selected($selectedTeacherId == $teacher->id)>{{ $teacher->name }}</option>
                        @endforeach
                    </select>
                </label>
                <div class="flex gap-2 sm:col-span-2 lg:col-span-4">
                    <x-ui.button variant="teal">Tampilkan jadwal</x-ui.button>
                    <x-ui.link-button :href="route('admin.schedules.index')" variant="muted">Reset</x-ui.link-button>
                </div>
            </form>
        </x-ui.card>

        @php
            $activeTab = request()->string('tab')->toString() === 'list' ? 'list' : 'grid';
        @endphp
        <nav class="flex gap-1 overflow-x-auto border-b border-ocular-teal/15" aria-label="Tampilan jadwal">
            <a href="{{ route('admin.schedules.index', array_merge(request()->query(), ['tab' => 'grid', 'page' => null])) }}" class="min-h-11 min-w-36 border-b-2 px-4 py-3 text-center text-xs font-black uppercase tracking-wider transition {{ $activeTab === 'grid' ? 'border-ocular-orange text-ocular-teal' : 'border-transparent text-ocular-copy/50 hover:border-ocular-teal/20 hover:text-ocular-teal' }}" aria-current="{{ $activeTab === 'grid' ? 'page' : 'false' }}">Peta jadwal</a>
            <a href="{{ route('admin.schedules.index', array_merge(request()->query(), ['tab' => 'list', 'page' => null])) }}" class="min-h-11 min-w-36 border-b-2 px-4 py-3 text-center text-xs font-black uppercase tracking-wider transition {{ $activeTab === 'list' ? 'border-ocular-orange text-ocular-teal' : 'border-transparent text-ocular-copy/50 hover:border-ocular-teal/20 hover:text-ocular-teal' }}" aria-current="{{ $activeTab === 'list' ? 'page' : 'false' }}">Daftar jadwal</a>
        </nav>

        @if ($activeTab === 'grid')
        <div data-schedule-grid class="space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-ocular-accent">Peta jadwal</p>
                <h2 class="mt-1 text-xl font-black text-ocular-teal">{{ $activeDayLabel }}</h2>
            </div>
            <span class="font-mono text-[10px] font-bold uppercase tracking-wider text-ocular-copy/60">{{ $gridSchedules->count() }} blok terfilter</span>
        </div>

        <div class="flex gap-1 overflow-x-auto border-b border-ocular-teal/15 pb-1" role="tablist" aria-label="Pilih hari">
            @foreach ($days as $day => $label)
                <a href="{{ route('admin.schedules.index', array_merge(request()->query(), ['day' => $day])) }}" class="min-w-20 rounded-t-lg px-3 py-2 text-center text-xs font-bold transition {{ $selectedDay === $day ? 'bg-ocular-teal text-white' : 'text-ocular-copy/60 hover:bg-ocular-teal/10 hover:text-ocular-teal' }}" role="tab" aria-selected="{{ $selectedDay === $day ? 'true' : 'false' }}">{{ $label }}</a>
            @endforeach
        </div>

        <x-ui.card class="overflow-hidden border border-ocular-teal/15 p-0">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[760px] border-collapse">
                    <thead>
                        <tr class="bg-ocular-teal/5">
                            <th class="sticky left-0 z-30 w-12 min-w-12 max-w-12 border-b border-r border-ocular-teal/10 bg-ocular-teal/5 px-2 py-3 text-center text-[10px] font-black uppercase tracking-wider text-ocular-copy/60">JP</th>
                            <th class="sticky left-12 z-30 w-24 min-w-24 max-w-24 border-b border-r border-ocular-teal/10 bg-ocular-teal/5 px-2 py-3 text-left text-[10px] font-black uppercase tracking-wider text-ocular-copy/60">Jam</th>
                            @foreach ($gridClasses as $class)
                                <th class="w-28 border-b border-r border-ocular-teal/10 px-2 py-3 text-center text-[10px] font-black uppercase tracking-wider text-ocular-copy/70">{{ $class->name }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($scheduleRows as $row)
                            @if ($row['type'] === 'break')
                                <tr>
                                    <td colspan="{{ $gridClasses->count() + 2 }}" class="border-b border-ocular-teal/10 bg-ocular-copy px-3 py-2 text-center text-[10px] font-black uppercase tracking-widest text-white">{{ $row['label'] }} · {{ $row['start'] }}–{{ $row['end'] }}</td>
                                </tr>
                                @continue
                            @endif

                            @php
                                $jp = $row['jp'];
                                $rowBg = $jp % 2 === 0 ? 'bg-slate-50/70' : 'bg-white';
                                $stickyBg = $jp % 2 === 0 ? 'bg-slate-50' : 'bg-white';
                            @endphp
                            <tr class="{{ $rowBg }}" style="height: 58px">
                                <td class="sticky left-0 z-20 w-12 min-w-12 max-w-12 border-b border-r border-ocular-teal/10 {{ $stickyBg }} px-2 text-center align-middle"><span class="font-mono text-xs font-black text-ocular-copy/60">{{ $jp }}</span></td>
                                <td class="sticky left-12 z-20 w-24 min-w-24 max-w-24 border-b border-r border-ocular-teal/10 {{ $stickyBg }} px-2 align-middle"><span class="whitespace-nowrap font-mono text-[10px] text-ocular-copy/60">{{ $row['start'] }}<br>{{ $row['end'] }}</span></td>
                                @foreach ($gridClasses as $class)
                                    @php $cell = $grid[$selectedDay][$class->id][$jp] ?? null; @endphp
                                    @if ($cell && isset($cell['continuation']))
                                        @continue
                                    @endif
                                    @if ($cell && isset($cell['schedule']))
                                        @php
                                            $item = $cell['schedule'];
                                            $color = $teacherColors[$item->user_id % count($teacherColors)];
                                            $initials = collect(explode(' ', $item->teacher->name))->map(fn ($part) => mb_substr($part, 0, 1))->join('');
                                        @endphp
                                        <td rowspan="{{ $cell['span'] }}" class="border-b border-r border-ocular-teal/10 p-1 align-stretch">
                                            <a href="{{ route('admin.schedules.edit', $item) }}" class="flex h-full min-h-12 flex-col justify-center rounded-md border-2 px-2 py-1 text-center transition hover:-translate-y-0.5 hover:shadow-md {{ $color['bg'] }} {{ $color['border'] }} {{ $color['text'] }}" title="Edit jadwal {{ $item->teacher->name }}">
                                                <span class="truncate text-[10px] font-black uppercase">{{ mb_strtoupper(mb_substr($initials, 0, 3)) }}</span>
                                                @if ($cell['span'] >= 2)
                                                    <span class="mt-0.5 truncate text-[10px] font-semibold">{{ $item->subject->name }}</span>
                                                @endif
                                            </a>
                                        </td>
                                        @continue
                                    @endif
                                    <td class="border-b border-r border-ocular-teal/10 p-1" style="height: 58px">
                                        <a href="{{ route('admin.schedules.create', ['day_of_week' => $selectedDay, 'class_id' => $class->id, 'start_time' => $row['start'], 'end_time' => $row['end']]) }}" class="group flex h-full w-full items-center justify-center rounded-md border border-dashed border-transparent transition hover:border-ocular-accent/40 hover:bg-ocular-accent/5" aria-label="Tambah jadwal {{ $class->name }} JP {{ $jp }}"><span class="text-lg font-light text-transparent transition group-hover:text-ocular-accent">+</span></a>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-ui.card>

        @if ($gridSchedules->isNotEmpty())
            <div class="flex flex-wrap gap-2">
                <span class="self-center text-[10px] font-black uppercase tracking-wider text-ocular-copy/50">Guru</span>
                @foreach ($gridSchedules->unique('user_id') as $item)
                    @php $color = $teacherColors[$item->user_id % count($teacherColors)]; @endphp
                    <span class="rounded-full border px-2.5 py-1 text-[10px] font-bold {{ $color['bg'] }} {{ $color['border'] }} {{ $color['text'] }}">{{ $item->teacher->name }}</span>
                @endforeach
            </div>
        @endif
        </div>
        @endif

        @if ($activeTab === 'list')
        <div class="flex items-center justify-between gap-3 border-t border-ocular-teal/10 pt-6">
            <h2 class="text-sm font-black uppercase tracking-widest text-ocular-teal">Daftar jadwal</h2>
            <span class="font-mono text-[10px] font-bold text-ocular-accent">{{ $schedules->total() }} jadwal</span>
        </div>
        <div class="space-y-3">
            @forelse ($schedules as $schedule)
                <x-ui.card class="border border-ocular-teal/10 p-4">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2"><span class="rounded bg-ocular-teal/10 px-2 py-1 text-[10px] font-black uppercase text-ocular-teal">{{ $days[$schedule->day_of_week] }}</span><span class="font-mono text-xs font-bold">{{ substr($schedule->start_time, 0, 5) }} – {{ substr($schedule->end_time, 0, 5) }}</span></div>
                            <h3 class="mt-2 truncate text-base font-black text-ocular-teal">{{ $schedule->schoolClass->name }} · {{ $schedule->subject->name }}</h3>
                            <p class="mt-1 truncate text-xs text-ocular-copy/60">{{ $schedule->teacher->name }} · {{ $schedule->academicYear->name }}</p>
                        </div>
                        <div class="flex shrink-0 items-center gap-3"><a href="{{ route('admin.schedules.edit', $schedule) }}" class="text-xs font-bold text-ocular-teal underline">Edit</a><form method="POST" action="{{ route('admin.schedules.destroy', $schedule) }}">@csrf @method('DELETE')<button class="text-xs font-bold text-rose-600 underline" onclick="return confirm('Hapus jadwal ini?')">Hapus</button></form></div>
                    </div>
                </x-ui.card>
            @empty
                <div class="border-2 border-dashed border-ocular-accent/20 px-5 py-12 text-center text-sm text-ocular-copy/60">Belum ada data jadwal untuk filter ini.</div>
            @endforelse
        </div>
        <x-ui.pagination :paginator="$schedules" />
        @endif
    </x-ui.page>

</x-layouts.app>
