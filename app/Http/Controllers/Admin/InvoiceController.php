<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Invoice;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with(['booking.user', 'booking.package'])
            ->latest()
            ->paginate(15)
            ->through(fn($i) => [
                'id'             => $i->id,
                'invoice_number' => $i->invoice_number,
                'invoice_date'   => $i->invoice_date->format('d M Y'),
                'total_price'    => $i->total_price,
                'shipping_cost'  => $i->shipping_cost,
                'grand_total'    => $i->grand_total,
                'user_name'      => $i->booking->user->name,
                'package_name'   => $i->booking->package->package_name,
                'pdf_url'        => $i->pdf_url,
                'booking_status' => $i->booking->status,
            ]);

        return inertia('Admin/Invoices/Index', compact('invoices'));
    }

    public function generate(Booking $booking)
    {
        if ($booking->invoice) {
            return back()->withErrors(['invoice' => 'Invoice sudah ada.']);
        }

        $invoice = app(InvoiceService::class)->generate($booking);

        return back()->with('success', "Invoice {$invoice->invoice_number} berhasil dibuat.");
    }

    public function update(Request $request, Invoice $invoice)
    {
        $data = $request->validate([
            'shipping_cost' => 'required|numeric|min:0',
        ]);

        $invoice->update([
            'shipping_cost' => $data['shipping_cost'],
        ]);

        app(InvoiceService::class)->generatePdf($invoice->fresh());

        return back()->with('success', 'Ongkir berhasil diperbarui.');
    }

    public function download(Invoice $invoice)
    {
        $disk = Storage::disk(config('filesystems.invoice_disk'));

        if (!$invoice->pdf_path || ! $disk->exists($invoice->pdf_path)) {
            app(InvoiceService::class)->generatePdf($invoice);
        }

        return $disk->download($invoice->pdf_path, $invoice->invoice_number . '.pdf');
    }
}
