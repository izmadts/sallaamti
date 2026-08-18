{{-- Shared infinite-scroll + reaction + save JS for any page rendering the
     #dua-grid feed (dua-wall.index, dua-wall.saved). Included (not a
     component) — expects $feedUrl and $paginated from the including view. --}}
<script>
    (function () {
        const grid = document.getElementById('dua-grid');
        const sentinel = document.getElementById('dua-scroll-sentinel');
        const loadingEl = document.getElementById('dua-scroll-loading');
        const endEl = document.getElementById('dua-scroll-end');
        const loadMoreBtn = document.getElementById('dua-scroll-load-more');

        let page = 1;
        let hasMore = {{ $paginated->hasMorePages() ? 'true' : 'false' }};
        let loading = false;

        async function loadNextPage() {
            if (loading || !hasMore) return;
            loading = true;
            loadMoreBtn.classList.add('hidden');
            loadingEl.classList.remove('hidden');
            loadingEl.classList.add('flex');

            page += 1;
            const params = new URLSearchParams(window.location.search);
            params.set('page', page);

            try {
                const res = await fetch('{{ $feedUrl }}?' + params.toString(), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                });
                const data = await res.json();
                if (data.html && data.html.trim()) {
                    grid.insertAdjacentHTML('beforeend', data.html);
                }
                hasMore = data.has_more;
            } catch (e) {
                hasMore = false;
            }

            loading = false;
            loadingEl.classList.add('hidden');
            loadingEl.classList.remove('flex');
            if (hasMore) {
                loadMoreBtn.classList.remove('hidden');
            } else {
                endEl.classList.remove('hidden');
                if (observer) observer.disconnect();
            }
        }

        let observer = null;
        if (hasMore) {
            loadMoreBtn.addEventListener('click', loadNextPage);
            observer = new IntersectionObserver((entries) => {
                if (entries[0].isIntersecting) loadNextPage();
            }, { rootMargin: '400px' });
            observer.observe(sentinel);
        } else {
            endEl.classList.remove('hidden');
        }

        // Reactions — delegated so it keeps working on cards appended by infinite scroll.
        // A user has one active reaction per post: tapping their current type removes it,
        // tapping a different type swaps it.
        document.addEventListener('click', async function (event) {
            const btn = event.target.closest('[data-react]');
            if (!btn) return;
            event.preventDefault();

            const group = btn.closest('[data-reaction-group]');

            try {
                const res = await fetch(btn.dataset.url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ type: btn.dataset.type }),
                });
                const data = await res.json();

                group.querySelectorAll('[data-react]').forEach((b) => {
                    const active = data.reacted && b.dataset.type === data.type;
                    b.classList.toggle('bg-teal-50', active);
                    b.classList.toggle('border-teal-300', active);
                    b.classList.toggle('text-[--teal]', active);
                    b.classList.toggle('border-gray-200', !active);
                    b.classList.toggle('text-gray-500', !active);
                });

                const total = group.querySelector('.reaction-total');
                if (total) {
                    total.textContent = data.count > 0 ? data.count : '';
                    total.classList.toggle('hidden', data.count === 0);
                }
            } catch (e) {
                // silently ignore — worst case the tap just didn't register
            }
        });

        // Save/bookmark — same delegated-click approach as reactions.
        document.addEventListener('click', async function (event) {
            const btn = event.target.closest('[data-save]');
            if (!btn) return;
            event.preventDefault();

            try {
                const res = await fetch(btn.dataset.url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                });
                const data = await res.json();

                btn.classList.toggle('bg-amber-50', data.saved);
                btn.classList.toggle('border-amber-300', data.saved);
                btn.classList.toggle('text-amber-700', data.saved);
                btn.classList.toggle('border-gray-200', !data.saved);
                btn.classList.toggle('text-gray-500', !data.saved);
                btn.querySelector('[data-save-icon]').textContent = data.saved ? '🔖' : '📑';
                btn.querySelector('[data-save-label]').textContent = data.saved ? '{{ __('db.Saved') }}' : '{{ __('db.Save') }}';

                // On the Saved page itself, un-saving should drop the card
                // from the list immediately rather than leave a stale item.
                if (!data.saved && grid.dataset.pruneOnUnsave === '1') {
                    btn.closest('#dua-grid > div')?.remove();
                }
            } catch (e) {
                // silently ignore — worst case the tap just didn't register
            }
        });
    })();
</script>
