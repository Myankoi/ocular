<x-layouts.app title="Laporan Absensi - Ocular">
    @php($activeFilterCount = collect($filters)->filter(fn ($value) => filled($value))->count())

    <x-ui.page eyebrow="Laporan Admin" title="Laporan absensi" description="Tinjau, filter, dan unduh rekap kehadiran sekolah.">
        <x-slot:actions>
            <x-ui.link-button :href="route('admin.attendances.export', request()->query())" variant="outline">
                <span class="text-base">↓</span>
                Unduh Excel
            </x-ui.link-button>
        </x-slot:actions>

        <x-ui.flash :message="session('success')" />
        @if ($errors->any())<x-ui.flash type="error" :message="$errors->first()" />@endif

        <x-ui.card class="border border-ocular-teal/15 bg-white">
            <div class="mb-5 flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-4">
                <div><h2 class="text-sm font-black uppercase tracking-widest text-ocular-teal">Filter laporan</h2><p class="mt-1 text-xs text-ocular-copy/60">Gunakan filter untuk menyiapkan rekap yang ingin diunduh.</p></div>
                @if ($activeFilterCount)<span class="bg-ocular-orange/10 px-2 py-1 text-[10px] font-black uppercase tracking-wider text-ocular-orange">{{ $activeFilterCount }} filter aktif</span>@endif
            </div>
            <form method="GET" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ([
                    ['name' => 'academic_year_id', 'label' => 'Tahun ajaran', 'options' => $academicYears],
                    ['name' => 'class_id', 'label' => 'Kelas', 'options' => $classes],
                    ['name' => 'subject_id', 'label' => 'Mata pelajaran', 'options' => $subjects],
                    ['name' => 'user_id', 'label' => 'Guru', 'options' => $teachers],
                ] as $field)
                    <label class="block text-xs font-bold uppercase tracking-wider text-ocular-teal">{{ $field['label'] }}
                        <select name="{{ $field['name'] }}" class="mt-2 min-h-11 w-full border border-slate-300 bg-white px-3 text-sm focus:border-ocular-teal focus:outline-none focus:ring-2 focus:ring-ocular-teal/20">
                            <option value="">Semua</option>
                            @foreach ($field['options'] as $option)<option value="{{ $option->id }}" @selected(($filters[$field['name']] ?? null) == $option->id)>{{ $option->name }}</option>@endforeach
                        </select>
                    </label>
                @endforeach
                <label class="block text-xs font-bold uppercase tracking-wider text-ocular-teal">Dari tanggal<input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="mt-2 min-h-11 w-full border border-slate-300 px-3 text-sm focus:border-ocular-teal focus:outline-none focus:ring-2 focus:ring-ocular-teal/20"></label>
                <label class="block text-xs font-bold uppercase tracking-wider text-ocular-teal">Sampai tanggal<input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" class="mt-2 min-h-11 w-full border border-slate-300 px-3 text-sm focus:border-ocular-teal focus:outline-none focus:ring-2 focus:ring-ocular-teal/20"></label>
                <div class="flex flex-wrap gap-2 sm:col-span-2 lg:col-span-3"><x-ui.button variant="teal"><span class="text-base">⌕</span> Tampilkan laporan</x-ui.button><x-ui.link-button :href="route('admin.attendances.index')" variant="muted">Reset filter</x-ui.link-button></div>
            </form>
        </x-ui.card>

        <x-ui.card padding="p-0" class="overflow-hidden border border-slate-200">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-4 py-4 sm:px-5"><div><h2 class="text-sm font-black uppercase tracking-widest text-ocular-teal">Hasil laporan</h2><p class="mt-1 text-xs text-ocular-copy/60">{{ $attendances->total() }} record ditemukan</p></div><span class="font-mono text-[10px] font-bold uppercase text-ocular-accent">Halaman {{ $attendances->currentPage() }}</span></div>
            <div class="hidden overflow-x-auto md:block"><table class="w-full min-w-[980px] text-left text-sm"><thead class="bg-ocular-surface text-[10px] uppercase tracking-widest text-ocular-accent"><tr><th class="px-5 py-3">Tanggal</th><th class="px-5 py-3">Siswa</th><th class="px-5 py-3">Kelas</th><th class="px-5 py-3">Mapel</th><th class="px-5 py-3">Guru</th><th class="px-5 py-3">Status</th><th class="px-5 py-3">Ubah status</th></tr></thead><tbody class="divide-y divide-slate-100">
                @forelse ($attendances as $attendance)
                    <tr class="hover:bg-ocular-surface/60"><td class="px-5 py-3 font-mono text-xs">{{ $attendance->session->date->format('d/m/Y') }}</td><td class="px-5 py-3 font-semibold text-ocular-teal">{{ $attendance->student->name }}</td><td class="px-5 py-3">{{ $attendance->session->schedule->schoolClass->name }}</td><td class="px-5 py-3">{{ $attendance->session->schedule->subject->name }}</td><td class="px-5 py-3">{{ $attendance->session->schedule->teacher->name }}</td><td class="px-5 py-3"><x-ui.status-badge :status="$attendance->status" /></td><td class="px-5 py-3"><form method="POST" action="{{ route('admin.attendances.update', $attendance) }}" class="flex min-w-44 items-center gap-2">@csrf @method('PATCH')<select name="status" class="min-h-10 flex-1 border border-slate-300 bg-white px-2 text-xs">@foreach (['hadir' => 'Hadir', 'sakit' => 'Sakit', 'izin' => 'Izin', 'alpha' => 'Alpha'] as $value => $label)<option value="{{ $value }}" @selected($attendance->status === $value)>{{ $label }}</option>@endforeach</select><button class="min-h-10 border border-ocular-teal/30 px-3 text-[10px] font-black uppercase tracking-wider text-ocular-teal hover:bg-ocular-teal/5">Simpan</button></form></td></tr>
                @empty
                    <tr><td colspan="7" class="px-5 py-12 text-center text-sm text-ocular-copy/60">Belum ada record sesuai filter.</td></tr>
                @endforelse
            </tbody></table></div>
            <div class="divide-y divide-slate-100 md:hidden">
                @forelse ($attendances as $attendance)
                    <article class="space-y-3 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h3 class="font-semibold text-ocular-teal">{{ $attendance->student->name }}</h3>
                                <p class="mt-1 font-mono text-[10px] text-ocular-copy/60">{{ $attendance->session->date->format('d/m/Y') }} · {{ $attendance->session->schedule->schoolClass->name }}</p>
                            </div>
                            <x-ui.status-badge :status="$attendance->status" />
                        </div>
                        <dl class="grid grid-cols-2 gap-3 border-t border-slate-100 pt-3 text-xs">
                            <div><dt class="text-ocular-copy/50">Mata pelajaran</dt><dd class="mt-1 font-semibold">{{ $attendance->session->schedule->subject->name }}</dd></div>
                            <div><dt class="text-ocular-copy/50">Guru</dt><dd class="mt-1 font-semibold">{{ $attendance->session->schedule->teacher->name }}</dd></div>
                        </dl>
                        <form method="POST" action="{{ route('admin.attendances.update', $attendance) }}" class="flex items-center gap-2 border-t border-slate-100 pt-3">@csrf @method('PATCH')<label class="sr-only" for="status-mobile-{{ $attendance->id }}">Ubah status</label><select id="status-mobile-{{ $attendance->id }}" name="status" class="min-h-10 flex-1 border border-slate-300 bg-white px-2 text-xs">@foreach (['hadir' => 'Hadir', 'sakit' => 'Sakit', 'izin' => 'Izin', 'alpha' => 'Alpha'] as $value => $label)<option value="{{ $value }}" @selected($attendance->status === $value)>{{ $label }}</option>@endforeach</select><button class="min-h-10 border border-ocular-teal/30 px-3 text-[10px] font-black uppercase tracking-wider text-ocular-teal">Simpan</button></form>
                    </article>
                @empty
                    <p class="px-4 py-12 text-center text-sm text-ocular-copy/60">Belum ada record sesuai filter.</p>
                @endforelse
            </div>
            <div class="px-4 pb-4 sm:px-5"><x-ui.pagination :paginator="$attendances" /></div>
        </x-ui.card>
    </x-ui.page>
</x-layouts.app>
