<x-layouts.app title="Tambah Jadwal - Ocular">
    <x-ui.page eyebrow="Data akademik" title="Tambah jadwal">
        <x-slot:actions><x-ui.link-button :href="route('admin.schedules.index')" variant="muted">Kembali ke jadwal</x-ui.link-button></x-slot:actions>
        <x-ui.card class="max-w-2xl border border-ocular-teal/15">
            <form method="POST" action="{{ route('admin.schedules.store') }}" class="space-y-5">
                @csrf
                @include('admin.schedules.form', ['schedule' => null])
                <div class="flex flex-col-reverse gap-2 border-t border-ocular-teal/10 pt-4 sm:flex-row sm:justify-end">
                    <x-ui.link-button :href="route('admin.schedules.index')" variant="muted">Batal</x-ui.link-button>
                    <x-ui.button>Simpan jadwal</x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </x-ui.page>
</x-layouts.app>
