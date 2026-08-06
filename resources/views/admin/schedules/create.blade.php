<x-layouts.app title="Tambah Jadwal - Ocular">
    <h1 class="mb-6 text-2xl font-semibold">Tambah Jadwal</h1>

    <form method="POST" action="{{ route('admin.schedules.store') }}" class="max-w-xl space-y-4">
        @csrf

        @include('admin.schedules.form', ['schedule' => null])

        <button class="rounded-md bg-slate-900 px-4 py-2 text-white">Simpan</button>
        <a href="{{ route('admin.schedules.index') }}" class="ml-2 text-sm underline">Batal</a>
    </form>
</x-layouts.app>
