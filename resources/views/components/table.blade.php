<div class="table-container overflow-x-auto w-full rounded-xl border border-gray-200">
    <table class="table min-w-full divide-y divide-gray-200">
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
        @endif
        
        @if(isset($footer))
        <tfoot>
            {{ $footer }}
        </tfoot>
        @endif
    </table>
    
    @if(isset($pagination))
    <div class="table-pagination px-4 py-3 border-t border-gray-200 overflow-x-auto">
        {{ $pagination }}
    </div>
    @endif
</div>

<style>
    .table-container {
        width: 100%;
        max-width: 100%;
        overflow-x: auto;
        overflow-y: hidden;
        -webkit-overflow-scrolling: touch;
    }

    .table th,
    .table td {
        padding: 0.75rem 1rem;
        white-space: nowrap;
    }

    @media (min-width: 768px) {
        .table th,
        .table td {
            padding: 1rem 1.25rem;
            white-space: normal;
        }
    }
</style>