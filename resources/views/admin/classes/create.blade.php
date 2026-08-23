<x-layouts.app title="Tambah Kelas - Ocular">
    <x-ui.page eyebrow="Data master" title="Tambah kelas">
        <x-ui.card class="max-w-2xl border border-ocular-teal/15">
            <form method="POST" action="{{ route('admin.classes.store') }}" class="space-y-5">
                @csrf

                @include('admin.classes.form', ['class' => null])

                <div class="flex flex-wrap items-center gap-2 border-t border-slate-100 pt-5">
                    <x-ui.button>Simpan kelas</x-ui.button>
                    <x-ui.link-button :href="route('admin.classes.index')" variant="muted">Batal</x-ui.link-button>
                </div>
            </form>
        </x-ui.card>
    </x-ui.page>
</x-layouts.app>
