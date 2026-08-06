<x-layouts.app title="Edit Guru - Ocular">
    <h1 class="mb-6 text-2xl font-semibold">Edit Guru</h1>

    <form method="POST" action="{{ route('admin.teachers.update', $teacher) }}" class="max-w-xl space-y-4">
        @csrf
        @method('PUT')

        @include('admin.teachers.form', ['teacher' => $teacher])

        <button class="rounded-md bg-slate-900 px-4 py-2 text-white">Update</button>
        <a href="{{ route('admin.teachers.index') }}" class="ml-2 text-sm underline">Batal</a>
    </form>
</x-layouts.app>
