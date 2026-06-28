<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Package;
use App\Models\Schedule;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    public function create()
    {
        $packages = Package::where('status', 'active')->get(['id', 'package_name', 'price']);

        return inertia('Booking/Create', [
            'packages' => $packages,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'package_id'          => 'required|exists:packages,id',
            'schedule_id'         => 'nullable|exists:schedules,id',
            'booking_date'        => 'required_without:schedule_id|nullable|date|after_or_equal:today',
            'booking_time'        => 'required_without:schedule_id|nullable|date_format:H:i',
            'event_type'          => 'required|string|max:100',
            'location'            => 'required|string|max:255',
            'customization_notes' => 'nullable|string|max:1000',
        ]);

        $booking = DB::transaction(function () use ($data) {
            $schedule = $this->resolveSchedule($data);

            $booking = Booking::create([
                'user_id'             => Auth::id(),
                'package_id'          => $data['package_id'],
                'schedule_id'         => $schedule->id,
                'event_type'          => $data['event_type'],
                'location'            => $data['location'],
                'customization_notes' => $data['customization_notes'] ?? null,
                'status'              => 'pending',
            ]);

            $schedule->syncStatusFromBookings();

            return $booking;
        });

        $waMessage = app(WhatsAppService::class)
            ->buildPreFilledMessage($booking->load(['package', 'user', 'schedule']));

        $ownerPhone = config('services.fonnte.owner_phone');
        $waUrl = "https://wa.me/{$ownerPhone}?text=" . urlencode($waMessage);

        return inertia('Booking/Success', [
            'booking' => [
                'id'     => $booking->id,
                'date'   => $booking->schedule->booking_date->format('d M Y') . ' ' . $booking->schedule->timeLabel(),
                'status' => $booking->status,
            ],
            'wa_url' => $waUrl,
        ]);
    }

    public function index()
    {
        $bookings = Booking::with(['package', 'schedule', 'invoice'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get()
            ->map(fn($b) => [
                'id'           => $b->id,
                'package_name' => $b->package->package_name,
                'date'         => $b->schedule->booking_date->format('d M Y') . ' ' . $b->schedule->timeLabel(),
                'event_type'   => $b->event_type,
                'location'     => $b->location,
                'status'       => $b->status,
                'invoice'      => $b->invoice ? [
                    'invoice_number' => $b->invoice->invoice_number,
                    'pdf_url'        => $b->invoice->pdf_url,
                ] : null,
            ]);

        return inertia('Booking/Index', ['bookings' => $bookings]);
    }

    private function resolveSchedule(array $data): Schedule
    {
        $query = Schedule::query()->lockForUpdate();

        $schedule = ! empty($data['schedule_id'])
            ? $query->whereKey($data['schedule_id'])->first()
            : $query
                ->whereDate('booking_date', $data['booking_date'])
                ->where('booking_time', $this->normalizeTime($data['booking_time']))
                ->first();

        if (! $schedule) {
            throw ValidationException::withMessages([
                'schedule_id' => 'Jadwal tanggal dan jam ini belum dibuka oleh owner.',
            ]);
        }

        if ($schedule->booking_date->lt(today())) {
            throw ValidationException::withMessages([
                'booking_date' => 'Tanggal ini sudah lewat.',
            ]);
        }

        $activeBookingsCount = $schedule->activeBookingsCount();

        if (! $schedule->isBookable($activeBookingsCount)) {
            throw ValidationException::withMessages([
                'schedule_id' => 'Slot jadwal ini sudah tidak tersedia.',
            ]);
        }

        return $schedule;
    }

    private function normalizeTime(string $time): string
    {
        return strlen($time) === 5 ? "{$time}:00" : $time;
    }
}
