<x-layouts.app title="Edit Siswa - Ocular">
    <h1 class="mb-6 text-2xl font-semibold">Edit Siswa</h1>

    <form method="POST" action="{{ route('admin.students.update', $student) }}" class="max-w-xl space-y-4">
        @csrf
        @method('PUT')

        @include('admin.students.form', ['student' => $student])

        <button class="rounded-md bg-slate-900 px-4 py-2 text-white">Update</button>
        <a href="{{ route('admin.students.index') }}" class="ml-2 text-sm underline">Batal</a>
    </form>
</x-layouts.app>
