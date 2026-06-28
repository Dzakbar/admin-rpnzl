import { Link } from '@inertiajs/react'
import Navbar from '../../components/Navbar'
import Footer from '../../components/Footer'

export default function BookingSuccess({ booking, wa_url }) {
  return (
    <div className="min-h-screen bg-pink-ultra flex flex-col">
      <Navbar />
      
      <main className="flex-1 flex items-center justify-center p-6">
        <div className="bg-white p-10 rounded-2xl shadow-sm border border-emerald-100 text-center max-w-lg w-full">
          <div className="w-20 h-20 bg-emerald-50 text-emerald-500 rounded-full flex items-center justify-center mx-auto mb-6">
            <i className="ti ti-check text-4xl" aria-hidden="true" />
          </div>
          
          <h1 className="font-display text-3xl text-plum-800 mb-2">Booking Berhasil!</h1>
          <p className="text-gray-500 text-sm mb-8">
            Terima kasih telah melakukan booking. Detail pesanan Anda telah kami terima dengan ID <strong className="text-gray-800">#{booking.id}</strong> untuk tanggal <strong className="text-gray-800">{booking.date}</strong>.
          </p>

          <div className="bg-gold-pale/30 border border-gold-base/20 rounded-xl p-5 mb-8">
            <p className="text-sm text-gold-deep mb-4">Silakan lanjutkan konfirmasi ke WhatsApp kami agar jadwal segera diproses.</p>
            <a
              href={wa_url}
              target="_blank"
              rel="noreferrer"
              className="inline-flex items-center gap-2 px-6 py-3 bg-[#25D366] text-white rounded-full font-medium hover:bg-[#128C7E] transition-colors"
            >
              <i className="ti ti-brand-whatsapp text-xl" aria-hidden="true" />
              Konfirmasi via WhatsApp
            </a>
          </div>

          <Link href="/booking/status" className="text-sm text-plum-800 hover:text-plum-600 font-medium hover:underline">
            Lihat status booking saya →
          </Link>
        </div>
      </main>

      <Footer />
    </div>
  )
}
