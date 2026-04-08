<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $invoices = Invoice::query()
            ->with(['subscription:id,reference,customer_name,customer_email'])
            ->when($request->query('status'), fn ($q, $s) => $q->where('status', $s))
            ->when($request->query('q'), function ($q, $term) {
                $q->where(function ($w) use ($term) {
                    $w->where('number', 'like', "%{$term}%")
                        ->orWhereHas('subscription', function ($s) use ($term) {
                            $s->where('customer_name', 'like', "%{$term}%")
                                ->orWhere('customer_email', 'like', "%{$term}%")
                                ->orWhere('reference', 'like', "%{$term}%");
                        });
                });
            })
            ->when($request->query('from'), fn ($q, $d) => $q->whereDate('created_at', '>=', $d))
            ->when($request->query('to'), fn ($q, $d) => $q->whereDate('created_at', '<=', $d))
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        $stats = [
            'total' => Invoice::count(),
            'paid' => Invoice::where('status', 'paid')->count(),
            'pending' => Invoice::where('status', 'pending')->count(),
            'failed' => Invoice::where('status', 'failed')->count(),
            'total_revenue' => (float) Invoice::where('status', 'paid')->sum('total'),
            'pending_amount' => (float) Invoice::where('status', 'pending')->sum('total'),
        ];

        return Inertia::render('Admin/Invoices', [
            'invoices' => $invoices,
            'stats' => $stats,
            'filters' => $request->only(['q', 'status', 'from', 'to']),
        ]);
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['subscription.plan', 'payments']);

        return Inertia::render('Admin/InvoiceDetails', [
            'invoice' => $invoice,
        ]);
    }

    /**
     * Print-friendly HTML invoice. The browser's print dialog can save it
     * as a PDF directly — no external dependency required.
     */
    public function print(Invoice $invoice): Response
    {
        $invoice->load(['subscription.plan']);

        return response()
            ->view('invoices.print', ['invoice' => $invoice])
            ->header('Content-Type', 'text/html; charset=utf-8');
    }
}
