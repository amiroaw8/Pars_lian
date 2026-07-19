<style>
@media print {
    @page { size: A4 portrait; margin: 10mm; }

    .screen-only, .screen-only * { display: none !important; }

    body * { visibility: hidden; }

    .print-only-full, .print-invoice-only {
        display: none !important;
        visibility: hidden;
    }

    body.print-layout-full .print-only-full,
    body.print-layout-full .print-only-full *,
    body.print-layout-invoice .print-invoice-only,
    body.print-layout-invoice .print-invoice-only * {
        visibility: visible;
    }

    body.print-layout-full .print-only-full,
    body.print-layout-invoice .print-invoice-only {
        display: block !important;
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        min-height: auto;
        height: auto;
        max-height: none;
        overflow: visible !important;
        background: #fff;
        z-index: 99999;
        font-family: 'Vazirmatn', Tahoma, sans-serif !important;
        font-size: 10pt;
        line-height: 1.45;
        color: #0f172a;
        box-sizing: border-box;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .prt-sheet { padding: 0; }
    .prt-header { display: flex !important; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #0f172a; padding-bottom: 10px; margin-bottom: 12px; }
    .prt-brand { display: flex !important; gap: 10px; align-items: center; }
    .prt-logo-img { display: block !important; flex-shrink: 0; object-fit: contain; }
    .prt-title { font-size: 16pt; font-weight: 800; margin: 0 0 4px; }
    .prt-sub { font-size: 8.5pt; color: #475569; margin: 0; }
    .prt-meta { text-align: left; }
    .prt-badge { background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 6px; padding: 6px 10px; margin-bottom: 6px; }
    .prt-badge-label { font-size: 8pt; color: #64748b; }
    .prt-badge-value { font-size: 14pt; font-weight: 800; }
    .prt-doc-title { text-align: center; font-size: 12pt; font-weight: 800; margin: 0 0 12px; padding-bottom: 8px; border-bottom: 1px solid #cbd5e1; }
    .prt-grid-2 { display: grid !important; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 12px; }
    .prt-box { border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px; background: #f8fafc; }
    .prt-box-title { font-size: 9pt; font-weight: 800; color: #334155; margin: 0 0 8px; padding-bottom: 4px; border-bottom: 1px dashed #cbd5e1; }
    .prt-row { display: flex !important; justify-content: space-between; font-size: 9pt; padding: 3px 0; border-bottom: 1px dotted #e2e8f0; }
    .prt-row:last-child { border-bottom: none; }
    .prt-row span:first-child { color: #64748b; }
    .prt-row span:last-child { font-weight: 700; }
    .prt-section { margin-bottom: 12px; }
    .prt-section-title { font-size: 10pt; font-weight: 800; margin: 0 0 6px; color: #1e293b; }
    .prt-text-block { border: 1px solid #e2e8f0; border-radius: 6px; padding: 8px; background: #fff; font-size: 9pt; line-height: 1.5; }
    .prt-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
    .prt-table th { background: #0f172a !important; color: #fff !important; font-size: 8.5pt; padding: 6px 8px; text-align: center; }
    .prt-table th:first-child { text-align: right; border-radius: 0 6px 0 0; }
    .prt-table th:last-child { border-radius: 6px 0 0 0; }
    .prt-table td { border: 1px solid #e2e8f0; padding: 5px 8px; font-size: 9pt; }
    .prt-table tfoot td { background: #f1f5f9; font-weight: 800; }
    .prt-terms { font-size: 7.5pt; color: #475569; border: 1px solid #e2e8f0; border-radius: 6px; padding: 8px; background: #f8fafc; }
    .prt-terms ul { margin: 4px 0 0; padding-right: 16px; }
    .prt-signatures { display: grid !important; grid-template-columns: 1fr 1fr; gap: 24px; margin-top: 12px; }
    .prt-sign-box { border: 1px solid #cbd5e1; border-radius: 6px; height: 52px; background: #fafafa; }
    .prt-sign-label { text-align: center; font-size: 8.5pt; font-weight: 700; margin-top: 4px; }
    .prt-footer { margin-top: 10px; padding-top: 8px; border-top: 1px solid #e2e8f0; font-size: 7.5pt; color: #94a3b8; display: flex !important; justify-content: space-between; }
    .prt-stamp-only { max-width: 280px; margin: 20px auto 0; text-align: center; }
    .prt-stamp-box { border: 2px dashed #94a3b8; border-radius: 8px; height: 72px; display: flex !important; align-items: center; justify-content: center; color: #64748b; font-size: 9pt; }
    .prt-invoice-meta { display: flex !important; justify-content: space-between; flex-wrap: wrap; gap: 8px; font-size: 9pt; margin-bottom: 12px; padding: 8px 12px; background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 8px; }
    .prt-total-box { display: flex !important; justify-content: space-between; align-items: center; margin: 14px 0; padding: 12px 16px; background: #0f172a !important; color: #fff !important; border-radius: 8px; font-size: 11pt; font-weight: 800; }
    .prt-total-box small { font-size: 9pt; font-weight: 400; opacity: 0.85; }
    body, html { background: #fff !important; margin: 0 !important; padding: 0 !important; }
}
</style>
