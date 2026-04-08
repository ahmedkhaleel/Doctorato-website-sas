<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\DemoRequest;
use App\Models\Invoice;
use App\Models\Subscription;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    /** CSV export — subscriptions. */
    public function subscriptions(): StreamedResponse
    {
        $headers = [
            'رقم الاشتراك', 'اسم العميل', 'البريد', 'الهاتف', 'العيادة',
            'الباقة', 'الدورة', 'المبلغ', 'العملة', 'كوبون', 'الخصم',
            'الحالة', 'أُنشئ في', 'بدأ في', 'ينتهي في',
        ];

        return $this->stream('subscriptions-' . now()->format('Y-m-d') . '.csv', $headers, function () {
            foreach (Subscription::with('plan:id,name_ar,name_en')->latest('id')->lazy(500) as $s) {
                yield [
                    $s->reference,
                    $s->customer_name,
                    $s->customer_email,
                    $s->customer_phone,
                    $s->clinic_name,
                    $s->plan?->name_ar ?: $s->plan?->name_en,
                    $s->billing_cycle,
                    $s->amount,
                    $s->currency,
                    $s->coupon_code,
                    $s->discount_amount,
                    $s->status,
                    $s->created_at?->format('Y-m-d H:i'),
                    $s->starts_at?->format('Y-m-d H:i'),
                    $s->ends_at?->format('Y-m-d H:i'),
                ];
            }
        });
    }

    /** CSV export — invoices. */
    public function invoices(): StreamedResponse
    {
        $headers = [
            'رقم الفاتورة', 'رقم الاشتراك', 'العميل', 'الفرعي', 'الخصم',
            'الضريبة', 'الإجمالي', 'العملة', 'الحالة', 'تاريخ الدفع', 'أُنشئت في',
        ];

        return $this->stream('invoices-' . now()->format('Y-m-d') . '.csv', $headers, function () {
            foreach (Invoice::with('subscription:id,reference,customer_name')->latest('id')->lazy(500) as $i) {
                yield [
                    $i->number,
                    $i->subscription?->reference,
                    $i->subscription?->customer_name,
                    $i->subtotal,
                    $i->discount,
                    $i->tax,
                    $i->total,
                    $i->currency,
                    $i->status,
                    $i->paid_at?->format('Y-m-d H:i'),
                    $i->created_at->format('Y-m-d H:i'),
                ];
            }
        });
    }

    /** CSV export — demo requests. */
    public function demos(): StreamedResponse
    {
        $headers = [
            'ID', 'الاسم', 'العيادة', 'البريد', 'الهاتف',
            'عدد الأطباء', 'التخصص', 'الحالة', 'حالة التجربة', 'أُرسل في',
        ];

        return $this->stream('demo-requests-' . now()->format('Y-m-d') . '.csv', $headers, function () {
            foreach (DemoRequest::latest('id')->lazy(500) as $d) {
                yield [
                    $d->id,
                    $d->full_name,
                    $d->clinic_name,
                    $d->email,
                    $d->phone,
                    $d->doctors_count ?? null,
                    $d->specialty ?? null,
                    $d->status,
                    $d->trial_status ?? null,
                    $d->created_at->format('Y-m-d H:i'),
                ];
            }
        });
    }

    /** CSV export — contact messages. */
    public function contacts(): StreamedResponse
    {
        $headers = ['ID', 'الاسم', 'البريد', 'الهاتف', 'الموضوع', 'الرسالة', 'مقروءة؟', 'أُرسلت في'];

        return $this->stream('contacts-' . now()->format('Y-m-d') . '.csv', $headers, function () {
            foreach (ContactMessage::latest('id')->lazy(500) as $c) {
                yield [
                    $c->id,
                    $c->name,
                    $c->email,
                    $c->phone,
                    $c->subject,
                    $c->message,
                    $c->is_read ? 'yes' : 'no',
                    $c->created_at->format('Y-m-d H:i'),
                ];
            }
        });
    }

    /**
     * Streams a CSV with the given headers. The callback is a generator
     * that yields arrays of row values — this keeps memory flat even for
     * tens of thousands of rows.
     */
    protected function stream(string $filename, array $headers, \Closure $rowsCallback): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $rowsCallback) {
            $out = fopen('php://output', 'w');
            // UTF-8 BOM so Excel opens Arabic correctly
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, $headers);

            foreach ($rowsCallback() ?? [] as $row) {
                fputcsv($out, $row);
            }
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
