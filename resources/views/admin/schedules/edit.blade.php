<x-layouts.app title="Edit Jadwal - Ocular">
    <x-ui.page eyebrow="Data akademik" title="Edit jadwal" description="Perbarui blok jadwal tanpa mengubah riwayat absensi.">
        <x-slot:actions><x-ui.link-button :href="route('admin.schedules.index')" variant="muted">Kembali ke jadwal</x-ui.link-button></x-slot:actions>
        <x-ui.card class="max-w-2xl border border-ocular-teal/15">
            <form id="schedule-edit-form" method="POST" action="{{ route('admin.schedules.update', $schedule) }}" class="space-y-5">
                @csrf
                @method('PUT')
                @include('admin.schedules.form', ['schedule' => $schedule])
                <div class="flex flex-col-reverse gap-2 border-t border-ocular-teal/10 pt-4 sm:flex-row sm:items-center sm:justify-between">
                    <button type="submit" form="schedule-delete-form" class="text-xs font-bold text-rose-600 underline">Hapus jadwal</button>
                    <div class="flex flex-col-reverse gap-2 sm:flex-row">
                        <x-ui.link-button :href="route('admin.schedules.index')" variant="muted">Batal</x-ui.link-button>
                        <x-ui.button variant="teal">Simpan perubahan</x-ui.button>
                    </div>
                </div>
            </form>
        </x-ui.card>
        <form id="schedule-delete-form" method="POST" action="{{ route('admin.schedules.destroy', $schedule) }}" onsubmit="return confirm('Hapus jadwal ini?')">
            @csrf
            @method('DELETE')
        </form>
    </x-ui.page>
</x-layouts.app>
