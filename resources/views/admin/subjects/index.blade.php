<x-layouts.app title="Mata Pelajaran - Ocular">
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold">Mata Pelajaran</h1>
            <a href="{{ route('admin.subjects.create') }}" class="rounded-md bg-slate-900 px-3 py-2 text-sm text-white">
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
                        <th class="px-4 py-3">Kode</th>
                        <th class="px-4 py-3">Guru</th>
                        <th class="px-4 py-3">Jadwal</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse ($subjects as $subject)
                        <tr>
                            <td class="px-4 py-3">{{ $subject->name }}</td>
                            <td class="px-4 py-3">{{ $subject->code ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $subject->teachers_count }}</td>
                            <td class="px-4 py-3">{{ $subject->schedules_count }}</td>
                            <td class="px-4 py-3">
                                <div class="flex gap-2">
                                    <a href="{{ route('admin.subjects.edit', $subject) }}" class="text-slate-900 underline">
                                        Edit
                                    </a>

                                    <form method="POST" action="{{ route('admin.subjects.destroy', $subject) }}">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="text-red-600 underline" onclick="return confirm('Hapus mata pelajaran ini?')">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-slate-500">
                                Belum ada data mata pelajaran.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $subjects->links() }}
    </div>
</x-layouts.app>
