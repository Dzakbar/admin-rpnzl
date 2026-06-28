<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Package;
use App\Models\Schedule;
use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'package_id' => 'required|exists:packages,id',
            'schedule_id' => 'nullable|exists:schedules,id',
            'booking_date' => 'required_without:schedule_id|nullable|date|after_or_equal:today',
            'booking_time' => 'required_without:schedule_id|nullable|date_format:H:i',
            'event_type' => 'required|string|max:100',
            'location' => 'required|string|max:255',
            'customization_notes' => 'nullable|string|max:1000',
            'customer.name' => 'required|string|max:255',
            'customer.whatsapp_number' => 'required|string|max:30',
            'customer.email' => 'required|email|max:255',
        ]);

        $booking = DB::transaction(function () use ($data) {
            $package = Package::findOrFail($data['package_id']);
            $customer = $data['customer'];

            // Hanya create user jika belum ada, jangan update nama
            $user = User::firstOrCreate(
                ['email' => $customer['email']],
                [
                    'name' => $customer['name'],
                    'whatsapp_number' => $customer['whatsapp_number'],
                    'password' => Str::password(32),
                    'role' => 'user',
                ]
            );

            $schedule = $this->resolveSchedule($data);

            $booking = Booking::create([
                'user_id' => $user->id,
                'package_id' => $package->id,
                'schedule_id' => $schedule->id,
                'event_type' => $data['event_type'],
                'location' => $data['location'],
                'customer_name' => $customer['name'], // Simpan nama booking terpisah
                'customization_notes' => $data['customization_notes'] ?? null,
                'status' => 'pending',
            ]);

            $schedule->syncStatusFromBookings();

            return $booking->load(['package', 'schedule', 'user']);
        });

        $waMessage = app(WhatsAppService::class)->buildPreFilledMessage($booking);
        $ownerPhone = config('services.fonnte.owner_phone') ?: '6282114352721';

        return response()->json([
            'message' => 'Booking berhasil dibuat.',
            'booking' => [
                'id' => $booking->id,
                'status' => $booking->status,
                'package_name' => $booking->package->package_name,
                'booking_date' => $booking->schedule->booking_date->format('Y-m-d'),
                'booking_time' => $booking->schedule->timeLabel(),
                'customer_name' => $booking->customer_name, // Gunakan customer_name dari booking, bukan user
                'customer_whatsapp' => $booking->user->whatsapp_number,
            ],
            'wa_url' => "https://wa.me/{$ownerPhone}?text=" . urlencode($waMessage),
        ], 201);
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
                'schedule_id' => ['Jadwal tanggal dan jam ini belum dibuka oleh owner.'],
            ]);
        }

        if ($schedule->booking_date->lt(today())) {
            throw ValidationException::withMessages([
                'booking_date' => ['Tanggal ini sudah lewat.'],
            ]);
        }

        $activeBookingsCount = $schedule->activeBookingsCount();

        if (! $schedule->isBookable($activeBookingsCount)) {
            throw ValidationException::withMessages([
                'schedule_id' => ['Slot jadwal ini sudah tidak tersedia.'],
            ]);
        }

        return $schedule;
    }

    private function normalizeTime(string $time): string
    {
        return strlen($time) === 5 ? "{$time}:00" : $time;
    }
}
