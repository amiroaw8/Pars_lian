<div id="dashboard-notifications" class="mb-8 hidden">
    <div class="space-y-4" id="notification-list">
        <!-- Notifications will be injected here -->
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('dashboard-notifications');
        const list = document.getElementById('notification-list');

        fetch("{{ route('api.notifications.summary') }}")
            .then(response => response.json())
            .then(data => {
                if (data.length > 0) {
                    container.classList.remove('hidden');
                    data.forEach(notif => {
                        const div = document.createElement('a');
                        div.href = notif.link;
                        div.className = `flex items-center gap-4 p-4 rounded-2xl border transition-all hover:scale-[1.01] ${getBgClass(notif.type)}`;
                        div.innerHTML = `
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 ${getIconBgClass(notif.type)}">
                                <i class="ti ${notif.icon} text-2xl"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-black text-sm">${notif.title}</h4>
                                <p class="text-xs opacity-70 mt-0.5">${notif.message}</p>
                            </div>
                            <i class="ti ti-chevron-left text-xl opacity-30"></i>
                        `;
                        list.appendChild(div);
                    });
                }
            });

        function getBgClass(type) {
            switch(type) {
                case 'warning': return 'bg-amber-50 border-amber-100 text-amber-900';
                case 'rose': return 'bg-rose-50 border-rose-100 text-rose-900';
                case 'success': return 'bg-emerald-50 border-emerald-100 text-emerald-900';
                case 'info': return 'bg-blue-50 border-blue-100 text-blue-900';
                case 'primary': return 'bg-indigo-50 border-indigo-100 text-indigo-900';
                default: return 'bg-slate-50 border-slate-100 text-slate-900';
            }
        }

        function getIconBgClass(type) {
            switch(type) {
                case 'warning': return 'bg-amber-100 text-amber-600';
                case 'rose': return 'bg-rose-100 text-rose-600';
                case 'success': return 'bg-emerald-100 text-emerald-600';
                case 'info': return 'bg-blue-100 text-blue-600';
                case 'primary': return 'bg-indigo-100 text-indigo-600';
                default: return 'bg-slate-200 text-slate-600';
            }
        }
    });
</script>
