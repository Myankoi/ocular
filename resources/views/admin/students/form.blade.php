<div>
    <label class="block text-sm font-medium">NIS</label>
    <input
        name="nis"
        value="{{ old('nis', $student?->nis) }}"
        class="mt-1 w-full rounded-md border px-3 py-2"
    >
    @error('nis') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
</div>

<div>
    <label class="block text-sm font-medium">NISN</label>
    <input
        name="nisn"
        value="{{ old('nisn', $student?->nisn) }}"
        class="mt-1 w-full rounded-md border px-3 py-2"
    >
    @error('nisn') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
</div>

<div>
    <label class="block text-sm font-medium">Nama Siswa</label>
    <input
        name="name"
        value="{{ old('name', $student?->name) }}"
        class="mt-1 w-full rounded-md border px-3 py-2"
    >
    @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
</div>

<div>
    <label class="block text-sm font-medium">Kelas</label>
    <select name="class_id" class="mt-1 w-full rounded-md border px-3 py-2">
        <option value="">Pilih kelas</option>
        @foreach ($classes as $class)
            <option value="{{ $class->id }}" @selected(old('class_id', $student?->class_id) == $class->id)>
                {{ $class->name }} - {{ $class->academicYear->name }} {{ $class->academicYear->semester === 1 ? 'Ganjil' : 'Genap' }}
            </option>
        @endforeach
    </select>
    @error('class_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
</div>

<div>
    <label class="block text-sm font-medium">Foto URL/Path</label>
    <input
        name="photo"
        value="{{ old('photo', $student?->photo) }}"
        class="mt-1 w-full rounded-md border px-3 py-2"
    >
    @error('photo') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
</div>

<label class="flex items-center gap-2 text-sm">
    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $student?->is_active ?? true))>
    Siswa aktif
</label>
