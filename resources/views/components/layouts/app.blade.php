<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Ocular' }}</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="icon" type="image/png" href="{{ asset('favicon-32x32.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-ocular-surface font-sans text-ocular-copy antialiased">
    @auth
        @php
            $isAdmin = auth()->user()->role === 'admin';
            $navigation = $isAdmin
                ? [
                    ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'active' => 'admin.dashboard', 'icon' => 'home'],
                    ['label' => 'Rekap Absensi', 'route' => 'admin.attendances.index', 'active' => 'admin.attendances.*', 'icon' => 'clipboard-list'],
                    ['label' => 'Guru', 'route' => 'admin.teachers.index', 'active' => 'admin.teachers.*', 'icon' => 'graduation-cap'],
                    ['label' => 'Siswa', 'route' => 'admin.students.index', 'active' => 'admin.students.*', 'icon' => 'users-round'],
                    ['label' => 'Import Data', 'route' => 'admin.imports.create', 'active' => 'admin.imports.*', 'icon' => 'upload'],
                    ['label' => 'Kelas', 'route' => 'admin.classes.index', 'active' => 'admin.classes.*', 'icon' => 'school'],
                    ['label' => 'Mata Pelajaran', 'route' => 'admin.subjects.index', 'active' => 'admin.subjects.*', 'icon' => 'book-open'],
                    ['label' => 'Tahun Ajaran', 'route' => 'admin.academic-years.index', 'active' => 'admin.academic-years.*', 'icon' => 'calendar-days'],
                    ['label' => 'Jadwal', 'route' => 'admin.schedules.index', 'active' => 'admin.schedules.*', 'icon' => 'calendar-clock'],
                ]
                : [
                    ['label' => 'Dashboard', 'route' => 'guru.dashboard', 'active' => 'guru.dashboard', 'icon' => 'home'],
                    ['label' => 'Sesi Absensi', 'route' => 'guru.sessions.index', 'active' => ['guru.sessions.index', 'guru.sessions.show'], 'icon' => 'scan-line'],
                    ['label' => 'Jadwal Mengajar', 'route' => 'guru.schedules.index', 'active' => 'guru.schedules.*', 'icon' => 'calendar-clock'],
                    ['label' => 'Kelas Saya', 'route' => 'guru.classes.index', 'active' => 'guru.classes.*', 'icon' => 'school'],
                    ['label' => 'Rekap Absensi', 'route' => 'guru.attendances.index', 'active' => 'guru.attendances.*', 'icon' => 'clipboard-list'],
                ];
        @endphp

        <div class="min-h-screen lg:flex">
            <aside class="sticky top-0 hidden h-screen w-64 shrink-0 flex-col bg-ocular-teal text-white lg:flex">
                <div class="border-b border-white/10 px-6 pb-6 pt-8">
                    <div>
                        <a href="{{ $isAdmin ? route('admin.dashboard') : route('guru.dashboard') }}" class="inline-flex max-w-full items-center gap-1">
                            <img src="{{ asset('images/ocular-mark.png') }}" alt="" class="ocular-sidebar-logo h-10 w-10 shrink-0 object-contain">
                            <span class="ocular-sidebar-wordmark text-4xl font-black leading-none tracking-normal">CULAR</span>
                        </a>
                    </div>
                </div>
                <nav class="flex-1 overflow-y-auto py-6">
                    @foreach ($navigation as $item)
                        @php $isActive = is_array($item['active']) ? request()->routeIs(...$item['active']) : request()->routeIs($item['active']); @endphp
                        @if ($item['route'] && Route::has($item['route']))
                            <a href="{{ route($item['route']) }}" class="group relative flex items-center gap-3 px-7 py-3.5 transition {{ $isActive ? 'bg-white/10 text-white' : 'text-white/60 hover:bg-white/5 hover:text-white' }}">
                                @if ($isActive)
                                    <span class="absolute inset-y-0 left-0 w-1 bg-ocular-orange" aria-hidden="true"></span>
                                @endif
                                <span class="grid size-6 place-items-center {{ $isActive ? 'text-ocular-orange' : 'text-white/45 group-hover:text-ocular-orange' }}"><i data-lucide="{{ $item['icon'] }}" class="size-[18px]"></i></span>
                                <span class="text-xs font-bold uppercase tracking-wider">{{ $item['label'] }}</span>
                            </a>
                        @else
                            <span class="flex items-center gap-3 rounded-md px-3 py-2.5 text-sm text-white/40">
                                <span class="size-1.5 rounded-full bg-white/20"></span>
                                {{ $item['label'] }}
                            </span>
                        @endif
                    @endforeach
                </nav>
                <div class="border-t border-white/10 p-5">
                    <div class="flex items-center gap-3">
                        <span class="grid size-10 place-items-center bg-white/15 font-mono text-xs font-bold text-white">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-xs font-bold text-white">{{ auth()->user()->name }}</p>
                            <p class="mt-0.5 text-[9px] uppercase tracking-wider text-white/50">{{ $isAdmin ? 'Administrator' : 'Guru' }}</p>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button title="Keluar" class="grid size-9 place-items-center border border-red-400/30 bg-red-500/10 text-red-200 transition hover:border-red-300 hover:bg-red-500/20 hover:text-white"><i data-lucide="log-out" class="size-4"></i></button>
                        </form>
                    </div>
                </div>
            </aside>

            <div class="min-w-0 flex-1">
                <header class="border-b px-4 py-4 sm:px-6 lg:px-8 {{ $isAdmin ? 'border-ocular-teal/15 bg-ocular-teal/5 text-ocular-copy' : 'border-slate-200 bg-ocular-teal text-white lg:bg-white lg:text-ocular-copy' }}">
                    <div class="mx-auto flex max-w-7xl items-center justify-between">
                        <div class="flex items-center gap-3 lg:hidden">
                            <details class="relative" data-mobile-drawer>
                                <summary class="grid min-h-11 min-w-11 cursor-pointer list-none place-items-center border {{ $isAdmin ? 'border-ocular-teal/25 text-ocular-teal' : 'border-white/30 text-white lg:border-slate-200 lg:text-ocular-teal' }}" aria-label="Buka navigasi"><i data-lucide="menu" class="size-5"></i></summary>
                                <div class="fixed inset-0 z-40 bg-slate-950/50" aria-hidden="true" onclick="this.parentElement.removeAttribute('open')"></div>
                                <nav class="fixed inset-y-0 left-0 z-50 flex w-[min(86vw,20rem)] touch-pan-y flex-col bg-ocular-teal text-white shadow-2xl" data-mobile-drawer-panel>
                                    <div class="flex items-center justify-between border-b border-white/10 px-6 pb-5 pt-7">
                                        <div>
                                            <a href="{{ $isAdmin ? route('admin.dashboard') : route('guru.dashboard') }}" class="inline-flex max-w-full items-center gap-1">
                                                <img src="{{ asset('images/ocular-mark.png') }}" alt="" class="ocular-sidebar-logo h-10 w-10 shrink-0 object-contain">
                                                <span class="ocular-sidebar-wordmark text-4xl font-black leading-none tracking-normal">CULAR</span>
                                            </a>
                                        </div>
                                        <button type="button" class="grid size-10 place-items-center border border-white/15 text-white/70 hover:text-white" aria-label="Tutup navigasi" onclick="this.closest('details').removeAttribute('open')"><i data-lucide="x" class="size-5"></i></button>
                                    </div>
                                    <div class="flex-1 overflow-y-auto py-6">
                                        @foreach ($navigation as $item)
                                            @php $isMobileActive = is_array($item['active']) ? request()->routeIs(...$item['active']) : request()->routeIs($item['active']); @endphp
                                            @if ($item['route'] && Route::has($item['route']))
                                                <a href="{{ route($item['route']) }}" class="group relative flex items-center gap-3 px-6 py-4 text-sm font-bold uppercase tracking-wider transition {{ $isMobileActive ? 'bg-white/10 text-white' : 'text-white/65 hover:bg-white/5 hover:text-white' }}">
                                                    @if ($isMobileActive)
                                                        <span class="absolute inset-y-0 left-0 w-1 bg-ocular-orange" aria-hidden="true"></span>
                                                    @endif
                                                    <span class="grid size-6 place-items-center {{ $isMobileActive ? 'text-ocular-orange' : 'text-white/45' }}"><i data-lucide="{{ $item['icon'] }}" class="size-[18px]"></i></span>
                                                    {{ $item['label'] }}
                                                </a>
                                            @endif
                                        @endforeach
                                    </div>
                                    <div class="border-t border-white/10 p-5">
                                        <div class="flex items-center gap-3">
                                            <span class="grid size-10 place-items-center bg-white/15 font-mono text-xs font-bold">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</span>
                                            <div class="min-w-0 flex-1">
                                                <p class="truncate text-xs font-bold">{{ auth()->user()->name }}</p>
                                                <p class="mt-0.5 text-[9px] uppercase tracking-wider text-white/50">{{ $isAdmin ? 'Administrator' : 'Guru' }}</p>
                                            </div>
                                            <form method="POST" action="{{ route('logout') }}">
                                                @csrf
                                                <button title="Keluar" class="grid size-9 place-items-center border border-red-400/30 bg-red-500/10 text-red-200 hover:border-red-300 hover:bg-red-500/20 hover:text-white"><i data-lucide="log-out" class="size-4"></i></button>
                                            </form>
                                        </div>
                                    </div>
                                </nav>
                            </details>
                            @if ($isAdmin)
                                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center">
                                    <img src="{{ asset('images/ocular-logo.png') }}" alt="Ocular" class="h-7 w-auto">
                                </a>
                            @else
                                <div>
                                    <p class="text-[9px] font-mono font-bold uppercase tracking-[0.2em] text-white/70 lg:text-ocular-accent">SMKN 24 JAKARTA</p>
                                    <p class="text-base font-black leading-tight text-white lg:text-ocular-teal">Selamat datang, {{ auth()->user()->name }}</p>
                                </div>
                            @endif
                        </div>
                        <p class="hidden text-xs uppercase tracking-[0.2em] {{ $isAdmin ? 'text-ocular-teal/70' : 'text-slate-400' }} lg:block">{{ $title ?? 'Ocular' }}</p>
                        <span class="text-xs {{ $isAdmin ? 'text-ocular-teal/75' : 'text-white/80 lg:text-slate-500' }}">{{ now()->format('d M Y · H:i') }} WIB</span>
                    </div>
                </header>
                <main class="mx-auto max-w-7xl px-4 pb-8 pt-6 sm:px-6 lg:px-8">
                    {{ $slot }}
                </main>
                <x-ui.back-to-top />
            </div>
        </div>
    @else
        <main class="min-h-screen px-4 py-8 sm:px-6 lg:px-8">{{ $slot }}</main>
    @endauth
    @stack('scripts')
    @auth
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const drawer = document.querySelector('[data-mobile-drawer]');
                const panel = drawer?.querySelector('[data-mobile-drawer-panel]');

                if (!drawer || !panel) return;

                let startX = 0;
                let startY = 0;

                panel.addEventListener('touchstart', (event) => {
                    const touch = event.changedTouches[0];
                    startX = touch.clientX;
                    startY = touch.clientY;
                }, { passive: true });

                panel.addEventListener('touchend', (event) => {
                    const touch = event.changedTouches[0];
                    const deltaX = touch.clientX - startX;
                    const deltaY = Math.abs(touch.clientY - startY);

                    if (deltaX < -60 && deltaY < 80) {
                        drawer.removeAttribute('open');
                    }
                }, { passive: true });

                document.addEventListener('touchstart', (event) => {
                    if (drawer.open || event.touches[0].clientX > 24) return;
                    startX = event.touches[0].clientX;
                    startY = event.touches[0].clientY;
                }, { passive: true });

                document.addEventListener('touchend', (event) => {
                    if (drawer.open || !startX) return;

                    const touch = event.changedTouches[0];
                    const deltaX = touch.clientX - startX;
                    const deltaY = Math.abs(touch.clientY - startY);

                    if (deltaX > 60 && deltaY < 80) {
                        drawer.setAttribute('open', '');
                    }

                    startX = 0;
                    startY = 0;
                }, { passive: true });

                document.addEventListener('keydown', (event) => {
                    if (event.key === 'Escape' && drawer.open) {
                        drawer.removeAttribute('open');
                    }
                });
            });
        </script>
    @endauth
</body>
</html>
