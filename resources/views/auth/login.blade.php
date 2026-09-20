<x-layouts.app title="Login - Ocular">
    <div class="mx-auto flex min-h-[calc(100vh-4rem)] max-w-md flex-col justify-center py-8">
        <div class="mb-8 text-center">
            <img src="{{ asset('images/ocular-logo.png') }}" alt="Ocular" class="mx-auto h-16 w-auto object-contain">
            <p class="mt-1 text-[10px] font-semibold uppercase tracking-[0.35em] text-ocular-teal/70">Sistem Absensi Digital RPL</p>
            <p class="mt-2 text-[10px] uppercase tracking-widest text-slate-400">SMKN 24 Jakarta</p>
        </div>

        <div class="border-t-8 border-ocular-teal bg-white p-6 shadow-[var(--shadow-card)] sm:p-10">
            <h2 class="text-xl font-bold text-ocular-teal sm:text-2xl">Selamat datang</h2>

            @if ($errors->any())
                <div role="alert" class="mt-5 rounded-md bg-rose-50 p-3 text-sm text-rose-700">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('login.store') }}" class="mt-7 space-y-5">
                @csrf
                <label class="block text-xs font-bold uppercase tracking-wider text-ocular-teal">Email / NIP
                    <input id="email" name="email" type="text" value="{{ old('email') }}" required autofocus placeholder="Masukkan Email atau NIP..." class="mt-2 min-h-14 w-full border border-slate-300 bg-slate-50 px-4 text-sm outline-none transition placeholder:text-slate-400 focus:border-ocular-teal focus:ring-2 focus:ring-ocular-teal/20">
                </label>
                <label class="block text-xs font-bold uppercase tracking-wider text-ocular-teal">Password
                    <input id="password" name="password" type="password" required placeholder="Masukkan password..." class="mt-2 min-h-14 w-full border border-slate-300 bg-slate-50 px-4 text-sm outline-none transition placeholder:text-slate-400 focus:border-ocular-teal focus:ring-2 focus:ring-ocular-teal/20">
                </label>
                <label class="flex items-center gap-2 text-sm text-slate-500"><input type="checkbox" name="remember" class="size-4 border-slate-300 text-ocular-teal"> Ingat saya</label>
                <button class="min-h-14 w-full bg-ocular-orange px-4 text-sm font-bold uppercase tracking-[0.2em] text-white transition hover:bg-ocular-orange-dark">Login</button>
            </form>
        </div>
        <p class="mt-7 text-center text-[10px] uppercase tracking-widest text-slate-400">© Ocular | RPL SMKN 24 Jakarta</p>
    </div>
</x-layouts.app>
