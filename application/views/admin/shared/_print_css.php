<style>
/* ============================================================
   Smart Hospital — Shared Print CSS
   Include at top of every print view:
   include(APPPATH . 'views/admin/shared/_print_css.php');
   ============================================================ */

* { box-sizing: border-box; }
body {
  font-family: 'Inter', Arial, sans-serif;
  font-size: 12px;
  color: #111;
  margin: 0;
  padding: 0;
  line-height: 1.4;
}

/* ── Existing system classes (keep working) ───────── */
.fixed-print-header { height: 100px; width: 100%; overflow: hidden; }
.fixed-print-header img { height: 100px; width: 100%; display: block; object-fit: cover; }
.header-space  { height: 100px; }
.footer-space  { height: 70px; }
.table-print-full { width: 100%; border-collapse: collapse; }
.print-area { padding: 0; }
.content-body { padding: 0 12px; }

.footer-fixed {
  position: fixed; bottom: 0; width: 100%;
  display: flex; align-items: center; justify-content: center;
  padding: 0 10px; overflow: hidden;
}

/* Old print-table / noborder_table stay working */
.print-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
.print-table th, .print-table td { font-size: 11px; padding: 4px 6px; border-top: 1px solid #ccc; vertical-align: top; }
.print-table thead th { font-weight: 600; border-top: 2px solid #999; border-bottom: 1px solid #999; }
.noborder_table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
.noborder_table th, .noborder_table td { font-size: 11px; padding: 3px 6px; vertical-align: top; border: none; }
.heading-title { font-size: 13px; font-weight: 600; margin: 10px 0 6px; padding-left: 4px; color: #111; }
.divider { height: 1px; border-top: 1px solid #ccc; margin: 6px 0; }

/* col grid for print */
.col-md-1,.col-md-2,.col-md-3,.col-md-4,.col-md-5,.col-md-6,
.col-md-7,.col-md-8,.col-md-9,.col-md-10,.col-md-11,.col-md-12 { float: left; }
.col-md-12{width:100%} .col-md-11{width:91.66%} .col-md-10{width:83.33%}
.col-md-9{width:75%}   .col-md-8{width:66.66%}  .col-md-7{width:58.33%}
.col-md-6{width:50%}   .col-md-5{width:41.66%}  .col-md-4{width:33.33%}
.col-md-3{width:25%}   .col-md-2{width:16.66%}  .col-md-1{width:8.33%}
.clear { clear: both; }
.text-end, .text-right { text-align: right; }
.text-center { text-align: center; }
.no-line { border-top: none !important; }

/* ── NEW sh-print-* design classes ───────────────── */
.sh-print-title {
  text-align: center;
  font-size: 13px;
  font-weight: 700;
  letter-spacing: 3px;
  text-transform: uppercase;
  padding: 8px 0 7px;
  border-top: 2px solid #111;
  border-bottom: 1px solid #111;
  margin-bottom: 14px;
}
.sh-print-info-block {
  border: 1px solid #a1a1aa;
  border-radius: 3px;
  padding: 9px 12px 5px;
  margin-bottom: 14px;
  background: #f9fafb;
}
.sh-print-info-table {
  width: 100%;
  border-collapse: collapse;
  table-layout: fixed;
}
.sh-print-info-table tr { vertical-align: top; }
.sh-print-info-table th {
  font-size: 9.5px;
  font-weight: 500;
  color: #111;
  white-space: normal;
  word-break: break-word;
  padding: 3px 5px 3px 0;
  text-align: right;
}
.sh-print-info-table th::after { content: ' :'; color: #111; font-weight: 400; }
.sh-print-info-table th:empty::after { content: ''; }
.sh-print-info-table td {
  font-size: 11px;
  font-weight: 700;
  padding: 3px 10px 3px 3px;
  color: #111;
}

/* Two-column flex layout for paired info tables (50/50 split) */
.sh-flex-gap18 {
  display: flex;
  gap: 18px;
  align-items: flex-start;
}
.sh-flex-gap18 > .sh-print-info-table.w-50 {
  flex: 1 1 0;
  min-width: 0;
  width: auto;
}
.sh-print-info-table.w-50 { width: 50%; }
.sh-px-12 { padding-left: 12px; padding-right: 12px; }
.sh-col-22 { width: 22%; }
.sh-text-right { text-align: right; }
.sh-section-divider {
  border-top: 1px solid #cbd5e1;
  margin: 10px 0 8px;
}
.sh-note-box {
  border: 1px solid #cbd5e1;
  border-left: 3px solid #94a3b8;
  background: #f9fafb;
  padding: 6px 10px;
  font-size: 11px;
  color: #111;
  margin-top: 8px;
  border-radius: 2px;
}
/* Bootstrap utility fallbacks for isolated print window */
.lh-normal     { line-height: normal; }
.fw-semibold   { font-weight: 600; }
.p-0           { padding: 0 !important; }

/* ── Content utilities (ported from sh-theme.css — NOT loaded in the
      print iframe, so they must live here to reach the print window) ── */
.sh-flex-row-g20-mb10 { display: flex; gap: 20px; margin-bottom: 10px; }
.sh-flex-row-g20-mb14 { display: flex; gap: 20px; margin-bottom: 14px; }
.sh-flex-text { flex: 1; font-size: 11px; color: #1e293b; line-height: 1.6; }
.sh-label-mini { font-size: 10px; font-weight: 500; color: #111; margin-bottom: 4px; }
.sh-value-cell {
  background: #fcfcfc;
  border-left: 2px solid #cbd5e1;
  padding: 4px 10px;
  font-size: 11.5px;
  font-weight: 700;
  color: #111;
}
.sh-label-dashed {
  font-size: 10px;
  font-weight: 500;
  color: #111;
  margin-bottom: 6px;
  border-bottom: 1px dashed #a1a1aa;
  padding-bottom: 2px;
}
.sh-list-inline-bold {
  margin: 0; padding: 0; list-style: none;
  font-size: 11.5px; font-weight: 700; color: #111;
}
.sh-mb-3px { margin-bottom: 3px; }
.sh-text-11-pad { font-size: 11px; color: #111; line-height: 1.6; margin-bottom: 14px; padding: 0 4px; }
/* Plain value / sub-label — simple flat style (no box, no dashed rule) */
.sh-print-value { font-size: 11.5px; font-weight: 600; color: #111; line-height: 1.5; }
.sh-print-sublabel { font-size: 10px; font-weight: 600; color: #555; margin-bottom: 5px; }
.sh-border-top-light { border-top: 1px solid #e2e8f0; margin-top: 8px; }
.sh-avatar-cover { height: 100px; width: 100%; display: block; object-fit: cover; }
.flex-fill { flex: 1 1 auto; }

.sh-print-section-title {
  font-size: 9.5px;
  font-weight: 700;
  color: #111;
  border-bottom: 1px solid #a1a1aa;
  padding-bottom: 4px;
  margin: 14px 0 8px;
}
.sh-print-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 11px;
  margin-bottom: 4px;
}
.sh-print-table thead th {
  background: #f1f5f9;
  border-top: 1px solid #94a3b8;
  border-bottom: 1px solid #94a3b8;
  padding: 6px 8px;
  font-weight: 700;
  font-size: 9.5px;
  color: #111;
  text-align: left;
}
.sh-print-table tbody td {
  padding: 6px 8px;
  border-bottom: 1px solid #cbd5e1;
  vertical-align: top;
  color: #111;
}
.sh-print-table tbody tr:last-child td { border-bottom: 1px solid #94a3b8; }
.sh-print-table tbody td small {
  display: block;
  color: #94a3b8;
  font-size: 9.5px;
  margin-top: 2px;
}
.sh-print-table tfoot td {
  padding: 3px 8px;
  border: none;
  text-align: right;
  font-size: 11px;
  color: #111;
}
.sh-print-table tfoot .sh-row-first td { padding-top: 7px; }
.sh-print-table tfoot .sh-row-total td {
  border-top: 2px solid #111;
  font-weight: 700;
  font-size: 12px;
  color: #111;
  padding-top: 6px;
}

/* ── @media print overrides ───────────────────────── */
@media print {
  * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
  @page { margin: 12mm; }
  .fixed-print-header { position: fixed; top: 0; width: 100%; z-index: 10; }
  .footer-fixed { position: fixed; bottom: 0; width: 100%; background: #fff; }
  .sh-print-info-block { background: #f9fafb !important; page-break-inside: avoid; }
  .sh-print-table thead th { background: #f1f5f9 !important; }
  .no-print { display: none !important; }
}

/* ── Dark mode overrides (screen/modal view only) ─── */
@media screen {
  body.variant-b .sh-print-info-block { background: var(--surface-2) !important; border-color: var(--border) !important; }
  body.variant-b .sh-print-info-table th,
  body.variant-b .sh-print-info-table th::after { color: var(--muted) !important; }
  body.variant-b .sh-print-info-table td { color: var(--ink) !important; }
  body.variant-b .sh-print-title { border-color: var(--border) !important; color: var(--ink) !important; }
  body.variant-b .sh-print-section-title { color: var(--ink-2) !important; border-bottom-color: var(--border) !important; }
  body.variant-b .sh-print-table thead th { background: var(--surface-2) !important; border-color: var(--border) !important; color: var(--ink) !important; }
  body.variant-b .sh-print-table tbody td { border-bottom-color: var(--border) !important; color: var(--ink) !important; }
  body.variant-b .sh-print-table tbody tr:last-child td { border-bottom-color: var(--border) !important; }
  body.variant-b .sh-print-table tbody td small { color: var(--muted) !important; }
  body.variant-b .sh-print-table tfoot td { color: var(--ink) !important; }
  body.variant-b .sh-print-table tfoot .sh-row-total td { border-top-color: var(--border) !important; color: var(--ink) !important; }
  body.variant-b .heading-title { color: var(--ink) !important; }
  body.variant-b .print-table th,
  body.variant-b .print-table td { border-color: var(--border) !important; color: var(--ink) !important; }
}
</style>
