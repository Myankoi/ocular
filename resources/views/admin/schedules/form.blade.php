@error('time')
    <div class="rounded-md bg-red-50 p-3 text-sm text-red-700">{{ $message }}</div>
@enderror

<div>
    <label class="block text-sm font-medium">Tahun Ajaran</label>
    <select name="academic_year_id" class="mt-1 w-full rounded-md border px-3 py-2">
        <option value="">Pilih tahun ajaran</option>
        @foreach ($academicYears as $academicYear)
            <option value="{{ $academicYear->id }}" @selected(old('academic_year_id', $schedule?->academic_year_id) == $academicYear->id)>
                {{ $academicYear->name }} - {{ $academicYear->semester === 1 ? 'Ganjil' : 'Genap' }}
                {{ $academicYear->is_active ? '(Aktif)' : '' }}
            </option>
        @endforeach
    </select>
    @error('academic_year_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
</div>

<div>
    <label class="block text-sm font-medium">Guru</label>
    <select name="user_id" class="mt-1 w-full rounded-md border px-3 py-2">
        <option value="">Pilih guru</option>
        @foreach ($teachers as $teacher)
            <option value="{{ $teacher->id }}" @selected(old('user_id', $schedule?->user_id) == $teacher->id)>
                {{ $teacher->name }}
            </option>
        @endforeach
    </select>
    @error('user_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
</div>

<div>
    <label class="block text-sm font-medium">Mata Pelajaran</label>
    <select name="subject_id" class="mt-1 w-full rounded-md border px-3 py-2">
        <option value="">Pilih mata pelajaran</option>
        @foreach ($subjects as $subject)
            <option value="{{ $subject->id }}" @selected(old('subject_id', $schedule?->subject_id) == $subject->id)>
                {{ $subject->name }} {{ $subject->code ? '(' . $subject->code . ')' : '' }}
            </option>
        @endforeach
    </select>
    @error('subject_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
</div>

<div>
    <label class="block text-sm font-medium">Kelas</label>
    <select name="class_id" class="mt-1 w-full rounded-md border px-3 py-2">
        <option value="">Pilih kelas</option>
        @foreach ($classes as $class)
            <option value="{{ $class->id }}" @selected(old('class_id', $schedule?->class_id) == $class->id)>
                {{ $class->name }} - {{ $class->academicYear->name }}
            </option>
        @endforeach
    </select>
    @error('class_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
</div>

<div>
    <label class="block text-sm font-medium">Hari</label>
    <select name="day_of_week" class="mt-1 w-full rounded-md border px-3 py-2">
        <option value="">Pilih hari</option>
        @foreach ($days as $value => $label)
            <option value="{{ $value }}" @selected(old('day_of_week', $schedule?->day_of_week) == $value)>
                {{ $label }}
            </option>
        @endforeach
    </select>
    @error('day_of_week') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
</div>

<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label class="block text-sm font-medium">Jam Mulai</label>
        <input
            type="time"
            name="start_time"
            value="{{ old('start_time', $schedule?->start_time ? substr($schedule->start_time, 0, 5) : null) }}"
            class="mt-1 w-full rounded-md border px-3 py-2"
        >
        @error('start_time') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium">Jam Selesai</label>
        <input
            type="time"
            name="end_time"
            value="{{ old('end_time', $schedule?->end_time ? substr($schedule->end_time, 0, 5) : null) }}"
            class="mt-1 w-full rounded-md border px-3 py-2"
        >
        @error('end_time') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
</div>
