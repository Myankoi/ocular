<x-layouts.app title="Edit Tahun Ajaran - Ocular">
    <x-ui.page eyebrow="Pengaturan akademik" title="Edit tahun ajaran">
        <x-ui.card class="max-w-2xl border border-ocular-teal/15">
            <form method="POST" action="{{ route('admin.academic-years.update', $academicYear) }}" class="space-y-5">
                @csrf
                @method('PUT')

                @include('admin.academic-years.form', ['academicYear' => $academicYear])

                <div class="flex flex-wrap items-center gap-2 border-t border-slate-100 pt-5">
                    <x-ui.button>Update tahun ajaran</x-ui.button>
                    <x-ui.link-button :href="route('admin.academic-years.index')" variant="muted">Batal</x-ui.link-button>
                </div>
            </form>
        </x-ui.card>
    </x-ui.page>
</x-layouts.app>
