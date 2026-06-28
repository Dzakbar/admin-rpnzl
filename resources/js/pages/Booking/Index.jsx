import Navbar from '../../components/Navbar'
import Footer from '../../components/Footer'
import Badge from '../../components/ui/Badge'

export default function UserBookings({ bookings }) {
  return (
    <div className="min-h-screen bg-pink-ultra flex flex-col">
      <Navbar />
      
      <main className="flex-1 max-w-4xl mx-auto w-full px-6 py-12">
        <h1 className="font-display text-4xl text-plum-800 mb-8">My Bookings</h1>

        {bookings.length === 0 ? (
          <div className="bg-white p-8 rounded-xl shadow-sm border border-pink-light/30 text-center">
            <p className="text-gray-500 mb-4">Anda belum memiliki riwayat booking.</p>
            <a href="/booking" className="text-gold-deep hover:underline font-medium">Buat booking sekarang →</a>
          </div>
        ) : (
          <div className="space-y-4">
            {bookings.map(b => (
              <div key={b.id} className="bg-white p-6 rounded-xl shadow-sm border border-pink-light/30 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                  <div className="flex items-center gap-3 mb-2">
                    <h3 className="font-medium text-lg text-gray-900">{b.package_name}</h3>
                    <Badge status={b.status} />
                  </div>
                  <p className="text-sm text-gray-500 mb-1">
                    <i className="ti ti-calendar mr-2" aria-hidden="true" />{b.date}
                  </p>
                  <p className="text-sm text-gray-500">
                    <i className="ti ti-map-pin mr-2" aria-hidden="true" />{b.location} ({b.event_type})
                  </p>
                </div>
                
                {b.invoice && (
                  <div className="md:text-right">
                    <p className="text-xs text-gray-400 mb-1">Invoice {b.invoice.invoice_number}</p>
                    <a
                      href={b.invoice.pdf_url}
                      target="_blank"
                      rel="noreferrer"
                      className="inline-flex items-center gap-2 px-4 py-2 bg-gold-pale text-gold-deep text-xs font-medium rounded hover:bg-gold-light/40 transition-colors"
                    >
                      <i className="ti ti-download" aria-hidden="true" /> Unduh Invoice
                    </a>
                  </div>
                )}
              </div>
            ))}
          </div>
        )}
      </main>

      <Footer />
    </div>
  )
}
