<x-layouts.app title="Edit Guru - Ocular">
    <h1 class="mb-6 text-2xl font-semibold">Edit Guru</h1>

    <form method="POST" action="{{ route('admin.teachers.update', $teacher) }}" class="max-w-xl space-y-4">
        @csrf
        @method('PUT')

        @include('admin.teachers.form', ['teacher' => $teacher])

        <button class="rounded-md bg-slate-900 px-4 py-2 text-white">Update</button>
        <a href="{{ route('admin.teachers.index') }}" class="ml-2 text-sm underline">Batal</a>
    </form>

    <div class="mt-6 max-w-xl rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="font-semibold text-slate-900">Reset password guru</h2>
        <p class="mt-1 text-sm text-slate-500">Gunakan jika guru lupa password. Minimal 8 karakter.</p>
        <form method="POST" action="{{ route('admin.teachers.reset-password', $teacher) }}" class="mt-4 grid gap-3 sm:grid-cols-2">
            @csrf
            @method('PATCH')
            <input type="password" name="password" required minlength="8" placeholder="Password baru" class="min-h-11 rounded-md border border-slate-300 px-3 text-sm">
            <input type="password" name="password_confirmation" required minlength="8" placeholder="Ulangi password" class="min-h-11 rounded-md border border-slate-300 px-3 text-sm">
            <button class="min-h-11 rounded-md bg-ocular-teal px-4 py-2 text-sm font-semibold text-white sm:col-span-2">Reset password</button>
        </form>
    </div>
</x-layouts.app>
