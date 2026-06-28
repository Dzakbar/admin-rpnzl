<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\InvoiceService;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $bookings = Booking::with(['user', 'package', 'schedule', 'invoice'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->search, fn($q) =>
                $q->whereHas('user', fn($u) =>
                    $u->where('name', 'like', "%{$request->search}%")
                )
            )
            ->latest()
            ->paginate(5)
            ->through(fn($b) => [
                'id'           => $b->id,
                'user_name'    => $b->customer_name ?? $b->user->name, // Gunakan customer_name jika ada, fallback ke user->name
                'user_wa'      => $b->user->whatsapp_number,
                'package_name' => $b->package->package_name,
                'date'         => $b->schedule->booking_date->format('d M Y') . ' ' . $b->schedule->timeLabel(),
                'event_type'   => $b->event_type,
                'location'     => $b->location,
                'status'       => $b->status,
                'has_invoice'  => $b->invoice !== null,
            ]);

        return inertia('Admin/Bookings/Index', [
            'bookings' => $bookings,
            'filters'  => $request->only('status', 'search'),
        ]);
    }

    public function show(Booking $booking)
    {
        $booking->load(['user', 'package', 'schedule', 'invoice']);

        return inertia('Admin/Bookings/Show', [
            'booking' => [
                'id'                  => $booking->id,
                'status'              => $booking->status,
                'event_type'          => $booking->event_type,
                'location'            => $booking->location,
                'customization_notes' => $booking->customization_notes,
                'created_at'          => $booking->created_at->format('d M Y H:i'),
                'user' => [
                    'name'             => $booking->customer_name ?? $booking->user->name, // Gunakan customer_name jika ada
                    'email'            => $booking->user->email,
                    'whatsapp_number'  => $booking->user->whatsapp_number,
                ],
                'package' => [
                    'package_name' => $booking->package->package_name,
                    'price'        => $booking->package->price,
                ],
                'schedule' => [
                    'booking_date' => $booking->schedule->booking_date->format('d M Y'),
                    'booking_time' => $booking->schedule->timeLabel(),
                ],
                'invoice' => $booking->invoice ? [
                    'id'             => $booking->invoice->id,
                    'invoice_number' => $booking->invoice->invoice_number,
                    'total_price'    => $booking->invoice->total_price,
                    'shipping_cost'  => $booking->invoice->shipping_cost,
                    'grand_total'    => $booking->invoice->grand_total,
                    'pdf_url'        => $booking->invoice->pdf_url,
                ] : null,
            ],
        ]);
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $data = $request->validate([
            'status' => 'required|in:confirmed,rejected,done',
            'shipping_cost' => 'required|numeric|min:0',
        ]);

        $booking->update(['status' => $data['status']]);

        if ($data['status'] === 'confirmed' && !$booking->invoice) {
            $invoice = app(InvoiceService::class)->generate($booking);
            $invoice->update(['shipping_cost' => $data['shipping_cost']]);
            app(InvoiceService::class)->generatePdf($invoice);
        } elseif ($booking->invoice && $data['status'] === 'confirmed') {
            $booking->invoice->update(['shipping_cost' => $data['shipping_cost']]);
            app(InvoiceService::class)->generatePdf($booking->invoice);
        }

        if ($data['status'] === 'rejected') {
            $schedule = $booking->schedule;
            $schedule->syncStatusFromBookings();
        }

        return back()->with('success', 'Status booking berhasil diperbarui.');
    }
}
