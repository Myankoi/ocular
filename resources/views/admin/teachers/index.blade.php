<x-layouts.app title="Guru - Ocular">
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold">Guru</h1>
            <a href="{{ route('admin.teachers.create') }}" class="rounded-md bg-slate-900 px-3 py-2 text-sm text-white">
                Tambah
            </a>
        </div>

        @if (session('success'))
            <div class="rounded-md bg-green-50 p-3 text-sm text-green-700">{{ session('success') }}</div>
        @endif

        @error('delete')
            <div class="rounded-md bg-red-50 p-3 text-sm text-red-700">{{ $message }}</div>
        @enderror

        <div class="overflow-hidden rounded-lg border border-slate-200 bg-white">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-100">
                    <tr>
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">NIP</th>
                        <th class="px-4 py-3">Mapel</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse ($teachers as $teacher)
                        <tr>
                            <td class="px-4 py-3">{{ $teacher->name }}</td>
                            <td class="px-4 py-3">{{ $teacher->email }}</td>
                            <td class="px-4 py-3">{{ $teacher->nip ?? '-' }}</td>
                            <td class="px-4 py-3">
                                {{ $teacher->subjects->pluck('name')->join(', ') ?: '-' }}
                            </td>
                            <td class="px-4 py-3">{{ $teacher->is_active ? 'Aktif' : 'Nonaktif' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex gap-2">
                                    <a href="{{ route('admin.teachers.edit', $teacher) }}" class="text-slate-900 underline">
                                        Edit
                                    </a>

                                    <form method="POST" action="{{ route('admin.teachers.destroy', $teacher) }}">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="text-red-600 underline" onclick="return confirm('Hapus guru ini?')">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-slate-500">
                                Belum ada data guru.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $teachers->links() }}
    </div>
</x-layouts.app>
