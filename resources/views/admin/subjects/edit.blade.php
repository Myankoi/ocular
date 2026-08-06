<x-layouts.app title="Edit Mata Pelajaran - Ocular">
    <h1 class="mb-6 text-2xl font-semibold">Edit Mata Pelajaran</h1>

    <form method="POST" action="{{ route('admin.subjects.update', $subject) }}" class="max-w-xl space-y-4">
        @csrf
        @method('PUT')

        @include('admin.subjects.form', ['subject' => $subject])

        <button class="rounded-md bg-slate-900 px-4 py-2 text-white">Update</button>
        <a href="{{ route('admin.subjects.index') }}" class="ml-2 text-sm underline">Batal</a>
    </form>
</x-layouts.app>
