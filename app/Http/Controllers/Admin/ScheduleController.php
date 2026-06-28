<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $year  = $request->integer('year', now()->year);
        $month = $request->integer('month', now()->month);

        $schedules = Schedule::with(['bookings.user'])
            ->withCount(['bookings as active_bookings_count' => fn ($query) => $query->whereNotIn('status', ['rejected'])])
            ->forMonth($year, $month)
            ->orderBy('booking_date')
            ->orderBy('booking_time')
            ->get()
            ->map(fn (Schedule $s) => [
                'id'            => $s->id,
                'booking_date'  => $s->booking_date->format('Y-m-d'),
                'booking_time'  => $s->timeLabel(),
                'status'        => $s->effectiveStatus($s->active_bookings_count),
                'configured_status' => $s->status,
                'notes'         => $s->notes,
                'booking_count' => $s->active_bookings_count,
                'remaining_slots' => $s->remainingSlots($s->active_bookings_count),
                'bookable'      => $s->isBookable($s->active_bookings_count),
            ]);

        return inertia('Admin/Schedule', [
            'schedules' => $schedules,
            'year'      => $year,
            'month'     => $month,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validatedSlot($request);

        Schedule::updateOrCreate(
            [
                'booking_date' => $data['date'],
                'booking_time' => $this->normalizeTime($data['time']),
            ],
            [
                'status' => $data['status'],
                'notes' => $data['notes'] ?? null,
            ]
        );

        return back()->with('success', 'Slot jadwal berhasil disimpan.');
    }

    public function update(Request $request, Schedule $schedule)
    {
        $data = $this->validatedSlot($request);

        $schedule->update([
            'booking_date' => $data['date'],
            'booking_time' => $this->normalizeTime($data['time']),
            'status' => $data['status'],
            'notes' => $data['notes'] ?? null,
        ]);

        return back()->with('success', 'Slot jadwal berhasil diperbarui.');
    }

    public function destroy(Schedule $schedule)
    {
        if ($schedule->bookings()->whereNotIn('status', ['rejected'])->exists()) {
            return back()->withErrors(['schedule' => 'Slot ini masih memiliki booking aktif.']);
        }

        $schedule->delete();

        return back()->with('success', 'Slot jadwal berhasil dihapus.');
    }

    public function unblock(Schedule $schedule)
    {
        if ($schedule->bookings()->whereNotIn('status', ['rejected'])->exists()) {
            return back()->withErrors(['date' => 'Masih ada booking aktif pada slot ini.']);
        }

        $schedule->update(['status' => Schedule::STATUS_AVAILABLE, 'notes' => null]);

        return back()->with('success', 'Slot berhasil dibuka kembali.');
    }

    private function validatedSlot(Request $request): array
    {
        return $request->validate([
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'status' => ['required', Rule::in(Schedule::STATUSES)],
            'notes' => 'nullable|string|max:255',
        ]);
    }

    private function normalizeTime(string $time): string
    {
        return strlen($time) === 5 ? "{$time}:00" : $time;
    }
}
