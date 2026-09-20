<x-ui.form.input label="Nama tahun ajaran" name="name" :value="$academicYear?->name" placeholder="2026/2027" />

<x-ui.form.select label="Semester" name="semester">
    <option value="1" @selected(old('semester', $academicYear?->semester) == 1)>Ganjil</option>
    <option value="2" @selected(old('semester', $academicYear?->semester) == 2)>Genap</option>
</x-ui.form.select>

<x-ui.form.input label="Tanggal mulai" name="start_date" type="date" :value="$academicYear?->start_date?->format('Y-m-d')" />
<x-ui.form.input label="Tanggal selesai" name="end_date" type="date" :value="$academicYear?->end_date?->format('Y-m-d')" />
<x-ui.form.checkbox label="Jadikan semester aktif" name="is_active" :checked="old('is_active', $academicYear?->is_active)" />
