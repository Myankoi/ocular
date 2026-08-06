# 🔵 Team Plan — Ocular (Sistem Absensi Digital RPL)

Panduan kerja untuk **3 Developer** mengerjakan project ini bareng-bareng: siapa ngerjain apa, branch apa aja yang dibuat, kapan boleh merge, dan gimana caranya. Dokumen ini melengkapi `prd.md` — baca PRD dulu (terutama **Section 1.1 MVP Scope & Prioritas**) sebelum mulai kerja.

**Asumsi tim:** 3 developer dengan skill Laravel/Livewire setara (tidak ada "junior/senior" khusus), sisa waktu ~9 hari efektif per 6 Agustus 2026.

---

## 0. Prinsip Kerja Tim

1. **Merge kecil & sering**, jangan nunggu satu modul 100% selesai baru di-push. Branch yang hidup >2 hari tanpa merge = risiko conflict besar.
2. **Jangan pernah kerja langsung di `main` atau `develop`.** Semua kerjaan lewat feature branch + Pull Request (PR).
3. **Satu fitur = satu branch = satu PR.** Jangan gabung banyak fitur gak nyambung dalam satu PR — bikin review lambat dan susah di-rollback kalau ada bug.
4. **Yang bikin branch, yang tanggung jawab resolve conflict-nya** — bukan reviewer.
5. **Migration itu shared resource** — lihat Section 7 sebelum nyentuh skema database punya orang lain.
6. Kalau ragu fitur masuk Must-have atau Nice-to-have, cek Section 1.1 `prd.md`. Must-have duluan, titik.

---

## 1. Pembagian Peran (3 Developer)

Pembagian berdasarkan alur data di PRD: **Dev A** bikin data master duluan (dipakai semua orang), **Dev B** pegang jalur paling kritis (jadwal → sesi absensi → scanner), **Dev C** pegang auth/shell + hal yang *mengonsumsi* data absensi (dashboard, laporan). Supaya Dev C gak nganggur nunggu Dev B, di **Fase 0** kita seed data dummy attendance duluan (lihat Section 2) sehingga Dev C bisa develop UI report tanpa nunggu scanner beneran jadi.

| Dev | Area | Modul PRD (Section) |
|-----|------|----------------------|
| **Dev A** | Data Master & Master Jadwal | 3.2 Tahun Ajaran, Kelas, Mapel, Guru, Siswa, Jadwal (+ validasi bentrok), Kenaikan Kelas, 3.6 QR Generate |
| **Dev B** | Sistem Absensi (jalur paling kritis) | 3.3 Sesi Absensi, QR Scanner, Manual Override, Edit & Audit Policy |
| **Dev C** | Auth, Dashboard, Laporan & Admin Settings | 3.1 Autentikasi, 3.4 Dashboard, 3.5 Laporan & Export, Admin Settings |

> Kalau salah satu track kelar duluan, dia bantu track lain — bukan langsung ngerjain Nice-to-have. Cek Task Board (Section 3) buat urutan prioritas.

---

## 2. Setup Awal Bersama (Fase 0 — sebelum split)

Ini **wajib dikerjain bareng-bareng dulu** (idealnya <1 hari) sebelum split ke 3 jalur, supaya semua orang punya fondasi yang sama dan gak saling nunggu:

1. **Init project Laravel 13** + install Livewire 3, Tailwind v4, Alpine.js, Vite — 1 orang drive, 2 lainnya review.
2. **Buat SEMUA migration** sesuai ERD di `prd.md` Section 4 (termasuk tabel yang belum dipakai fase awal seperti `attendance_logs`) — dikerjain bareng dalam **satu branch `feature/setup-foundation`**, satu PR, di-merge duluan ke `develop`. Ini mencegah 3 orang bikin migration berantakan belakangan.
3. **Buat seeder/factory dummy data**: tahun ajaran aktif, 6 kelas, beberapa mapel, 8 guru, ~50 siswa contoh, beberapa jadwal, dan **beberapa attendance session + attendance record dummy** (biar Dev C bisa langsung develop dashboard/laporan tanpa nunggu scanner Dev B jadi).
4. **Setup Auth skeleton** (role `admin`/`guru`, middleware, redirect ke dashboard sesuai role) — dasar buat semua route terproteksi.
5. **Base layout** (navbar per role, Tailwind base, struktur folder Livewire component) supaya 3 dev gak bikin layout beda-beda.
6. Sepakatin **branch `develop`** sebagai basis kerja semua orang mulai sekarang (lihat Section 4).

Setelah Fase 0 merge ke `develop`, baru split ke 3 track paralel.

---

## 3. Task Board

Legend: 🔴 Must-have (wajib buat go-live) · 🟡 Nice-to-have (kalau waktu cukup, lihat PRD 1.1)

### Dev A — Data Master & Jadwal

| # | Fitur | Prioritas | Branch | Depends on |
|---|-------|-----------|--------|------------|
| A1 | CRUD Tahun Ajaran & Semester | 🔴 | `feature/a-tahun-ajaran` | Fase 0 |
| A2 | CRUD Kelas (terikat tahun ajaran) | 🔴 | `feature/a-kelas` | A1 |
| A3 | CRUD Mata Pelajaran | 🔴 | `feature/a-mapel` | Fase 0 |
| A4 | CRUD Guru + assign mapel (many-to-many) | 🔴 | `feature/a-guru` | A3 |
| A5 | CRUD Siswa (manual) | 🔴 | `feature/a-siswa` | A2 |
| A6 | Generate QR Code siswa (per siswa & batch ZIP) | 🔴 | `feature/a-qr-generate` | A5 |
| A7 | Import Siswa via CSV + laporan error per baris | 🟡 | `feature/a-siswa-import` | A5 |
| A8 | CRUD Jadwal manual | 🔴 | `feature/a-jadwal` | A2, A4 |
| A9 | **Validasi bentrok jadwal di level aplikasi** (kelas & guru, overlap jam) — lihat PRD 3.2 | 🔴 | `feature/a-jadwal-validasi` | A8 |
| A10 | Import Jadwal via CSV + laporan error | 🟡 | `feature/a-jadwal-import` | A9 |
| A11 | Kenaikan Kelas & Kelulusan (bulk promotion) | 🟡 | `feature/a-kenaikan-kelas` | A2, A5 |

### Dev B — Sistem Absensi (jalur kritis)

| # | Fitur | Prioritas | Branch | Depends on |
|---|-------|-----------|--------|------------|
| B1 | Buka sesi absensi dari jadwal (generate attendance ALPHA utk semua siswa kelas) | 🔴 | `feature/b-sesi-absensi` | A8 (bisa mulai dg data dummy Fase 0 sebelum A8 kelar) |
| B2 | Tutup sesi (manual & otomatis saat jam selesai) | 🔴 | `feature/b-tutup-sesi` | B1 |
| B3 | QR Scanner integration (`html5-qrcode`) + decode + lookup NISN | 🔴 | `feature/b-scanner` | B1 |
| B4 | Validasi scan: NISN tidak ketemu / siswa bukan bagian roster sesi ini / sudah discan | 🔴 | `feature/b-scanner-validasi` | B3 |
| B5 | Cooldown/debounce 3 detik, audio+visual feedback, unlock AudioContext | 🔴 | `feature/b-scanner-feedback` | B3 |
| B6 | Fallback input manual NISN | 🔴 | `feature/b-scanner-fallback` | B3 |
| B7 | Network failover indicator (online/offline dot, error timeout) | 🔴 | `feature/b-scanner-network` | B3 |
| B8 | Manual set status Sakit/Izin oleh guru via list siswa | 🔴 | `feature/b-manual-status` | B1 |
| B9 | Edit attendance policy: guru edit sampai H+3, lewat itu lock utk guru | 🔴 | `feature/b-edit-policy` | B1 |
| B10 | Tabel `attendance_logs` (histori status lama→baru) | 🟡 | `feature/b-attendance-logs` | B9 |

### Dev C — Auth, Dashboard, Laporan & Admin Settings

| # | Fitur | Prioritas | Branch | Depends on |
|---|-------|-----------|--------|------------|
| C1 | Login/Logout + role-based routing | 🔴 | `feature/c-auth` | Fase 0 |
| C2 | Admin reset password guru | 🔴 | `feature/c-reset-password-guru` | C1 |
| C3 | Fallback reset password admin (artisan command) | 🔴 | `feature/c-cli-reset-admin` | C1 |
| C4 | Dashboard Guru (jadwal hari ini + ringkasan kehadiran) | 🔴 | `feature/c-dashboard-guru` | A8, data dummy Fase 0 |
| C5 | Dashboard Admin ringkas (total siswa/guru/kelas, % kehadiran) | 🔴 | `feature/c-dashboard-admin` | Data dummy Fase 0 |
| C6 | Admin: edit/override attendance lintas guru/kelas (UI, sesuai PRD 2.3) | 🔴 | `feature/c-admin-edit-attendance` | B9 |
| C7 | Laporan & Export Excel (filter tahun ajaran/kelas/mapel/guru/tanggal) | 🔴 | `feature/c-export-excel` | Data dummy Fase 0 |
| C8 | Data privacy: batasi akses lihat/export foto & NISN sesuai role | 🔴 | `feature/c-data-privacy` | C1 |
| C9 | Dashboard chart detail (tren mingguan/bulanan, top 5 Alpha) | 🟡 | `feature/c-dashboard-chart` | C5 |
| C10 | Export PDF | 🟡 | `feature/c-export-pdf` | C7 |
| C11 | Admin: akses darurat buka sesi atas nama guru lain | 🟡 | `feature/c-admin-emergency-session` | B1 |

---

## 4. Strategi Branching

```
main
 └── develop
      ├── feature/setup-foundation      (Fase 0, sekali jalan bareng)
      ├── feature/a-tahun-ajaran
      ├── feature/a-kelas
      ├── feature/b-sesi-absensi
      ├── feature/b-scanner
      ├── feature/c-auth
      ├── feature/c-dashboard-admin
      ├── fix/<bug-singkat>             (bug ditemukan di develop)
      └── hotfix/<bug-singkat>          (bug kritis di main/production)
```

| Branch | Dibuat dari | Fungsi | Siapa yang push langsung? |
|--------|-------------|--------|----------------------------|
| `main` | — | Kode production-ready, yang di-deploy ke sekolah | **Tidak ada.** Hanya lewat merge PR dari `develop` di checkpoint integrasi |
| `develop` | `main` | Branch integrasi harian, tempat semua feature ketemu | **Tidak ada.** Hanya lewat merge PR dari `feature/*` |
| `feature/<area>-<deskripsi>` | `develop` (paling baru) | Satu branch = satu fitur/task dari Task Board | Yang ngerjain fitur itu |
| `fix/<deskripsi>` | `develop` | Perbaikan bug yang ketemu selama development (bukan urgent) | Siapapun yang nemuin/ditugasin |
| `hotfix/<deskripsi>` | `main` | Bug kritis di kode yang sudah kepakai/mendekati go-live | Siapapun, harus segera info tim |

**Konvensi nama branch:** huruf kecil, pisah pakai `-`, prefix dev opsional kalau mau makin jelas (`feature/a-jadwal`, bukan `feature/Jadwal_Andi`).

**Konvensi commit message** (Conventional Commits, biar histori gampang dibaca):
```
feat: tambah CRUD jadwal dengan validasi bentrok
fix: perbaiki cooldown scanner yang tidak reset
chore: update seeder data dummy siswa
docs: update PRD section data privasi
```

---

## 5. Alur Kerja per Fitur (step by step)

1. `git checkout develop && git pull origin develop` — **selalu mulai dari develop terbaru**, jangan dari branch lama.
2. `git checkout -b feature/b-scanner-validasi`
3. Kerjain fiturnya. Commit kecil & sering (jangan 1 commit raksasa di akhir).
4. Sebelum push final: `git pull origin develop --rebase` (atau merge) buat narik update terbaru dan resolve conflict di branch sendiri, **bukan di develop**.
5. Test manual sesuai checklist fitur (Section 11).
6. `git push origin feature/b-scanner-validasi` → buka **Pull Request ke `develop`**.
7. Isi PR: deskripsi singkat, screenshot/video kalau ada UI, checklist testing yang sudah dilakukan.
8. **Minta review ke 1 dev lain** (idealnya dev dari track lain yang datanya related — misal PR jadwal direview Dev B karena dia yang konsumsi jadwal itu).
9. Reviewer approve → merge (rekomendasi **squash and merge** biar histori `develop` bersih) → hapus branch.
10. Kalau ada perubahan diminta reviewer, push commit baru ke branch yang sama (PR auto-update), gak perlu bikin PR baru.

---

## 6. Aturan Merge — Kapan Boleh, Kapan Enggak

### Boleh merge `feature/*` → `develop` kapan saja, asalkan:
- ✅ Kode bisa di-build/run tanpa error di lokal
- ✅ Migration jalan mulus di database bersih (`php artisan migrate:fresh --seed`)
- ✅ Fitur sudah ditest manual sesuai checklist (Section 11) — bukan cuma "kelihatannya jalan"
- ✅ Tidak ada `dd()`, `dump()`, `console.log` debug yang ketinggalan
- ✅ Sudah di-rebase/merge dengan `develop` terbaru, tidak ada conflict tersisa
- ✅ Sudah di-review & di-approve minimal 1 dev lain
- ✅ Kalau fitur itu Must-have dan ada bug diketahui, **ditulis eksplisit di deskripsi PR** — jangan disembunyiin, biar tim tahu status realnya

**Jangan tunggu semua fitur "sempurna"** — merge duluan yang sudah jalan untuk kasus normal, lanjut edge case di PR/commit berikutnya. Ingat waktu cuma ~9 hari.

### Boleh merge `develop` → `main` HANYA di checkpoint integrasi (lihat Section 8), dan setelah:
- ✅ Semua fitur 🔴 Must-have di Task Board sudah masuk `develop`
- ✅ Smoke test bareng 3 dev: login admin & guru, jalanin flow absensi end-to-end (buka sesi → scan → edit → export), pastikan gak ada error fatal
- ✅ Tidak ada migration yang saling konflik / merusak data seed
- ✅ Untuk merge **final sebelum go-live**: harus lolos UAT (PRD Section 11) dulu

### Hotfix ke `main`:
- Hanya untuk bug kritis yang ditemukan setelah `develop` sudah masuk `main` (mendekati/setelah go-live).
- Setelah hotfix di-merge ke `main`, **wajib** merge balik ke `develop` juga supaya tidak hilang di iterasi berikutnya.

---

## 7. Koordinasi File & Resource Bersama

Area rawan conflict yang butuh komunikasi ekstra sebelum ngerjain:

| Resource | Aturan |
|----------|--------|
| **Migration files** | Jangan pernah edit migration file yang **sudah di-merge ke `develop`**. Kalau butuh kolom tambahan di tabel existing, bikin migration baru (`add_xxx_to_yyy_table`). Kalau masih dalam 1 PR yang belum merge, boleh edit langsung. |
| **Route files** (`web.php`) | Tiap dev nambah route sendiri di grup masing-masing (comment per section: `// Dev A - Data Master`, dst) supaya minim baris yang sama di-edit 2 orang. |
| **Seeder/Factory** | Perubahan besar ke seeder utama harus diumumkan di grup chat dulu — dev lain mungkin lagi bergantung ke data dummy yang ada. |
| **Layout/komponen shared** (navbar, base Livewire component) | Hasil Fase 0, kalau butuh diubah setelah split, **diskusi dulu di grup**, jangan langsung ubah sepihak karena dipakai semua track. |
| **`.env.example`** | Kalau nambah config baru (misal API key QR library), update `.env.example` di PR yang sama + kabari tim buat update `.env` lokal masing-masing. |

**Kalau ketemu conflict saat rebase:** yang punya branch itu yang resolve. Kalau conflict-nya di logic (bukan cuma format), koordinasi dulu sama dev pemilik kode yang di-conflict-in sebelum nekat pilih salah satu versi.

---

## 8. Timeline & Checkpoint Integrasi

Selaras dengan `prd.md` Section 1.1 (MVP Scope) dan Section 11 (UAT & Rollout).

| Fase | Fokus | Checkpoint |
|------|-------|------------|
| **Fase 0** (hari ini, setengah hari) | Setup bareng (Section 2): project init, semua migration, seeder dummy, auth skeleton, base layout | Merge `feature/setup-foundation` → `develop` |
| **Fase 1** (hari 1–3) | Kerjain fitur 🔴 Must-have paralel per track (A1–A9, B1–B9, C1–C8) | **Checkpoint 1:** smoke test bareng, merge `develop` → `main` kalau flow inti (jadwal → sesi → scan → laporan Excel) sudah nyambung end-to-end |
| **Fase 2** (hari 4–6) | Beresin sisa Must-have + mulai Nice-to-have kalau on-track (import CSV, kenaikan kelas, chart, PDF) | **Checkpoint 2:** regresi test semua fitur Must-have, merge lagi ke `main` |
| **Fase 3** (hari 7–8) | Bugfix, polish UI/UX (Section 7 PRD: mobile-first, feedback scan), testing menyeluruh | **Checkpoint 3:** freeze fitur baru, fokus stabilitas |
| **Fase 4** (hari 9) | **UAT bareng guru asli + training singkat** (PRD Section 11), fix temuan UAT prioritas tinggi, final merge ke `main` | **Go-live** |

> Kalau di Checkpoint 1 atau 2 ada fitur Must-have yang belum kelar, itu sinyal buat pangkas Nice-to-have lebih agresif (lihat prioritas di PRD 1.1), bukan lembur ngoyo ngejar semuanya.

---

## 9. Definition of Done (per tipe fitur)

**CRUD/Data Master:**
- Create, Read, Update, Delete jalan dari UI (bukan cuma dari tinker)
- Validasi input dasar (required, format, unique) ada
- Role yang gak berhak gak bisa akses (dicek via middleware, bukan cuma disembunyiin di UI)

**Fitur Scanner/Absensi:**
- Semua case di flow (NISN tidak ketemu, siswa bukan roster, sudah discan, valid) sudah ditest manual
- Cooldown & audio feedback jalan di HP (bukan cuma browser desktop)
- Fallback manual input jalan kalau kamera dimatikan

**Laporan/Export:**
- Filter kombinasi (tahun ajaran + kelas + tanggal, dst) menghasilkan data yang benar
- File hasil export bisa dibuka & datanya sesuai filter

**Umum:**
- Tidak ada error di console/log saat flow normal
- PR sudah direview & di-merge ke `develop`

---

## 10. Komunikasi & Sync Harian

- **Daily check-in singkat** (bisa via chat, 10 menit): masing-masing update progress, blocker, dan rencana hari ini. Wajib mengingat waktu mepet.
- **Update Task Board** (Section 3) tiap fitur selesai — centang/update status biar semua tahu progress real-time, gak perlu nanya-nanya.
- Kalau **blocked >2 jam** karena nunggu fitur dev lain, langsung chat — jangan diem nunggu sampai daily check-in.
- Perubahan scope (misal mutusin skip satu fitur Nice-to-have) didiskusikan bertiga, update juga di `prd.md` Section 1.1 biar dokumentasi tetap sinkron.

---

## 11. Testing Checklist Sebelum PR (ringkas per area)

**Dev A (Data Master/Jadwal):**
- [ ] CRUD masing-masing entitas jalan + validasi unique (NISN, NIS, email)
- [ ] Jadwal bentrok (kelas sama & guru sama, jam overlap sebagian) **ketolak** dengan pesan jelas
- [ ] Import CSV: baris invalid tidak menggagalkan baris valid, laporan error muncul

**Dev B (Sistem Absensi):**
- [ ] Scan NISN valid → status jadi Hadir, feedback muncul
- [ ] Scan NISN tidak terdaftar → error, tidak ada row baru dibuat
- [ ] Scan siswa dari kelas lain (bukan roster sesi ini) → error spesifik, tidak numpang masuk sesi
- [ ] Scan ulang NISN yang sama → notif "sudah absen"
- [ ] Edit manual status (S/I) tersimpan dengan `updated_by`
- [ ] Edit setelah H+3 terkunci untuk guru

**Dev C (Dashboard/Laporan/Auth):**
- [ ] Guru hanya bisa lihat data kelas yang diampu, tidak bisa lihat punya guru lain
- [ ] Admin bisa edit/override attendance lintas guru
- [ ] Export Excel: data sesuai filter, file bisa dibuka
- [ ] Foto/NISN siswa tidak bisa diakses role di luar Admin/Guru terkait

---

## 12. Rencana Darurat (kalau waktu makin mepet)

1. **Prioritaskan ulang** sesuai PRD Section 1.1 — pangkas Nice-to-have dulu, jangan korbankan Must-have.
2. Kalau satu track jauh lebih lambat (misal Dev B kepatok scanner), 2 dev lain yang sudah selesai Must-have-nya **wajib bantu**, bukan lanjut ke Nice-to-have masing-masing.
3. Kalau ada fitur Must-have yang benar-benar tidak akan kelar sebelum go-live (misal validasi bentrok jadwal app-level), **jangan diam-diam di-skip** — dokumentasikan sebagai *known limitation* di `prd.md` Section 10 (Risk table), dan pastikan tim sekolah tahu sebelum pakai sistemnya.
4. Bug kritis yang ketemu H-1 sebelum go-live → `hotfix/*` langsung dari `main`, prioritas di atas semua kerjaan lain.

---

## 13. Go-Live Checklist

Selaras dengan `prd.md` Section 11:

- [ ] Semua fitur 🔴 Must-have di Task Board (Section 3) sudah di `main`
- [ ] Minimal 1 sesi UAT bareng guru asli (bukan cuma dev) sudah dilakukan
- [ ] Training singkat guru (flow scan, fallback manual, cara edit) sudah dilakukan
- [ ] Temuan prioritas tinggi dari UAT sudah di-fix dan di-merge ke `main`
- [ ] Hosting produksi (domain + SSL + cron auto-close sesi) sudah siap — bukan cuma HTTPS lokal dev
- [ ] Backup MySQL harian sudah dikonfigurasi
- [ ] Known limitations (kalau ada fitur yang di-skip) sudah didokumentasikan & dikomunikasikan ke pihak sekolah
