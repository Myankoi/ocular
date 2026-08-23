<x-layouts.app title="Kenaikan Kelas - Ocular">
    <x-ui.page eyebrow="Students" title="Kenaikan kelas" description="Pindahkan seluruh siswa aktif dari kelas asal ke kelas tujuan tanpa menghapus histori absensi.">
        <x-ui.flash :message="session('success')" />

        <x-ui.card class="max-w-2xl border border-ocular-teal/15">
            <form method="POST" action="{{ route('admin.students.promote') }}" class="space-y-5">
                @csrf

                <label class="block text-xs font-bold uppercase tracking-wider text-ocular-teal">Kelas asal
                    <select name="from_class_id" required class="mt-2 min-h-11 w-full border border-slate-300 bg-white px-3 text-sm focus:border-ocular-teal focus:outline-none focus:ring-2 focus:ring-ocular-teal/20">
                        <option value="">Pilih kelas asal</option>
                        @foreach ($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }} · {{ $class->academicYear->name }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="block text-xs font-bold uppercase tracking-wider text-ocular-teal">Kelas tujuan
                    <select name="to_class_id" required class="mt-2 min-h-11 w-full border border-slate-300 bg-white px-3 text-sm focus:border-ocular-teal focus:outline-none focus:ring-2 focus:ring-ocular-teal/20">
                        <option value="">Pilih kelas tujuan</option>
                        @foreach ($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }} · {{ $class->academicYear->name }}</option>
                        @endforeach
                    </select>
                </label>

                <div class="border border-amber-200 bg-amber-50 p-3 text-sm font-medium text-amber-800">Histori absensi tetap tersimpan. Pastikan kelas tujuan sudah benar sebelum menyimpan.</div>

                <div class="flex flex-wrap items-center gap-2 border-t border-slate-100 pt-5">
                    <x-ui.button onclick="return confirm('Pindahkan semua siswa aktif?')">Pindahkan siswa</x-ui.button>
                    <x-ui.link-button :href="route('admin.students.index')" variant="muted">Kembali</x-ui.link-button>
                </div>
            </form>
        </x-ui.card>
    </x-ui.page>
</x-layouts.app>
