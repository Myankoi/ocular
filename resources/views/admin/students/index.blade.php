<x-layouts.app title="Siswa - Ocular">
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold">Siswa</h1>
            <a href="{{ route('admin.students.create') }}" class="rounded-md bg-slate-900 px-3 py-2 text-sm text-white">
                Tambah
            </a>
        </div>

        @if (session('success'))
            <div class="rounded-md bg-green-50 p-3 text-sm text-green-700">{{ session('success') }}</div>
        @endif

        @error('delete')
            <div class="rounded-md bg-red-50 p-3 text-sm text-red-700">{{ $message }}</div>
        @enderror

        <form method="GET" class="flex flex-col gap-3 rounded-lg border border-slate-200 bg-white p-4 sm:flex-row">
            <input
                name="search"
                value="{{ $search }}"
                placeholder="Cari nama, NIS, atau NISN"
                class="w-full rounded-md border px-3 py-2 text-sm"
            >

            <select name="class_id" class="rounded-md border px-3 py-2 text-sm">
                <option value="">Semua kelas</option>
                @foreach ($classes as $class)
                    <option value="{{ $class->id }}" @selected($selectedClassId == $class->id)>
                        {{ $class->name }} - {{ $class->academicYear->name }}
                    </option>
                @endforeach
            </select>

            <button class="rounded-md bg-slate-900 px-4 py-2 text-sm text-white">
                Filter
            </button>

            <a href="{{ route('admin.students.index') }}" class="rounded-md border px-4 py-2 text-center text-sm">
                Reset
            </a>
        </form>

        <div class="overflow-hidden rounded-lg border border-slate-200 bg-white">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-100">
                    <tr>
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">NIS</th>
                        <th class="px-4 py-3">NISN</th>
                        <th class="px-4 py-3">Kelas</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse ($students as $student)
                        <tr>
                            <td class="px-4 py-3">{{ $student->name }}</td>
                            <td class="px-4 py-3">{{ $student->nis }}</td>
                            <td class="px-4 py-3">{{ $student->nisn }}</td>
                            <td class="px-4 py-3">{{ $student->schoolClass->name }}</td>
                            <td class="px-4 py-3">{{ $student->is_active ? 'Aktif' : 'Nonaktif' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex gap-2">
                                    <a href="{{ route('admin.students.edit', $student) }}" class="text-slate-900 underline">
                                        Edit
                                    </a>

                                    <form method="POST" action="{{ route('admin.students.destroy', $student) }}">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="text-red-600 underline" onclick="return confirm('Hapus siswa ini?')">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-slate-500">
                                Belum ada data siswa.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $students->links() }}
    </div>
</x-layouts.app>
