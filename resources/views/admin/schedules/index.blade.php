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

    <x-ui.page eyebrow="Data akademik" title="Jadwal">
        <x-slot:actions>
            <form method="GET" class="flex min-w-0 items-center gap-2 sm:w-[22rem]">
                <x-ui.search-input name="search" :value="$search" placeholder="Cari kelas, mapel, atau guru..." id="schedule-search" />
                <x-ui.button type="submit" variant="teal" class="min-h-10 px-3">Cari</x-ui.button>
                @if ($search !== '')<x-ui.link-button :href="route('admin.schedules.index')" variant="muted" class="min-h-10 px-3">Reset</x-ui.link-button>@endif
            </form>
            <x-ui.filter-panel title="Filter jadwal" :active="collect([$selectedAcademicYearId, $selectedClassId, $selectedTeacherId])->filter(fn ($value) => filled($value))->count()">
                <form method="GET" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    <input type="hidden" name="search" value="{{ $search }}">
                    <label class="block text-xs font-bold uppercase tracking-wider text-ocular-teal">Tahun ajaran<select name="academic_year_id" class="mt-2 min-h-11 w-full border border-slate-300 bg-white px-3 text-sm"><option value="">Semua</option>@foreach ($academicYears as $academicYear)<option value="{{ $academicYear->id }}" @selected($selectedAcademicYearId == $academicYear->id)>{{ $academicYear->name }} · {{ $academicYear->semester === 1 ? 'Ganjil' : 'Genap' }}</option>@endforeach</select></label>
                    <label class="block text-xs font-bold uppercase tracking-wider text-ocular-teal">Kelas<select name="class_id" class="mt-2 min-h-11 w-full border border-slate-300 bg-white px-3 text-sm"><option value="">Semua</option>@foreach ($classes as $class)<option value="{{ $class->id }}" @selected($selectedClassId == $class->id)>{{ $class->name }}</option>@endforeach</select></label>
                    <label class="block text-xs font-bold uppercase tracking-wider text-ocular-teal">Guru<select name="user_id" class="mt-2 min-h-11 w-full border border-slate-300 bg-white px-3 text-sm"><option value="">Semua</option>@foreach ($teachers as $teacher)<option value="{{ $teacher->id }}" @selected($selectedTeacherId == $teacher->id)>{{ $teacher->name }}</option>@endforeach</select></label>
                    <div class="flex gap-2 sm:col-span-2 lg:col-span-3"><x-ui.button variant="teal">Tampilkan jadwal</x-ui.button><x-ui.link-button :href="route('admin.schedules.index')" variant="muted">Reset</x-ui.link-button></div>
                </form>
            </x-ui.filter-panel>
            <x-ui.link-button :href="route('admin.schedules.create', ['day_of_week' => $selectedDay])">Tambah jadwal</x-ui.link-button>
        </x-slot:actions>

        <x-ui.flash :message="session('success')" />
        @if (session('warning'))
            <x-ui.flash type="warning" :message="session('warning')" />
        @endif
        @if ($errors->any())
            <x-ui.flash type="error" :message="$errors->first()" />
        @endif

        @php
            $activeTab = request()->string('tab')->toString() === 'list' ? 'list' : 'grid';
        @endphp
        <nav class="flex gap-0 overflow-x-auto border-b border-ocular-teal/15" aria-label="Tampilan jadwal">
            <a href="{{ route('admin.schedules.index', array_merge(request()->query(), ['tab' => 'grid', 'page' => null])) }}" class="min-h-11 min-w-36 border-x border-t px-4 py-3 text-center text-xs font-black uppercase tracking-wider transition {{ $activeTab === 'grid' ? 'border-ocular-teal/20 border-b-white bg-white text-ocular-teal' : 'border-transparent text-ocular-copy/50 hover:border-ocular-teal/20 hover:text-ocular-teal' }}" aria-current="{{ $activeTab === 'grid' ? 'page' : 'false' }}">Peta jadwal</a>
            <a href="{{ route('admin.schedules.index', array_merge(request()->query(), ['tab' => 'list', 'page' => null])) }}" class="min-h-11 min-w-36 border-x border-t px-4 py-3 text-center text-xs font-black uppercase tracking-wider transition {{ $activeTab === 'list' ? 'border-ocular-teal/20 border-b-white bg-white text-ocular-teal' : 'border-transparent text-ocular-copy/50 hover:border-ocular-teal/20 hover:text-ocular-teal' }}" aria-current="{{ $activeTab === 'list' ? 'page' : 'false' }}">Daftar jadwal</a>
        </nav>

        @if ($activeTab === 'grid')
        <div data-schedule-grid class="space-y-4">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-ocular-accent">Peta jadwal</p>
                <h2 class="mt-1 text-xl font-black text-ocular-teal">{{ $activeDayLabel }}</h2>
            </div>
            <span class="font-mono text-[10px] font-bold uppercase tracking-wider text-ocular-copy/60">{{ $gridSchedules->count() }} blok terfilter</span>
        </div>

        <div class="flex gap-0 overflow-x-auto border border-ocular-teal/15 bg-white" role="tablist" aria-label="Pilih hari">
            @foreach ($days as $day => $label)
                <a href="{{ route('admin.schedules.index', array_merge(request()->query(), ['day' => $day])) }}" class="min-w-24 border-r border-ocular-teal/10 px-3 py-3 text-center text-xs font-black uppercase tracking-wider transition {{ $selectedDay === $day ? 'bg-ocular-teal text-white' : 'text-ocular-copy/55 hover:bg-ocular-teal/5 hover:text-ocular-teal' }}" role="tab" aria-selected="{{ $selectedDay === $day ? 'true' : 'false' }}">{{ $label }}</a>
            @endforeach
        </div>

        <div class="border border-ocular-teal/15 bg-white">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[980px] table-fixed border-collapse">
                    <colgroup>
                        <col class="w-12">
                        <col class="w-24">
                        @foreach ($gridClasses as $class)
                            <col class="w-36">
                        @endforeach
                    </colgroup>
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
                                            $blockHeight = max(48, ($cell['span'] * 58) - 8);
                                        @endphp
                                        <td rowspan="{{ $cell['span'] }}" class="border-b border-r border-ocular-teal/10 p-1 align-stretch">
                                            <button type="button" data-schedule-detail data-edit-url="{{ route('admin.schedules.edit', $item) }}" data-class="{{ $item->schoolClass->name }}" data-subject="{{ $item->subject->name }}" data-teacher="{{ $item->teacher->name }}" data-day="{{ $days[$item->day_of_week] ?? '-' }}" data-time="{{ substr($item->start_time, 0, 5) }} - {{ substr($item->end_time, 0, 5) }}" data-year="{{ $item->academicYear->name }} · {{ $item->academicYear->semester === 1 ? 'Ganjil' : 'Genap' }}" data-sessions="{{ $item->attendance_sessions_count ?? 0 }}" class="flex h-full w-full flex-col justify-center border-2 px-2 py-1 text-center transition hover:-translate-y-0.5 hover:shadow-md {{ $color['bg'] }} {{ $color['border'] }} {{ $color['text'] }}" style="min-height: {{ $blockHeight }}px" title="Lihat detail jadwal {{ $item->teacher->name }}">
                                                <span class="truncate text-[10px] font-black uppercase">{{ mb_strtoupper(mb_substr($initials, 0, 3)) }}</span>
                                                @if ($cell['span'] >= 2)
                                                    <span class="mt-0.5 truncate text-[10px] font-semibold">{{ $item->subject->name }}</span>
                                                @endif
                                            </button>
                                        </td>
                                        @continue
                                    @endif
                                    <td class="border-b border-r border-ocular-teal/10 p-1" style="height: 58px">
                                        <a href="{{ route('admin.schedules.create', ['day_of_week' => $selectedDay, 'class_id' => $class->id, 'start_time' => $row['start'], 'end_time' => $row['end']]) }}" class="group flex h-full w-full items-center justify-center border border-dashed border-transparent transition hover:border-ocular-accent/40 hover:bg-ocular-accent/5" aria-label="Tambah jadwal {{ $class->name }} JP {{ $jp }}"><span class="text-lg font-light text-transparent transition group-hover:text-ocular-accent">+</span></a>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @if ($gridSchedules->isNotEmpty())
            <div class="flex flex-wrap gap-2">
                <span class="self-center text-[10px] font-black uppercase tracking-wider text-ocular-copy/50">Guru</span>
                @foreach ($gridSchedules->unique('user_id') as $item)
                    @php $color = $teacherColors[$item->user_id % count($teacherColors)]; @endphp
                    <span class="border px-2.5 py-1 text-[10px] font-bold {{ $color['bg'] }} {{ $color['border'] }} {{ $color['text'] }}">{{ $item->teacher->name }}</span>
                @endforeach
            </div>
        @endif
        </div>
        @endif

        @if ($activeTab === 'list')
        <div class="flex flex-col gap-3 border-t border-ocular-teal/10 pt-6 sm:flex-row sm:items-center sm:justify-between">
            <div><h2 class="text-sm font-black uppercase tracking-widest text-ocular-teal">Daftar jadwal</h2><span class="font-mono text-[10px] font-bold text-ocular-accent">{{ $schedules->total() }} jadwal · pilih jadwal di halaman ini untuk aksi massal</span></div>
            <x-ui.bulk-actions form-id="bulk-schedules-form" :action="route('admin.schedules.bulk-destroy')" checkbox-name="schedule_ids[]" button-label="Hapus / arsipkan" confirm-message="Hapus jadwal yang dipilih? Jadwal yang sudah memiliki sesi absensi akan diarsipkan agar histori tetap aman." />
        </div>
        <div class="space-y-3">
            @forelse ($schedules as $schedule)
                <x-ui.card class="border border-ocular-teal/10 p-4">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex min-w-0 items-start gap-3">
                            <input type="checkbox" name="schedule_ids[]" value="{{ $schedule->id }}" form="bulk-schedules-form" data-bulk-checkbox="bulk-schedules-form" data-bulk-id="{{ $schedule->id }}" class="mt-1 size-4 shrink-0 border-slate-300 text-ocular-teal">
                            <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2"><span class="bg-ocular-teal/10 px-2 py-1 text-[10px] font-black uppercase text-ocular-teal">{{ $days[$schedule->day_of_week] }}</span><span class="font-mono text-xs font-bold">{{ substr($schedule->start_time, 0, 5) }} - {{ substr($schedule->end_time, 0, 5) }}</span></div>
                            <h3 class="mt-2 truncate text-base font-black text-ocular-teal">{{ $schedule->schoolClass->name }} · {{ $schedule->subject->name }}</h3>
                            <p class="mt-1 truncate text-xs text-ocular-copy/60">{{ $schedule->teacher->name }} · {{ $schedule->academicYear->name }}</p>
                            </div>
                        </div>
                        <x-ui.row-actions class="shrink-0" :edit-href="route('admin.schedules.edit', $schedule)" :delete-action="route('admin.schedules.destroy', $schedule)" :delete-label="$schedule->attendance_sessions_count > 0 ? 'Arsipkan' : 'Hapus'" :confirm="$schedule->attendance_sessions_count > 0 ? 'Jadwal ini punya histori absensi dan akan diarsipkan. Lanjutkan?' : 'Hapus jadwal ini?'" />
                    </div>
                </x-ui.card>
            @empty
                <div class="border-2 border-dashed border-ocular-accent/20 px-5 py-12 text-center text-sm text-ocular-copy/60">Jadwal kosong.</div>
            @endforelse
        </div>
        <x-ui.pagination :paginator="$schedules" />
        @endif
    </x-ui.page>

    <dialog data-schedule-detail-dialog class="border-0 bg-transparent p-0 backdrop:bg-slate-950/55" style="position: fixed; inset: 0; width: min(92vw, 30rem); max-width: min(92vw, 30rem); height: fit-content; margin: auto">
        <div class="border border-slate-200 bg-white shadow-2xl">
            <div class="flex items-start justify-between gap-4 border-b border-slate-200 bg-ocular-surface px-5 py-4">
                <div><p class="text-[10px] font-black uppercase tracking-[0.18em] text-ocular-orange">Detail jadwal</p><h2 data-schedule-detail-title class="mt-1 text-lg font-black text-ocular-teal">-</h2></div>
                <button type="button" data-schedule-detail-close class="grid size-9 place-items-center border border-slate-200 bg-white text-xl leading-none text-ocular-copy hover:border-ocular-teal hover:text-ocular-teal" aria-label="Tutup detail">×</button>
            </div>
            <div class="space-y-4 p-5">
                <dl class="grid gap-3 text-sm sm:grid-cols-2">
                    <div><dt class="text-[10px] font-black uppercase tracking-wider text-ocular-accent">Kelas</dt><dd data-schedule-detail-class class="mt-1 font-bold text-ocular-teal">-</dd></div>
                    <div><dt class="text-[10px] font-black uppercase tracking-wider text-ocular-accent">Mapel</dt><dd data-schedule-detail-subject class="mt-1 font-bold text-ocular-teal">-</dd></div>
                    <div><dt class="text-[10px] font-black uppercase tracking-wider text-ocular-accent">Guru</dt><dd data-schedule-detail-teacher class="mt-1 font-bold text-ocular-teal">-</dd></div>
                    <div><dt class="text-[10px] font-black uppercase tracking-wider text-ocular-accent">Waktu</dt><dd data-schedule-detail-time class="mt-1 font-mono text-xs font-bold text-ocular-copy">-</dd></div>
                    <div><dt class="text-[10px] font-black uppercase tracking-wider text-ocular-accent">Tahun ajaran</dt><dd data-schedule-detail-year class="mt-1 text-ocular-copy">-</dd></div>
                    <div><dt class="text-[10px] font-black uppercase tracking-wider text-ocular-accent">Sesi absensi</dt><dd data-schedule-detail-sessions class="mt-1 font-mono text-xs font-bold text-ocular-copy">-</dd></div>
                </dl>
                <div class="flex justify-end gap-2 border-t border-slate-100 pt-4">
                    <button type="button" data-schedule-detail-cancel class="min-h-10 border border-slate-200 px-3 text-[10px] font-black uppercase tracking-wider text-ocular-copy hover:bg-slate-50">Tutup</button>
                    <a data-schedule-detail-edit href="#" class="inline-flex min-h-10 items-center justify-center bg-ocular-orange px-4 text-[10px] font-black uppercase tracking-wider text-white hover:bg-ocular-orange-dark">Edit jadwal</a>
                </div>
            </div>
        </div>
    </dialog>

    <script>
        (() => {
            const dialog = document.querySelector('[data-schedule-detail-dialog]');
            if (!dialog) return;
            const fields = {
                title: dialog.querySelector('[data-schedule-detail-title]'),
                className: dialog.querySelector('[data-schedule-detail-class]'),
                subject: dialog.querySelector('[data-schedule-detail-subject]'),
                teacher: dialog.querySelector('[data-schedule-detail-teacher]'),
                time: dialog.querySelector('[data-schedule-detail-time]'),
                year: dialog.querySelector('[data-schedule-detail-year]'),
                sessions: dialog.querySelector('[data-schedule-detail-sessions]'),
                edit: dialog.querySelector('[data-schedule-detail-edit]'),
            };
            const close = () => dialog.close();
            document.querySelectorAll('[data-schedule-detail]').forEach((button) => button.addEventListener('click', () => {
                fields.title.textContent = `${button.dataset.day} · ${button.dataset.time}`;
                fields.className.textContent = button.dataset.class;
                fields.subject.textContent = button.dataset.subject;
                fields.teacher.textContent = button.dataset.teacher;
                fields.time.textContent = button.dataset.time;
                fields.year.textContent = button.dataset.year;
                fields.sessions.textContent = `${button.dataset.sessions} sesi`;
                fields.edit.href = button.dataset.editUrl;
                dialog.showModal();
            }));
            dialog.querySelector('[data-schedule-detail-close]')?.addEventListener('click', close);
            dialog.querySelector('[data-schedule-detail-cancel]')?.addEventListener('click', close);
            dialog.addEventListener('click', (event) => { if (event.target === dialog) close(); });
        })();
    </script>
</x-layouts.app>
