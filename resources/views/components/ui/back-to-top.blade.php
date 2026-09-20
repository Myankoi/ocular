<button
    type="button"
    data-back-to-top
    aria-label="Kembali ke atas"
    class="fixed bottom-5 right-5 z-20 hidden size-11 items-center justify-center border border-ocular-teal/20 bg-white text-xl font-bold text-ocular-teal shadow-[var(--shadow-card)] transition hover:bg-ocular-teal hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-ocular-orange sm:bottom-7 sm:right-7"
>
    ↑
</button>

@once
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const button = document.querySelector('[data-back-to-top]');

                if (!button) return;

                const toggleButton = () => {
                    button.classList.toggle('hidden', window.scrollY < 320);
                    button.classList.toggle('flex', window.scrollY >= 320);
                };

                button.addEventListener('click', () => {
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                });

                window.addEventListener('scroll', toggleButton, { passive: true });
                toggleButton();
            });
        </script>
    @endpush
@endonce
