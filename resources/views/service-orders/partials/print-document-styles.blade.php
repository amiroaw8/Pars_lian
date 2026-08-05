<style>
    .prt-grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-bottom: 12px;
    }
    .prt-box {
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        padding: 8px 10px;
        background: #f8fafc;
        font-size: 11px;
    }
    .prt-box-title {
        font-size: 11px;
        font-weight: 700;
        color: #334155;
        margin: 0 0 6px;
        padding-bottom: 4px;
        border-bottom: 1px dashed #cbd5e1;
    }
    .prt-row {
        display: flex;
        justify-content: space-between;
        gap: 8px;
        padding: 2px 0;
        font-size: 11px;
        border-bottom: 1px dotted #e2e8f0;
    }
    .prt-row:last-child { border-bottom: none; }
    .prt-row span:first-child { color: #64748b; }
    .prt-row span:last-child { font-weight: 700; text-align: left; }
    .prt-section { margin-bottom: 12px; }
    .prt-section-title {
        font-size: 12px;
        font-weight: 700;
        margin: 0 0 6px;
        color: #1e293b;
    }
    .prt-text-block {
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 8px;
        background: #fff;
        font-size: 11px;
        line-height: 1.6;
    }
    .prt-doc-title {
        text-align: center;
        font-size: 14px;
        font-weight: 700;
        margin: 0 0 12px;
        padding-bottom: 8px;
        border-bottom: 1px solid #cbd5e1;
    }
    .prt-invoice-meta {
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 8px;
        font-size: 11px;
        margin-bottom: 12px;
        padding: 8px 10px;
        background: #f1f5f9;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
    }
    .prt-total-box {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: 12px 0;
        padding: 10px 14px;
        background: #0f172a;
        color: #fff;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 700;
    }
    .prt-total-box small { font-size: 11px; font-weight: 400; opacity: 0.9; }
    .prt-terms {
        font-size: 10px;
        color: #475569;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 8px;
        background: #f8fafc;
        margin-bottom: 12px;
    }
    .prt-terms ul { margin: 4px 0 0; padding-right: 16px; }
    .prt-signatures {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-top: 12px;
    }
    .prt-sign-box {
        border: 1px solid #333;
        border-radius: 4px;
        height: 60px;
        background: #fafafa;
    }
    .prt-sign-label {
        text-align: center;
        font-size: 11px;
        font-weight: 700;
        margin: 4px 0 0;
    }
    .prt-stamp-only { max-width: 260px; margin: 16px auto 0; text-align: center; }
    .prt-stamp-box {
        border: 2px dashed #94a3b8;
        border-radius: 6px;
        height: 68px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #64748b;
        font-size: 11px;
    }
    .prt-footer {
        margin-top: 12px;
        padding-top: 8px;
        border-top: 1px solid #e2e8f0;
        font-size: 10px;
        color: #64748b;
        display: flex;
        justify-content: space-between;
        gap: 12px;
    }
    .prt-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
        border-bottom: 2px solid #111;
        padding-bottom: 8px;
        margin-bottom: 12px;
    }
    .prt-brand {
        display: flex;
        gap: 10px;
        align-items: center;
        min-width: 0;
    }
    .prt-brand .print-doc-logo,
    .print-sheet .print-doc-logo {
        display: block;
        flex-shrink: 0;
        height: 52px;
        width: auto;
        max-width: 180px;
        max-height: 52px;
        object-fit: contain;
        border-radius: 8px;
    }
    .print-sheet img {
        max-width: 180px;
        max-height: 52px;
        height: auto;
        object-fit: contain;
    }
    .prt-sub { font-size: 10px; color: #475569; margin: 2px 0 0; line-height: 1.5; }
    .prt-meta { text-align: left; font-size: 11px; }
    .prt-badge {
        background: #f1f5f9;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        padding: 6px 10px;
    }
    .prt-badge-label { font-size: 10px; color: #64748b; }
    .prt-badge-value { font-size: 16px; font-weight: 800; }
    .prt-meta-date { font-size: 10px; color: #64748b; margin-top: 4px; text-align: left; }
    .prt-ltr { direction: ltr; unicode-bidi: embed; display: inline-block; }

    /* رسید سفارش — یک صفحه A4 + برچسب برشی */
    .prt-order-receipt {
        max-height: 277mm;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        page-break-inside: avoid;
    }
    .prt-triple-receipt { gap: 0; }
    .prt-receipt-section { padding-bottom: 4px; }
    .prt-receipt-section-mini { padding-top: 2px; }
    .prt-doc-title-mini {
        font-size: 11px;
        margin-bottom: 6px;
        padding-bottom: 4px;
    }
    .prt-receipt-main { flex: 1; min-height: 0; }
    .prt-order-receipt .prt-section { margin-bottom: 8px; }
    .prt-order-receipt .prt-grid-2 { margin-bottom: 8px; gap: 8px; }
    .prt-grid-compact .prt-section { margin-bottom: 0; }
    .prt-text-compact {
        padding: 5px 7px;
        font-size: 10px;
        line-height: 1.45;
        max-height: 3.2em;
        overflow: hidden;
    }
    .prt-table-compact th,
    .prt-table-compact td { padding: 3px 5px; font-size: 10px; }
    .prt-terms-compact {
        font-size: 9px;
        padding: 5px 7px;
        margin-bottom: 8px;
    }
    .prt-terms-expanded {
        font-size: 10px;
        line-height: 1.55;
        padding: 8px 10px;
        margin-bottom: 10px;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        background: #f8fafc;
        color: #475569;
    }
    .prt-terms-expanded ul {
        margin: 6px 0 0;
        padding-right: 18px;
    }
    .prt-terms-expanded li { margin-bottom: 3px; }
    .prt-signatures-compact { margin-top: 6px; gap: 12px; }
    .prt-signatures-compact .prt-sign-box { height: 42px; }
    .prt-signatures-compact .prt-sign-label { font-size: 10px; }

    .prt-tear-line {
        flex-shrink: 0;
        border-top: 2px dashed #64748b;
        margin: 8px 0 6px;
        height: 0;
    }

    .prt-mini-stub {
        flex-shrink: 0;
        display: grid;
        grid-template-columns: minmax(88px, 1fr) minmax(72px, 120px) minmax(100px, 1.1fr) minmax(120px, 1.6fr);
        gap: 8px;
        align-items: start;
        border: 1px dashed #94a3b8;
        border-radius: 6px;
        padding: 8px 10px;
        background: #f8fafc;
        font-size: 9px;
        line-height: 1.45;
        min-height: 56px;
    }
    .prt-mini-col {
        display: flex;
        flex-direction: column;
        gap: 2px;
        min-width: 0;
    }
    .prt-mini-col strong, .prt-mini-strong { font-size: 10px; font-weight: 800; }
    .prt-mini-phone { font-weight: 700; font-size: 10px; }
    .prt-mini-order {
        text-align: center;
        border-right: 1px dashed #cbd5e1;
        border-left: 1px dashed #cbd5e1;
        padding: 0 6px;
    }
    .prt-mini-label { font-size: 8px; color: #64748b; }
    .prt-mini-id {
        font-size: clamp(14px, 3.5vw, 24px);
        font-weight: 900;
        line-height: 1.1;
        color: #0f172a;
        word-break: break-all;
        letter-spacing: -0.02em;
    }
    .prt-mini-device span,
    .prt-mini-faults span {
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        line-clamp: 2;
        -webkit-box-orient: vertical;
        white-space: normal;
    }
    .prt-mini-faults strong { color: #334155; }
    .prt-mini-only { max-height: none; }

    @media print {
        .prt-grid-2 { page-break-inside: avoid; }
        .prt-signatures { page-break-inside: avoid; }
        .prt-order-receipt {
            max-height: 277mm;
            overflow: hidden;
        }
        .prt-tear-line,
        .prt-mini-stub { page-break-inside: avoid; }
    }
</style>
