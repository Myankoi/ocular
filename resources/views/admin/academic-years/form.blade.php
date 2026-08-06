<div>
    <label class="block text-sm font-medium">Nama Tahun Ajaran</label>
    <input name="name" value="{{ old('name', $academicYear?->name) }}" placeholder="2026/2027" class="mt-1 w-full rounded-md border px-3 py-2">
    @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
</div>

<div>
    <label class="block text-sm font-medium">Semester</label>
    <select name="semester" class="mt-1 w-full rounded-md border px-3 py-2">
        <option value="1" @selected(old('semester', $academicYear?->semester) == 1)>Ganjil</option>
        <option value="2" @selected(old('semester', $academicYear?->semester) == 2)>Genap</option>
    </select>
    @error('semester') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
</div>

<div>
    <label class="block text-sm font-medium">Tanggal Mulai</label>
    <input type="date" name="start_date" value="{{ old('start_date', $academicYear?->start_date?->format('Y-m-d')) }}" class="mt-1 w-full rounded-md border px-3 py-2">
    @error('start_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
</div>

<div>
    <label class="block text-sm font-medium">Tanggal Selesai</label>
    <input type="date" name="end_date" value="{{ old('end_date', $academicYear?->end_date?->format('Y-m-d')) }}" class="mt-1 w-full rounded-md border px-3 py-2">
    @error('end_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
</div>

<label class="flex items-center gap-2 text-sm">
    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $academicYear?->is_active))>
    Jadikan semester aktif
</label>
