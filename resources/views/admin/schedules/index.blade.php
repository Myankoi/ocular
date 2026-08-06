<x-layouts.app title="Jadwal - Ocular">
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold">Jadwal</h1>
            <a href="{{ route('admin.schedules.create') }}" class="rounded-md bg-slate-900 px-3 py-2 text-sm text-white">
                Tambah
            </a>
        </div>

        @if (session('success'))
            <div class="rounded-md bg-green-50 p-3 text-sm text-green-700">{{ session('success') }}</div>
        @endif

        @error('delete')
            <div class="rounded-md bg-red-50 p-3 text-sm text-red-700">{{ $message }}</div>
        @enderror

        <form method="GET" class="grid gap-3 rounded-lg border border-slate-200 bg-white p-4 md:grid-cols-4">
            <select name="academic_year_id" class="rounded-md border px-3 py-2 text-sm">
                <option value="">Semua tahun ajaran</option>
                @foreach ($academicYears as $academicYear)
                    <option value="{{ $academicYear->id }}" @selected($selectedAcademicYearId == $academicYear->id)>
                        {{ $academicYear->name }} - {{ $academicYear->semester === 1 ? 'Ganjil' : 'Genap' }}
                    </option>
                @endforeach
            </select>

            <select name="class_id" class="rounded-md border px-3 py-2 text-sm">
                <option value="">Semua kelas</option>
                @foreach ($classes as $class)
                    <option value="{{ $class->id }}" @selected($selectedClassId == $class->id)>
                        {{ $class->name }}
                    </option>
                @endforeach
            </select>

            <select name="user_id" class="rounded-md border px-3 py-2 text-sm">
                <option value="">Semua guru</option>
                @foreach ($teachers as $teacher)
                    <option value="{{ $teacher->id }}" @selected($selectedTeacherId == $teacher->id)>
                        {{ $teacher->name }}
                    </option>
                @endforeach
            </select>

            <div class="flex gap-2">
                <button class="rounded-md bg-slate-900 px-4 py-2 text-sm text-white">
                    Filter
                </button>

                <a href="{{ route('admin.schedules.index') }}" class="rounded-md border px-4 py-2 text-sm">
                    Reset
                </a>
            </div>
        </form>

        <div class="overflow-hidden rounded-lg border border-slate-200 bg-white">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-100">
                    <tr>
                        <th class="px-4 py-3">Hari</th>
                        <th class="px-4 py-3">Jam</th>
                        <th class="px-4 py-3">Kelas</th>
                        <th class="px-4 py-3">Mapel</th>
                        <th class="px-4 py-3">Guru</th>
                        <th class="px-4 py-3">Tahun Ajaran</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse ($schedules as $schedule)
                        <tr>
                            <td class="px-4 py-3">{{ $days[$schedule->day_of_week] }}</td>
                            <td class="px-4 py-3">{{ substr($schedule->start_time, 0, 5) }} - {{ substr($schedule->end_time, 0, 5) }}</td>
                            <td class="px-4 py-3">{{ $schedule->schoolClass->name }}</td>
                            <td class="px-4 py-3">{{ $schedule->subject->name }}</td>
                            <td class="px-4 py-3">{{ $schedule->teacher->name }}</td>
                            <td class="px-4 py-3">
                                {{ $schedule->academicYear->name }} - {{ $schedule->academicYear->semester === 1 ? 'Ganjil' : 'Genap' }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex gap-2">
                                    <a href="{{ route('admin.schedules.edit', $schedule) }}" class="text-slate-900 underline">
                                        Edit
                                    </a>

                                    <form method="POST" action="{{ route('admin.schedules.destroy', $schedule) }}">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="text-red-600 underline" onclick="return confirm('Hapus jadwal ini?')">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-slate-500">
                                Belum ada data jadwal.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $schedules->links() }}
    </div>
</x-layouts.app>
