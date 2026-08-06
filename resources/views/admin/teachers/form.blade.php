<div>
    <label class="block text-sm font-medium">Nama Guru</label>
    <input
        name="name"
        value="{{ old('name', $teacher?->name) }}"
        class="mt-1 w-full rounded-md border px-3 py-2"
    >
    @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
</div>

<div>
    <label class="block text-sm font-medium">Email</label>
    <input
        type="email"
        name="email"
        value="{{ old('email', $teacher?->email) }}"
        class="mt-1 w-full rounded-md border px-3 py-2"
    >
    @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
</div>

<div>
    <label class="block text-sm font-medium">NIP</label>
    <input
        name="nip"
        value="{{ old('nip', $teacher?->nip) }}"
        class="mt-1 w-full rounded-md border px-3 py-2"
    >
    @error('nip') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
</div>

<div>
    <label class="block text-sm font-medium">Password {{ $teacher ? '(kosongkan kalau tidak diganti)' : '' }}</label>
    <input
        type="password"
        name="password"
        class="mt-1 w-full rounded-md border px-3 py-2"
    >
    @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
</div>

<div>
    <label class="block text-sm font-medium">Foto URL/Path</label>
    <input
        name="photo"
        value="{{ old('photo', $teacher?->photo) }}"
        class="mt-1 w-full rounded-md border px-3 py-2"
    >
    @error('photo') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
</div>

<div>
    <label class="block text-sm font-medium">Mapel yang Diampu</label>
    <div class="mt-2 space-y-2 rounded-md border p-3">
        @foreach ($subjects as $subject)
            <label class="flex items-center gap-2 text-sm">
                <input
                    type="checkbox"
                    name="subject_ids[]"
                    value="{{ $subject->id }}"
                    @checked(collect(old('subject_ids', $teacher?->subjects->pluck('id')->all() ?? []))->contains($subject->id))
                >
                {{ $subject->name }} {{ $subject->code ? '(' . $subject->code . ')' : '' }}
            </label>
        @endforeach
    </div>
    @error('subject_ids') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    @error('subject_ids.*') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
</div>

<label class="flex items-center gap-2 text-sm">
    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $teacher?->is_active ?? true))>
    Guru aktif
</label>
