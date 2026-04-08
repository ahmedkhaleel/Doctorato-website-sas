<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>{{ $invoice->number }}</title>
    <style>
        @page { size: A4; margin: 20mm; }
        * { box-sizing: border-box; font-family: 'Helvetica Neue', Arial, sans-serif; }
        body { color: #1C2833; background: #fff; font-size: 12px; line-height: 1.5; margin: 0; padding: 0; }
        .invoice { max-width: 800px; margin: 0 auto; padding: 20px; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; padding-bottom: 24px; border-bottom: 3px solid #1B4F72; }
        .brand h1 { margin: 0 0 4px; font-size: 24px; color: #1B4F72; font-weight: 800; }
        .brand p { margin: 0; color: #566573; font-size: 11px; }
        .meta { text-align: left; }
        .meta h2 { margin: 0 0 4px; font-size: 22px; color: #1C2833; }
        .meta .num { font-family: monospace; color: #C4A265; font-weight: bold; font-size: 14px; }
        .status { display: inline-block; padding: 4px 12px; border-radius: 9999px; font-size: 10px; font-weight: bold; text-transform: uppercase; margin-top: 6px; }
        .status-paid { background: #d1fae5; color: #065f46; }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-failed { background: #fee2e2; color: #991b1b; }
        .status-cancelled { background: #f3f4f6; color: #4b5563; }
        .parties { display: flex; gap: 32px; margin: 28px 0; }
        .party { flex: 1; }
        .party .label { font-size: 10px; text-transform: uppercase; color: #9ca3af; letter-spacing: 1px; margin-bottom: 4px; }
        .party .name { font-weight: bold; font-size: 14px; color: #1C2833; }
        .party .line { color: #566573; font-size: 11px; margin-top: 2px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th { background: #f9fafb; text-align: right; padding: 10px; font-size: 10px; text-transform: uppercase; color: #6b7280; font-weight: bold; border-bottom: 2px solid #e5e7eb; letter-spacing: 0.5px; }
        td { padding: 12px 10px; border-bottom: 1px solid #e5e7eb; font-size: 12px; }
        .totals { margin-top: 20px; display: flex; justify-content: flex-end; }
        .totals table { width: 280px; }
        .totals td { padding: 6px 10px; border: none; }
        .totals .label { color: #566573; }
        .totals .amount { text-align: left; font-weight: bold; color: #1C2833; }
        .totals .grand { border-top: 2px solid #1B4F72; padding-top: 10px; font-size: 16px; }
        .totals .grand .amount { color: #1B4F72; }
        .footer { margin-top: 40px; padding-top: 20px; border-top: 1px solid #e5e7eb; text-align: center; font-size: 10px; color: #9ca3af; }
        .no-print { margin: 20px auto; max-width: 800px; padding: 20px; text-align: center; background: #fffbeb; border: 1px solid #fef3c7; border-radius: 8px; }
        .no-print button { padding: 10px 24px; background: #1B4F72; color: white; border: none; border-radius: 8px; font-size: 14px; font-weight: bold; cursor: pointer; margin: 0 4px; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()">🖨 طباعة / حفظ كـ PDF</button>
        <button onclick="window.close()" style="background: #e5e7eb; color: #1C2833;">إغلاق</button>
    </div>

    <div class="invoice">
        <div class="header">
            <div class="brand">
                <h1>Doctorato</h1>
                <p>نظام إدارة العيادات الطبية المتكامل</p>
                <p>{{ config('app.url') }}</p>
            </div>
            <div class="meta">
                <h2>فاتورة</h2>
                <div class="num">{{ $invoice->number }}</div>
                @php
                    $statusClass = 'status-' . $invoice->status;
                    $statusLabel = ['paid' => 'مدفوعة', 'pending' => 'قيد الانتظار', 'failed' => 'فشلت', 'cancelled' => 'ملغاة', 'draft' => 'مسودة', 'refunded' => 'مستردة'][$invoice->status] ?? $invoice->status;
                @endphp
                <div class="status {{ $statusClass }}">{{ $statusLabel }}</div>
                <div style="margin-top: 8px; font-size: 11px; color: #566573;">
                    تاريخ الإصدار: {{ $invoice->created_at->format('d M Y') }}
                    @if($invoice->paid_at)
                        <br>تاريخ الدفع: {{ $invoice->paid_at->format('d M Y') }}
                    @endif
                </div>
            </div>
        </div>

        <div class="parties">
            <div class="party">
                <div class="label">من</div>
                <div class="name">Doctorato — Markeza Group</div>
                <div class="line">مصر</div>
            </div>
            <div class="party">
                <div class="label">إلى</div>
                <div class="name">{{ $invoice->subscription->customer_name ?? '—' }}</div>
                @if($invoice->subscription)
                    @if($invoice->subscription->clinic_name)
                        <div class="line">{{ $invoice->subscription->clinic_name }}</div>
                    @endif
                    <div class="line">{{ $invoice->subscription->customer_email }}</div>
                    <div class="line">{{ $invoice->subscription->customer_phone }}</div>
                    @if($invoice->subscription->city)
                        <div class="line">{{ $invoice->subscription->city }}, {{ $invoice->subscription->country }}</div>
                    @endif
                @endif
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>البند</th>
                    <th style="text-align: center;">الكمية</th>
                    <th style="text-align: left;">السعر</th>
                    <th style="text-align: left;">الإجمالي</th>
                </tr>
            </thead>
            <tbody>
                @foreach(($invoice->line_items ?? []) as $item)
                    <tr>
                        <td>{{ $item['name'] ?? '—' }}</td>
                        <td style="text-align: center;">{{ $item['qty'] ?? 1 }}</td>
                        <td style="text-align: left;">{{ number_format((float) ($item['unit_price'] ?? $item['total'] ?? 0), 2) }}</td>
                        <td style="text-align: left;">{{ number_format((float) ($item['total'] ?? 0), 2) }}</td>
                    </tr>
                @endforeach
                @if(empty($invoice->line_items))
                    <tr>
                        <td>اشتراك {{ $invoice->subscription->plan->name_ar ?? 'دكتوراتو' }}</td>
                        <td style="text-align: center;">1</td>
                        <td style="text-align: left;">{{ number_format((float) $invoice->subtotal, 2) }}</td>
                        <td style="text-align: left;">{{ number_format((float) $invoice->subtotal, 2) }}</td>
                    </tr>
                @endif
            </tbody>
        </table>

        <div class="totals">
            <table>
                <tr>
                    <td class="label">المجموع الفرعي</td>
                    <td class="amount">{{ number_format((float) $invoice->subtotal, 2) }} {{ $invoice->currency }}</td>
                </tr>
                @if((float) $invoice->discount > 0)
                    <tr>
                        <td class="label">الخصم</td>
                        <td class="amount" style="color: #10b981;">- {{ number_format((float) $invoice->discount, 2) }} {{ $invoice->currency }}</td>
                    </tr>
                @endif
                @if((float) $invoice->tax > 0)
                    <tr>
                        <td class="label">الضريبة</td>
                        <td class="amount">{{ number_format((float) $invoice->tax, 2) }} {{ $invoice->currency }}</td>
                    </tr>
                @endif
                <tr class="grand">
                    <td class="label">المستحق</td>
                    <td class="amount">{{ number_format((float) $invoice->total, 2) }} {{ $invoice->currency }}</td>
                </tr>
            </table>
        </div>

        <div class="footer">
            <p>شكراً لثقتكم في Doctorato</p>
            <p>هذه فاتورة إلكترونية ولا تحتاج إلى توقيع أو ختم</p>
        </div>
    </div>
</body>
</html>
