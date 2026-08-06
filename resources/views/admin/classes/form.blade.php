<div>
    <label class="block text-sm font-medium">Tahun Ajaran</label>
    <select name="academic_year_id" class="mt-1 w-full rounded-md border px-3 py-2">
        <option value="">Pilih tahun ajaran</option>
        @foreach ($academicYears as $academicYear)
            <option
                value="{{ $academicYear->id }}"
                @selected(old('academic_year_id', $class?->academic_year_id) == $academicYear->id)
            >
                {{ $academicYear->name }} - {{ $academicYear->semester === 1 ? 'Ganjil' : 'Genap' }}
                {{ $academicYear->is_active ? '(Aktif)' : '' }}
            </option>
        @endforeach
    </select>
    @error('academic_year_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
</div>

<div>
    <label class="block text-sm font-medium">Nama Kelas</label>
    <input
        name="name"
        value="{{ old('name', $class?->name) }}"
        placeholder="X RPL 1"
        class="mt-1 w-full rounded-md border px-3 py-2"
    >
    @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
</div>

<div>
    <label class="block text-sm font-medium">Tingkat</label>
    <select name="grade_level" class="mt-1 w-full rounded-md border px-3 py-2">
        <option value="">Pilih tingkat</option>
        <option value="10" @selected(old('grade_level', $class?->grade_level) == 10)>X</option>
        <option value="11" @selected(old('grade_level', $class?->grade_level) == 11)>XI</option>
        <option value="12" @selected(old('grade_level', $class?->grade_level) == 12)>XII</option>
    </select>
    @error('grade_level') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
</div>
