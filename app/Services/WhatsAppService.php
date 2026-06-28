<?php

namespace App\Services;

use App\Models\Booking;
use Illuminate\Support\Facades\Http;

class WhatsAppService
{
    private string $token;
    private string $ownerPhone;

    public function __construct()
    {
        $this->token      = config('services.fonnte.token') ?? '';
        $this->ownerPhone = config('services.fonnte.owner_phone') ?? '';
    }

    public function buildPreFilledMessage(Booking $booking): string
    {
        $date    = $booking->schedule->booking_date->format('d M Y');
        $time    = $booking->schedule->timeLabel();
        $package = $booking->package->package_name;
        $price   = 'Rp ' . number_format($booking->package->price, 0, ',', '.');

        return "Halo RPNZL Art!\n\n"
            . "Saya ingin konfirmasi booking saya:\n"
            . "------------------------------\n"
            . "Nama     : {$booking->user->name}\n"
            . "Tanggal  : {$date}\n"
            . "Jam      : {$time}\n"
            . "Paket    : {$package}\n"
            . "Harga    : {$price}\n"
            . "Acara    : {$booking->event_type}\n"
            . "Lokasi   : {$booking->location}\n"
            . "Catatan  : " . ($booking->customization_notes ?? '-') . "\n"
            . "------------------------------\n"
            . "ID Booking : #{$booking->id}\n\n"
            . "Mohon konfirmasi ketersediaan. Terima kasih!";
    }

    public function sendToOwner(string $message): bool
    {
        if (empty($this->token) || empty($this->ownerPhone)) {
            return false;
        }

        $response = Http::withHeaders([
            'Authorization' => $this->token,
        ])->post('https://api.fonnte.com/send', [
            'target'  => $this->ownerPhone,
            'message' => $message,
        ]);

        return $response->successful();
    }

    public function sendToClient(string $phone, string $message): bool
    {
        if (empty($this->token)) {
            return false;
        }

        $response = Http::withHeaders([
            'Authorization' => $this->token,
        ])->post('https://api.fonnte.com/send', [
            'target'  => $phone,
            'message' => $message,
        ]);

        return $response->successful();
    }

    public function notifyOwnerNewBooking(Booking $booking): void
    {
        $message = "BOOKING BARU - RPNZL Art\n\n"
            . "{$booking->user->name}\n"
            . "{$booking->user->whatsapp_number}\n"
            . "{$booking->schedule->booking_date->format('d M Y')} {$booking->schedule->timeLabel()}\n"
            . "{$booking->package->package_name}\n\n"
            . "Cek dashboard untuk konfirmasi.";

        $this->sendToOwner($message);
    }
}
