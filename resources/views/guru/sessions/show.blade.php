<x-layouts.app title="Sesi Absensi - Ocular">
    <div class="space-y-4">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-wide text-slate-500">Sesi Absensi</p>
                <h1 class="text-2xl font-semibold">{{ $session->schedule->subject->name }}</h1>
                <p class="text-sm text-slate-500">
                    {{ $session->schedule->schoolClass->name }} -
                    {{ $session->date->format('d/m/Y') }} -
                    {{ substr($session->schedule->start_time, 0, 5) }} - {{ substr($session->schedule->end_time, 0, 5) }}
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

        @error('scan')
            <div class="rounded-md bg-red-50 p-3 text-sm text-red-700">{{ $message }}</div>
        @enderror

        @if ($session->status === 'open')
            <div
                class="rounded-lg border border-slate-200 bg-white p-4"
                data-qr-scanner
                data-submit-url="{{ route('guru.sessions.scan', $session) }}"
                data-csrf-token="{{ csrf_token() }}"
            >
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="font-semibold">Scan QR Siswa</h2>
                        <p class="mt-1 text-sm text-slate-500">
                            QR Code ID card berisi NISN siswa. Input manual tetap tersedia kalau kamera bermasalah.
                        </p>
                    </div>

                    <div class="flex items-center gap-2">
                        <span data-network-dot class="h-2.5 w-2.5 rounded-full bg-green-500"></span>
                        <span data-network-label class="text-sm text-slate-600">Online</span>
                    </div>
                </div>

                <div data-scan-message class="mt-3 hidden rounded-md p-3 text-sm"></div>

                <div class="mt-4 grid gap-4 lg:grid-cols-[360px_1fr]">
                    <div class="space-y-3">
                        <div id="qr-reader" class="overflow-hidden rounded-lg border border-slate-200 bg-slate-50"></div>

                        <div class="flex gap-2">
                            <button
                                type="button"
                                data-start-scanner
                                class="rounded-md bg-slate-900 px-4 py-2 text-sm text-white disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                Buka Kamera
                            </button>

                            <button
                                type="button"
                                data-stop-scanner
                                disabled
                                class="rounded-md border px-4 py-2 text-sm disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                Tutup Kamera
                            </button>
                        </div>
                    </div>

                    <div class="rounded-lg border border-slate-200 p-4">
                        <h3 class="font-medium">Input Manual NISN</h3>
                        <form method="POST" action="{{ route('guru.sessions.scan', $session) }}" class="mt-3 flex flex-col gap-3 sm:flex-row">
                            @csrf

                            <input
                                name="nisn"
                                placeholder="Ketik NISN"
                                autofocus
                                class="w-full rounded-md border px-3 py-2"
                            >

                            <button class="rounded-md bg-slate-900 px-4 py-2 text-white">
                                Tandai Hadir
                            </button>
                        </form>

                        <div class="mt-4 text-sm text-slate-500">
                            <p>Scan terakhir: <span data-last-scanned>-</span></p>
                            <p>Status kamera: <span data-scanner-status>mati</span></p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid gap-4 sm:grid-cols-4">
            <div class="rounded-lg border border-slate-200 bg-white p-4">
                <p class="text-sm text-slate-500">Total</p>
                <p class="mt-1 text-2xl font-semibold" data-count-total>{{ $attendances->count() }}</p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-4">
                <p class="text-sm text-slate-500">Hadir</p>
                <p class="mt-1 text-2xl font-semibold" data-count-hadir>{{ $attendances->where('status', 'hadir')->count() }}</p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-4">
                <p class="text-sm text-slate-500">Izin/Sakit</p>
                <p class="mt-1 text-2xl font-semibold" data-count-excused>
                    {{ $attendances->whereIn('status', ['izin', 'sakit'])->count() }}
                </p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-4">
                <p class="text-sm text-slate-500">Alpha</p>
                <p class="mt-1 text-2xl font-semibold" data-count-alpha>{{ $attendances->where('status', 'alpha')->count() }}</p>
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
                        <tr data-attendance-row="{{ $attendance->id }}" data-attendance-status="{{ $attendance->status }}">
                            <td class="px-4 py-3">{{ $attendance->student->name }}</td>
                            <td class="px-4 py-3">{{ $attendance->student->nisn }}</td>
                            <td class="px-4 py-3">
                                <span data-attendance-status-badge @class([
                                    'rounded px-2 py-1 text-xs font-medium',
                                    'bg-green-50 text-green-700' => $attendance->status === 'hadir',
                                    'bg-yellow-50 text-yellow-700' => in_array($attendance->status, ['izin', 'sakit'], true),
                                    'bg-red-50 text-red-700' => $attendance->status === 'alpha',
                                ])>
                                    {{ strtoupper($attendance->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3" data-attendance-scanned-at>
                                {{ $attendance->scanned_at?->format('H:i') ?? '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <a href="{{ route('guru.dashboard') }}" class="inline-flex text-sm underline">Kembali ke dashboard</a>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const root = document.querySelector('[data-qr-scanner]');

                if (!root) {
                    return;
                }

                const submitUrl = root.dataset.submitUrl;
                const csrfToken = root.dataset.csrfToken;
                const startButton = root.querySelector('[data-start-scanner]');
                const stopButton = root.querySelector('[data-stop-scanner]');
                const messageBox = root.querySelector('[data-scan-message]');
                const lastScanned = root.querySelector('[data-last-scanned]');
                const scannerStatus = root.querySelector('[data-scanner-status]');
                const networkDot = root.querySelector('[data-network-dot]');
                const networkLabel = root.querySelector('[data-network-label]');
                const countHadir = document.querySelector('[data-count-hadir]');
                const countExcused = document.querySelector('[data-count-excused]');
                const countAlpha = document.querySelector('[data-count-alpha]');
                const cooldown = new Map();

                let scanner = null;
                let isScanning = false;

                const setMessage = (message, type = 'success') => {
                    messageBox.textContent = message;
                    messageBox.classList.remove('hidden', 'bg-green-50', 'text-green-700', 'bg-red-50', 'text-red-700');
                    messageBox.classList.add(type === 'success' ? 'bg-green-50' : 'bg-red-50');
                    messageBox.classList.add(type === 'success' ? 'text-green-700' : 'text-red-700');
                };

                const setNetworkState = () => {
                    networkDot.classList.toggle('bg-green-500', navigator.onLine);
                    networkDot.classList.toggle('bg-red-500', !navigator.onLine);
                    networkLabel.textContent = navigator.onLine ? 'Online' : 'Offline';
                };

                const incrementCount = (element, amount) => {
                    if (!element) {
                        return;
                    }

                    element.textContent = String(Math.max(0, Number(element.textContent.trim()) + amount));
                };

                const updateCounts = (oldStatus, newStatus) => {
                    if (oldStatus === newStatus) {
                        return;
                    }

                    if (oldStatus === 'hadir') {
                        incrementCount(countHadir, -1);
                    }

                    if (oldStatus === 'alpha') {
                        incrementCount(countAlpha, -1);
                    }

                    if (['izin', 'sakit'].includes(oldStatus)) {
                        incrementCount(countExcused, -1);
                    }

                    if (newStatus === 'hadir') {
                        incrementCount(countHadir, 1);
                    }

                    if (newStatus === 'alpha') {
                        incrementCount(countAlpha, 1);
                    }

                    if (['izin', 'sakit'].includes(newStatus)) {
                        incrementCount(countExcused, 1);
                    }
                };

                const setBadgeStatus = (badge, status) => {
                    badge.className = 'rounded px-2 py-1 text-xs font-medium';

                    if (status === 'hadir') {
                        badge.classList.add('bg-green-50', 'text-green-700');
                    } else if (['izin', 'sakit'].includes(status)) {
                        badge.classList.add('bg-yellow-50', 'text-yellow-700');
                    } else {
                        badge.classList.add('bg-red-50', 'text-red-700');
                    }

                    badge.textContent = status.toUpperCase();
                };

                const updateAttendanceRow = (attendance) => {
                    const row = document.querySelector(`[data-attendance-row="${attendance.id}"]`);

                    if (!row) {
                        return;
                    }

                    const oldStatus = row.dataset.attendanceStatus;
                    const newStatus = attendance.status;
                    const badge = row.querySelector('[data-attendance-status-badge]');
                    const scannedAt = row.querySelector('[data-attendance-scanned-at]');

                    row.dataset.attendanceStatus = newStatus;
                    setBadgeStatus(badge, newStatus);
                    scannedAt.textContent = attendance.scanned_at || '-';
                    updateCounts(oldStatus, newStatus);
                };

                const submitScan = async (nisn) => {
                    try {
                        const response = await fetch(submitUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                            },
                            body: JSON.stringify({ nisn }),
                        });

                        const payload = await response.json().catch(() => null);

                        if (!response.ok) {
                            setMessage(payload?.errors?.scan?.[0] || payload?.message || 'Scan gagal diproses.', 'error');
                            return;
                        }

                        setMessage(payload?.message || 'Scan berhasil. Kamera tetap standby.', 'success');

                        if (payload?.attendance) {
                            updateAttendanceRow(payload.attendance);
                        }
                    } catch (error) {
                        setMessage('Koneksi bermasalah. Scan belum tersimpan.', 'error');
                    }
                };

                const handleScan = (decodedText) => {
                    const nisn = decodedText.trim();

                    if (!nisn) {
                        return;
                    }

                    const now = Date.now();
                    const lastScanTime = cooldown.get(nisn) || 0;

                    if (now - lastScanTime < 3000) {
                        return;
                    }

                    cooldown.set(nisn, now);
                    lastScanned.textContent = nisn;

                    if (!navigator.onLine) {
                        setMessage('Koneksi offline. Scan belum dikirim.', 'error');
                        return;
                    }

                    submitScan(nisn);
                };

                startButton.addEventListener('click', async () => {
                    if (!window.Html5Qrcode) {
                        setMessage('Library scanner belum ter-load. Jalankan npm run build atau npm run dev.', 'error');
                        return;
                    }

                    if (isScanning) {
                        return;
                    }

                    scanner = new window.Html5Qrcode('qr-reader');

                    try {
                        await scanner.start(
                            { facingMode: 'environment' },
                            { fps: 10, qrbox: { width: 240, height: 240 } },
                            handleScan,
                            () => {}
                        );

                        isScanning = true;
                        startButton.disabled = true;
                        stopButton.disabled = false;
                        scannerStatus.textContent = 'aktif';
                        setMessage('Kamera aktif. Arahkan ke QR Code siswa.', 'success');
                    } catch (error) {
                        setMessage('Kamera gagal dibuka. Cek izin kamera dan pastikan pakai HTTPS atau localhost.', 'error');
                    }
                });

                stopButton.addEventListener('click', async () => {
                    if (!scanner || !isScanning) {
                        return;
                    }

                    await scanner.stop();
                    scanner.clear();

                    isScanning = false;
                    startButton.disabled = false;
                    stopButton.disabled = true;
                    scannerStatus.textContent = 'mati';
                    setMessage('Kamera ditutup.', 'success');
                });

                window.addEventListener('online', setNetworkState);
                window.addEventListener('offline', setNetworkState);
                setNetworkState();
            });
        </script>
    @endpush
</x-layouts.app>
