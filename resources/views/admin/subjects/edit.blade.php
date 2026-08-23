<x-layouts.app title="Edit Mata Pelajaran - Ocular">
    <x-ui.page eyebrow="Subjects" title="Edit mata pelajaran" description="Perbarui nama atau kode mata pelajaran yang digunakan pada jadwal.">
        <x-ui.card class="max-w-2xl border border-ocular-teal/15">
            <form method="POST" action="{{ route('admin.subjects.update', $subject) }}" class="space-y-5">
                @csrf
                @method('PUT')

                @include('admin.subjects.form', ['subject' => $subject])

                <div class="flex flex-wrap items-center gap-2 border-t border-slate-100 pt-5">
                    <x-ui.button>Update mapel</x-ui.button>
                    <x-ui.link-button :href="route('admin.subjects.index')" variant="muted">Batal</x-ui.link-button>
                </div>
            </form>
        </x-ui.card>
    </x-ui.page>
</x-layouts.app>
