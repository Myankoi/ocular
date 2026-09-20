@props([
    'formId',
    'action',
    'checkboxName',
    'confirmMessage' => 'Hapus semua data yang dipilih?',
    'buttonLabel' => 'Hapus terpilih',
])

<form id="{{ $formId }}" method="POST" action="{{ $action }}" onsubmit="return confirm(@js($confirmMessage))" class="flex flex-wrap items-center justify-end gap-3">
    @csrf
    @method('DELETE')
    <label class="flex cursor-pointer items-center gap-2 text-[10px] font-black uppercase tracking-wider text-ocular-copy/70">
        <input type="checkbox" data-bulk-select-all="{{ $formId }}" class="size-4 border-slate-300 text-ocular-teal">
        Pilih semua
    </label>
    <span data-bulk-selected-count="{{ $formId }}" class="min-w-20 text-right text-[10px] font-bold text-ocular-copy/50">0 dipilih</span>
    <x-ui.button type="submit" variant="danger" data-bulk-submit="{{ $formId }}" disabled>{{ $buttonLabel }}</x-ui.button>
</form>

<script>
    (() => {
        const formId = @js($formId);
        const form = document.getElementById(formId);
        if (!form) return;

        const selectAll = form.querySelector(`[data-bulk-select-all="${formId}"]`);
        const count = form.querySelector(`[data-bulk-selected-count="${formId}"]`);
        const submit = form.querySelector(`[data-bulk-submit="${formId}"]`);
        const boxes = () => [...document.querySelectorAll(`[data-bulk-checkbox="${formId}"]`)];

        const update = () => {
            const allBoxes = boxes();
            const selectedIds = new Set(allBoxes.filter((box) => box.checked).map((box) => box.dataset.bulkId));
            const allIds = new Set(allBoxes.map((box) => box.dataset.bulkId));

            count.textContent = `${selectedIds.size} dipilih`;
            submit.disabled = selectedIds.size === 0;
            selectAll.checked = allIds.size > 0 && selectedIds.size === allIds.size;
            selectAll.indeterminate = selectedIds.size > 0 && selectedIds.size < allIds.size;
        };

        selectAll.addEventListener('change', () => {
            boxes().forEach((box) => { box.checked = selectAll.checked; });
            update();
        });

        document.addEventListener('change', (event) => {
            if (!event.target.matches(`[data-bulk-checkbox="${formId}"]`)) return;
            boxes().filter((box) => box.dataset.bulkId === event.target.dataset.bulkId).forEach((box) => {
                box.checked = event.target.checked;
            });
            update();
        });

        update();
    })();
</script>
