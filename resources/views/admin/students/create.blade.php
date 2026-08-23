<x-layouts.app title="Tambah Siswa - Ocular">
    <x-ui.page eyebrow="Students" title="Tambah siswa" description="Tambahkan data siswa aktif dan hubungkan ke kelas pada tahun ajaran berjalan.">
        <x-ui.card class="max-w-2xl border border-ocular-teal/15">
            <form method="POST" action="{{ route('admin.students.store') }}" class="space-y-5">
                @csrf

                @include('admin.students.form', ['student' => null])

                <div class="flex flex-wrap items-center gap-2 border-t border-slate-100 pt-5">
                    <x-ui.button>Simpan siswa</x-ui.button>
                    <x-ui.link-button :href="route('admin.students.index')" variant="muted">Batal</x-ui.link-button>
                </div>
            </form>
        </x-ui.card>
    </x-ui.page>
</x-layouts.app>
