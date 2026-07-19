@props(['note' => '', 'inventoryId' => null, 'inventoryUrl' => null])

@php
    use App\Support\InventoryTransactionNoteFormatter;

    $text = trim((string) $note);
    $html = InventoryTransactionNoteFormatter::toHtml($text, $inventoryUrl);
@endphp

@if($text === '')
    <span class="text-slate-400">—</span>
@else
    <span class="text-slate-600 leading-relaxed">{!! $html !!}</span>
@endif
