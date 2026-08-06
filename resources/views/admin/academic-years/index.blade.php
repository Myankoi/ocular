<x-layouts.app title="Tahun Ajaran - Ocular">
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold">Tahun Ajaran</h1>
            <a href="{{ route('admin.academic-years.create') }}" class="rounded-md bg-slate-900 px-3 py-2 text-sm text-white">
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
                        <th class="px-4 py-3">Semester</th>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach ($academicYears as $academicYear)
                        <tr>
                            <td class="px-4 py-3">{{ $academicYear->name }}</td>
                            <td class="px-4 py-3">{{ $academicYear->semester === 1 ? 'Ganjil' : 'Genap' }}</td>
                            <td class="px-4 py-3">
                                {{ $academicYear->start_date->format('d/m/Y') }} - {{ $academicYear->end_date->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-3">{{ $academicYear->is_active ? 'Aktif' : 'Nonaktif' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex gap-2">
                                    <a href="{{ route('admin.academic-years.edit', $academicYear) }}" class="text-slate-900 underline">
                                        Edit
                                    </a>

                                    <form method="POST" action="{{ route('admin.academic-years.destroy', $academicYear) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 underline" onclick="return confirm('Hapus tahun ajaran ini?')">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $academicYears->links() }}
    </div>
</x-layouts.app>
