<x-layouts.app title="Siswa - Ocular">
    <x-ui.page eyebrow="Data master" title="Siswa" description="Kelola data siswa, kelas, status aktif, dan import CSV.">
        <x-slot:actions>
            <x-ui.link-button :href="route('admin.imports.create')" variant="teal">Import data</x-ui.link-button>
            <x-ui.link-button :href="route('admin.students.promotion')" variant="outline">Kenaikan kelas</x-ui.link-button>
            <x-ui.link-button :href="route('admin.students.create')">Tambah siswa</x-ui.link-button>
        </x-slot:actions>
        <x-ui.flash :message="session('success')" />
        @if (session('warning')) <x-ui.flash type="warning" :message="session('warning')" /> @endif
        @if ($errors->any()) <x-ui.flash type="error" :message="$errors->first()" /> @endif

        <x-ui.card padding="p-0" class="overflow-hidden border border-slate-200">
            <div class="flex flex-col gap-3 border-b border-slate-200 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-5">
                <div><h2 class="text-sm font-black uppercase tracking-widest text-ocular-teal">Daftar siswa</h2><p class="mt-1 text-xs text-ocular-copy/60">{{ $students->total() }} siswa · pilih siswa di halaman ini untuk aksi massal</p></div>
                <div class="flex flex-wrap items-center justify-end gap-2">
                    <form method="GET" class="flex min-w-0 items-center gap-2 sm:w-[20rem]">
                        <x-ui.search-input name="search" :value="$search" placeholder="Cari nama, NIS, atau NISN..." id="student-search" />
                        <x-ui.button type="submit" variant="teal" class="min-h-10 px-3">Cari</x-ui.button>
                        @if ($search !== '')<x-ui.link-button :href="route('admin.students.index')" variant="muted" class="min-h-10 px-3">Reset</x-ui.link-button>@endif
                    </form>
                    <x-ui.filter-panel title="Filter siswa" description="Persempit daftar berdasarkan kelas." :active="$selectedClassId ? 1 : 0">
                        <form method="GET" class="grid gap-3 sm:grid-cols-[1fr_auto_auto] sm:items-end">
                            <input type="hidden" name="search" value="{{ $search }}">
                            <label class="block text-xs font-bold uppercase tracking-wider text-ocular-teal">Kelas<select name="class_id" class="mt-2 min-h-11 w-full border border-slate-300 bg-white px-3 text-sm"><option value="">Semua kelas</option>@foreach ($classes as $class)<option value="{{ $class->id }}" @selected($selectedClassId == $class->id)>{{ $class->name }} · {{ $class->academicYear->name }}</option>@endforeach</select></label>
                            <x-ui.button variant="teal">Tampilkan</x-ui.button>
                            <x-ui.link-button :href="route('admin.students.index')" variant="muted">Reset</x-ui.link-button>
                        </form>
                    </x-ui.filter-panel>
                    <x-ui.bulk-actions form-id="bulk-students-form" :action="route('admin.students.bulk-destroy')" checkbox-name="student_ids[]" confirm-message="Hapus semua siswa yang dipilih? Siswa dengan histori absensi akan dilewati." />
                </div>
            </div>

            <div class="hidden overflow-x-auto md:block"><table class="w-full min-w-[860px] text-left text-sm"><thead class="bg-ocular-surface text-[10px] uppercase tracking-widest text-ocular-accent"><tr><th class="w-12 px-5 py-3">Pilih</th><th class="px-5 py-3">Nama</th><th class="px-5 py-3">NIS</th><th class="px-5 py-3">NISN</th><th class="px-5 py-3">Kelas</th><th class="px-5 py-3">Status</th><th class="px-5 py-3">Aksi</th></tr></thead><tbody class="divide-y divide-slate-100">
                @forelse ($students as $student)
                    <tr class="hover:bg-ocular-surface/60"><td class="px-5 py-3"><input type="checkbox" name="student_ids[]" value="{{ $student->id }}" form="bulk-students-form" data-bulk-checkbox="bulk-students-form" data-bulk-id="{{ $student->id }}" class="size-4 border-slate-300 text-ocular-teal"></td><td class="px-5 py-3 font-semibold text-ocular-teal">{{ $student->name }}</td><td class="px-5 py-3 font-mono text-xs">{{ $student->nis }}</td><td class="px-5 py-3 font-mono text-xs">{{ $student->nisn }}</td><td class="px-5 py-3">{{ $student->schoolClass->name }}</td><td class="px-5 py-3"><x-ui.status-badge :status="$student->is_active ? 'open' : 'closed'" :label="$student->is_active ? 'Aktif' : 'Nonaktif'" /></td><td class="px-5 py-3"><x-ui.row-actions :edit-href="route('admin.students.edit', $student)" :delete-action="route('admin.students.destroy', $student)" confirm="Hapus siswa ini?" /></td></tr>
                @empty <tr><td colspan="7" class="px-5 py-12 text-center text-sm text-ocular-copy/60">Belum ada data siswa.</td></tr> @endforelse
            </tbody></table></div>

            <div class="divide-y divide-slate-100 md:hidden">@forelse ($students as $student)<article class="space-y-3 p-4"><div class="flex items-start justify-between gap-3"><div class="flex items-start gap-3"><input type="checkbox" name="student_ids[]" value="{{ $student->id }}" form="bulk-students-form" data-bulk-checkbox="bulk-students-form" data-bulk-id="{{ $student->id }}" class="mt-1 size-4 border-slate-300 text-ocular-teal"><div><h3 class="font-semibold text-ocular-teal">{{ $student->name }}</h3><p class="mt-1 font-mono text-[10px] text-ocular-copy/60">NIS {{ $student->nis }} · NISN {{ $student->nisn }}</p></div></div><x-ui.status-badge :status="$student->is_active ? 'open' : 'closed'" :label="$student->is_active ? 'Aktif' : 'Nonaktif'" /></div><p class="text-xs text-ocular-copy/70">Kelas {{ $student->schoolClass->name }}</p><x-ui.row-actions class="border-t border-slate-100 pt-3" :edit-href="route('admin.students.edit', $student)" :delete-action="route('admin.students.destroy', $student)" confirm="Hapus siswa ini?" /></article>@empty<p class="px-4 py-12 text-center text-sm text-ocular-copy/60">Belum ada data siswa.</p>@endforelse</div>
            <div class="px-4 pb-4 sm:px-5"><x-ui.pagination :paginator="$students" /></div>
        </x-ui.card>
    </x-ui.page>
</x-layouts.app>
