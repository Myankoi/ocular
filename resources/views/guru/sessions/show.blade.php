<x-layouts.app title="Sesi Absensi - Ocular">
    <a href="{{ route('guru.dashboard') }}" class="mb-4 inline-flex min-h-10 items-center gap-2 text-xs font-bold uppercase tracking-wider text-ocular-teal hover:text-ocular-teal-dark"><span class="text-base">←</span> Dashboard</a>
    <div class="space-y-5 pb-2">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-[0.22em] text-ocular-teal">Sesi absensi</p>
                <h1 class="mt-1 text-2xl font-black tracking-tight text-ocular-teal sm:text-3xl">{{ $session->schedule->subject->name }}</h1>
                <p class="mt-2 font-mono text-xs text-ocular-copy/70">
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

                        <x-ui.button variant="teal" onclick="return confirm('Tutup sesi absensi ini?')">
                            Tutup Sesi
                        </x-ui.button>
                    </form>
                @else
                    <x-ui.status-badge status="closed" />
                @endif
            </div>
        </div>

        <x-ui.flash :message="session('success')" />

        @error('session')
            <x-ui.flash type="error" :message="$message" />
        @enderror

        @error('scan')
            <x-ui.flash type="error" :message="$message" />
        @enderror

        @error('attendance')
            <x-ui.flash type="error" :message="$message" />
        @enderror

        @if ($session->status === 'open')
            <x-ui.card
                class="border border-slate-200 bg-white"
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

                <div class="mt-4 space-y-4">
                    <div class="ocular-scanner-stage space-y-3" data-scanner-stage>
                        <div class="ocular-scanner-viewport relative h-[min(62vh,34rem)] overflow-hidden bg-slate-950 p-3">
                            <div id="qr-reader" class="min-h-[280px] overflow-hidden bg-slate-900"></div>
                            <div class="ocular-scanner-frame pointer-events-none absolute left-1/2 top-1/2 size-[min(70vw,18rem)] -translate-x-1/2 -translate-y-1/2 border-4 border-ocular-orange sm:size-64">
                                <span class="absolute -left-1 -top-1 size-6 border-l-4 border-t-4 border-white"></span>
                                <span class="absolute -right-1 -top-1 size-6 border-r-4 border-t-4 border-white"></span>
                                <span class="absolute -bottom-1 -left-1 size-6 border-b-4 border-l-4 border-white"></span>
                                <span class="absolute -bottom-1 -right-1 size-6 border-b-4 border-r-4 border-white"></span>
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <x-ui.button
                                type="button"
                                variant="primary"
                                data-start-scanner
                                class="disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                Buka Kamera
                            </x-ui.button>

                            <x-ui.button
                                type="button"
                                variant="outline"
                                data-stop-scanner
                                disabled
                                class="disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                Tutup Kamera
                            </x-ui.button>

                            <x-ui.button
                                type="button"
                                variant="muted"
                                data-fullscreen-scanner
                                aria-pressed="false"
                            >
                                Layar penuh
                            </x-ui.button>
                        </div>
                    </div>

                    <div class="border border-slate-200 bg-ocular-surface p-4 sm:p-5">
                        <h3 class="font-medium">Input Manual NISN</h3>
                        <form method="POST" action="{{ route('guru.sessions.scan', $session) }}" class="mt-3 flex flex-col gap-3 sm:flex-row" data-manual-scan-form>
                            @csrf

                            <input
                                name="nisn"
                                data-manual-nisn
                                placeholder="Ketik NISN"
                                autofocus
                                class="w-full rounded-md border px-3 py-2"
                            >

                            <x-ui.button variant="teal">
                                Tandai Hadir
                            </x-ui.button>
                        </form>

                        <div class="mt-4 text-sm text-slate-500">
                            <p>Scan terakhir: <span data-last-scanned>-</span></p>
                            <p>Status kamera: <span data-scanner-status>mati</span></p>
                        </div>
                    </div>
                </div>
            </x-ui.card>
        @endif

        <div class="grid gap-4 sm:grid-cols-4">
            <x-ui.stat-card label="Total" :value="$studentTotal" tone="teal" data-count-total />
            <x-ui.stat-card label="Hadir" :value="$studentPresent" tone="success" data-count-hadir />
            <x-ui.stat-card label="Izin / Sakit" :value="$studentExcused" tone="warning" data-count-excused />
            <x-ui.stat-card label="Alpha" :value="$studentAlpha" tone="danger" data-count-alpha />
        </div>

        <div class="overflow-hidden rounded-lg border border-slate-200 bg-white">
            <div class="border-b border-slate-200 px-4 py-4 sm:px-5">
                <div class="flex items-center justify-between gap-3">
                    <h2 class="text-xs font-black uppercase tracking-widest text-ocular-teal">Daftar siswa</h2>
                    <span class="font-mono text-[10px] font-bold text-ocular-accent">{{ $studentTotal }} siswa</span>
                </div>
            </div>
            <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-100">
                    <tr>
                        <th class="px-4 py-3">Siswa</th>
                        <th class="px-4 py-3">NISN</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Scan</th>
                        <th class="px-4 py-3">Ubah status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach ($attendances as $attendance)
                        <tr data-attendance-row="{{ $attendance->id }}" data-attendance-status="{{ $attendance->status }}">
                            <td class="px-4 py-3">{{ $attendance->student->name }}</td>
                            <td class="px-4 py-3">{{ $attendance->student->nisn }}</td>
                            <td class="px-4 py-3">
                                <x-ui.status-badge :status="$attendance->status" data-attendance-status-badge />
                            </td>
                            <td class="px-4 py-3" data-attendance-scanned-at>
                                {{ $attendance->scanned_at?->format('H:i') ?? '-' }}
                            </td>
                            <td class="px-4 py-3">
                                @if ($session->date->gte(now()->subDays(3)->startOfDay()))
                                    <form method="POST" action="{{ route('guru.sessions.attendances.update', [$session, $attendance]) }}" class="flex min-w-36 items-center gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" class="min-h-10 rounded-md border border-slate-300 bg-white px-2 text-xs" aria-label="Status {{ $attendance->student->name }}">
                                            @foreach (['hadir' => 'Hadir', 'sakit' => 'Sakit', 'izin' => 'Izin', 'alpha' => 'Alpha'] as $value => $label)
                                                <option value="{{ $value }}" @selected($attendance->status === $value)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        <button class="min-h-10 rounded-md border border-slate-300 px-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">Simpan</button>
                                    </form>
                                @else
                                    <span class="text-xs text-slate-400">Terkunci H+3</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
            <div class="px-4 pb-4 sm:px-5"><x-ui.pagination :paginator="$attendances" /></div>
        </div>

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
                const fullscreenButton = root.querySelector('[data-fullscreen-scanner]');
                const scannerStage = root.querySelector('[data-scanner-stage]');
                const messageBox = root.querySelector('[data-scan-message]');
                const lastScanned = root.querySelector('[data-last-scanned]');
                const scannerStatus = root.querySelector('[data-scanner-status]');
                const manualScanForm = root.querySelector('[data-manual-scan-form]');
                const manualNisn = root.querySelector('[data-manual-nisn]');
                const networkDot = root.querySelector('[data-network-dot]');
                const networkLabel = root.querySelector('[data-network-label]');
                const countHadir = document.querySelector('[data-count-hadir]');
                const countExcused = document.querySelector('[data-count-excused]');
                const countAlpha = document.querySelector('[data-count-alpha]');
                const cooldown = new Map();
                let audioContext = null;

                let scanner = null;
                let isScanning = false;

                const setMessage = (message, type = 'success') => {
                    messageBox.textContent = message;
                    messageBox.classList.remove('hidden', 'bg-green-50', 'text-green-700', 'bg-red-50', 'text-red-700');
                    messageBox.classList.add(type === 'success' ? 'bg-green-50' : 'bg-red-50');
                    messageBox.classList.add(type === 'success' ? 'text-green-700' : 'text-red-700');
                };

                const beep = (frequency = 880, duration = 100) => {
                    if (!audioContext) return;
                    const oscillator = audioContext.createOscillator();
                    const gain = audioContext.createGain();
                    oscillator.frequency.value = frequency;
                    gain.gain.value = 0.08;
                    oscillator.connect(gain).connect(audioContext.destination);
                    oscillator.start();
                    oscillator.stop(audioContext.currentTime + duration / 1000);
                };

                const announceAttendance = (studentName) => {
                    if (!studentName || !('speechSynthesis' in window) || typeof SpeechSynthesisUtterance === 'undefined') {
                        return;
                    }

                    window.speechSynthesis.cancel();
                    const announcement = new SpeechSynthesisUtterance(`${studentName} hadir`);
                    announcement.lang = 'id-ID';
                    announcement.rate = 0.95;
                    announcement.pitch = 1;
                    announcement.volume = 1;
                    window.speechSynthesis.speak(announcement);
                };

                const setNetworkState = () => {
                    networkDot.classList.toggle('bg-green-500', navigator.onLine);
                    networkDot.classList.toggle('bg-red-500', !navigator.onLine);
                    networkLabel.textContent = navigator.onLine ? 'Online' : 'Offline';
                };

                const updateFullscreenButton = () => {
                    if (!fullscreenButton) return;

                    const isFullscreen = document.fullscreenElement === scannerStage;
                    fullscreenButton.textContent = isFullscreen ? 'Keluar layar penuh' : 'Layar penuh';
                    fullscreenButton.setAttribute('aria-pressed', isFullscreen ? 'true' : 'false');
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
                    if (!badge) {
                        return;
                    }

                    badge.className = 'inline-flex px-2 py-1 text-[10px] font-black uppercase tracking-wider';

                    if (status === 'hadir') {
                        badge.classList.add('bg-emerald-50', 'text-emerald-700');
                    } else if (status === 'sakit') {
                        badge.classList.add('bg-amber-50', 'text-amber-700');
                    } else if (status === 'izin') {
                        badge.classList.add('bg-blue-50', 'text-blue-700');
                    } else {
                        badge.classList.add('bg-rose-50', 'text-rose-700');
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
                        const controller = new AbortController();
                        const timeout = setTimeout(() => controller.abort(), 5000);
                        const response = await fetch(submitUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                            },
                            body: JSON.stringify({ nisn }),
                            signal: controller.signal,
                        });
                        clearTimeout(timeout);

                        const payload = await response.json().catch(() => null);

                        if (!response.ok) {
                            beep(220, 180);
                            setMessage(payload?.errors?.scan?.[0] || payload?.message || 'Scan gagal diproses.', 'error');
                            return;
                        }

                        setMessage(payload?.message || 'Scan berhasil. Kamera tetap standby.', 'success');
                        beep();
                        announceAttendance(payload?.student?.name);

                        if (payload?.attendance) {
                            updateAttendanceRow(payload.attendance);
                        }
                    } catch (error) {
                        beep(220, 180);
                        setMessage(error.name === 'AbortError' ? 'Request timeout. Scan belum tersimpan.' : 'Koneksi bermasalah. Scan belum tersimpan.', 'error');
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

                manualScanForm?.addEventListener('submit', (event) => {
                    event.preventDefault();
                    handleScan(manualNisn?.value || '');
                    if (manualNisn) {
                        manualNisn.value = '';
                        manualNisn.focus();
                    }
                });

                startButton.addEventListener('click', async () => {
                    if (!window.Html5Qrcode) {
                        setMessage('Library scanner belum ter-load. Pastikan Vite aktif: docker compose up -d vite atau npm run dev.', 'error');
                        return;
                    }

                    if (isScanning) {
                        return;
                    }

                    scanner = new window.Html5Qrcode('qr-reader');
                    const AudioContextClass = window.AudioContext || window.webkitAudioContext;
                    if (AudioContextClass) {
                        audioContext = new AudioContextClass();
                        await audioContext.resume();
                    }

                    try {
                        await scanner.start(
                            { facingMode: 'environment' },
                            { fps: 10 },
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

                fullscreenButton?.addEventListener('click', async () => {
                    if (!document.fullscreenEnabled || !scannerStage) {
                        setMessage('Mode layar penuh tidak didukung browser ini.', 'error');
                        return;
                    }

                    try {
                        if (document.fullscreenElement === scannerStage) {
                            await document.exitFullscreen();
                        } else {
                            await scannerStage.requestFullscreen();
                        }
                    } catch (error) {
                        setMessage('Mode layar penuh tidak dapat dibuka.', 'error');
                    }
                });

                document.addEventListener('fullscreenchange', updateFullscreenButton);
                window.addEventListener('online', setNetworkState);
                window.addEventListener('offline', setNetworkState);
                setNetworkState();
            });
        </script>
    @endpush
</x-layouts.app>
