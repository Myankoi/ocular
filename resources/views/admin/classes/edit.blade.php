<x-layouts.app title="Edit Kelas - Ocular">
    <x-ui.page eyebrow="Data master" title="Edit kelas">
        <x-ui.card class="max-w-2xl border border-ocular-teal/15">
            <form method="POST" action="{{ route('admin.classes.update', $class) }}" class="space-y-5">
                @csrf
                @method('PUT')

                @include('admin.classes.form', ['class' => $class])

                <div class="flex flex-wrap items-center gap-2 border-t border-slate-100 pt-5">
                    <x-ui.button>Update kelas</x-ui.button>
                    <x-ui.link-button :href="route('admin.classes.index')" variant="muted">Batal</x-ui.link-button>
                </div>
            </form>
        </x-ui.card>
    </x-ui.page>
</x-layouts.app>
