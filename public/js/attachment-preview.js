(function () {
    function formatFileSize(bytes) {
        if (bytes >= 1048576) {
            return (bytes / 1048576).toFixed(2) + ' MB';
        }

        return (bytes / 1024).toFixed(2) + ' KB';
    }

    function escapeHtml(str) {
        return String(str).replace(/[&<>"']/g, function (char) {
            return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[char];
        });
    }

    function buildAttachmentPreviewCard(file, objectUrl) {
        const isImage = file.type.startsWith('image/');
        const isPdf = file.type.includes('pdf');
        const ext = (file.name.split('.').pop() || '').toUpperCase();
        const card = document.createElement('div');
        card.className = 'attachment-card group';

        let previewHtml = '';
        if (isImage && objectUrl) {
            previewHtml =
                '<div class="attachment-card-preview block">' +
                '<img src="' + objectUrl + '" alt="' + escapeHtml(file.name) + '">' +
                '<div class="attachment-card-preview-overlay"><i class="ti ti-eye"></i></div>' +
                '</div>';
        } else {
            const iconWrapClass = isPdf
                ? 'attachment-card-file-icon text-rose-500 bg-rose-50/50'
                : 'attachment-card-file-icon text-blue-500 bg-blue-50/50';
            const iconClass = isPdf ? 'ti-file-type-pdf' : 'ti-file';
            previewHtml =
                '<div class="' + iconWrapClass + '">' +
                '<i class="ti ' + iconClass + ' text-6xl opacity-80"></i>' +
                '</div>';
        }

        card.innerHTML =
            previewHtml +
            '<div class="p-5 flex flex-col flex-1">' +
            '<div class="text-sm font-bold text-slate-800 truncate mb-2 group-hover:text-primary-600 transition-colors" title="' +
            escapeHtml(file.name) +
            '">' +
            escapeHtml(file.name) +
            '</div>' +
            '<div class="space-y-1.5 text-xs text-slate-500">' +
            '<div class="flex items-center gap-2 flex-wrap">' +
            '<i class="ti ti-database text-slate-400"></i>' +
            '<span>' +
            formatFileSize(file.size) +
            '</span>' +
            (ext
                ? '<span class="w-1 h-1 rounded-full bg-slate-300"></span><span class="uppercase">' +
                  escapeHtml(ext) +
                  '</span>'
                : '') +
            '</div>' +
            '</div>' +
            '<div class="flex flex-wrap gap-2 mt-4 pt-4 border-t border-slate-100">' +
            '<span class="btn-modern btn-modern-secondary btn-sm flex-1 justify-center opacity-90">' +
            '<i class="ti ti-clock"></i> در انتظار آپلود' +
            '</span>' +
            '</div>' +
            '</div>';

        return card;
    }

    window.initAttachmentFilePreview = function (input, previewEl, containerEl) {
        if (!input || !previewEl || !containerEl) {
            return;
        }

        let objectUrls = [];

        input.addEventListener('change', function () {
            objectUrls.forEach(function (url) {
                URL.revokeObjectURL(url);
            });
            objectUrls = [];
            containerEl.innerHTML = '';

            if (!this.files || this.files.length === 0) {
                previewEl.classList.add('hidden');
                return;
            }

            previewEl.classList.remove('hidden');

            Array.from(this.files).forEach(function (file) {
                const objectUrl = file.type.startsWith('image/') ? URL.createObjectURL(file) : null;
                if (objectUrl) {
                    objectUrls.push(objectUrl);
                }
                containerEl.appendChild(buildAttachmentPreviewCard(file, objectUrl));
            });
        });
    };
})();
