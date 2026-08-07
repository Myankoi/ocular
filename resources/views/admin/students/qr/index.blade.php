<x-layouts.app title="QR Code Siswa - Ocular">
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold">QR Code Siswa</h1>
                <p class="text-sm text-slate-500">QR Code berisi NISN siswa tanpa data tambahan.</p>
            </div>
        </div>

        @error('download')
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

            <a href="{{ route('admin.students.qr-codes.index') }}" class="rounded-md border px-4 py-2 text-center text-sm">
                Reset
            </a>
        </form>

        @if ($selectedClassId)
            <a
                href="{{ route('admin.classes.qr-codes.download', $selectedClassId) }}"
                class="inline-flex rounded-md bg-slate-900 px-4 py-2 text-sm text-white"
            >
                Download ZIP Kelas
            </a>
        @endif

        <div class="overflow-hidden rounded-lg border border-slate-200 bg-white">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-100">
                    <tr>
                        <th class="px-4 py-3">Siswa</th>
                        <th class="px-4 py-3">NISN</th>
                        <th class="px-4 py-3">Kelas</th>
                        <th class="px-4 py-3">QR</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse ($students as $student)
                        <tr>
                            <td class="px-4 py-3">
                                <div class="font-medium">{{ $student->name }}</div>
                                <div class="text-xs text-slate-500">NIS: {{ $student->nis }}</div>
                            </td>
                            <td class="px-4 py-3">{{ $student->nisn }}</td>
                            <td class="px-4 py-3">{{ $student->schoolClass->name }}</td>
                            <td class="px-4 py-3">
                                <img
                                    src="{{ route('admin.students.qr-codes.show', $student) }}"
                                    alt="QR {{ $student->nisn }}"
                                    class="h-20 w-20 rounded border bg-white p-1"
                                >
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.students.qr-codes.download', $student) }}" class="text-slate-900 underline">
                                    Download PNG
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-slate-500">
                                Tidak ada siswa.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $students->links() }}
    </div>
</x-layouts.app>
