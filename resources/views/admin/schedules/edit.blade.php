<x-layouts.app title="Edit Jadwal - Ocular">
    <h1 class="mb-6 text-2xl font-semibold">Edit Jadwal</h1>

    <form method="POST" action="{{ route('admin.schedules.update', $schedule) }}" class="max-w-xl space-y-4">
        @csrf
        @method('PUT')

        @include('admin.schedules.form', ['schedule' => $schedule])

        <button class="rounded-md bg-slate-900 px-4 py-2 text-white">Update</button>
        <a href="{{ route('admin.schedules.index') }}" class="ml-2 text-sm underline">Batal</a>
    </form>
</x-layouts.app>
