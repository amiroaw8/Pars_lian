<link href="{{ asset('fonts/vazirmatn/misc/Farsi-Digits/Vazirmatn-FD-font-face.css') }}" rel="stylesheet">
<style>
    @page {
        size: A4 portrait;
        margin: 10mm;
    }
    * { box-sizing: border-box; }
    html, body {
        font-family: 'Vazirmatn FD', 'Vazirmatn', Tahoma, sans-serif;
        font-size: 12px;
        line-height: 1.5;
        color: #111;
        margin: 0;
        padding: 0;
        background: #fff;
        -webkit-font-smoothing: antialiased;
        text-rendering: optimizeLegibility;
        font-feature-settings: "kern" 1;
    }
    .print-sheet {
        max-width: 190mm;
        margin: 0 auto;
        padding: 8mm;
        min-height: 0;
    }
    .print-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 2px solid #111;
        padding-bottom: 8px;
        margin-bottom: 12px;
        gap: 12px;
    }
    .print-header-brand {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 4px;
    }
    .print-header .print-doc-logo,
    .print-sheet .print-doc-logo {
        display: block;
        height: 52px;
        width: auto;
        max-width: 180px;
        max-height: 52px;
        object-fit: contain;
        border-radius: 8px;
    }
    .print-doc-type {
        font-size: 13px;
        font-weight: 700;
        color: #334155;
    }
    .print-header h1 {
        margin: 0 0 4px;
        font-size: 18px;
        font-weight: 700;
    }
    .print-meta { font-size: 11px; text-align: left; }
    .print-table {
        width: 100%;
        border-collapse: collapse;
        margin: 10px 0;
        font-size: 11px;
    }
    .print-table th,
    .print-table td {
        border: 1px solid #333;
        padding: 5px 6px;
        text-align: center;
    }
    .print-table th { background: #f3f4f6; font-weight: 700; }
    .print-total {
        font-size: 14px;
        font-weight: 700;
        text-align: left;
        margin-top: 8px;
    }
    .signatures {
        display: flex;
        justify-content: space-between;
        gap: 24px;
        margin-top: 20px;
        page-break-inside: avoid;
    }
    .sign-box {
        flex: 1;
        text-align: center;
        font-size: 11px;
        font-weight: 700;
    }
    .sign-area {
        height: 70px;
        border: 1px solid #333;
        border-radius: 4px;
        margin-top: 8px;
    }
    .no-print { margin-bottom: 12px; }
    @media print {
        .no-print { display: none !important; }
        html, body { height: auto; overflow: hidden; }
        .print-sheet {
            padding: 0;
            max-height: 277mm;
            overflow: hidden;
        }
    }
</style>
