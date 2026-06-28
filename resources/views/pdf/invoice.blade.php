<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Helvetica Neue', Helvetica, sans-serif; font-size: 13px; color: #2C2218; padding: 40px; }
    .header { display: flex; justify-content: space-between; margin-bottom: 40px; border-bottom: 1px solid #E8D5C4; padding-bottom: 20px; }
    .brand { font-size: 24px; font-weight: 300; letter-spacing: 4px; color: #6B2D45; }
    .brand span { color: #C9A84C; }
    .invoice-number { font-size: 11px; color: #9C8170; letter-spacing: 2px; text-transform: uppercase; }
    .section-title { font-size: 10px; text-transform: uppercase; letter-spacing: 2px; color: #C9A84C; margin-bottom: 8px; }
    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 30px; }
    .info-item { margin-bottom: 6px; }
    .info-label { font-size: 11px; color: #9C8170; }
    .info-value { font-size: 13px; color: #2C2218; font-weight: 500; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
    th { background: #FDF0F5; padding: 10px 12px; text-align: left; font-size: 10px; text-transform: uppercase; letter-spacing: 1.5px; color: #9C8170; border-bottom: 1px solid #F5D0DF; }
    td { padding: 12px; border-bottom: 1px solid #FDF0F5; font-size: 12px; }
    .total-row td { font-size: 14px; font-weight: 600; color: #6B2D45; border-top: 2px solid #E8A4BB; border-bottom: none; }
    .footer-note { font-size: 11px; color: #9C8170; text-align: center; margin-top: 40px; border-top: 1px solid #F5D0DF; padding-top: 20px; }
  </style>
</head>
<body>
  <div class="header">
    <div style="float: left; width: 50%;">
      <div class="brand">RPNZL <span>Art</span></div>
      <p style="font-size:11px; color:#9C8170; margin-top:4px;">Henna Art Professional</p>
    </div>
    <div style="float: right; width: 50%; text-align:right">
      <div class="invoice-number">Invoice #{{ $invoice->invoice_number }}</div>
      <p style="font-size:11px; color:#9C8170; margin-top:4px;">{{ $invoice->invoice_date->format('d M Y') }}</p>
    </div>
    <div style="clear: both;"></div>
  </div>

  <div class="info-grid">
    <div style="float: left; width: 50%;">
      <p class="section-title">Tagihan Kepada</p>
      <div class="info-item"><p class="info-value">{{ $invoice->booking->user->name }}</p></div>
      <div class="info-item"><p class="info-label">Email</p><p class="info-value">{{ $invoice->booking->user->email }}</p></div>
      <div class="info-item"><p class="info-label">WhatsApp</p><p class="info-value">{{ $invoice->booking->user->whatsapp_number }}</p></div>
    </div>
    <div style="float: right; width: 50%;">
      <p class="section-title">Detail Booking</p>
      <div class="info-item"><p class="info-label">Tanggal layanan</p><p class="info-value">{{ $invoice->booking->schedule->booking_date->format('d M Y') }}</p></div>
      <div class="info-item"><p class="info-label">Jam layanan</p><p class="info-value">{{ $invoice->booking->schedule->timeLabel() }}</p></div>
      <div class="info-item"><p class="info-label">Jenis acara</p><p class="info-value">{{ $invoice->booking->event_type }}</p></div>
      <div class="info-item"><p class="info-label">Lokasi</p><p class="info-value">{{ $invoice->booking->location }}</p></div>
    </div>
    <div style="clear: both;"></div>
  </div>

    <table>
    <thead>
      <tr><th>Layanan</th><th>Deskripsi</th><th style="text-align:right">Harga</th></tr>
    </thead>
    <tbody>
      <tr>
        <td>{{ $invoice->booking->package->package_name }}</td>
        <td style="color:#9C8170">{{ $invoice->booking->package->description }}</td>
        <td style="text-align:right">Rp {{ number_format($invoice->total_price, 0, ',', '.') }}</td>
      </tr>
      @if ($invoice->shipping_cost > 0)
      <tr>
        <td>Ongkir</td>
        <td style="color:#9C8170">Biaya pengiriman</td>
        <td style="text-align:right">Rp {{ number_format($invoice->shipping_cost, 0, ',', '.') }}</td>
      </tr>
      @endif
    </tbody>
    <tfoot>
      <tr class="total-row">
        <td colspan="2">Total</td>
        <td style="text-align:right">Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}</td>
      </tr>
    </tfoot>
  </table>

  <div class="footer-note">
    Terima kasih telah mempercayai RPNZL Art untuk momen spesial Anda. ✨<br>
    Invoice ini dihasilkan secara otomatis oleh sistem.
  </div>
</body>
</html>
