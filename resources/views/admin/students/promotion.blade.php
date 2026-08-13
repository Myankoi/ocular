<x-layouts.app title="Kenaikan Kelas - Ocular">
    <div class="mx-auto max-w-2xl space-y-6">
        <div><p class="text-xs font-semibold uppercase tracking-[0.2em] text-ocular-teal">Students</p><h1 class="mt-1 text-2xl font-bold">Kenaikan kelas</h1><p class="mt-1 text-sm text-slate-500">Pindahkan seluruh siswa aktif dari kelas asal ke kelas tujuan.</p></div>
        @if (session('success'))<div class="rounded-md bg-emerald-50 p-3 text-sm text-emerald-700">{{ session('success') }}</div>@endif
        <form method="POST" action="{{ route('admin.students.promote') }}" class="space-y-4 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            @csrf
            <label class="block text-sm font-medium">Kelas asal<select name="from_class_id" required class="mt-1 min-h-11 w-full rounded-md border border-slate-300 px-3"><option value="">Pilih kelas asal</option>@foreach ($classes as $class)<option value="{{ $class->id }}">{{ $class->name }} · {{ $class->academicYear->name }}</option>@endforeach</select></label>
            <label class="block text-sm font-medium">Kelas tujuan<select name="to_class_id" required class="mt-1 min-h-11 w-full rounded-md border border-slate-300 px-3"><option value="">Pilih kelas tujuan</option>@foreach ($classes as $class)<option value="{{ $class->id }}">{{ $class->name }} · {{ $class->academicYear->name }}</option>@endforeach</select></label>
            <div class="rounded-md bg-amber-50 p-3 text-sm text-amber-800">Histori absensi tetap tersimpan. Pastikan kelas tujuan sudah benar sebelum menyimpan.</div>
            <button onclick="return confirm('Pindahkan semua siswa aktif?')" class="min-h-11 rounded-md bg-ocular-orange px-4 py-2 text-sm font-semibold text-white">Pindahkan siswa</button>
            <a href="{{ route('admin.students.index') }}" class="ml-2 text-sm underline">Kembali</a>
        </form>
    </div>
</x-layouts.app>
