<x-ui.form.input label="Nama guru" name="name" :value="$teacher?->name" />
<x-ui.form.input label="Email" name="email" type="email" :value="$teacher?->email" />
<x-ui.form.input label="NIP" name="nip" :value="$teacher?->nip" />
<x-ui.form.input :label="$teacher ? 'Password (kosongkan kalau tidak diganti)' : 'Password'" name="password" type="password" />
<x-ui.form.input label="Foto URL/path" name="photo" :value="$teacher?->photo" />

<div>
    <label class="block text-xs font-bold uppercase tracking-wider text-ocular-teal">Mapel yang diampu</label>
    <div class="mt-2 space-y-2 border border-slate-200 bg-slate-50 p-3">
        @foreach ($subjects as $subject)
            <label class="flex items-center gap-2 text-sm font-medium text-ocular-copy">
                <input
                    type="checkbox"
                    name="subject_ids[]"
                    value="{{ $subject->id }}"
                    @checked(collect(old('subject_ids', $teacher?->subjects->pluck('id')->all() ?? []))->contains($subject->id))
                    class="size-4 border-slate-300 text-ocular-teal"
                >
                {{ $subject->name }} {{ $subject->code ? '(' . $subject->code . ')' : '' }}
            </label>
        @endforeach
    </div>
    @error('subject_ids') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
    @error('subject_ids.*') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
</div>

<x-ui.form.checkbox label="Guru aktif" name="is_active" :checked="old('is_active', $teacher?->is_active ?? true)" />
