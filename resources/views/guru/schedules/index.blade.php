<x-layouts.app title="Jadwal Mengajar - Ocular">
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
    @endphp
    <a href="{{ route('guru.dashboard') }}" class="mb-4 inline-flex min-h-10 items-center gap-2 text-xs font-bold uppercase tracking-wider text-ocular-teal hover:text-ocular-teal-dark"><i data-lucide="arrow-left" class="size-4"></i> Dashboard</a>
    <x-ui.page eyebrow="Jadwal Guru" title="{{ $scope === 'all' ? 'Semua jadwal' : 'Jadwal saya' }}" description="Lihat jadwal aktif dan buka detail tanpa kehilangan konteks.">
        <x-slot:actions>
            <div class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row sm:items-center">
                <nav class="flex border border-ocular-teal/15 bg-white" aria-label="Mode tampilan jadwal">
                    <a href="{{ route('guru.schedules.index', array_merge(request()->query(), ['view' => 'map', 'page' => null])) }}" class="flex min-h-10 flex-1 items-center justify-center px-3 py-2 text-[10px] font-black uppercase tracking-wider sm:flex-none {{ $viewMode === 'map' ? 'bg-ocular-orange text-white' : 'text-ocular-teal hover:bg-ocular-teal/5' }}">Peta</a>
                    <a href="{{ route('guru.schedules.index', array_merge(request()->query(), ['view' => 'list', 'page' => null])) }}" class="flex min-h-10 flex-1 items-center justify-center border-l border-ocular-teal/15 px-3 py-2 text-[10px] font-black uppercase tracking-wider sm:flex-none {{ $viewMode === 'list' ? 'bg-ocular-orange text-white' : 'text-ocular-teal hover:bg-ocular-teal/5' }}">Daftar</a>
                </nav>
                <nav class="flex border border-ocular-teal/15 bg-white" aria-label="Scope jadwal">
                    <a href="{{ route('guru.schedules.index', array_merge(request()->query(), ['scope' => 'mine', 'page' => null])) }}" class="flex min-h-10 flex-1 items-center justify-center px-3 py-2 text-[10px] font-black uppercase tracking-wider sm:flex-none {{ $scope === 'mine' ? 'bg-ocular-teal text-white' : 'text-ocular-teal hover:bg-ocular-teal/5' }}">Jadwal saya</a>
                    <a href="{{ route('guru.schedules.index', array_merge(request()->query(), ['scope' => 'all', 'page' => null])) }}" class="flex min-h-10 flex-1 items-center justify-center border-l border-ocular-teal/15 px-3 py-2 text-[10px] font-black uppercase tracking-wider sm:flex-none {{ $scope === 'all' ? 'bg-ocular-teal text-white' : 'text-ocular-teal hover:bg-ocular-teal/5' }}">Semua guru</a>
                </nav>
                <form method="GET" class="flex min-w-0 items-center gap-2 sm:w-[22rem]"><input type="hidden" name="scope" value="{{ $scope }}"><input type="hidden" name="view" value="{{ $viewMode }}"><input type="hidden" name="day" value="{{ $selectedDay }}"><x-ui.search-input name="search" :value="$search" placeholder="Cari kelas, mapel, atau guru..." id="schedule-search" /><x-ui.button variant="teal" class="min-h-10 px-3">Cari</x-ui.button>@if ($search !== '')<x-ui.link-button :href="route('guru.schedules.index', ['scope' => $scope, 'view' => $viewMode, 'day' => $selectedDay])" variant="muted" class="min-h-10 px-3">Reset</x-ui.link-button>@endif</form>
            </div>
        </x-slot:actions>

        @if ($viewMode === 'map')
        <div class="space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-3">
                @if ($activeYear)
                    <span class="font-mono text-[10px] font-bold uppercase tracking-wider text-ocular-accent">{{ $activeYear->name }} · {{ $activeYear->semester === 1 ? 'Ganjil' : 'Genap' }}</span>
                @endif
                <span class="font-mono text-[10px] font-bold uppercase tracking-wider text-ocular-copy/60">{{ $gridSchedules->count() }} blok aktif</span>
            </div>

            <div class="flex gap-0 overflow-x-auto border border-ocular-teal/15 bg-white" role="tablist" aria-label="Pilih hari">
                @foreach ($days as $day => $label)
                    <a href="{{ route('guru.schedules.index', array_merge(request()->query(), ['day' => $day, 'page' => null])) }}" class="min-w-24 border-r border-ocular-teal/10 px-3 py-3 text-center text-xs font-black uppercase tracking-wider transition {{ $selectedDay === $day ? 'bg-ocular-teal text-white' : 'text-ocular-copy/55 hover:bg-ocular-teal/5 hover:text-ocular-teal' }}" role="tab" aria-selected="{{ $selectedDay === $day ? 'true' : 'false' }}">{{ $label }}</a>
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
                                <th class="sticky left-0 z-30 w-12 border-b border-r border-ocular-teal/10 bg-ocular-teal/5 px-2 py-3 text-center text-[10px] font-black uppercase tracking-wider text-ocular-copy/60">JP</th>
                                <th class="sticky left-12 z-30 w-24 border-b border-r border-ocular-teal/10 bg-ocular-teal/5 px-2 py-3 text-left text-[10px] font-black uppercase tracking-wider text-ocular-copy/60">Jam</th>
                                @foreach ($gridClasses as $class)
                                    <th class="border-b border-r border-ocular-teal/10 px-2 py-3 text-center text-[10px] font-black uppercase tracking-wider text-ocular-copy/70">{{ $class->name }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($scheduleRows as $row)
                                @if ($row['type'] === 'break')
                                    <tr><td colspan="{{ $gridClasses->count() + 2 }}" class="border-b border-ocular-teal/10 bg-ocular-copy px-3 py-2 text-center text-[10px] font-black uppercase tracking-widest text-white">{{ $row['label'] }} · {{ $row['start'] }}-{{ $row['end'] }}</td></tr>
                                    @continue
                                @endif
                                @php
                                    $jp = $row['jp'];
                                    $rowBg = $jp % 2 === 0 ? 'bg-slate-50/70' : 'bg-white';
                                    $stickyBg = $jp % 2 === 0 ? 'bg-slate-50' : 'bg-white';
                                @endphp
                                <tr class="{{ $rowBg }}" style="height: 58px">
                                    <td class="sticky left-0 z-20 border-b border-r border-ocular-teal/10 {{ $stickyBg }} px-2 text-center align-middle"><span class="font-mono text-xs font-black text-ocular-copy/60">{{ $jp }}</span></td>
                                    <td class="sticky left-12 z-20 border-b border-r border-ocular-teal/10 {{ $stickyBg }} px-2 align-middle"><span class="whitespace-nowrap font-mono text-[10px] text-ocular-copy/60">{{ $row['start'] }}<br>{{ $row['end'] }}</span></td>
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
                                                $canOpen = $item->user_id === auth()->id() && (int) $item->day_of_week === (int) $today;
                                            @endphp
                                            <td rowspan="{{ $cell['span'] }}" class="border-b border-r border-ocular-teal/10 p-1 align-stretch">
                                                <button type="button" data-guru-schedule-detail data-class="{{ $item->schoolClass->name }}" data-subject="{{ $item->subject->name }}" data-teacher="{{ $item->teacher->name }}" data-day="{{ $days[$item->day_of_week] ?? '-' }}" data-time="{{ substr($item->start_time, 0, 5) }} - {{ substr($item->end_time, 0, 5) }}" data-year="{{ $item->academicYear->name }} · {{ $item->academicYear->semester === 1 ? 'Ganjil' : 'Genap' }}" data-session-url="{{ $canOpen ? route('guru.sessions.store', $item) : '' }}" data-can-open="{{ $canOpen ? '1' : '0' }}" data-is-mine="{{ $item->user_id === auth()->id() ? '1' : '0' }}" class="flex h-full w-full flex-col justify-center border-2 px-2 py-1 text-center transition hover:-translate-y-0.5 hover:shadow-md {{ $color['bg'] }} {{ $color['border'] }} {{ $color['text'] }}" style="min-height: {{ $blockHeight }}px" title="Lihat detail jadwal {{ $item->teacher->name }}">
                                                    <span class="truncate text-[10px] font-black uppercase">{{ mb_strtoupper(mb_substr($initials, 0, 3)) }}</span>
                                                    @if ($cell['span'] >= 2)
                                                        <span class="mt-0.5 truncate text-[10px] font-semibold">{{ $item->subject->name }}</span>
                                                    @endif
                                                </button>
                                            </td>
                                            @continue
                                        @endif
                                        <td class="border-b border-r border-ocular-teal/10 p-1" style="height: 58px"></td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        @if ($viewMode === 'list')
        <div class="space-y-4">
            @forelse ($schedules as $schedule)
                @php($canOpen = $schedule->user_id === auth()->id() && (int) $schedule->day_of_week === (int) $today)
                <x-ui.card class="relative overflow-hidden border-l-4 border-ocular-accent">
                    <div class="grid gap-4 lg:grid-cols-[minmax(0,1fr)_auto] lg:items-center">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2 text-[10px] font-black uppercase tracking-wider">
                                <span class="bg-ocular-teal/10 px-2.5 py-1.5 text-ocular-teal">{{ $days[$schedule->day_of_week] ?? '-' }}</span>
                                <span class="font-mono text-ocular-copy/70">{{ substr($schedule->start_time, 0, 5) }} – {{ substr($schedule->end_time, 0, 5) }}</span>
                            </div>
                            <h3 class="mt-3 text-2xl font-black leading-tight text-ocular-teal">{{ $schedule->schoolClass->name }}</h3>
                            <p class="mt-1 text-sm font-semibold text-ocular-copy">{{ $schedule->subject->name }}</p>
                            <div class="mt-4 grid gap-2 text-xs text-ocular-copy/65 sm:grid-cols-2">
                                <p><span class="font-bold text-ocular-teal">Guru:</span> {{ $schedule->teacher->name }}</p>
                                <p><span class="font-bold text-ocular-teal">Tahun:</span> {{ $schedule->academicYear->name }} · {{ $schedule->academicYear->semester === 1 ? 'Ganjil' : 'Genap' }}</p>
                            </div>
                        </div>
                        <div class="flex flex-col gap-2 sm:flex-row lg:flex-col lg:items-stretch">
                            <button type="button" data-guru-schedule-detail data-class="{{ $schedule->schoolClass->name }}" data-subject="{{ $schedule->subject->name }}" data-teacher="{{ $schedule->teacher->name }}" data-day="{{ $days[$schedule->day_of_week] ?? '-' }}" data-time="{{ substr($schedule->start_time, 0, 5) }} - {{ substr($schedule->end_time, 0, 5) }}" data-year="{{ $schedule->academicYear->name }} · {{ $schedule->academicYear->semester === 1 ? 'Ganjil' : 'Genap' }}" data-session-url="{{ $canOpen ? route('guru.sessions.store', $schedule) : '' }}" data-can-open="{{ $canOpen ? '1' : '0' }}" data-is-mine="{{ $schedule->user_id === auth()->id() ? '1' : '0' }}" class="inline-flex min-h-10 items-center justify-center border border-ocular-teal/30 px-4 text-[10px] font-black uppercase tracking-wider text-ocular-teal hover:bg-ocular-teal/5">Detail</button>
                            @if ($schedule->user_id === auth()->id())
                                @if ($canOpen)
                                    <form method="POST" action="{{ route('guru.sessions.store', $schedule) }}">@csrf<button class="inline-flex min-h-10 w-full items-center justify-center bg-ocular-orange px-4 text-[10px] font-black uppercase tracking-wider text-white hover:bg-ocular-orange-dark">Buka sesi</button></form>
                                @else
                                    <button type="button" disabled class="inline-flex min-h-10 items-center justify-center border border-slate-200 bg-slate-50 px-4 text-[10px] font-black uppercase tracking-wider text-ocular-copy/35">Buka sesi</button>
                                @endif
                            @endif
                        </div>
                    </div>
                </x-ui.card>
            @empty
                <div class="border-2 border-dashed border-ocular-accent/20 px-5 py-10 text-center text-sm text-ocular-copy/60">Belum ada jadwal mengajar pada tahun ajaran aktif.</div>
            @endforelse
        </div>

        <x-ui.pagination :paginator="$schedules" />
        @endif
    </x-ui.page>

    <dialog data-guru-schedule-dialog class="border-0 bg-transparent p-0 backdrop:bg-slate-950/55" style="position: fixed; inset: 0; width: min(92vw, 28rem); max-width: min(92vw, 28rem); height: fit-content; margin: auto">
        <div class="border border-slate-200 bg-white shadow-2xl">
            <div class="flex items-start justify-between gap-4 border-b border-slate-200 bg-ocular-surface px-5 py-4">
                <div><p class="text-[10px] font-black uppercase tracking-[0.18em] text-ocular-orange">Detail jadwal</p><h2 data-guru-schedule-title class="mt-1 text-lg font-black text-ocular-teal">-</h2></div>
                <button type="button" data-guru-schedule-close class="grid size-9 place-items-center border border-slate-200 bg-white text-xl leading-none text-ocular-copy hover:border-ocular-teal hover:text-ocular-teal" aria-label="Tutup detail">×</button>
            </div>
            <div class="space-y-4 p-5">
                <dl class="grid gap-3 text-sm sm:grid-cols-2">
                    <div><dt class="text-[10px] font-black uppercase tracking-wider text-ocular-accent">Kelas</dt><dd data-guru-schedule-class class="mt-1 font-bold text-ocular-teal">-</dd></div>
                    <div><dt class="text-[10px] font-black uppercase tracking-wider text-ocular-accent">Mapel</dt><dd data-guru-schedule-subject class="mt-1 font-bold text-ocular-teal">-</dd></div>
                    <div><dt class="text-[10px] font-black uppercase tracking-wider text-ocular-accent">Guru</dt><dd data-guru-schedule-teacher class="mt-1 font-bold text-ocular-teal">-</dd></div>
                    <div><dt class="text-[10px] font-black uppercase tracking-wider text-ocular-accent">Waktu</dt><dd data-guru-schedule-time class="mt-1 font-mono text-xs font-bold text-ocular-copy">-</dd></div>
                    <div class="sm:col-span-2"><dt class="text-[10px] font-black uppercase tracking-wider text-ocular-accent">Tahun ajaran</dt><dd data-guru-schedule-year class="mt-1 text-ocular-copy">-</dd></div>
                </dl>
                <form method="POST" data-guru-schedule-session-form class="hidden border-t border-slate-100 pt-4">
                    @csrf
                    <button class="min-h-10 bg-ocular-orange px-4 text-[10px] font-black uppercase tracking-wider text-white hover:bg-ocular-orange-dark">Buka sesi absensi</button>
                </form>
                <p data-guru-schedule-session-note class="hidden border-t border-slate-100 pt-4 text-xs text-ocular-copy/60"></p>
            </div>
        </div>
    </dialog>

    <script>
        (() => {
            const dialog = document.querySelector('[data-guru-schedule-dialog]');
            if (!dialog) return;
            const set = (name, value) => { const target = dialog.querySelector(`[data-guru-schedule-${name}]`); if (target) target.textContent = value; };
            const form = dialog.querySelector('[data-guru-schedule-session-form]');
            const note = dialog.querySelector('[data-guru-schedule-session-note]');
            const close = () => dialog.close();
            document.querySelectorAll('[data-guru-schedule-detail]').forEach((button) => button.addEventListener('click', () => {
                set('title', `${button.dataset.day} · ${button.dataset.time}`);
                set('class', button.dataset.class);
                set('subject', button.dataset.subject);
                set('teacher', button.dataset.teacher);
                set('time', button.dataset.time);
                set('year', button.dataset.year);
                if (button.dataset.canOpen === '1') {
                    form.action = button.dataset.sessionUrl;
                    form.classList.remove('hidden');
                    note.classList.add('hidden');
                } else {
                    form.classList.add('hidden');
                    note.textContent = button.dataset.isMine === '1' ? 'Sesi hanya bisa dibuka pada hari jadwal tersebut.' : 'Jadwal ini milik guru lain, hanya bisa dilihat.';
                    note.classList.remove('hidden');
                }
                dialog.showModal();
            }));
            dialog.querySelector('[data-guru-schedule-close]')?.addEventListener('click', close);
            dialog.addEventListener('click', (event) => { if (event.target === dialog) close(); });
        })();
    </script>
</x-layouts.app>
