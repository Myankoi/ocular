<x-layouts.app title="Edit Guru - Ocular">
    <x-ui.page eyebrow="Teachers" title="Edit guru" description="Perbarui profil guru, status akun, dan mata pelajaran yang diampu.">
        <x-ui.card class="max-w-2xl border border-ocular-teal/15">
            <form method="POST" action="{{ route('admin.teachers.update', $teacher) }}" class="space-y-5">
                @csrf
                @method('PUT')

                @include('admin.teachers.form', ['teacher' => $teacher])

                <div class="flex flex-wrap items-center gap-2 border-t border-slate-100 pt-5">
                    <x-ui.button>Update guru</x-ui.button>
                    <x-ui.link-button :href="route('admin.teachers.index')" variant="muted">Batal</x-ui.link-button>
                </div>
            </form>
        </x-ui.card>

        <x-ui.card class="max-w-2xl border border-ocular-orange/25">
            <h2 class="text-sm font-black uppercase tracking-widest text-ocular-teal">Reset password guru</h2>
            <p class="mt-1 text-sm text-ocular-copy/70">Gunakan jika guru lupa password. Minimal 8 karakter.</p>
            <form method="POST" action="{{ route('admin.teachers.reset-password', $teacher) }}" class="mt-5 grid gap-3 sm:grid-cols-2">
                @csrf
                @method('PATCH')
                <input type="password" name="password" required minlength="8" placeholder="Password baru" class="min-h-11 border border-slate-300 px-3 text-sm focus:border-ocular-teal focus:outline-none focus:ring-2 focus:ring-ocular-teal/20">
                <input type="password" name="password_confirmation" required minlength="8" placeholder="Ulangi password" class="min-h-11 border border-slate-300 px-3 text-sm focus:border-ocular-teal focus:outline-none focus:ring-2 focus:ring-ocular-teal/20">
                <x-ui.button variant="teal" class="sm:col-span-2">Reset password</x-ui.button>
            </form>
        </x-ui.card>
    </x-ui.page>
</x-layouts.app>
