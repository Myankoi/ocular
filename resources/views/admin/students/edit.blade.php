@php
    $hasPhoto = $student->photo && \Illuminate\Support\Facades\Storage::disk('local')->exists($student->photo);
    $photoUrl = $hasPhoto ? route('admin.students.photo', $student) : null;
@endphp

<x-layouts.app title="Edit Siswa - Ocular">
    <style>
        [data-edit-student-layout] { display: grid; gap: 1.5rem; }
        @media (min-width: 900px) {
            [data-edit-student-layout] { grid-template-columns: minmax(0, 1fr) minmax(18rem, 20rem); }
        }
    </style>

    <x-ui.page eyebrow="Data master / Siswa" title="Edit siswa" description="Perbarui biodata dan referensi foto ID card siswa.">
        <x-slot:actions><x-ui.link-button :href="route('admin.students.index')" variant="muted">Kembali ke daftar</x-ui.link-button></x-slot:actions>

        <x-ui.flash :message="session('success')" />
        @if ($errors->any()) <x-ui.flash type="error" :message="$errors->first()" /> @endif

        <x-ui.card padding="p-0" class="overflow-hidden border border-ocular-teal/15">
            <div class="border-b border-slate-100 bg-gradient-to-r from-ocular-teal/[0.06] to-transparent px-5 py-5 sm:px-6">
                <div class="min-w-0"><p class="text-[10px] font-black uppercase tracking-[0.18em] text-ocular-accent">Profil siswa</p><h2 class="mt-1 truncate text-lg font-black text-ocular-teal">{{ $student->name }}</h2></div>
            </div>

            <div class="grid lg:grid-cols-[minmax(0,1fr)_20rem]" data-edit-student-layout>
                <section class="p-5 sm:p-6">
                    <form id="student-edit-form" method="POST" action="{{ route('admin.students.update', $student) }}" enctype="multipart/form-data" class="space-y-5">
                        @csrf
                        @method('PUT')
                        @include('admin.students.form', ['student' => $student])

                        <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-5">
                            <p class="text-xs text-ocular-copy/55">Perubahan langsung diterapkan ke data siswa.</p>
                            <div class="flex flex-wrap gap-2"><x-ui.link-button :href="route('admin.students.index')" variant="muted">Batal</x-ui.link-button><x-ui.button>Update siswa</x-ui.button></div>
                        </div>
                    </form>
                </section>

                <section class="border-t border-slate-100 bg-ocular-surface/45 p-5 sm:p-6 lg:border-l lg:border-t-0">
                    <div class="flex items-start justify-between gap-3">
                        <div><p class="text-[10px] font-black uppercase tracking-[0.18em] text-ocular-accent">Dokumen siswa</p><h3 class="mt-1 text-base font-black text-ocular-teal">Foto ID card</h3></div>
                        <span class="shrink-0 px-2.5 py-1 text-[10px] font-black uppercase tracking-wider {{ $hasPhoto ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">{{ $hasPhoto ? 'Tersedia' : 'Belum ada' }}</span>
                    </div>

                    <div class="mt-5">
                        @if ($hasPhoto)
                            <button type="button" data-open-photo class="group relative mx-auto block w-full overflow-hidden border border-ocular-teal/15 bg-slate-100 text-left shadow-sm" style="max-width: 12rem" aria-label="Preview foto ID card {{ $student->name }}">
                                <div class="absolute inset-x-0 top-0 z-10 flex items-center justify-between bg-gradient-to-b from-slate-950/55 to-transparent px-3 pb-8 pt-3 text-[10px] font-black uppercase tracking-wider text-white opacity-0 transition group-hover:opacity-100"><span>Preview</span></div>
                                <img data-photo-preview src="{{ $photoUrl }}" alt="Foto ID card {{ $student->name }}" class="aspect-[1.586/1] w-full object-contain transition duration-300 group-hover:scale-[1.03]" loading="lazy">
                            </button>
                        @elseif ($student->photo)
                            <div data-photo-empty class="grid aspect-[1.586/1] place-items-center border border-dashed border-amber-300 bg-amber-50 p-5 text-center"><div><div class="mx-auto grid size-11 place-items-center bg-amber-100 text-xl text-amber-700">!</div><p class="mt-3 text-xs font-bold text-amber-800">File tidak ditemukan</p><p class="mt-1 text-[10px] leading-relaxed text-amber-700">Path foto tersimpan, tetapi file fisiknya belum tersedia di storage aplikasi.</p></div></div><img data-photo-preview alt="Preview foto ID card baru" class="hidden aspect-[1.586/1] w-full border border-ocular-teal/15 bg-slate-100 object-contain">
                        @else
                            <div data-photo-empty class="grid aspect-[1.586/1] place-items-center border border-dashed border-slate-300 bg-slate-50 p-5 text-center"><div><div class="mx-auto grid size-11 place-items-center bg-slate-200 text-lg text-slate-500">ID</div><p class="mt-3 text-xs font-bold text-ocular-teal">Belum ada foto ID card</p><p class="mt-1 text-[10px] leading-relaxed text-ocular-copy/55">Pilih file baru di bawah untuk mengunggah foto.</p></div></div><img data-photo-preview alt="Preview foto ID card baru" class="hidden aspect-[1.586/1] w-full border border-ocular-teal/15 bg-slate-100 object-contain">
                        @endif
                    </div>

                    <div class="mt-5 border-t border-slate-200 pt-5">
                        <label for="photo-upload" class="block text-[10px] font-black uppercase tracking-[0.16em] text-ocular-teal">Upload foto baru</label>
                        <div class="mt-2 flex items-center gap-3 border border-slate-300 bg-white p-2 transition focus-within:border-ocular-teal focus-within:ring-2 focus-within:ring-ocular-teal/20">
                            <input id="photo-upload" type="file" name="photo_upload" form="student-edit-form" accept="image/jpeg,image/png,image/webp" data-photo-upload class="sr-only">
                            <label for="photo-upload" class="inline-flex min-h-10 shrink-0 cursor-pointer items-center gap-2 bg-ocular-teal px-3 text-[10px] font-black uppercase tracking-wider text-white transition hover:bg-ocular-teal-dark"><span class="text-base leading-none">↑</span> Pilih foto</label>
                            <span data-photo-upload-name class="min-w-0 truncate text-xs text-ocular-copy/55">Belum ada file dipilih</span>
                        </div>
                        <p class="mt-2 text-[10px] leading-relaxed text-ocular-copy/55">JPG, PNG, atau WEBP · maksimal 10 MB. Foto lama akan diganti setelah disimpan.</p>
                    </div>

                </section>
            </div>
        </x-ui.card>
    </x-ui.page>

    <dialog data-photo-dialog class="border-0 bg-transparent p-0 backdrop:bg-slate-950/80" style="position: fixed; inset: 0; width: min(88vw, 24rem); max-width: min(88vw, 24rem); height: fit-content; max-height: 90vh; margin: auto">
        <div class="overflow-hidden border border-slate-200 bg-white shadow-2xl"><div class="flex items-center justify-between gap-4 border-b border-slate-100 px-4 py-3"><div><p class="text-[10px] font-black uppercase tracking-[0.18em] text-ocular-accent">Preview ID card</p><p class="mt-1 text-sm font-black text-ocular-teal">{{ $student->name }}</p></div><button type="button" data-close-photo class="grid size-9 place-items-center border border-slate-200 bg-white text-xl leading-none text-ocular-copy transition hover:border-ocular-teal hover:text-ocular-teal" aria-label="Tutup preview">×</button></div><div class="bg-slate-950 p-3"><img data-photo-dialog-image src="{{ $photoUrl }}" alt="Foto ID card {{ $student->name }} ukuran penuh" class="mx-auto max-w-full object-contain" style="max-height: 48vh"></div></div>
    </dialog>
    <script>
        (() => {
            const dialog = document.querySelector('[data-photo-dialog]');
            const upload = document.querySelector('[data-photo-upload]');
            const preview = document.querySelector('[data-photo-preview]');
            const dialogImage = document.querySelector('[data-photo-dialog-image]');
            const uploadName = document.querySelector('[data-photo-upload-name]');
            const emptyStates = document.querySelectorAll('[data-photo-empty]');

            if (dialog) {
                document.querySelectorAll('[data-open-photo]').forEach((button) => button.addEventListener('click', () => dialog.showModal()));
                dialog.querySelector('[data-close-photo]')?.addEventListener('click', () => dialog.close());
                dialog.addEventListener('click', (event) => { if (event.target === dialog) dialog.close(); });
            }

            upload?.addEventListener('change', () => {
                const file = upload.files?.[0];
                if (!file) return;
                const url = URL.createObjectURL(file);
                if (preview) {
                    preview.src = url;
                    preview.classList.remove('hidden');
                }
                if (dialogImage) dialogImage.src = url;
                emptyStates.forEach((state) => state.classList.add('hidden'));
                if (uploadName) uploadName.textContent = `File baru: ${file.name}`;
            });
        })();
    </script>
</x-layouts.app>
