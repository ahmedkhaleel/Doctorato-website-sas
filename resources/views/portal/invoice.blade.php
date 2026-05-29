<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Invoice {{ $invoice->number }} — Doctorato</title>
    <style>
        :root { color-scheme: light; }
        body {
            margin: 0; padding: 40px 16px;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #1C2833; background: #F4F1EA;
            -webkit-font-smoothing: antialiased;
        }
        .page {
            max-width: 720px; margin: 0 auto; background: #fff;
            border-radius: 16px; padding: 48px 56px;
            box-shadow: 0 1px 3px rgba(13,43,69,0.04), 0 12px 32px rgba(13,43,69,0.06);
        }
        .actions {
            max-width: 720px; margin: 0 auto 16px;
            display: flex; justify-content: space-between; align-items: center;
        }
        .actions a, .actions button {
            font-size: 13px; color: #1B4F72; text-decoration: none; font-weight: 600;
            display: inline-flex; align-items: center; gap: 6px;
            border: 1px solid #D6DBE2; background: #fff; padding: 9px 16px;
            border-radius: 8px; cursor: pointer;
        }
        .actions .primary { background: #0A1628; color: #fff; border-color: #0A1628; }
        header.brand {
            display: flex; justify-content: space-between; align-items: flex-start;
            padding-bottom: 32px; border-bottom: 1px solid #EEE6D4; margin-bottom: 32px;
        }
        .brand-mark { font-size: 22px; font-weight: 700; letter-spacing: -0.01em; color: #0A1628; }
        .brand-sub { font-size: 11px; letter-spacing: 0.18em; text-transform: uppercase; color: #C4A265; font-weight: 600; margin-top: 4px; }
        .meta { text-align: right; font-size: 12px; color: #5A6C7D; line-height: 1.7; }
        .meta strong { color: #1C2833; }
        h1.invoice-no {
            font-size: 32px; margin: 0 0 4px; font-weight: 700; letter-spacing: -0.02em;
            color: #0A1628;
        }
        .status {
            display: inline-block; font-size: 10px; font-weight: 700;
            letter-spacing: 0.18em; text-transform: uppercase;
            padding: 4px 10px; border-radius: 999px;
        }
        .status.paid    { background: #d1fae5; color: #065f46; }
        .status.pending { background: #dbeafe; color: #1e40af; }
        .status.failed  { background: #fee2e2; color: #991b1b; }
        .status.refunded{ background: #f3f4f6; color: #4b5563; }

        section { margin-bottom: 28px; }
        .label {
            font-size: 10px; font-weight: 700; letter-spacing: 0.2em;
            text-transform: uppercase; color: #C4A265; margin-bottom: 8px;
        }
        .two-col {
            display: grid; grid-template-columns: 1fr 1fr; gap: 24px;
        }
        .field-label { font-size: 11px; color: #8B9BAC; margin-bottom: 2px; }
        .field-value { font-size: 14px; color: #1C2833; font-weight: 500; }

        table.items {
            width: 100%; border-collapse: collapse; margin-top: 12px;
        }
        table.items th {
            text-align: left; font-size: 11px; font-weight: 700;
            color: #8B9BAC; letter-spacing: 0.06em; text-transform: uppercase;
            padding: 12px 0; border-bottom: 2px solid #EEE6D4;
        }
        table.items td {
            padding: 14px 0; font-size: 14px; color: #1C2833;
            border-bottom: 1px solid #F0EBDF;
        }
        table.items td.r, table.items th.r { text-align: right; }
        table.items td.qty { color: #5A6C7D; }
        .totals {
            margin-top: 16px; margin-left: auto; max-width: 320px;
            font-size: 14px;
        }
        .totals-row {
            display: flex; justify-content: space-between;
            padding: 8px 0;
        }
        .totals-row.grand {
            border-top: 2px solid #0A1628; margin-top: 8px;
            padding-top: 14px; font-size: 16px; font-weight: 700;
            color: #0A1628;
        }
        footer.fine {
            margin-top: 40px; padding-top: 24px; border-top: 1px solid #EEE6D4;
            font-size: 11px; color: #8B9BAC; line-height: 1.7;
        }

        /* Print-only: hide the action bar, fit one page, remove background */
        @media print {
            body { background: #fff; padding: 0; }
            .page { box-shadow: none; border-radius: 0; padding: 24px 32px; max-width: 100%; }
            .actions { display: none; }
            @page { margin: 16mm; }
        }
    </style>
</head>
<body>

<div class="actions">
    <a href="{{ url('/portal/dashboard') }}">← Back to dashboard</a>
    <button class="primary" onclick="window.print()">Print / Save as PDF</button>
</div>

<div class="page">
    <header class="brand">
        <div>
            <div class="brand-mark">Doctorato</div>
            <div class="brand-sub">Tax Invoice</div>
        </div>
        <div class="meta">
            <h1 class="invoice-no">{{ $invoice->number }}</h1>
            <span class="status {{ $invoice->status }}">{{ $invoice->status }}</span>
        </div>
    </header>

    <section>
        <div class="two-col">
            <div>
                <div class="label">Billed to</div>
                <p class="field-value" style="margin:0 0 2px;">{{ $customer->clinic_name }}</p>
                <p class="field-label" style="margin:0;">{{ $customer->full_name }}</p>
                <p class="field-label" style="margin:0;">{{ $customer->email }}</p>
                @if($customer->phone)
                    <p class="field-label" style="margin:0;" dir="ltr">{{ $customer->phone }}</p>
                @endif
            </div>
            <div>
                <div class="label">Invoice details</div>
                <div style="display:grid;grid-template-columns:auto 1fr;gap:6px 14px;">
                    <span class="field-label">Issued</span>
                    <span class="field-value">{{ $invoice->created_at->format('M j, Y') }}</span>
                    @if($invoice->due_at)
                        <span class="field-label">Due</span>
                        <span class="field-value">{{ $invoice->due_at->format('M j, Y') }}</span>
                    @endif
                    @if($invoice->paid_at)
                        <span class="field-label">Paid</span>
                        <span class="field-value">{{ $invoice->paid_at->format('M j, Y') }}</span>
                    @endif
                    <span class="field-label">Plan</span>
                    <span class="field-value">{{ $subscription->plan->name_en ?? '—' }} ({{ $subscription->billing_cycle }})</span>
                </div>
            </div>
        </div>
    </section>

    <section>
        <div class="label">Line items</div>
        <table class="items">
            <thead>
                <tr>
                    <th>Description</th>
                    <th class="r" style="width:120px;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @php
                    // The line_items column is a JSON cast; if it's empty we
                    // fall back to a synthesized "Plan subscription" row so
                    // even legacy invoices render usefully.
                    $items = is_array($invoice->line_items) && count($invoice->line_items)
                        ? $invoice->line_items
                        : [[
                            'label' => ($subscription->plan->name_en ?? 'Subscription') . ' — ' . ucfirst($subscription->billing_cycle ?? 'monthly'),
                            'amount' => $invoice->subtotal,
                        ]];
                @endphp
                @foreach($items as $item)
                    <tr>
                        <td>{{ $item['label'] ?? '—' }}</td>
                        <td class="r" dir="ltr">{{ number_format((float) ($item['amount'] ?? 0), 2) }} {{ $invoice->currency }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </section>

    <section>
        <div class="totals">
            <div class="totals-row">
                <span>Subtotal</span>
                <span dir="ltr">{{ number_format((float) $invoice->subtotal, 2) }} {{ $invoice->currency }}</span>
            </div>
            @if((float) $invoice->setup_fee_amount > 0)
                <div class="totals-row">
                    <span>Setup fee</span>
                    <span dir="ltr">{{ number_format((float) $invoice->setup_fee_amount, 2) }} {{ $invoice->currency }}</span>
                </div>
            @endif
            @if((float) $invoice->discount > 0)
                <div class="totals-row">
                    <span>Discount</span>
                    <span dir="ltr">- {{ number_format((float) $invoice->discount, 2) }} {{ $invoice->currency }}</span>
                </div>
            @endif
            @if((float) $invoice->tax > 0)
                <div class="totals-row">
                    <span>Tax</span>
                    <span dir="ltr">{{ number_format((float) $invoice->tax, 2) }} {{ $invoice->currency }}</span>
                </div>
            @endif
            <div class="totals-row grand">
                <span>Total</span>
                <span dir="ltr">{{ number_format((float) $invoice->total, 2) }} {{ $invoice->currency }}</span>
            </div>
        </div>
    </section>

    <footer class="fine">
        Generated by Doctorato — clinic management software for the Middle East.<br>
        Questions about this invoice? Email <strong>info@doctorato.com</strong> or visit <strong>doctorato.com</strong>.<br>
        This invoice was issued electronically and is valid without a signature.
    </footer>
</div>

@if($autoPrint)
    {{-- Auto-trigger the print dialog when the customer hit the
         "Download" button on the dashboard. The setTimeout gives
         the browser a tick to lay out the page first. --}}
    <script>setTimeout(() => window.print(), 250);</script>
@endif

</body>
</html>
