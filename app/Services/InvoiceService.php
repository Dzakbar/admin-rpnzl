<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class InvoiceService
{
    public function generate(Booking $booking): Invoice
    {
        $booking->load(['user', 'package', 'schedule']);

        $invoiceNumber = 'INV-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));

        $invoice = Invoice::create([
            'booking_id'     => $booking->id,
            'invoice_number' => $invoiceNumber,
            'invoice_date'   => now()->toDateString(),
            'total_price'    => $booking->package->price,
        ]);

        $this->generatePdf($invoice);
        return $invoice;
    }

    public function generatePdf(Invoice $invoice): void
    {
        $invoice->load(['booking.user', 'booking.package', 'booking.schedule']);

        $pdf = Pdf::loadView('pdf.invoice', [
            'invoice' => $invoice,
        ])->setPaper('a4');

        $filename = 'invoices/' . $invoice->invoice_number . '.pdf';
        Storage::disk(config('filesystems.invoice_disk'))->put($filename, $pdf->output());

        $invoice->update(['pdf_path' => $filename]);
    }
}
