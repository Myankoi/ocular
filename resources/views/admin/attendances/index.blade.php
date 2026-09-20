<x-layouts.app title="Laporan Absensi - Ocular">
    @php($activeFilterCount = collect($filters)->filter(fn ($value) => filled($value))->count())

    <x-ui.page eyebrow="Laporan Admin" title="Attendance logs">
        <x-slot:actions>
            <x-ui.link-button :href="route('admin.attendances.export', request()->query())" variant="outline">
                <i data-lucide="download" class="size-4"></i>
                Unduh Excel
            </x-ui.link-button>
            <x-ui.filter-panel title="Filter laporan" :active="$activeFilterCount">
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
                    <div class="flex flex-wrap gap-2 sm:col-span-2 lg:col-span-3"><x-ui.button variant="teal"><i data-lucide="search" class="size-4"></i> Tampilkan laporan</x-ui.button><x-ui.link-button :href="route('admin.attendances.index')" variant="muted">Reset filter</x-ui.link-button></div>
                </form>
            </x-ui.filter-panel>
        </x-slot:actions>

        <x-ui.flash :message="session('success')" />
        @if ($errors->any())<x-ui.flash type="error" :message="$errors->first()" />@endif

        <x-ui.card padding="p-0" class="overflow-hidden border border-slate-200">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-4 py-4 sm:px-5"><div><h2 class="text-sm font-black uppercase tracking-widest text-ocular-teal">Hasil laporan</h2><p class="mt-1 text-xs text-ocular-copy/60">{{ $attendances->total() }} record ditemukan</p></div><span class="font-mono text-[10px] font-bold uppercase text-ocular-accent">Halaman {{ $attendances->currentPage() }}</span></div>
            <div class="hidden overflow-x-auto md:block"><table class="w-full min-w-[980px] text-left text-sm"><thead class="bg-ocular-surface text-[10px] uppercase tracking-widest text-ocular-accent"><tr><th class="px-5 py-3">Tanggal</th><th class="px-5 py-3">Siswa</th><th class="px-5 py-3">Kelas</th><th class="px-5 py-3">Mapel</th><th class="px-5 py-3">Guru</th><th class="px-5 py-3">Status</th><th class="px-5 py-3">Aksi</th></tr></thead><tbody class="divide-y divide-slate-100">
                @forelse ($attendances as $attendance)
                    <tr class="hover:bg-ocular-surface/60"><td class="px-5 py-3 font-mono text-xs">{{ $attendance->session->date->format('d/m/Y') }}</td><td class="px-5 py-3 font-semibold text-ocular-teal">{{ $attendance->student->name }}</td><td class="px-5 py-3">{{ $attendance->session->schedule->schoolClass->name }}</td><td class="px-5 py-3">{{ $attendance->session->schedule->subject->name }}</td><td class="px-5 py-3">{{ $attendance->session->schedule->teacher->name }}</td><td class="px-5 py-3"><x-ui.status-badge :status="$attendance->status" /></td><td class="px-5 py-3"><button type="button" data-attendance-edit data-action="{{ route('admin.attendances.update', $attendance) }}" data-status="{{ $attendance->status }}" data-notes="{{ $attendance->notes ?? '' }}" data-student="{{ $attendance->student->name }}" data-context="{{ $attendance->session->date->format('d/m/Y') }} · {{ $attendance->session->schedule->schoolClass->name }}" class="min-h-9 border border-ocular-teal/30 px-3 text-[10px] font-black uppercase tracking-wider text-ocular-teal transition hover:bg-ocular-teal/5">Ubah status</button></td></tr>
                @empty
                    <tr><td colspan="7" class="px-5 py-12 text-center text-sm text-ocular-copy/60">Data kosong.</td></tr>
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
                        <div class="border-t border-slate-100 pt-3"><button type="button" data-attendance-edit data-action="{{ route('admin.attendances.update', $attendance) }}" data-status="{{ $attendance->status }}" data-notes="{{ $attendance->notes ?? '' }}" data-student="{{ $attendance->student->name }}" data-context="{{ $attendance->session->date->format('d/m/Y') }} · {{ $attendance->session->schedule->schoolClass->name }}" class="min-h-9 border border-ocular-teal/30 px-3 text-[10px] font-black uppercase tracking-wider text-ocular-teal transition hover:bg-ocular-teal/5">Ubah status</button></div>
                    </article>
                @empty
                    <p class="px-4 py-12 text-center text-sm text-ocular-copy/60">Data kosong.</p>
                @endforelse
            </div>
            <div class="px-4 pb-4 sm:px-5"><x-ui.pagination :paginator="$attendances" /></div>
        </x-ui.card>
    </x-ui.page>

    <dialog id="attendance-correction-dialog" class="border-0 bg-transparent p-0 backdrop:bg-slate-950/50" style="position: fixed; inset: 0; width: min(92vw, 30rem); max-width: min(92vw, 30rem); height: fit-content; margin: auto">
        <div class="border border-slate-200 bg-white shadow-2xl">
            <div class="flex items-start justify-between gap-4 border-b border-slate-200 bg-ocular-surface px-5 py-4">
                <div><p class="text-[10px] font-black uppercase tracking-[0.18em] text-ocular-orange">Edit absensi</p><h2 class="mt-1 text-base font-black text-ocular-teal">Ubah status kehadiran</h2><p data-correction-context class="mt-1 text-xs text-ocular-copy/60"></p></div>
                <button type="button" data-correction-close class="grid size-8 place-items-center border border-slate-200 bg-white text-lg leading-none text-ocular-copy hover:border-ocular-teal hover:text-ocular-teal" aria-label="Tutup dialog">×</button>
            </div>
            <form id="attendance-correction-form" method="POST" action="#" class="space-y-5 p-5">
                @csrf
                @method('PATCH')
                <p class="text-sm text-ocular-copy">Ubah status untuk <strong data-correction-student class="text-ocular-teal"></strong>.</p>
                <label class="block text-xs font-bold uppercase tracking-wider text-ocular-teal">Status baru<select name="status" data-correction-status class="mt-2 min-h-11 w-full border border-slate-300 bg-white px-3 text-sm focus:border-ocular-teal focus:outline-none focus:ring-2 focus:ring-ocular-teal/20"><option value="hadir">Hadir</option><option value="sakit">Sakit</option><option value="izin">Izin</option><option value="alpha">Alpha</option></select></label>
                <label class="block text-xs font-bold uppercase tracking-wider text-ocular-teal">Catatan<textarea name="notes" data-correction-notes rows="3" placeholder="Alasan atau catatan tambahan..." class="mt-2 w-full border border-slate-300 bg-white px-3 py-3 text-sm focus:border-ocular-teal focus:outline-none focus:ring-2 focus:ring-ocular-teal/20"></textarea></label>
                <div class="flex justify-end gap-2 border-t border-slate-100 pt-4"><button type="button" data-correction-cancel class="min-h-10 border border-slate-200 px-3 text-[10px] font-black uppercase tracking-wider text-ocular-copy hover:bg-slate-50">Batal</button><button type="submit" class="min-h-10 bg-ocular-orange px-4 text-[10px] font-black uppercase tracking-wider text-white hover:bg-ocular-orange-dark">Simpan perubahan</button></div>
            </form>
        </div>
    </dialog>

    <script>
        (() => {
            const dialog = document.querySelector('#attendance-correction-dialog');
            const form = document.querySelector('#attendance-correction-form');
            if (!dialog || !form) return;
            const student = dialog.querySelector('[data-correction-student]');
            const context = dialog.querySelector('[data-correction-context]');
            const status = dialog.querySelector('[data-correction-status]');
            const notes = dialog.querySelector('[data-correction-notes]');
            const close = () => dialog.close();

            document.querySelectorAll('[data-attendance-edit]').forEach((button) => button.addEventListener('click', () => {
                form.action = button.dataset.action;
                student.textContent = button.dataset.student;
                context.textContent = button.dataset.context;
                status.value = button.dataset.status;
                notes.value = button.dataset.notes || '';
                dialog.showModal();
            }));
            dialog.querySelector('[data-correction-close]').addEventListener('click', close);
            dialog.querySelector('[data-correction-cancel]').addEventListener('click', close);
            dialog.addEventListener('click', (event) => { if (event.target === dialog) close(); });
        })();
    </script>
</x-layouts.app>
