<x-layouts.app title="Edit Tahun Ajaran - Ocular">
    <h1 class="mb-6 text-2xl font-semibold">Edit Tahun Ajaran</h1>

    <form method="POST" action="{{ route('admin.academic-years.update', $academicYear) }}" class="max-w-xl space-y-4">
        @csrf
        @method('PUT')

        @include('admin.academic-years.form', ['academicYear' => $academicYear])

        <button class="rounded-md bg-slate-900 px-4 py-2 text-white">Update</button>
        <a href="{{ route('admin.academic-years.index') }}" class="ml-2 text-sm underline">Batal</a>
    </form>
</x-layouts.app>
