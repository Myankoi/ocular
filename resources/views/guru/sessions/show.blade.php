<x-layouts.app title="Sesi Absensi - Ocular">
    <div class="space-y-4">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-wide text-slate-500">Sesi Absensi</p>
                <h1 class="text-2xl font-semibold">{{ $session->schedule->subject->name }}</h1>
                <p class="text-sm text-slate-500">
                    {{ $session->schedule->schoolClass->name }} · {{ $session->date->format('d/m/Y') }}
                    · {{ substr($session->schedule->start_time, 0, 5) }} - {{ substr($session->schedule->end_time, 0, 5) }}
                </p>
            </div>

            <div>
                @if ($session->status === 'open')
                    <form method="POST" action="{{ route('guru.sessions.close', $session) }}">
                        @csrf
                        @method('PATCH')

                        <button class="rounded-md bg-slate-900 px-4 py-2 text-sm text-white" onclick="return confirm('Tutup sesi absensi ini?')">
                            Tutup Sesi
                        </button>
                    </form>
                @else
                    <span class="rounded-md bg-slate-100 px-3 py-2 text-sm text-slate-700">
                        Sesi Ditutup
                    </span>
                @endif
            </div>
        </div>

        @if (session('success'))
            <div class="rounded-md bg-green-50 p-3 text-sm text-green-700">{{ session('success') }}</div>
        @endif

        @error('session')
            <div class="rounded-md bg-red-50 p-3 text-sm text-red-700">{{ $message }}</div>
        @enderror

        @if ($session->status === 'open')
    <div class="rounded-lg border border-slate-200 bg-white p-4">
        <h2 class="font-semibold">Scan / Input NISN</h2>
        <p class="mt-1 text-sm text-slate-500">
            Input ini sementara bisa dipakai manual. Integrasi kamera QR akan mengisi NISN ke flow yang sama.
        </p>

        @error('scan')
            <div class="mt-3 rounded-md bg-red-50 p-3 text-sm text-red-700">{{ $message }}</div>
        @enderror

        <form method="POST" action="{{ route('guru.sessions.scan', $session) }}" class="mt-4 flex flex-col gap-3 sm:flex-row">
            @csrf

            <input
                name="nisn"
                placeholder="Scan atau ketik NISN"
                autofocus
                class="w-full rounded-md border px-3 py-2"
            >

            <button class="rounded-md bg-slate-900 px-4 py-2 text-white">
                Tandai Hadir
            </button>
        </form>
    </div>
@endif

        <div class="grid gap-4 sm:grid-cols-4">
            <div class="rounded-lg border border-slate-200 bg-white p-4">
                <p class="text-sm text-slate-500">Total</p>
                <p class="mt-1 text-2xl font-semibold">{{ $attendances->count() }}</p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-4">
                <p class="text-sm text-slate-500">Hadir</p>
                <p class="mt-1 text-2xl font-semibold">{{ $attendances->where('status', 'hadir')->count() }}</p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-4">
                <p class="text-sm text-slate-500">Izin/Sakit</p>
                <p class="mt-1 text-2xl font-semibold">
                    {{ $attendances->whereIn('status', ['izin', 'sakit'])->count() }}
                </p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-4">
                <p class="text-sm text-slate-500">Alpha</p>
                <p class="mt-1 text-2xl font-semibold">{{ $attendances->where('status', 'alpha')->count() }}</p>
            </div>
        </div>

        <div class="overflow-hidden rounded-lg border border-slate-200 bg-white">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-100">
                    <tr>
                        <th class="px-4 py-3">Siswa</th>
                        <th class="px-4 py-3">NISN</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Scan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach ($attendances as $attendance)
                        <tr>
                            <td class="px-4 py-3">{{ $attendance->student->name }}</td>
                            <td class="px-4 py-3">{{ $attendance->student->nisn }}</td>
                            <td class="px-4 py-3">
                                <span @class([
                                    'rounded px-2 py-1 text-xs font-medium',
                                    'bg-green-50 text-green-700' => $attendance->status === 'hadir',
                                    'bg-yellow-50 text-yellow-700' => in_array($attendance->status, ['izin', 'sakit'], true),
                                    'bg-red-50 text-red-700' => $attendance->status === 'alpha',
                                ])>
                                    {{ strtoupper($attendance->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                {{ $attendance->scanned_at?->format('H:i') ?? '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <a href="{{ route('guru.dashboard') }}" class="inline-flex text-sm underline">Kembali ke dashboard</a>
    </div>
</x-layouts.app>
