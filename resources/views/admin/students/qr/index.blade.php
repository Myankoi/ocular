<x-layouts.app title="QR Code Siswa - Ocular">
    <x-ui.page eyebrow="Data siswa" title="QR code siswa" description="Unduh QR code berisi NISN untuk kebutuhan absensi.">
        <x-slot:actions>
            @if ($selectedClassId)
                <x-ui.link-button :href="route('admin.classes.qr-codes.download', $selectedClassId)" variant="teal">Download ZIP kelas</x-ui.link-button>
            @endif
        </x-slot:actions>

        @if ($errors->any())<x-ui.flash type="error" :message="$errors->first()" />@endif

        <x-ui.card class="border border-ocular-teal/15">
            <form method="GET" class="grid gap-3 sm:grid-cols-[1fr_1fr_auto_auto] sm:items-end">
                <label class="block text-xs font-bold uppercase tracking-wider text-ocular-teal">Cari siswa
                    <input name="search" value="{{ $search }}" placeholder="Nama, NIS, atau NISN" class="mt-2 min-h-11 w-full border border-slate-300 px-3 text-sm focus:border-ocular-teal focus:outline-none focus:ring-2 focus:ring-ocular-teal/20">
                </label>
                <label class="block text-xs font-bold uppercase tracking-wider text-ocular-teal">Kelas
                    <select name="class_id" class="mt-2 min-h-11 w-full border border-slate-300 bg-white px-3 text-sm focus:border-ocular-teal focus:outline-none focus:ring-2 focus:ring-ocular-teal/20">
                        <option value="">Semua kelas</option>
                        @foreach ($classes as $class)
                            <option value="{{ $class->id }}" @selected($selectedClassId == $class->id)>{{ $class->name }} · {{ $class->academicYear->name }}</option>
                        @endforeach
                    </select>
                </label>
                <x-ui.button variant="teal">Tampilkan</x-ui.button>
                <x-ui.link-button :href="route('admin.students.qr-codes.index')" variant="muted">Reset</x-ui.link-button>
            </form>
        </x-ui.card>

        <div class="flex items-center justify-between gap-3">
            <h2 class="text-sm font-black uppercase tracking-widest text-ocular-teal">Daftar QR</h2>
            <span class="font-mono text-[10px] font-bold text-ocular-accent">{{ $students->total() }} siswa</span>
        </div>

        <x-ui.card padding="p-0" class="overflow-hidden border border-ocular-teal/10">
            <div class="hidden overflow-x-auto md:block">
                <table class="w-full min-w-[720px] text-left text-sm">
                    <thead class="bg-ocular-surface text-[10px] uppercase tracking-widest text-ocular-accent"><tr><th class="px-5 py-3">Siswa</th><th class="px-5 py-3">NISN</th><th class="px-5 py-3">Kelas</th><th class="px-5 py-3">QR</th><th class="px-5 py-3">Aksi</th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($students as $student)
                            <tr class="hover:bg-ocular-surface/60">
                                <td class="px-5 py-3"><p class="font-semibold text-ocular-teal">{{ $student->name }}</p><p class="mt-1 text-xs text-ocular-copy/60">NIS: {{ $student->nis }}</p></td>
                                <td class="px-5 py-3 font-mono text-xs">{{ $student->nisn }}</td>
                                <td class="px-5 py-3">{{ $student->schoolClass->name }}</td>
                                <td class="px-5 py-3"><img src="{{ route('admin.students.qr-codes.show', $student) }}" alt="QR {{ $student->nisn }}" class="h-16 w-16 rounded border border-slate-200 bg-white p-1"></td>
                                <td class="px-5 py-3"><a href="{{ route('admin.students.qr-codes.download', $student) }}" class="text-xs font-bold text-ocular-teal underline">Download PNG</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-5 py-12 text-center text-sm text-ocular-copy/60">Tidak ada siswa sesuai filter.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="divide-y divide-slate-100 md:hidden">
                @forelse ($students as $student)
                    <article class="flex items-center gap-4 p-4">
                        <img src="{{ route('admin.students.qr-codes.show', $student) }}" alt="QR {{ $student->nisn }}" class="h-20 w-20 shrink-0 rounded border border-slate-200 bg-white p-1">
                        <div class="min-w-0 flex-1"><h3 class="truncate font-semibold text-ocular-teal">{{ $student->name }}</h3><p class="mt-1 font-mono text-[10px] text-ocular-copy/60">{{ $student->nisn }} · {{ $student->schoolClass->name }}</p><a href="{{ route('admin.students.qr-codes.download', $student) }}" class="mt-3 inline-block text-xs font-bold text-ocular-teal underline">Download PNG</a></div>
                    </article>
                @empty
                    <p class="px-4 py-12 text-center text-sm text-ocular-copy/60">Tidak ada siswa sesuai filter.</p>
                @endforelse
            </div>
            <div class="px-4 pb-4 sm:px-5"><x-ui.pagination :paginator="$students" /></div>
        </x-ui.card>
    </x-ui.page>
</x-layouts.app>
