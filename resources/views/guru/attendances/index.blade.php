<x-layouts.app title="Riwayat Absensi - Ocular">
    @php($activeFilterCount = collect($filters)->filter(fn ($value) => filled($value))->count())

    <x-ui.page eyebrow="Laporan Guru" title="Rekap absensi">
        <x-slot:actions>
            <x-ui.link-button :href="route('guru.attendances.export', request()->query())" variant="outline">
                <i data-lucide="download" class="size-4"></i>
                Unduh Excel
            </x-ui.link-button>
            <x-ui.filter-panel title="Filter laporan" :active="$activeFilterCount">
                <form method="GET" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <label class="block text-xs font-bold uppercase tracking-wider text-ocular-teal">Kelas<select name="class_id" class="mt-2 min-h-11 w-full border border-slate-300 bg-white px-3 text-sm focus:border-ocular-teal focus:outline-none focus:ring-2 focus:ring-ocular-teal/20"><option value="">Semua kelas</option>@foreach ($classes as $class)<option value="{{ $class->id }}" @selected(($filters['class_id'] ?? null) == $class->id)>{{ $class->name }}</option>@endforeach</select></label>
                    <label class="block text-xs font-bold uppercase tracking-wider text-ocular-teal">Mata pelajaran<select name="subject_id" class="mt-2 min-h-11 w-full border border-slate-300 bg-white px-3 text-sm focus:border-ocular-teal focus:outline-none focus:ring-2 focus:ring-ocular-teal/20"><option value="">Semua mata pelajaran</option>@foreach ($subjects as $subject)<option value="{{ $subject->id }}" @selected(($filters['subject_id'] ?? null) == $subject->id)>{{ $subject->name }}</option>@endforeach</select></label>
                    <label class="block text-xs font-bold uppercase tracking-wider text-ocular-teal">Dari tanggal<input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="mt-2 min-h-11 w-full border border-slate-300 px-3 text-sm focus:border-ocular-teal focus:outline-none focus:ring-2 focus:ring-ocular-teal/20"></label>
                    <label class="block text-xs font-bold uppercase tracking-wider text-ocular-teal">Sampai tanggal<input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" class="mt-2 min-h-11 w-full border border-slate-300 px-3 text-sm focus:border-ocular-teal focus:outline-none focus:ring-2 focus:ring-ocular-teal/20"></label>
                    <div class="flex flex-wrap gap-2 sm:col-span-2 lg:col-span-4"><x-ui.button variant="teal"><i data-lucide="search" class="size-4"></i> Tampilkan laporan</x-ui.button><x-ui.link-button :href="route('guru.attendances.index')" variant="muted">Reset filter</x-ui.link-button></div>
                </form>
            </x-ui.filter-panel>
        </x-slot:actions>

        <x-ui.card padding="p-0" class="overflow-hidden border border-slate-200">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-4 py-4 sm:px-5">
                <div><h2 class="text-sm font-black uppercase tracking-widest text-ocular-teal">Data absensi</h2><p class="mt-1 text-xs text-ocular-copy/60">{{ $attendances->total() }} record</p></div>
                <span class="font-mono text-[10px] font-bold uppercase text-ocular-accent">Halaman {{ $attendances->currentPage() }}</span>
            </div>
            <div class="hidden overflow-x-auto md:block">
                <table class="w-full min-w-[760px] text-left text-sm">
                    <thead class="bg-ocular-surface text-[10px] uppercase tracking-widest text-ocular-accent"><tr><th class="px-5 py-3">Tanggal</th><th class="px-5 py-3">Siswa</th><th class="px-5 py-3">Kelas</th><th class="px-5 py-3">Mapel</th><th class="px-5 py-3">Status</th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($attendances as $attendance)
                            <tr class="hover:bg-ocular-surface/60"><td class="px-5 py-3 font-mono text-xs">{{ $attendance->session->date->format('d/m/Y') }}</td><td class="px-5 py-3 font-semibold text-ocular-teal">{{ $attendance->student->name }}</td><td class="px-5 py-3">{{ $attendance->session->schedule->schoolClass->name }}</td><td class="px-5 py-3">{{ $attendance->session->schedule->subject->name }}</td><td class="px-5 py-3"><x-ui.status-badge :status="$attendance->status" /></td></tr>
                        @empty
                            <tr><td colspan="5" class="px-5 py-12 text-center text-sm text-ocular-copy/60">Data kosong.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="divide-y divide-slate-100 md:hidden">
                @forelse ($attendances as $attendance)
                    <article class="space-y-3 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h3 class="font-semibold text-ocular-teal">{{ $attendance->student->name }}</h3>
                                <p class="mt-1 font-mono text-[10px] text-ocular-copy/60">{{ $attendance->session->date->format('d/m/Y') }}</p>
                            </div>
                            <x-ui.status-badge :status="$attendance->status" />
                        </div>
                        <dl class="grid grid-cols-2 gap-3 border-t border-slate-100 pt-3 text-xs">
                            <div><dt class="text-ocular-copy/50">Kelas</dt><dd class="mt-1 font-semibold">{{ $attendance->session->schedule->schoolClass->name }}</dd></div>
                            <div><dt class="text-ocular-copy/50">Mata pelajaran</dt><dd class="mt-1 font-semibold">{{ $attendance->session->schedule->subject->name }}</dd></div>
                        </dl>
                    </article>
                @empty
                    <p class="px-4 py-12 text-center text-sm text-ocular-copy/60">Data kosong.</p>
                @endforelse
            </div>
            <div class="px-4 pb-4 sm:px-5"><x-ui.pagination :paginator="$attendances" /></div>
        </x-ui.card>
    </x-ui.page>
</x-layouts.app>
