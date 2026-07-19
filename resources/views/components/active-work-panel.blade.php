@props([
    'sections' => [],
    'pollUrl' => '',
])

<div
    id="active-work-root"
    class="mb-8 animate-slide-up"
    data-poll-url="{{ $pollUrl }}"
    data-poll-interval="5000"
>
    <div class="flex flex-wrap items-center justify-between gap-4 mb-5">
        <h2 class="text-lg font-black text-slate-800 flex items-center gap-2">
            <i class="ti ti-activity text-primary-600"></i>
            کارهای فعال
        </h2>
        <div class="flex items-center gap-3 text-xs text-slate-500">
            <span id="active-work-updated-at" class="font-bold"></span>
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse" id="active-work-live-dot"></span>
                به‌روزرسانی خودکار
            </span>
        </div>
    </div>

    <div id="active-work-sections" class="space-y-6">
        @forelse($sections as $section)
            @include('components.partials.active-work-role-section', ['section' => $section])
        @empty
            <div class="modern-card p-8 text-center text-slate-500 text-sm">
                <i class="ti ti-mood-smile text-3xl mb-2 block text-slate-300"></i>
                نقش کاری برای نمایش کارهای فعال یافت نشد.
            </div>
        @endforelse
    </div>
</div>

@once
    @push('scripts')
    <script>
    (function () {
        const root = document.getElementById('active-work-root');
        if (!root) return;

        const pollUrl = root.dataset.pollUrl;
        const intervalMs = parseInt(root.dataset.pollInterval || '5000', 10);
        const sectionsEl = document.getElementById('active-work-sections');
        const updatedAtEl = document.getElementById('active-work-updated-at');
        const breadcrumbEl = document.getElementById('dashboard-breadcrumb');

        let isFetching = false;
        let pollTimer = null;
        let lastPayload = '';

        const colorMap = {
            blue: 'bg-blue-50 text-blue-700 border-blue-200',
            indigo: 'bg-indigo-50 text-indigo-700 border-indigo-200',
            yellow: 'bg-amber-50 text-amber-700 border-amber-200',
            orange: 'bg-orange-50 text-orange-700 border-orange-200',
            purple: 'bg-purple-50 text-purple-700 border-purple-200',
            red: 'bg-rose-50 text-rose-700 border-rose-200',
            green: 'bg-emerald-50 text-emerald-700 border-emerald-200',
            teal: 'bg-teal-50 text-teal-700 border-teal-200',
            gray: 'bg-slate-100 text-slate-600 border-slate-200',
            slate: 'bg-slate-100 text-slate-600 border-slate-200',
        };

        function badgeClass(color) {
            return colorMap[color] || colorMap.slate;
        }

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;');
        }

        function renderSections(sections) {
            if (!sections || sections.length === 0) {
                sectionsEl.innerHTML = '<div class="modern-card p-8 text-center text-slate-500 text-sm">کار فعالی در صف نیست.</div>';
                return;
            }

            sectionsEl.innerHTML = sections.map(function (section) {
                const itemsHtml = (section.items || []).map(function (item) {
                    return `
                        <a href="${escapeHtml(item.url)}" class="flex items-start gap-4 p-4 rounded-2xl border border-slate-200 bg-white hover:border-primary-300 hover:shadow-md transition-all group">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" style="background:${escapeHtml(section.accent)}1a;color:${escapeHtml(section.accent)}">
                                <i class="ti ti-chevron-left text-lg"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-bold text-slate-800 text-sm truncate group-hover:text-primary-700">${escapeHtml(item.title)}</div>
                                <div class="text-xs text-slate-500 mt-0.5 truncate">${escapeHtml(item.subtitle)}</div>
                            </div>
                            <div class="text-left shrink-0 space-y-1">
                                <span class="inline-flex px-2 py-0.5 rounded-lg text-[10px] font-black border ${badgeClass(item.status_color)}">${escapeHtml(item.status_label)}</span>
                                <div class="text-[10px] text-slate-400 font-bold">${escapeHtml(item.updated_at_human)}</div>
                            </div>
                        </a>
                    `;
                }).join('');

                const emptyHtml = '<div class="p-6 text-center text-slate-400 text-xs font-bold">کار فعالی برای این نقش نیست.</div>';
                const cartableLink = section.cartable_url
                    ? `<a href="${escapeHtml(section.cartable_url)}" class="text-xs font-bold text-primary-600 hover:underline">مشاهده کارتابل</a>`
                    : '';

                return `
                    <section id="active-work-${escapeHtml(section.key)}" class="modern-card overflow-hidden scroll-mt-28" style="border-top: 3px solid ${escapeHtml(section.accent)}">
                        <div class="card-header flex flex-wrap items-center justify-between gap-3" style="background: ${escapeHtml(section.accent)}0d;">
                            <h3 class="card-title text-base font-black text-slate-800 flex items-center gap-2 m-0">
                                <i class="${escapeHtml(section.icon)} text-lg" style="color:${escapeHtml(section.accent)}"></i>
                                ${escapeHtml(section.label)}
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-white border border-slate-200 text-slate-600">${section.count || 0}</span>
                            </h3>
                            ${cartableLink}
                        </div>
                        <div class="card-body p-4 space-y-2">
                            ${itemsHtml || emptyHtml}
                        </div>
                    </section>
                `;
            }).join('');
        }

        function updateBreadcrumbCounts(sections) {
            if (!breadcrumbEl || !sections) return;

            sections.forEach(function (section) {
                const badge = breadcrumbEl.querySelector('.active-work-breadcrumb-count[data-role-key="' + section.key + '"]');
                if (!badge) return;

                const count = section.count || 0;
                if (count > 0) {
                    badge.textContent = String(count);
                    badge.classList.remove('hidden');
                } else {
                    badge.textContent = '';
                    badge.classList.add('hidden');
                }
            });
        }

        function setUpdatedLabel(iso) {
            if (!updatedAtEl) return;
            const date = iso ? new Date(iso) : new Date();
            updatedAtEl.textContent = 'آخرین به‌روزرسانی: ' + date.toLocaleTimeString('fa-IR', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
            });
        }

        function schedulePoll() {
            if (pollTimer) clearInterval(pollTimer);
            pollTimer = setInterval(function () {
                if (!document.hidden) refresh();
            }, intervalMs);
        }

        async function refresh() {
            if (!pollUrl || isFetching || document.hidden) return;

            isFetching = true;
            try {
                const url = pollUrl + (pollUrl.includes('?') ? '&' : '?') + '_=' + Date.now();
                const response = await fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Cache-Control': 'no-cache',
                    },
                    credentials: 'same-origin',
                    cache: 'no-store',
                });

                if (!response.ok) return;

                const data = await response.json();
                const payload = JSON.stringify(data.sections || []);

                if (payload !== lastPayload) {
                    lastPayload = payload;
                    renderSections(data.sections || []);
                    updateBreadcrumbCounts(data.sections || []);
                }

                setUpdatedLabel(data.generated_at);
            } catch (e) {
                /* silent */
            } finally {
                isFetching = false;
            }
        }

        function startLiveUpdates() {
            refresh();
            schedulePoll();
        }

        function stopLiveUpdates() {
            if (pollTimer) {
                clearInterval(pollTimer);
                pollTimer = null;
            }
        }

        if (pollUrl) {
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', startLiveUpdates);
            } else {
                startLiveUpdates();
            }

            document.addEventListener('visibilitychange', function () {
                if (document.hidden) {
                    stopLiveUpdates();
                } else {
                    refresh();
                    schedulePoll();
                }
            });

            window.addEventListener('focus', function () {
                if (!document.hidden) refresh();
            });
        } else {
            setUpdatedLabel(new Date().toISOString());
        }
    })();
    </script>
    @endpush
@endonce
