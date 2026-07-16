import { useState } from 'react'
import { Link, router } from '@inertiajs/react'
import AdminLayout from '../../../layouts/AdminLayout'
import Badge from '../../../components/ui/Badge'
import Button from '../../../components/ui/Button'

export default function BookingsIndex({ bookings, filters }) {
  const [search, setSearch] = useState(filters.search ?? '')
  const [status, setStatus] = useState(filters.status ?? '')

  const doFilter = () => {
    router.get('/admin/bookings', { search, status }, { preserveState: true })
  }

  return (
    <AdminLayout title="Manajemen Booking">
      {/* Filter bar */}
      <div className="flex flex-col gap-3 mb-6 sm:flex-row sm:flex-wrap">
        <input
          value={search}
          onChange={e => setSearch(e.target.value)}
          onKeyDown={e => e.key === 'Enter' && doFilter()}
          placeholder="Cari nama pelanggan..."
          className="border border-gray-200 rounded-lg px-4 py-2 text-sm w-full sm:flex-1 sm:min-w-48 focus:outline-none focus:border-gold-base"
        />
        <select value={status} onChange={e => { setStatus(e.target.value); doFilter() }}
          className="border border-gray-200 rounded-lg px-3 py-2 text-sm w-full sm:w-auto focus:outline-none">
          <option value="">Semua status</option>
          <option value="pending">Menunggu</option>
          <option value="confirmed">Dikonfirmasi</option>
          <option value="rejected">Ditolak</option>
          <option value="done">Selesai</option>
        </select>
      </div>

      {/* Table */}
      <div className="bg-white rounded-xl border border-gray-100 overflow-hidden">
        <div className="overflow-x-auto">
        <table className="min-w-[820px] w-full text-sm">
          <thead className="bg-gray-50 border-b border-gray-100">
            <tr>
              {['Pelanggan', 'Paket', 'Tanggal', 'Event', 'Status', 'Invoice', 'Aksi'].map(h => (
                <th key={h} className="text-left px-5 py-3.5 text-xs font-medium text-gray-500 tracking-wide">{h}</th>
              ))}
            </tr>
          </thead>
          <tbody className="divide-y divide-gray-50">
            {bookings.data.map(b => (
              <tr key={b.id} className="hover:bg-gray-50/50 transition-colors">
                <td className="px-5 py-4 font-medium text-gray-800">{b.user_name}</td>
                <td className="px-5 py-4 text-gray-600">{b.package_name}</td>
                <td className="px-5 py-4 text-gray-600">{b.date}</td>
                <td className="px-5 py-4 text-gray-600">{b.event_type}</td>
                <td className="px-5 py-4"><Badge status={b.status} /></td>
                <td className="px-5 py-4">
                  {b.has_invoice
                    ? <span className="text-xs text-emerald-600"><i className="ti ti-check" aria-hidden="true" /> Ada</span>
                    : <span className="text-xs text-gray-400">Belum</span>
                  }
                </td>
                <td className="px-5 py-4">
                  <Link href={`/admin/bookings/${b.id}`}>
                    <Button variant="outline" size="sm">Detail</Button>
                  </Link>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
        </div>

        {/* Pagination */}
        <div className="flex flex-col gap-3 px-4 py-4 border-t border-gray-100 sm:flex-row sm:items-center sm:justify-between sm:px-5">
          <p className="text-xs text-gray-500 font-medium">
            Halaman {bookings.current_page} dari {bookings.last_page} • Menampilkan {bookings.from}–{bookings.to} dari {bookings.total} booking
          </p>
          <div className="flex gap-2 overflow-x-auto pb-1 sm:pb-0">
            {bookings.links.map((link, i) => {
              // Skip "Previous" and "Next" labels, show as buttons
              if (link.label.includes('Previous')) {
                return (
                  <Link key={i} href={link.url ?? '#'}
                    className={`px-3 py-2 text-xs rounded-lg border transition-colors ${
                      link.url 
                        ? 'border-gray-300 text-gray-700 hover:bg-gray-50 cursor-pointer' 
                        : 'border-gray-200 text-gray-400 cursor-not-allowed'
                    }`}
                    disabled={!link.url}
                  >
                    ← Sebelumnya
                  </Link>
                )
              }
              
              if (link.label.includes('Next')) {
                return (
                  <Link key={i} href={link.url ?? '#'}
                    className={`px-3 py-2 text-xs rounded-lg border transition-colors ${
                      link.url 
                        ? 'border-gold-base text-gold-base hover:bg-gold-base hover:text-white cursor-pointer font-medium' 
                        : 'border-gray-200 text-gray-400 cursor-not-allowed'
                    }`}
                    disabled={!link.url}
                  >
                    Berikutnya →
                  </Link>
                )
              }
              
              // Show page numbers
              return (
                <Link key={i} href={link.url ?? '#'}
                  className={`px-3 py-2 text-xs rounded-lg border transition-colors ${
                    link.active 
                      ? 'bg-gold-base text-white border-gold-base font-medium' 
                      : 'border-gray-300 text-gray-700 hover:bg-gray-50'
                  }`}
                  dangerouslySetInnerHTML={{ __html: link.label }}
                />
              )
            })}
          </div>
        </div>
      </div>
    </AdminLayout>
  )
}
