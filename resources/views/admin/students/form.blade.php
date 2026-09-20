<x-ui.form.input label="NIS" name="nis" :value="$student?->nis" />
<x-ui.form.input label="NISN" name="nisn" :value="$student?->nisn" />
<x-ui.form.input label="Nama siswa" name="name" :value="$student?->name" />

<x-ui.form.select label="Kelas" name="class_id">
    <option value="">Pilih kelas</option>
    @foreach ($classes as $class)
        <option value="{{ $class->id }}" @selected(old('class_id', $student?->class_id) == $class->id)>
            {{ $class->name }} - {{ $class->academicYear->name }} {{ $class->academicYear->semester === 1 ? 'Ganjil' : 'Genap' }}
        </option>
    @endforeach
</x-ui.form.select>

<x-ui.form.checkbox label="Siswa aktif" name="is_active" :checked="old('is_active', $student?->is_active ?? true)" />
