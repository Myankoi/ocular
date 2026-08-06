<x-layouts.app title="Edit Kelas - Ocular">
    <h1 class="mb-6 text-2xl font-semibold">Edit Kelas</h1>

    <form method="POST" action="{{ route('admin.classes.update', $class) }}" class="max-w-xl space-y-4">
        @csrf
        @method('PUT')

        @include('admin.classes.form', ['class' => $class])

        <button class="rounded-md bg-slate-900 px-4 py-2 text-white">Update</button>
        <a href="{{ route('admin.classes.index') }}" class="ml-2 text-sm underline">Batal</a>
    </form>
</x-layouts.app>
