@props(['striped' => false, 'hover' => true, 'bordered' => true, 'responsive' => true, 'compact' => false, 'title' => null])

<div class="modern-card animate-fade-in">
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

    <div class="modern-table-container">
        <table class="modern-table {{ $striped ? 'table-striped' : '' }} {{ $hover ? 'table-hover' : '' }}" style="width: 100%; border-collapse: collapse;">
            @if(isset($headers))
                <thead>
                    <tr>
                        {{ $headers }}
                    </tr>
                </thead>
            @endif

            @if(isset($rows))
                <tbody>
                    {{ $rows }}
                </tbody>
            @else
                <tbody>
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
            <div class="flex items-center justify-between" style="display: flex; align-items: center; justify-content: space-between;">
                <div class="text-sm" style="font-size: 0.875rem; color: var(--color-slate-500);">
                    @if(isset($total))
                        نمایش {{ $from ?? 1 }} تا {{ $to ?? '...' }} از {{ $total }} مورد
                    @endif
                </div>
                <div class="modern-pagination">
                    {{ $pagination }}
                </div>
            </div>
        </div>
    @endif
</div>

<style>
    .modern-table-container {
        width: 100%;
        overflow-x: auto;
    }

    .modern-table thead tr {
        background-color: var(--color-slate-50);
    }

    .modern-table {
        border: 1.5px solid var(--frame-border, #57534e);
    }

    .modern-table th,
    .modern-table td {
        padding: 1rem 1.5rem;
        border: 1px solid var(--frame-border, #57534e);
        vertical-align: middle;
    }

    .modern-table th {
        text-align: right;
        font-weight: 700;
        font-size: 0.85rem;
        color: var(--color-slate-600);
        letter-spacing: 0.025em;
    }

    .modern-table td {
        color: var(--color-slate-700);
        font-size: 0.9rem;
    }

    .modern-table.table-hover tbody tr:hover {
        background-color: var(--color-primary-50);
        transition: var(--transition-fast);
    }

    .modern-table.table-striped tbody tr:nth-child(even) {
        background-color: var(--color-slate-50);
    }
</style>
