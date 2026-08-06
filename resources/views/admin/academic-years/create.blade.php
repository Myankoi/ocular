<x-layouts.app title="Tambah Tahun Ajaran - Ocular">
    <h1 class="mb-6 text-2xl font-semibold">Tambah Tahun Ajaran</h1>

    <form method="POST" action="{{ route('admin.academic-years.store') }}" class="max-w-xl space-y-4">
        @csrf

        @include('admin.academic-years.form', ['academicYear' => null])

        <button class="rounded-md bg-slate-900 px-4 py-2 text-white">Simpan</button>
        <a href="{{ route('admin.academic-years.index') }}" class="ml-2 text-sm underline">Batal</a>
    </form>
</x-layouts.app>
