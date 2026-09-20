<x-ui.form.select label="Tahun ajaran" name="academic_year_id">
    <option value="">Pilih tahun ajaran</option>
    @foreach ($academicYears as $academicYear)
        <option value="{{ $academicYear->id }}" @selected(old('academic_year_id', $class?->academic_year_id) == $academicYear->id)>
            {{ $academicYear->name }} - {{ $academicYear->semester === 1 ? 'Ganjil' : 'Genap' }}
            {{ $academicYear->is_active ? '(Aktif)' : '' }}
        </option>
    @endforeach
</x-ui.form.select>

<x-ui.form.input label="Nama kelas" name="name" :value="$class?->name" placeholder="X RPL 1" />

<x-ui.form.select label="Tingkat" name="grade_level">
    <option value="">Pilih tingkat</option>
    <option value="10" @selected(old('grade_level', $class?->grade_level) == 10)>X</option>
    <option value="11" @selected(old('grade_level', $class?->grade_level) == 11)>XI</option>
    <option value="12" @selected(old('grade_level', $class?->grade_level) == 12)>XII</option>
</x-ui.form.select>
