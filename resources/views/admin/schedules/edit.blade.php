@php($hasAttendanceHistory = $schedule->attendanceSessions()->exists())

<x-layouts.app title="Edit Jadwal - Ocular">
    <x-ui.page eyebrow="Data akademik" title="{{ $hasAttendanceHistory ? 'Kelola jadwal berhistori' : 'Edit jadwal' }}" description="{{ $hasAttendanceHistory ? 'Jadwal ini dikunci agar histori absensi tetap utuh.' : 'Perbarui blok jadwal sebelum sesi absensi dibuat.' }}">
        <x-slot:actions><x-ui.link-button :href="route('admin.schedules.index')" variant="muted">Kembali ke jadwal</x-ui.link-button></x-slot:actions>
        <x-ui.card class="max-w-2xl border border-ocular-teal/15">
            @if ($hasAttendanceHistory)
                <div class="mb-5 border-l-2 border-ocular-orange bg-ocular-orange/5 px-4 py-3 text-sm text-ocular-copy">
                    Jadwal ini sudah memiliki sesi absensi. Detailnya tidak diedit langsung; arsipkan jadwal lama lalu buat jadwal pengganti.
                </div>
            @endif
            <form id="schedule-edit-form" method="POST" action="{{ route('admin.schedules.update', $schedule) }}" class="space-y-5">
                @csrf
                @method('PUT')
                <fieldset @disabled($hasAttendanceHistory) class="space-y-5">
                    @include('admin.schedules.form', ['schedule' => $schedule])
                </fieldset>
                <div class="flex flex-col-reverse gap-2 border-t border-ocular-teal/10 pt-4 sm:flex-row sm:items-center sm:justify-between">
                    @if ($hasAttendanceHistory)
                        <x-ui.button type="submit" form="schedule-delete-form" variant="danger">Arsipkan jadwal</x-ui.button>
                        <div class="flex flex-col-reverse gap-2 sm:flex-row">
                            <x-ui.link-button :href="route('admin.schedules.index')" variant="muted">Batal</x-ui.link-button>
                            <x-ui.link-button :href="route('admin.schedules.create', ['academic_year_id' => $schedule->academic_year_id, 'user_id' => $schedule->user_id, 'subject_id' => $schedule->subject_id, 'class_id' => $schedule->class_id, 'day_of_week' => $schedule->day_of_week, 'start_time' => substr($schedule->start_time, 0, 5), 'end_time' => substr($schedule->end_time, 0, 5)])">Buat jadwal pengganti</x-ui.link-button>
                        </div>
                    @else
                        <x-ui.button type="submit" form="schedule-delete-form" variant="danger">Hapus jadwal</x-ui.button>
                        <div class="flex flex-col-reverse gap-2 sm:flex-row">
                            <x-ui.link-button :href="route('admin.schedules.index')" variant="muted">Batal</x-ui.link-button>
                            <x-ui.button>Simpan perubahan</x-ui.button>
                        </div>
                    @endif
                </div>
            </form>
        </x-ui.card>
        <form id="schedule-delete-form" method="POST" action="{{ route('admin.schedules.destroy', $schedule) }}" onsubmit="return confirm(@js($hasAttendanceHistory ? 'Jadwal ini akan diarsipkan agar histori absensi tetap aman. Lanjutkan?' : 'Hapus jadwal ini?'))">
            @csrf
            @method('DELETE')
        </form>
    </x-ui.page>
</x-layouts.app>
