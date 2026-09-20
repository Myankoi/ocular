@error('time')
    <div class="border border-rose-200 bg-rose-50 p-3 text-sm text-rose-700">{{ $message }}</div>
@enderror

@php
    $defaultAcademicYear = old('academic_year_id', $schedule?->academic_year_id);
    $defaultTeacher = old('user_id', $schedule?->user_id);
    $defaultSubject = old('subject_id', $schedule?->subject_id);
    $defaultClass = old('class_id', $schedule?->class_id ?? request('class_id'));
    $defaultDay = old('day_of_week', $schedule?->day_of_week ?? request('day_of_week'));
    $defaultStart = old('start_time', $schedule?->start_time ? substr($schedule->start_time, 0, 5) : request('start_time'));
    $defaultEnd = old('end_time', $schedule?->end_time ? substr($schedule->end_time, 0, 5) : request('end_time'));
@endphp

<div>
    <label class="block text-xs font-bold uppercase tracking-wider text-ocular-teal">Tahun ajaran</label>
    <select name="academic_year_id" class="mt-2 min-h-11 w-full border border-slate-300 bg-white px-3 py-2 text-sm focus:border-ocular-teal focus:outline-none focus:ring-2 focus:ring-ocular-teal/20">
        <option value="">Pilih tahun ajaran</option>
        @foreach ($academicYears as $academicYear)
            <option value="{{ $academicYear->id }}" @selected($defaultAcademicYear == $academicYear->id)>
                {{ $academicYear->name }} - {{ $academicYear->semester === 1 ? 'Ganjil' : 'Genap' }}
                {{ $academicYear->is_active ? '(Aktif)' : '' }}
            </option>
        @endforeach
    </select>
    @error('academic_year_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
</div>

<div>
    <label class="block text-xs font-bold uppercase tracking-wider text-ocular-teal">Guru</label>
    <select name="user_id" class="mt-2 min-h-11 w-full border border-slate-300 bg-white px-3 py-2 text-sm focus:border-ocular-teal focus:outline-none focus:ring-2 focus:ring-ocular-teal/20">
        <option value="">Pilih guru</option>
        @foreach ($teachers as $teacher)
            <option value="{{ $teacher->id }}" @selected($defaultTeacher == $teacher->id)>
                {{ $teacher->name }}
            </option>
        @endforeach
    </select>
    @error('user_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
</div>

<div>
    <label class="block text-xs font-bold uppercase tracking-wider text-ocular-teal">Mata pelajaran</label>
    <select name="subject_id" class="mt-2 min-h-11 w-full border border-slate-300 bg-white px-3 py-2 text-sm focus:border-ocular-teal focus:outline-none focus:ring-2 focus:ring-ocular-teal/20">
        <option value="">Pilih mata pelajaran</option>
        @foreach ($subjects as $subject)
            <option value="{{ $subject->id }}" @selected($defaultSubject == $subject->id)>
                {{ $subject->name }} {{ $subject->code ? '(' . $subject->code . ')' : '' }}
            </option>
        @endforeach
    </select>
    @error('subject_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
</div>

<div>
    <label class="block text-xs font-bold uppercase tracking-wider text-ocular-teal">Kelas</label>
    <select name="class_id" class="mt-2 min-h-11 w-full border border-slate-300 bg-white px-3 py-2 text-sm focus:border-ocular-teal focus:outline-none focus:ring-2 focus:ring-ocular-teal/20">
        <option value="">Pilih kelas</option>
        @foreach ($classes as $class)
            <option value="{{ $class->id }}" @selected($defaultClass == $class->id)>
                {{ $class->name }} - {{ $class->academicYear->name }}
            </option>
        @endforeach
    </select>
    @error('class_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
</div>

<div>
    <label class="block text-xs font-bold uppercase tracking-wider text-ocular-teal">Hari</label>
    <select name="day_of_week" class="mt-2 min-h-11 w-full border border-slate-300 bg-white px-3 py-2 text-sm focus:border-ocular-teal focus:outline-none focus:ring-2 focus:ring-ocular-teal/20">
        <option value="">Pilih hari</option>
        @foreach ($days as $value => $label)
            <option value="{{ $value }}" @selected($defaultDay == $value)>
                {{ $label }}
            </option>
        @endforeach
    </select>
    @error('day_of_week') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
</div>

<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-ocular-teal">Jam mulai</label>
        <input
            type="time"
            name="start_time"
            value="{{ $defaultStart }}"
            class="mt-2 min-h-11 w-full border border-slate-300 px-3 py-2 text-sm focus:border-ocular-teal focus:outline-none focus:ring-2 focus:ring-ocular-teal/20"
        >
        @error('start_time') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-ocular-teal">Jam selesai</label>
        <input
            type="time"
            name="end_time"
            value="{{ $defaultEnd }}"
            class="mt-2 min-h-11 w-full border border-slate-300 px-3 py-2 text-sm focus:border-ocular-teal focus:outline-none focus:ring-2 focus:ring-ocular-teal/20"
        >
        @error('end_time') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
</div>
