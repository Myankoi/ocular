<x-layouts.app title="Kelas - Ocular">
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold">Kelas</h1>
            <a href="{{ route('admin.classes.create') }}" class="rounded-md bg-slate-900 px-3 py-2 text-sm text-white">
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
                        <th class="px-4 py-3">Tingkat</th>
                        <th class="px-4 py-3">Tahun Ajaran</th>
                        <th class="px-4 py-3">Semester</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse ($classes as $class)
                        <tr>
                            <td class="px-4 py-3">{{ $class->name }}</td>
                            <td class="px-4 py-3">{{ $class->grade_level }}</td>
                            <td class="px-4 py-3">{{ $class->academicYear->name }}</td>
                            <td class="px-4 py-3">{{ $class->academicYear->semester === 1 ? 'Ganjil' : 'Genap' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex gap-2">
                                    <a href="{{ route('admin.classes.edit', $class) }}" class="text-slate-900 underline">
                                        Edit
                                    </a>

                                    <form method="POST" action="{{ route('admin.classes.destroy', $class) }}">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="text-red-600 underline" onclick="return confirm('Hapus kelas ini?')">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-slate-500">
                                Belum ada data kelas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $classes->links() }}
    </div>
</x-layouts.app>
