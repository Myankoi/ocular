<x-layouts.app title="Tambah Mata Pelajaran - Ocular">
    <h1 class="mb-6 text-2xl font-semibold">Tambah Mata Pelajaran</h1>

    <form method="POST" action="{{ route('admin.subjects.store') }}" class="max-w-xl space-y-4">
        @csrf

        @include('admin.subjects.form', ['subject' => null])

        <button class="rounded-md bg-slate-900 px-4 py-2 text-white">Simpan</button>
        <a href="{{ route('admin.subjects.index') }}" class="ml-2 text-sm underline">Batal</a>
    </form>
</x-layouts.app>
