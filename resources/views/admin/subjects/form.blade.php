<div>
    <label class="block text-sm font-medium">Nama Mata Pelajaran</label>
    <input
        name="name"
        value="{{ old('name', $subject?->name) }}"
        placeholder="Pemrograman Web"
        class="mt-1 w-full rounded-md border px-3 py-2"
    >
    @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
</div>

<div>
    <label class="block text-sm font-medium">Kode</label>
    <input
        name="code"
        value="{{ old('code', $subject?->code) }}"
        placeholder="PWEB"
        class="mt-1 w-full rounded-md border px-3 py-2"
    >
    @error('code') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
</div>
