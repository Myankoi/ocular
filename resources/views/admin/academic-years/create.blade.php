<x-layouts.app title="Tambah Tahun Ajaran - Ocular">
    <x-ui.page eyebrow="Academic Years" title="Tambah tahun ajaran" description="Atur tahun ajaran dan semester yang menjadi konteks data kelas, jadwal, dan absensi.">
        <x-ui.card class="max-w-2xl border border-ocular-teal/15">
            <form method="POST" action="{{ route('admin.academic-years.store') }}" class="space-y-5">
                @csrf

                @include('admin.academic-years.form', ['academicYear' => null])

                <div class="flex flex-wrap items-center gap-2 border-t border-slate-100 pt-5">
                    <x-ui.button>Simpan tahun ajaran</x-ui.button>
                    <x-ui.link-button :href="route('admin.academic-years.index')" variant="muted">Batal</x-ui.link-button>
                </div>
            </form>
        </x-ui.card>
    </x-ui.page>
</x-layouts.app>
