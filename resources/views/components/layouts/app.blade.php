<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Ocular' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">
    <div class="min-h-screen">
        <nav class="border-b border-slate-200 bg-white">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                <a href="{{ url('/') }}" class="text-lg font-semibold tracking-tight">Ocular RPL</a>

                @auth
                    <div class="flex items-center gap-4 text-sm">
                        <a
                            href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('guru.dashboard') }}"
                            class="font-medium text-slate-700 hover:text-slate-950"
                        >
                            Dashboard {{ strtoupper(auth()->user()->role) }}
                        </a>

                        <span class="hidden text-slate-500 sm:inline">{{ auth()->user()->name }}</span>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="rounded-md bg-slate-900 px-3 py-2 text-white hover:bg-slate-700">
                                Logout
                            </button>
                        </form>
                    </div>
                @endauth
            </div>
        </nav>

        <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            {{ $slot }}
        </main>
    </div>
</body>
</html>
