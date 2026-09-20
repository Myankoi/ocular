<x-layouts.app title="Sesi Absensi - Ocular">
    <a href="{{ route('guru.dashboard') }}" class="mb-4 inline-flex min-h-10 items-center gap-2 text-xs font-bold uppercase tracking-wider text-ocular-teal hover:text-ocular-teal-dark"><i data-lucide="arrow-left" class="size-4"></i> Dashboard</a>
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
                        <p class="mt-1 text-sm text-slate-500">Arahkan QR ke area fokus.</p>
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
                            <div id="qr-reader" class="min-h-[320px] overflow-hidden bg-slate-900"></div>
                            <div class="ocular-scanner-frame pointer-events-none absolute left-1/2 top-1/2 size-[min(70vw,18rem)] -translate-x-1/2 -translate-y-1/2 border-4 border-ocular-orange sm:size-64">
                                <span class="absolute -left-1 -top-1 size-6 border-l-4 border-t-4 border-white"></span>
                                <span class="absolute -right-1 -top-1 size-6 border-r-4 border-t-4 border-white"></span>
                                <span class="absolute -bottom-1 -left-1 size-6 border-b-4 border-l-4 border-white"></span>
                                <span class="absolute -bottom-1 -right-1 size-6 border-b-4 border-r-4 border-white"></span>
                            </div>
                            <div data-last-result class="ocular-last-result-card absolute bottom-2 left-2 right-2 z-20 hidden border border-white/20 bg-white/95 p-2.5 shadow-2xl backdrop-blur sm:bottom-4 sm:left-4 sm:right-auto sm:w-[min(32rem,calc(100%-2rem))] sm:p-4">
                                <div class="flex items-center gap-3 sm:gap-4">
                                    <button type="button" data-open-photo class="hidden shrink-0 overflow-hidden border-2 border-white bg-white shadow-sm" aria-label="Perbesar foto ID card">
                                        <img data-last-result-photo src="" alt="Foto ID card siswa" class="h-24 w-16 object-contain sm:h-32 sm:w-24">
                                    </button>
                                    <div class="min-w-0 flex-1">
                                        <h3 data-last-result-name class="truncate text-lg font-black leading-tight text-ocular-teal sm:text-2xl">—</h3>
                                        <p data-last-result-class class="mt-0.5 truncate text-xs text-ocular-copy/70 sm:mt-1 sm:text-base">—</p>
                                        <div class="mt-2 flex flex-wrap items-center gap-x-3 gap-y-1 font-mono text-[9px] text-ocular-copy/60 sm:mt-3 sm:gap-x-4 sm:text-[10px]">
                                            <span data-last-result-nisn>NISN —</span>
                                            <span data-last-result-time>--:--</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <x-ui.button
                                type="button"
                                variant="primary"
                                data-start-scanner
                                class="size-11 px-0 py-0 disabled:cursor-not-allowed disabled:opacity-50"
                                aria-label="Buka kamera"
                                title="Buka kamera"
                            >
                                <i data-lucide="camera" class="size-4"></i>
                                <span class="sr-only">Buka kamera</span>
                            </x-ui.button>

                            <x-ui.button
                                type="button"
                                variant="outline"
                                data-stop-scanner
                                disabled
                                class="size-11 px-0 py-0 disabled:cursor-not-allowed disabled:opacity-50"
                                aria-label="Tutup kamera"
                                title="Tutup kamera"
                            >
                                <i data-lucide="camera-off" class="size-4"></i>
                                <span class="sr-only">Tutup kamera</span>
                            </x-ui.button>

                            <x-ui.button
                                type="button"
                                variant="muted"
                                data-switch-camera
                                disabled
                                class="size-11 px-0 py-0 disabled:cursor-not-allowed disabled:opacity-50"
                                aria-label="Ganti ke kamera depan"
                                title="Ganti kamera"
                            >
                                <i data-lucide="switch-camera" class="size-4"></i>
                                <span data-switch-camera-label class="sr-only">Ganti kamera</span>
                            </x-ui.button>

                            <x-ui.button
                                type="button"
                                variant="muted"
                                data-fullscreen-scanner
                                aria-pressed="false"
                                aria-label="Masuk layar penuh"
                                class="size-11 px-0 py-0"
                                title="Layar penuh"
                            >
                                <i data-fullscreen-icon data-lucide="maximize-2" class="size-4"></i>
                                <span data-fullscreen-label class="sr-only">Layar penuh</span>
                            </x-ui.button>
                            <div class="ml-auto flex items-center gap-1 border border-slate-200 bg-white px-1 py-1">
                                <button type="button" data-zoom-down disabled class="grid size-9 place-items-center text-lg font-bold text-ocular-teal disabled:cursor-not-allowed disabled:opacity-30" aria-label="Kurangi zoom">−</button>
                                <span data-zoom-label class="min-w-12 text-center font-mono text-[10px] font-bold text-ocular-copy/60">Zoom —</span>
                                <button type="button" data-zoom-up disabled class="grid size-9 place-items-center text-lg font-bold text-ocular-teal disabled:cursor-not-allowed disabled:opacity-30" aria-label="Tambah zoom">+</button>
                            </div>
                        </div>
                    </div>

                    <dialog data-photo-dialog class="border-0 bg-transparent p-0 backdrop:bg-slate-950/80" style="position: fixed; inset: 0; width: fit-content; max-width: 92vw; height: fit-content; margin: auto">
                        <div class="relative inline-block border-4 border-white bg-white shadow-2xl">
                            <button type="button" data-close-photo class="absolute right-2 top-2 z-10 grid size-10 place-items-center bg-slate-950/70 text-2xl text-white" aria-label="Tutup foto">×</button>
                            <img data-dialog-photo src="" alt="Foto ID card siswa ukuran besar" class="block max-h-[86vh] max-w-[92vw] object-contain">
                        </div>
                    </dialog>

                    <div class="border border-slate-200 bg-ocular-surface px-4 py-3 text-sm text-slate-500">
                        <div class="flex flex-wrap items-center gap-x-5 gap-y-1">
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
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h2 class="text-xs font-black uppercase tracking-widest text-ocular-teal">Daftar siswa</h2>
                        <p class="mt-1 font-mono text-[10px] font-bold text-ocular-accent">{{ $studentTotal }} siswa · <span data-selected-attendance-count>0</span> dipilih</p>
                    </div>
                    @if ($session->date->gte(now()->subDays(3)->startOfDay()))
                        <form id="bulk-attendance-form" method="POST" action="{{ route('guru.sessions.attendances.bulk-update', $session) }}" class="flex flex-col gap-2 sm:flex-row sm:items-center" data-bulk-attendance-form>
                            @csrf
                            @method('PATCH')
                            <select name="status" class="min-h-10 border border-slate-300 bg-white px-3 text-xs font-semibold text-slate-700" aria-label="Status massal">
                                @foreach (['hadir' => 'Hadir', 'sakit' => 'Sakit', 'izin' => 'Izin', 'alpha' => 'Alpha'] as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            <button data-bulk-attendance-submit disabled class="min-h-10 border border-ocular-teal bg-ocular-teal px-4 text-[10px] font-black uppercase tracking-wider text-white disabled:cursor-not-allowed disabled:border-slate-200 disabled:bg-slate-100 disabled:text-slate-400">Terapkan ke pilihan</button>
                        </form>
                    @else
                        <span class="text-xs text-slate-400">Terkunci H+3</span>
                    @endif
                </div>
            </div>
            <div class="hidden overflow-x-auto md:block">
            <table class="w-full min-w-[820px] text-left text-sm">
                <thead class="bg-slate-100">
                    <tr>
                        <th class="w-12 px-4 py-3">
                            @if ($session->date->gte(now()->subDays(3)->startOfDay()))
                                <input type="checkbox" data-select-all-attendances class="size-4 border-slate-300 text-ocular-teal" aria-label="Pilih semua siswa di halaman ini">
                            @endif
                        </th>
                        <th class="px-4 py-3">Siswa</th>
                        <th class="px-4 py-3">NISN</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Scan</th>
                        <th class="px-4 py-3">Ubah status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach ($attendances as $attendance)
                        <tr data-attendance-row="{{ $attendance->id }}" data-attendance-student-id="{{ $attendance->student_id }}" data-attendance-status="{{ $attendance->status }}">
                            <td class="px-4 py-3">
                                @if ($session->date->gte(now()->subDays(3)->startOfDay()))
                                    <input form="bulk-attendance-form" type="checkbox" name="attendance_ids[]" value="{{ $attendance->id }}" data-attendance-checkbox class="size-4 border-slate-300 text-ocular-teal" aria-label="Pilih {{ $attendance->student->name }}">
                                @endif
                            </td>
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
                                    <form method="POST" action="{{ route('guru.sessions.attendances.update', [$session, $attendance]) }}" class="flex min-w-36 items-center gap-2" data-attendance-update-form>
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" data-attendance-status-select class="min-h-10 rounded-md border border-slate-300 bg-white px-2 text-xs" aria-label="Status {{ $attendance->student->name }}">
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
            <div class="divide-y divide-slate-100 md:hidden">
                @foreach ($attendances as $attendance)
                    <article class="space-y-3 p-4" data-attendance-row="{{ $attendance->id }}" data-attendance-student-id="{{ $attendance->student_id }}" data-attendance-status="{{ $attendance->status }}">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex min-w-0 items-start gap-3">
                                @if ($session->date->gte(now()->subDays(3)->startOfDay()))
                                    <input form="bulk-attendance-form" type="checkbox" name="attendance_ids[]" value="{{ $attendance->id }}" data-attendance-checkbox class="mt-1 size-4 shrink-0 border-slate-300 text-ocular-teal" aria-label="Pilih {{ $attendance->student->name }}">
                                @endif
                                <div class="min-w-0">
                                    <h3 class="truncate font-semibold text-ocular-teal">{{ $attendance->student->name }}</h3>
                                    <p class="mt-1 font-mono text-[10px] text-ocular-copy/60">NISN {{ $attendance->student->nisn }}</p>
                                </div>
                            </div>
                            <x-ui.status-badge :status="$attendance->status" data-attendance-status-badge />
                        </div>
                        <dl class="grid grid-cols-2 gap-3 border-t border-slate-100 pt-3 text-xs">
                            <div>
                                <dt class="text-ocular-copy/50">Scan</dt>
                                <dd class="mt-1 font-mono" data-attendance-scanned-at>{{ $attendance->scanned_at?->format('H:i') ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-ocular-copy/50">Status</dt>
                                <dd class="mt-1 font-semibold" data-attendance-status-text>{{ strtoupper($attendance->status) }}</dd>
                            </div>
                        </dl>
                        @if ($session->date->gte(now()->subDays(3)->startOfDay()))
                            <form method="POST" action="{{ route('guru.sessions.attendances.update', [$session, $attendance]) }}" class="grid gap-2 border-t border-slate-100 pt-3" data-attendance-update-form>
                                @csrf
                                @method('PATCH')
                                <select name="status" data-attendance-status-select class="min-h-10 border border-slate-300 bg-white px-3 text-xs" aria-label="Status {{ $attendance->student->name }}">
                                    @foreach (['hadir' => 'Hadir', 'sakit' => 'Sakit', 'izin' => 'Izin', 'alpha' => 'Alpha'] as $value => $label)
                                        <option value="{{ $value }}" @selected($attendance->status === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <button class="min-h-10 border border-slate-300 px-3 text-xs font-semibold text-slate-700 hover:bg-slate-50">Simpan status</button>
                            </form>
                        @endif
                    </article>
                @endforeach
            </div>
            <div class="px-4 pb-4 sm:px-5"><x-ui.pagination :paginator="$attendances" /></div>
        </div>

    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const root = document.querySelector('[data-qr-scanner]');
                const attendanceCheckboxes = [...document.querySelectorAll('[data-attendance-checkbox]')];
                const selectAllAttendances = document.querySelector('[data-select-all-attendances]');
                const selectedAttendanceCount = document.querySelector('[data-selected-attendance-count]');
                const bulkAttendanceSubmit = document.querySelector('[data-bulk-attendance-submit]');
                const bulkAttendanceForm = document.querySelector('[data-bulk-attendance-form]');
                const attendanceUpdateForms = [...document.querySelectorAll('[data-attendance-update-form]')];

                const syncBulkAttendance = () => {
                    const selectedCount = attendanceCheckboxes.filter((checkbox) => checkbox.checked).length;

                    if (selectedAttendanceCount) {
                        selectedAttendanceCount.textContent = String(selectedCount);
                    }

                    if (bulkAttendanceSubmit) {
                        bulkAttendanceSubmit.disabled = selectedCount === 0;
                    }

                    if (selectAllAttendances) {
                        selectAllAttendances.checked = selectedCount > 0 && selectedCount === attendanceCheckboxes.length;
                        selectAllAttendances.indeterminate = selectedCount > 0 && selectedCount < attendanceCheckboxes.length;
                    }
                };

                selectAllAttendances?.addEventListener('change', () => {
                    attendanceCheckboxes.forEach((checkbox) => {
                        checkbox.checked = selectAllAttendances.checked;
                    });
                    syncBulkAttendance();
                });

                attendanceCheckboxes.forEach((checkbox) => checkbox.addEventListener('change', syncBulkAttendance));
                syncBulkAttendance();

                if (!root) {
                    return;
                }

                const submitUrl = root.dataset.submitUrl;
                const csrfToken = root.dataset.csrfToken;
                const startButton = root.querySelector('[data-start-scanner]');
                const stopButton = root.querySelector('[data-stop-scanner]');
                const switchCameraButton = root.querySelector('[data-switch-camera]');
                const switchCameraLabel = root.querySelector('[data-switch-camera-label]');
                const fullscreenButton = root.querySelector('[data-fullscreen-scanner]');
                const fullscreenLabel = root.querySelector('[data-fullscreen-label]');
                const fullscreenIcon = root.querySelector('[data-fullscreen-icon]');
                const scannerStage = root.querySelector('[data-scanner-stage]');
                const messageBox = root.querySelector('[data-scan-message]');
                const lastScanned = root.querySelector('[data-last-scanned]');
                const scannerStatus = root.querySelector('[data-scanner-status]');
                const zoomDown = root.querySelector('[data-zoom-down]');
                const zoomUp = root.querySelector('[data-zoom-up]');
                const zoomLabel = root.querySelector('[data-zoom-label]');
                const lastResult = root.querySelector('[data-last-result]');
                const lastResultPhoto = root.querySelector('[data-last-result-photo]');
                const openPhoto = root.querySelector('[data-open-photo]');
                const photoDialog = root.querySelector('[data-photo-dialog]');
                const dialogPhoto = root.querySelector('[data-dialog-photo]');
                const closePhoto = root.querySelector('[data-close-photo]');
                const lastResultName = root.querySelector('[data-last-result-name]');
                const lastResultClass = root.querySelector('[data-last-result-class]');
                const lastResultNisn = root.querySelector('[data-last-result-nisn]');
                const lastResultTime = root.querySelector('[data-last-result-time]');
                const networkDot = root.querySelector('[data-network-dot]');
                const networkLabel = root.querySelector('[data-network-label]');
                const countHadir = document.querySelector('[data-count-hadir]');
                const countExcused = document.querySelector('[data-count-excused]');
                const countAlpha = document.querySelector('[data-count-alpha]');
                const cooldown = new Map();
                let audioContext = null;

                let scanner = null;
                let isScanning = false;
                let facingMode = 'environment';
                let zoomCapability = null;
                let zoomValue = null;

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

                const speechName = (studentName) => {
                    if (String(studentName || '').trim().toLowerCase() === 'abdu salam') {
                        return 'Kocin';
                    }

                    return studentName;
                };

                const announceAttendance = (studentName) => {
                    if (!studentName || !('speechSynthesis' in window) || typeof SpeechSynthesisUtterance === 'undefined') {
                        return;
                    }

                    window.speechSynthesis.cancel();
                    const announcement = new SpeechSynthesisUtterance(`${speechName(studentName)} hadir`);
                    announcement.lang = 'id-ID';
                    announcement.rate = 0.95;
                    announcement.pitch = 1;
                    announcement.volume = 1;
                    window.speechSynthesis.speak(announcement);
                };

                const setLastResult = (student, attendance) => {
                    if (!student || !attendance) return;

                    lastResult.classList.remove('hidden', 'ocular-last-result-card--animate');
                    void lastResult.offsetWidth;
                    lastResult.classList.add('ocular-last-result-card--animate');
                    lastResultName.textContent = student.name || 'Siswa';
                    lastResultClass.textContent = student.class ? `Kelas ${student.class}` : 'Kelas —';
                    lastResultNisn.textContent = `NISN ${student.nisn || '—'}`;
                    lastResultTime.textContent = attendance.scanned_at || '--:--';

                    if (student.photo_url) {
                        lastResultPhoto.src = student.photo_url;
                        dialogPhoto.src = student.photo_url;
                        openPhoto.classList.remove('hidden');
                    } else {
                        lastResultPhoto.removeAttribute('src');
                        dialogPhoto.removeAttribute('src');
                        openPhoto.classList.add('hidden');
                    }
                };

                const refreshZoom = () => {
                    if (!scanner || !isScanning) return;

                    try {
                        zoomCapability = scanner.getRunningTrackCameraCapabilities().zoomFeature();
                        if (!zoomCapability.isSupported()) {
                            zoomLabel.textContent = 'Zoom —';
                            zoomDown.disabled = true;
                            zoomUp.disabled = true;
                            return;
                        }

                        zoomValue = zoomCapability.value() ?? zoomCapability.min();
                        zoomLabel.textContent = `Zoom ${Number(zoomValue).toFixed(1)}×`;
                        zoomDown.disabled = zoomValue <= zoomCapability.min();
                        zoomUp.disabled = zoomValue >= zoomCapability.max();
                    } catch (error) {
                        zoomLabel.textContent = 'Zoom —';
                    }
                };

                const changeZoom = async (direction) => {
                    if (!zoomCapability?.isSupported()) return;
                    const next = Math.min(zoomCapability.max(), Math.max(zoomCapability.min(), Number(zoomValue) + direction * zoomCapability.step()));
                    await zoomCapability.apply(next);
                    zoomValue = next;
                    zoomLabel.textContent = `Zoom ${Number(next).toFixed(1)}×`;
                    zoomDown.disabled = next <= zoomCapability.min();
                    zoomUp.disabled = next >= zoomCapability.max();
                };

                const setNetworkState = () => {
                    networkDot.classList.toggle('bg-green-500', navigator.onLine);
                    networkDot.classList.toggle('bg-red-500', !navigator.onLine);
                    networkLabel.textContent = navigator.onLine ? 'Online' : 'Offline';
                };

                const updateFullscreenButton = () => {
                    if (!fullscreenButton) return;

                    const isFullscreen = document.fullscreenElement === scannerStage;
                    if (fullscreenLabel) fullscreenLabel.textContent = isFullscreen ? 'Keluar' : 'Penuh';
                    fullscreenButton.setAttribute('aria-label', isFullscreen ? 'Keluar layar penuh' : 'Masuk layar penuh');
                    fullscreenButton.setAttribute('aria-pressed', isFullscreen ? 'true' : 'false');
                    fullscreenIcon?.setAttribute('data-lucide', isFullscreen ? 'minimize-2' : 'maximize-2');
                    window.refreshLucideIcons?.();
                };

                const incrementCount = (element, amount) => {
                    if (!element) {
                        return;
                    }

                    const value = element.querySelector('.font-mono.text-2xl') || element;
                    const current = Number.parseInt(value.textContent, 10);
                    value.textContent = String(Math.max(0, (Number.isNaN(current) ? 0 : current) + amount));
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
                    const rows = [
                        ...document.querySelectorAll(`[data-attendance-row="${attendance.id}"]`),
                        ...document.querySelectorAll(`[data-attendance-student-id="${attendance.student_id}"]`),
                    ].filter((row, index, all) => all.indexOf(row) === index);

                    if (rows.length === 0) {
                        return false;
                    }

                    const oldStatus = rows[0].dataset.attendanceStatus;
                    const newStatus = attendance.status;

                    rows.forEach((row) => {
                        const badge = row.querySelector('[data-attendance-status-badge]');
                        const scannedAt = row.querySelector('[data-attendance-scanned-at]');
                        const statusText = row.querySelector('[data-attendance-status-text]');
                        const statusSelect = row.querySelector('[data-attendance-status-select]');

                        row.dataset.attendanceStatus = newStatus;
                        setBadgeStatus(badge, newStatus);
                        if (scannedAt) scannedAt.textContent = attendance.scanned_at || '-';
                        if (statusText) statusText.textContent = String(newStatus).toUpperCase();
                        if (statusSelect) statusSelect.value = newStatus;
                    });

                    updateCounts(oldStatus, newStatus);
                    return true;
                };

                const submitAttendanceForm = async (form, submitButton = null) => {
                    const button = submitButton || form.querySelector('button[type="submit"], button:not([type])');
                    const originalText = button?.textContent;

                    if (button) {
                        button.disabled = true;
                        button.textContent = 'Menyimpan...';
                    }

                    try {
                        const response = await fetch(form.action, {
                            method: form.method || 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            body: new FormData(form),
                        });

                        const payload = await response.json().catch(() => null);

                        if (!response.ok) {
                            setMessage(payload?.message || payload?.errors?.attendance?.[0] || 'Status gagal disimpan.', 'error');
                            return;
                        }

                        if (payload?.attendance) {
                            updateAttendanceRow(payload.attendance);
                        }

                        if (payload?.attendances) {
                            payload.attendances.forEach(updateAttendanceRow);
                            attendanceCheckboxes.forEach((checkbox) => {
                                checkbox.checked = false;
                            });
                            syncBulkAttendance();
                        }

                        setMessage(payload?.message || 'Status berhasil disimpan.', 'success');
                    } catch (error) {
                        setMessage('Koneksi bermasalah. Status belum tersimpan.', 'error');
                    } finally {
                        if (button) {
                            button.disabled = false;
                            button.textContent = originalText;
                        }
                    }
                };

                attendanceUpdateForms.forEach((form) => form.addEventListener('submit', (event) => {
                    event.preventDefault();
                    submitAttendanceForm(form);
                }));

                bulkAttendanceForm?.addEventListener('submit', (event) => {
                    event.preventDefault();
                    submitAttendanceForm(bulkAttendanceForm, bulkAttendanceSubmit);
                });

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
                            const rowUpdated = updateAttendanceRow(payload.attendance);
                            setLastResult(payload.student, payload.attendance);
                            if (!rowUpdated) {
                                setMessage(`${payload?.message || 'Scan berhasil.'} Siswa ini tidak ada di halaman daftar yang sedang tampil.`, 'success');
                            }
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

                const stopScanner = async (message = 'Kamera ditutup.') => {
                    if (!scanner || !isScanning) {
                        return;
                    }

                    await scanner.stop();
                    scanner.clear();

                    isScanning = false;
                    startButton.disabled = false;
                    stopButton.disabled = true;
                    if (switchCameraButton) switchCameraButton.disabled = true;
                    scannerStatus.textContent = 'mati';
                    zoomCapability = null;
                    zoomValue = null;
                    zoomLabel.textContent = 'Zoom —';
                    zoomDown.disabled = true;
                    zoomUp.disabled = true;
                    setMessage(message, 'success');
                };

                const startScanner = async () => {
                    if (!window.Html5Qrcode) {
                        setMessage('Library scanner belum ter-load. Pastikan Vite aktif: docker compose up -d vite atau npm run dev.', 'error');
                        return;
                    }

                    if (isScanning) {
                        return;
                    }

                    scanner = new window.Html5Qrcode('qr-reader', {
                        formatsToSupport: [window.Html5QrcodeSupportedFormats?.QR_CODE ?? 0],
                        useBarCodeDetectorIfSupported: true,
                    });
                    const AudioContextClass = window.AudioContext || window.webkitAudioContext;
                    if (AudioContextClass) {
                        audioContext = new AudioContextClass();
                        await audioContext.resume();
                    }

                    try {
                        await scanner.start(
                            { facingMode: { ideal: facingMode } },
                            {
                                fps: 15,
                                qrbox: (width, height) => {
                                    const size = Math.floor(Math.min(width, height) * 0.62);
                                    return { width: size, height: size };
                                },
                                videoConstraints: {
                                    facingMode: { ideal: facingMode },
                                    width: { ideal: 1920 },
                                    height: { ideal: 1080 },
                                },
                            },
                            handleScan,
                            () => {}
                        );

                        isScanning = true;
                        startButton.disabled = true;
                        stopButton.disabled = false;
                        if (switchCameraButton) {
                            switchCameraButton.disabled = false;
                            switchCameraButton.setAttribute('aria-label', facingMode === 'environment' ? 'Ganti ke kamera depan' : 'Ganti ke kamera belakang');
                            if (switchCameraLabel) switchCameraLabel.textContent = facingMode === 'environment' ? 'Depan' : 'Belakang';
                        }
                        scannerStatus.textContent = 'aktif';
                        refreshZoom();
                        setMessage('Kamera aktif. Arahkan ke QR Code siswa.', 'success');
                    } catch (error) {
                        setMessage('Kamera gagal dibuka. Cek izin kamera dan pastikan pakai HTTPS atau localhost.', 'error');
                    }
                };

                startButton.addEventListener('click', startScanner);
                stopButton.addEventListener('click', () => stopScanner());
                switchCameraButton?.addEventListener('click', async () => {
                    facingMode = facingMode === 'environment' ? 'user' : 'environment';
                    await stopScanner('Mengganti kamera...');
                    await startScanner();
                });

                zoomDown.addEventListener('click', () => changeZoom(-1));
                zoomUp.addEventListener('click', () => changeZoom(1));
                openPhoto.addEventListener('click', () => photoDialog?.showModal());
                closePhoto.addEventListener('click', () => photoDialog?.close());
                photoDialog.addEventListener('click', (event) => {
                    if (event.target === photoDialog) photoDialog.close();
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
