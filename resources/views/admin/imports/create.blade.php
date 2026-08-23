<x-layouts.app title="Import Data - Ocular">
    <x-ui.page eyebrow="Data center" title="Import data">
        <x-slot:actions>
            <x-ui.link-button :href="route('admin.students.index')" variant="muted">Kembali ke siswa</x-ui.link-button>
            <x-ui.link-button :href="route('admin.imports.template')" variant="outline">Download template</x-ui.link-button>
        </x-slot:actions>

        <x-ui.flash :message="session('success')" />
        @if ($errors->any())
            <x-ui.flash type="error" :message="$errors->first()" />
        @endif

        @if (! isset($preview))
            <x-ui.card padding="p-0" class="overflow-hidden border border-ocular-teal/15">
                <div class="grid lg:grid-cols-[minmax(0,1fr)_22rem]">
                    <section class="p-5 sm:p-6 lg:p-7">
                        <div class="max-w-2xl">
                            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-ocular-orange">Langkah 1</p>
                            <h2 class="mt-2 text-xl font-black text-ocular-teal">Upload file sumber</h2>
                        </div>

                        <form method="POST" action="{{ route('admin.imports.preview') }}" enctype="multipart/form-data" class="mt-6 max-w-2xl space-y-4">
                            @csrf

                            <div class="grid gap-3 sm:grid-cols-2">
                                <label for="workbook-upload" class="group block cursor-pointer border border-slate-300 bg-white p-4 transition hover:border-ocular-teal hover:bg-ocular-teal/5">
                                    <input id="workbook-upload" type="file" name="workbook" accept=".xlsx,.xls" required class="sr-only" data-import-file="workbook">
                                    <span class="flex items-center gap-3">
                                        <span class="grid size-10 shrink-0 place-items-center bg-ocular-teal text-white"><i data-lucide="file-spreadsheet" class="size-5"></i></span>
                                        <span class="min-w-0">
                                            <span class="block text-[10px] font-black uppercase tracking-[0.16em] text-ocular-teal">Workbook Excel</span>
                                            <span class="mt-1 block truncate text-xs text-ocular-copy/55" data-import-file-name="workbook">Pilih file .xlsx</span>
                                        </span>
                                    </span>
                                </label>

                                <label for="photos-upload" class="group block cursor-pointer border border-slate-300 bg-white p-4 transition hover:border-ocular-orange hover:bg-ocular-orange/5">
                                    <input id="photos-upload" type="file" name="photos" accept=".zip" class="sr-only" data-import-file="photos">
                                    <span class="flex items-center gap-3">
                                        <span class="grid size-10 shrink-0 place-items-center bg-ocular-orange text-white"><i data-lucide="archive" class="size-5"></i></span>
                                        <span class="min-w-0">
                                            <span class="block text-[10px] font-black uppercase tracking-[0.16em] text-ocular-teal">ZIP foto ID card</span>
                                            <span class="mt-1 block truncate text-xs text-ocular-copy/55" data-import-file-name="photos">Opsional</span>
                                        </span>
                                    </span>
                                </label>
                            </div>

                            <div class="flex flex-col gap-2 border-t border-slate-100 pt-5 sm:flex-row sm:items-center sm:justify-between">
                                <p class="text-xs text-ocular-copy/55">Preview data sebelum simpan.</p>
                                <x-ui.button variant="teal" class="w-full sm:w-auto">Validasi dan preview</x-ui.button>
                            </div>
                        </form>
                    </section>

                    <aside class="border-t border-ocular-teal/10 bg-ocular-surface/55 p-5 sm:p-6 lg:border-l lg:border-t-0 lg:p-7">
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] text-ocular-orange">Format data</p>
                        <h2 class="mt-2 text-lg font-black text-ocular-teal">Format</h2>
                        <dl class="mt-5 space-y-4 text-sm">
                            <div>
                                <dt class="text-[10px] font-black uppercase tracking-[0.16em] text-ocular-accent">Sheet Excel</dt>
                                <dd class="mt-1 text-ocular-copy/70">subjects, teachers, students, schedules</dd>
                            </div>
                            <div>
                                <dt class="text-[10px] font-black uppercase tracking-[0.16em] text-ocular-accent">Foto siswa</dt>
                                <dd class="mt-1 font-mono text-xs text-ocular-copy/70">NISN.jpg</dd>
                            </div>
                            <div>
                                <dt class="text-[10px] font-black uppercase tracking-[0.16em] text-ocular-accent">Foto guru</dt>
                                <dd class="mt-1 font-mono text-xs text-ocular-copy/70">NIP.png</dd>
                            </div>
                        </dl>
                    </aside>
                </div>
            </x-ui.card>

            <script>
                (() => {
                    document.querySelectorAll('[data-import-file]').forEach((input) => {
                        input.addEventListener('change', () => {
                            const target = document.querySelector(`[data-import-file-name="${input.dataset.importFile}"]`);
                            const file = input.files?.[0];
                            if (target && file) target.textContent = file.name;
                        });
                    });
                })();
            </script>
        @else
            @php($summary = $preview['summary'])
            <x-ui.card class="border border-ocular-teal/15">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] text-ocular-orange">Langkah 2</p>
                        <h2 class="mt-2 text-xl font-black text-ocular-teal">Preview import</h2>
                        <p class="mt-1 text-sm text-ocular-copy/70">Error wajib beres.</p>
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-center sm:grid-cols-4">
                        <div class="border border-slate-200 px-3 py-2"><p class="font-mono text-lg font-black text-ocular-teal">{{ $summary['total'] }}</p><p class="text-[9px] font-bold uppercase tracking-wider text-ocular-copy/50">Total</p></div>
                        <div class="border border-emerald-200 bg-emerald-50 px-3 py-2"><p class="font-mono text-lg font-black text-emerald-700">{{ $summary['new'] }}</p><p class="text-[9px] font-bold uppercase tracking-wider text-emerald-700/70">Baru</p></div>
                        <div class="border border-amber-200 bg-amber-50 px-3 py-2"><p class="font-mono text-lg font-black text-amber-700">{{ $summary['update'] }}</p><p class="text-[9px] font-bold uppercase tracking-wider text-amber-700/70">Update</p></div>
                        <div class="border border-rose-200 bg-rose-50 px-3 py-2"><p class="font-mono text-lg font-black text-rose-700">{{ $summary['errors'] }}</p><p class="text-[9px] font-bold uppercase tracking-wider text-rose-700/70">Error</p></div>
                    </div>
                </div>
            </x-ui.card>

            @if ($preview['errors'])
                <x-ui.flash type="error" :message="implode(' ', $preview['errors'])" />
            @endif

            @foreach ($preview['sheets'] as $sheet => $items)
                <x-ui.card padding="p-0" class="overflow-hidden border border-slate-200">
                    <div class="border-b border-slate-200 px-4 py-4 sm:px-5">
                        <h2 class="text-sm font-black uppercase tracking-widest text-ocular-teal">{{ $sheet }}</h2>
                        <p class="mt-1 text-xs text-ocular-copy/60">{{ count($items) }} baris ditemukan</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[760px] text-left text-sm">
                            <thead class="bg-ocular-surface text-[10px] uppercase tracking-widest text-ocular-accent"><tr><th class="px-4 py-3">Pilih</th><th class="px-4 py-3">Baris</th><th class="px-4 py-3">Data</th><th class="px-4 py-3">Foto</th><th class="px-4 py-3">Status</th></tr></thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse ($items as $index => $item)
                                    <tr class="{{ $item['status'] === 'error' ? 'bg-rose-50/50' : 'hover:bg-ocular-surface/60' }}">
                                        <td class="px-4 py-3 align-top"><input type="checkbox" name="selected[]" value="{{ $sheet }}:{{ $index }}" form="commit-import" @checked($item['status'] !== 'error') @disabled($item['status'] === 'error') class="size-4 border-slate-300 text-ocular-teal"></td>
                                        <td class="px-4 py-3 align-top font-mono text-xs text-ocular-copy/60">{{ $item['row'] }}</td>
                                        <td class="max-w-lg px-4 py-3 align-top"><p class="font-semibold text-ocular-teal">{{ $item['label'] }}</p>@if ($item['errors'])<p class="mt-1 text-xs leading-5 text-rose-600">{{ implode(' ', $item['errors']) }}</p>@endif</td>
                                        <td class="px-4 py-3 align-top text-xs">@if ($item['photo'])<span class="text-emerald-700">{{ basename($item['photo']) }}</span>@elseif (in_array($sheet, ['teachers', 'students'], true))<span class="text-amber-700">Tidak ditemukan</span>@else<span class="text-slate-400">—</span>@endif</td>
                                        <td class="px-4 py-3 align-top"><x-ui.status-badge :status="$item['status'] === 'error' ? 'closed' : ($item['status'] === 'update' ? 'pending' : 'open')" :label="strtoupper($item['status'])" /></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="px-4 py-10 text-center text-sm text-ocular-copy/60">Tidak ada baris data.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </x-ui.card>
            @endforeach

            <form id="commit-import" method="POST" action="{{ route('admin.imports.commit') }}" class="flex flex-col gap-3 border border-ocular-orange/25 bg-ocular-orange/5 p-4 sm:flex-row sm:items-center sm:justify-between">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <label class="flex items-start gap-3 text-sm text-ocular-copy">
                    <input type="checkbox" name="reset_first" value="1" class="mt-1 size-4 border-slate-300 text-ocular-orange">
                    <span><span class="block font-bold text-ocular-teal">Reset data lama</span><span class="mt-1 block text-xs text-ocular-copy/60">Hapus absensi, jadwal, siswa, guru, dan mapel.</span></span>
                </label>
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                    <x-ui.link-button :href="route('admin.imports.create')" variant="muted">Batalkan</x-ui.link-button>
                        <x-ui.button :disabled="$summary['errors'] > 0" class="disabled:cursor-not-allowed disabled:opacity-50">Simpan data terpilih</x-ui.button>
                </div>
            </form>
        @endif
    </x-ui.page>
</x-layouts.app>
