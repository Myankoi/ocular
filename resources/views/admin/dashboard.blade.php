<x-layouts.app title="Admin Dashboard - Ocular">
    <div class="space-y-6">
        <div>
            <p class="text-sm font-medium uppercase tracking-wide text-slate-500">Admin</p>
            <h1 class="text-2xl font-semibold tracking-tight">Dashboard Admin</h1>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-slate-500">Siswa Aktif</p>
                <p class="mt-2 text-3xl font-semibold">{{ $totalStudents }}</p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-slate-500">Guru Aktif</p>
                <p class="mt-2 text-3xl font-semibold">{{ $totalTeachers }}</p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-slate-500">Kelas</p>
                <p class="mt-2 text-3xl font-semibold">{{ $totalClasses }}</p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-slate-500">Record Absensi</p>
                <p class="mt-2 text-3xl font-semibold">{{ $totalAttendances }}</p>
            </div>
        </div>

        <div class="rounded-lg border border-dashed border-slate-300 bg-white p-6 text-sm text-slate-600">
            Placeholder dashboard admin. Modul CRUD data master, laporan, dan reset password akan masuk di branch fitur berikutnya.
        </div>
    </div>
</x-layouts.app>
