<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ScheduleController extends Controller
{
    public function availability(Request $request)
    {
        $year  = $request->integer('year', now()->year);
        $month = $request->integer('month', now()->month);

        $slots = $this->queryMonthSlots($year, $month)
            ->get()
            ->groupBy(fn (Schedule $schedule) => $schedule->booking_date->format('Y-m-d'))
            ->map(fn ($dateSlots, string $date) => $this->datePayload($date, $dateSlots))
            ->values();

        return response()->json(['dates' => $slots]);
    }

    public function slots(Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date',
        ]);

        $slots = Schedule::query()
            ->withCount(['bookings as active_bookings_count' => fn ($query) => $query->whereNotIn('status', ['rejected'])])
            ->whereDate('booking_date', $data['date'])
            ->orderBy('booking_time')
            ->get()
            ->map(fn (Schedule $schedule) => $this->slotPayload($schedule))
            ->values();

        return response()->json(['slots' => $slots]);
    }

    public function unavailable(Request $request)
    {
        $year  = $request->integer('year', now()->year);
        $month = $request->integer('month', now()->month);

        $availability = $this->queryMonthSlots($year, $month)
            ->get()
            ->groupBy(fn (Schedule $schedule) => $schedule->booking_date->format('Y-m-d'))
            ->map(fn ($dateSlots, string $date) => $this->datePayload($date, $dateSlots));

        $start = Carbon::create($year, $month, 1)->startOfDay();
        $today = today();
        $unavailable = collect(range(1, $start->daysInMonth))
            ->map(fn (int $day) => $start->copy()->day($day)->format('Y-m-d'))
            ->filter(function (string $date) use ($availability, $today) {
                if (Carbon::parse($date)->lt($today)) {
                    return true;
                }

                return ! ($availability->get($date)['bookable'] ?? false);
            })
            ->values();

        return response()->json([
            'dates' => $unavailable,
            'available_dates' => $availability
                ->filter(fn (array $date) => $date['bookable'])
                ->keys()
                ->values(),
        ]);
    }

    private function queryMonthSlots(int $year, int $month)
    {
        return Schedule::query()
            ->withCount(['bookings as active_bookings_count' => fn ($query) => $query->whereNotIn('status', ['rejected'])])
            ->forMonth($year, $month)
            ->orderBy('booking_date')
            ->orderBy('booking_time');
    }

    private function datePayload(string $date, $slots): array
    {
        $slotPayloads = $slots
            ->map(fn (Schedule $schedule) => $this->slotPayload($schedule))
            ->values();

        $bookableSlots = $slotPayloads->where('bookable', true);
        $status = match (true) {
            $bookableSlots->isEmpty() && $slotPayloads->where('status', Schedule::STATUS_BLOCKED)->count() === $slotPayloads->count() => Schedule::STATUS_BLOCKED,
            $bookableSlots->isEmpty() => Schedule::STATUS_FULLY_BOOKED,
            $bookableSlots->where('status', Schedule::STATUS_AVAILABLE)->isNotEmpty() => Schedule::STATUS_AVAILABLE,
            default => Schedule::STATUS_LIMITED,
        };

        return [
            'date' => $date,
            'status' => $status,
            'bookable' => $bookableSlots->isNotEmpty(),
            'available_slots' => $bookableSlots->count(),
            'slots' => $slotPayloads,
        ];
    }

    private function slotPayload(Schedule $schedule): array
    {
        $activeBookingsCount = $schedule->activeBookingsCount();
        $status = $schedule->effectiveStatus($activeBookingsCount);

        return [
            'id' => $schedule->id,
            'booking_date' => $schedule->booking_date->format('Y-m-d'),
            'booking_time' => $schedule->timeLabel(),
            'time' => $schedule->timeLabel(),
            'status' => $status,
            'configured_status' => $schedule->status,
            'bookable' => $schedule->isBookable($activeBookingsCount),
            'remaining_slots' => $schedule->remainingSlots($activeBookingsCount),
            'booking_count' => $activeBookingsCount,
            'notes' => $schedule->notes,
        ];
    }
}
