<div class="table-container">
    <table class="table">
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
        @endif
        
        @if(isset($footer))
        <tfoot>
            {{ $footer }}
        </tfoot>
        @endif
    </table>
    
    @if(isset($pagination))
    <div class="table-pagination">
        {{ $pagination }}
    </div>
    @endif
</div>