@props(['striped' => false, 'hover' => true, 'bordered' => true, 'responsive' => true, 'compact' => false, 'title' => null])

<div class="modern-card animate-fade-in w-full max-w-full overflow-hidden">
    @if($title || isset($headerAction))
        <div class="card-header" style="padding: 1.25rem 1.5rem; border-bottom: 1.5px solid var(--frame-border, #57534e); display: flex; align-items: center; justify-content: space-between;">
            @if($title)
                <h3 class="card-title" style="font-size: 1rem; font-weight: 700; color: var(--color-slate-800); margin: 0; display: flex; align-items: center; gap: 0.75rem;">
                    @if(isset($icon))
                        <i class="ti {{ $icon }} text-primary-600"></i>
                    @endif
                    {{ $title }}
                </h3>
            @elseif(isset($icon))
                <h3 class="card-title" style="font-size: 1rem; font-weight: 700; color: var(--color-slate-800); margin: 0; display: flex; align-items: center; gap: 0.75rem;">
                    <i class="ti {{ $icon }} text-primary-600"></i>
                </h3>
            @endif

            @if(isset($headerAction))
                <div class="header-action">
                    {{ $headerAction }}
                </div>
            @endif
        </div>
    @endif

    <div class="modern-table-container overflow-x-auto w-full rounded-xl border border-gray-200">
        <table class="modern-table min-w-full divide-y divide-gray-200 {{ $striped ? 'table-striped' : '' }} {{ $hover ? 'table-hover' : '' }}" style="border-collapse: collapse; width: 100%; min-width: 100%;">
            @if(isset($headers))
                <thead>
                    <tr>
                        {{ $headers }}
                    </tr>
                </thead>
            @endif

            @if(isset($rows))
                <tbody class="bg-white divide-y divide-gray-200">
                    {{ $rows }}
                </tbody>
            @else
                <tbody class="bg-white divide-y divide-gray-200">
                    {{ $slot }}
                </tbody>
            @endif

            @if(isset($footer))
                <tfoot>
                    {{ $footer }}
                </tfoot>
            @endif
        </table>
    </div>

    @if(isset($pagination))
        <div class="card-footer" style="padding: 1rem 1.5rem; background-color: var(--color-slate-50); border-top: 1.5px solid var(--frame-border, #57534e);">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3" style="display: flex; align-items: center; justify-content: space-between;">
                <div class="text-sm w-full sm:w-auto" style="font-size: 0.875rem; color: var(--color-slate-500);">
                    @if(isset($total))
                        نمایش {{ $from ?? 1 }} تا {{ $to ?? '...' }} از {{ $total }} مورد
                    @endif
                </div>
                <div class="modern-pagination w-full sm:w-auto overflow-x-auto">
                    {{ $pagination }}
                </div>
            </div>
        </div>
    @endif
</div>

<style>
    .modern-table-container {
        width: 100%;
        max-width: 100%;
        overflow-x: auto;
        overflow-y: hidden;
        -webkit-overflow-scrolling: touch;
    }

    .modern-table thead tr {
        background-color: var(--color-slate-50);
    }

    .modern-table {
        border: 1.5px solid var(--frame-border, #57534e);
    }

    .modern-table th,
    .modern-table td {
        padding: 0.85rem 1rem;
        border: 1px solid var(--frame-border, #57534e);
        vertical-align: middle;
        white-space: nowrap;
    }

    @media (min-width: 768px) {
        .modern-table th,
        .modern-table td {
            padding: 1rem 1.5rem;
            white-space: normal;
        }
    }

    .modern-table th {
        text-align: right;
        font-weight: 700;
        font-size: 0.8rem;
        color: var(--color-slate-600);
        letter-spacing: 0.025em;
        background-color: #f8fafc;
    }

    .modern-table td {
        color: var(--color-slate-700);
        font-size: 0.85rem;
    }

    .modern-table.table-hover tbody tr:hover {
        background-color: var(--color-primary-50);
        transition: var(--transition-fast);
    }

    .modern-table.table-striped tbody tr:nth-child(even) {
        background-color: var(--color-slate-50);
    }
</style>
