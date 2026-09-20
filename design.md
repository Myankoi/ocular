# Ocular design system

Dokumen ini adalah kontrak visual untuk implementasi Ocular. Referensi utama
adalah markup pada `ui-example/*.html`; screenshot hanya dipakai untuk melihat
komposisi ketika markup tidak menjelaskan perilakunya.

## Prinsip

- Mobile-first: layar 360px tetap menjadi layout utama, desktop menambah ruang
  dan navigasi tanpa mengubah hierarki informasi.
- Ocular terasa seperti alat kerja sekolah: cepat, tegas, terbaca, dan tidak
  memakai ornamen yang mengganggu proses absensi.
- Semua kontrol interaktif memiliki tinggi minimum 44px dan state focus yang
  terlihat.
- Radius default 0px. Radius kecil hanya untuk badge, avatar, atau kontrol yang
  membutuhkan affordance sentuh.

## Token visual (source of truth dari file HTML)

| Token | Nilai | Pemakaian |
|---|---|---|
| `primary` | `#3C6974` | sidebar, header guru, tombol sekunder, focus |
| `primary-dark` | `#2A4B53` | hover dan teks teal gelap |
| `secondary` | `#CD6F2D` | CTA utama, aksen aktif, tombol mulai |
| `secondary-dark` | `#B65E23` | hover CTA |
| `accent` | `#6C929B` | icon, garis bantu, elemen sekunder |
| `surface` | `#F8FAFC` | background aplikasi |
| `copy` | `#4F5253` | teks utama |
| `success` | `#10B981` | hadir/sukses |
| `warning` | `#E9A025` | sakit/peringatan |
| `info` | `#3B82F6` | izin/informasi |
| `danger` | `#EF4444` | alpha/error |
| `shadow-card` | `-2px 4px 12px 4px rgba(51,51,51,.05)` | card dan panel |

Tipografi memakai `Bricolage Grotesque` untuk UI dan `JetBrains Mono` untuk
angka, tanggal, NIS/NISN, serta metadata teknis. Jangan mengganti font dengan
font default pada halaman baru.

## Shell aplikasi

### Desktop

- Sidebar fixed selebar 256px di kiri, background `primary`, logo di atas, menu
  di tengah, dan identitas user + logout di bawah.
- Item aktif memakai background putih 10%, teks putih, dan garis aksen orange
  4px di sisi kiri.
- Area konten memakai header putih sticky, lebar maksimum 1280px, dan padding
  horizontal yang bertambah pada breakpoint besar.

### Mobile

- Sidebar berubah menjadi drawer dari kiri yang dibuka melalui hamburger, dengan
  overlay, gesture swipe, identitas user, dan logout.
- Konten memakai padding 16px; navigasi utama tetap berada di drawer mobile.
- Dashboard guru menampilkan header teal, greeting/profile, periode aktif,
  summary 2 kolom, dan kartu jadwal.
- Tabel panjang memakai `overflow-x-auto`; informasi penting tidak boleh hilang
  hanya karena layar sempit.
- Semua read list yang berpotensi panjang memakai pagination; daftar siswa sesi juga
  memakai pagination, dengan ringkasan total tetap terlihat di atas tabel.

## Halaman referensi

- `01-Ocular - Login.html`: panel login terpusat, icon mata 64px, garis teal
  8px, field label uppercase, dan CTA orange full-width.
- `02-Ocular - AdminDashboard.html`: sidebar desktop, stat cards, persentase
  absensi hari ini, quick action, dan tabel ringkasan kelas.
- `03-Ocular - TeacherManagement.html`, `06-Ocular - AcademicYears.html`,
  `07-Ocular - ClassManagement.html`: pola data master, filter, tabel,
  pagination, dan modal/form yang tetap usable di mobile.
- `04-Ocular - QRScanner.html`: preview kamera dominan, status online,
  feedback scan, input NISN fallback, dan tombol tutup sesi.
- `05-Ocular - Dashboard.html`: dashboard guru mobile-first dengan summary,
  jadwal hari ini, dan tombol mulai/buka sesi. Navigasi produksi memakai
  hamburger agar tetap terasa sebagai web responsif.

## Pola interaksi

- Gunakan `page-header`, `stat-card`, `data-card`, `status-badge`, dan
  `responsive-table` secara konsisten.
- Flash message dan error validasi harus berada dekat konteks aksi dan dapat
  dibaca screen reader.
- Scanner memberi feedback visual, nama siswa, bunyi singkat bila didukung
  browser, timeout yang bisa dicoba ulang, dan fallback NISN manual.
- Tombol aksi yang mengubah atau menghapus data menampilkan konfirmasi dan
  mencatat actor/waktu pada audit attendance.

Komponen Blade reusable tersedia di `resources/views/components/ui/`:
`x-ui.page`, `x-ui.card`, `x-ui.button`, `x-ui.link-button`,
`x-ui.stat-card`, `x-ui.status-badge`, `x-ui.flash`, dan `x-ui.pagination`.
Gunakan komponen tersebut sebelum membuat markup baru agar warna, focus state,
tinggi tap target, dan pagination tetap konsisten.

## Data dan privasi

NISN, foto, dan data siswa hanya ditampilkan pada role yang berhak. Credential
tidak pernah ditulis ke URL, log frontend, atau dokumentasi. Export mengikuti
filter aktif dan nama file harus menjelaskan rentang data.

## Implementasi

Gunakan utility Tailwind v4 dan token pada `resources/css/app.css`. Semua fitur
baru harus mengikuti shell `<x-layouts.app>`, mempertahankan input ketika validasi
gagal, dan diverifikasi pada viewport mobile serta desktop dengan Vite aktif.
