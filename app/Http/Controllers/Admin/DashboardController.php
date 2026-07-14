<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Invoice;
use App\Models\Schedule;
use App\Models\Testimonial;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'pending_bookings'    => Booking::where('status', 'pending')->count(),
            'bookings_this_month' => Booking::whereMonth('created_at', now()->month)
                                            ->whereYear('created_at', now()->year)
                                            ->count(),
            'total_revenue'       => Invoice::whereHas('booking', fn($q) =>
                                         $q->where('status', 'confirmed')
                                    )->sum('total_price'),
            'available_dates'     => Schedule::available()
                                             ->where('booking_date', '>=', today())
                                             ->count(),
            'pending_testimonials' => Testimonial::where('status', Testimonial::STATUS_PENDING)->count(),
        ];

        $salesChart = Invoice::selectRaw(
                'EXTRACT(MONTH FROM invoice_date) as month,
                 EXTRACT(YEAR FROM invoice_date) as year,
                 SUM(total_price) as total,
                 COUNT(*) as count'
            )
            ->whereHas('booking', fn($q) => $q->where('status', 'confirmed'))
            ->where('invoice_date', '>=', now()->subMonths(6))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->map(fn($r) => [
                'label' => \Carbon\Carbon::create($r->year, $r->month)->format('M Y'),
                'total' => (float) $r->total,
                'count' => $r->count,
            ]);

        $recentBookings = Booking::with(['user', 'package', 'schedule'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn($b) => [
                'id'           => $b->id,
                'user_name'    => $b->user->name,
                'package_name' => $b->package->package_name,
                'date'         => $b->schedule->booking_date->format('d M Y') . ' ' . $b->schedule->timeLabel(),
                'status'       => $b->status,
            ]);

        return inertia('Admin/Dashboard', compact('stats', 'salesChart', 'recentBookings'));
    }
}
