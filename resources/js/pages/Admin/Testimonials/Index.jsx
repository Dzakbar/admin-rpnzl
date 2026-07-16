import { useState } from 'react'
import { Link, router } from '@inertiajs/react'
import AdminLayout from '../../../layouts/AdminLayout'
import Badge from '../../../components/ui/Badge'
import Button from '../../../components/ui/Button'

function RatingStars({ rating }) {
  return (
    <div className="flex items-center gap-0.5 text-gold-base" aria-label={`${rating} dari 5 bintang`}>
      {Array.from({ length: 5 }, (_, index) => (
        <i
          key={index}
          className={`ti ${index < rating ? 'ti-star-filled' : 'ti-star'} text-sm`}
          aria-hidden="true"
        />
      ))}
    </div>
  )
}

export default function TestimonialsIndex({ testimonials, filters }) {
  const [search, setSearch] = useState(filters.search ?? '')
  const [status, setStatus] = useState(filters.status ?? '')
  const [rating, setRating] = useState(filters.rating ?? '')
  const [source, setSource] = useState(filters.source ?? '')

  const doFilter = (next = {}) => {
    router.get('/admin/testimonials', {
      search,
      status,
      rating,
      source,
      ...next,
    }, { preserveState: true, preserveScroll: true })
  }

  const updateStatus = (testimonial, nextStatus) => {
    router.patch(`/admin/testimonials/${testimonial.id}/status`, { status: nextStatus }, {
      preserveScroll: true,
    })
  }

  const toggleFeatured = (testimonial) => {
    router.patch(`/admin/testimonials/${testimonial.id}/featured`, {}, {
      preserveScroll: true,
    })
  }

  const deleteTestimonial = (testimonial) => {
    if (!window.confirm('Hapus testimoni ini?')) return

    router.delete(`/admin/testimonials/${testimonial.id}`, {
      preserveScroll: true,
    })
  }

  return (
    <AdminLayout title="Manajemen Testimoni">
      <div className="mb-6 grid gap-3 sm:grid-cols-2 xl:grid-cols-[minmax(0,1fr)_160px_150px_150px_auto]">
        <input
          value={search}
          onChange={e => setSearch(e.target.value)}
          onKeyDown={e => e.key === 'Enter' && doFilter()}
          placeholder="Cari nama, email, paket, atau isi testimoni..."
          className="border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:border-gold-base"
        />
        <select
          value={status}
          onChange={e => {
            setStatus(e.target.value)
            doFilter({ status: e.target.value })
          }}
          className="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none"
        >
          <option value="">Semua status</option>
          <option value="pending">Menunggu</option>
          <option value="published">Published</option>
          <option value="hidden">Hidden</option>
        </select>
        <select
          value={rating}
          onChange={e => {
            setRating(e.target.value)
            doFilter({ rating: e.target.value })
          }}
          className="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none"
        >
          <option value="">Semua rating</option>
          {[5, 4, 3, 2, 1].map(value => (
            <option key={value} value={value}>{value} bintang</option>
          ))}
        </select>
        <select
          value={source}
          onChange={e => {
            setSource(e.target.value)
            doFilter({ source: e.target.value })
          }}
          className="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none"
        >
          <option value="">Semua sumber</option>
          <option value="home">Home</option>
          <option value="booking">Booking</option>
        </select>
        <Button type="button" variant="gold" onClick={() => doFilter()}>
          Filter
        </Button>
      </div>

      <div className="bg-white rounded-xl border border-gray-100 overflow-hidden">
        <div className="overflow-x-auto">
        <table className="min-w-[980px] w-full text-sm">
          <thead className="bg-gray-50 border-b border-gray-100">
            <tr>
              {['Pelanggan', 'Rating', 'Testimoni', 'Paket', 'Status', 'Aksi'].map(header => (
                <th key={header} className="text-left px-5 py-3.5 text-xs font-medium text-gray-500 tracking-wide">
                  {header}
                </th>
              ))}
            </tr>
          </thead>
          <tbody className="divide-y divide-gray-50">
            {testimonials.data.length === 0 ? (
              <tr>
                <td colSpan="6" className="px-5 py-12 text-center text-sm text-gray-400">
                  Belum ada testimoni.
                </td>
              </tr>
            ) : testimonials.data.map(testimonial => (
              <tr key={testimonial.id} className="align-top hover:bg-gray-50/50 transition-colors">
                <td className="px-5 py-4">
                  <p className="font-medium text-gray-800">{testimonial.customer_name}</p>
                  {testimonial.customer_email && (
                    <p className="mt-1 text-xs text-gray-400">{testimonial.customer_email}</p>
                  )}
                  {testimonial.booking_id && (
                    <Link href={`/admin/bookings/${testimonial.booking_id}`} className="mt-2 inline-flex text-xs text-gold-deep hover:text-gold-base">
                      Lihat booking
                    </Link>
                  )}
                </td>
                <td className="px-5 py-4">
                  <RatingStars rating={testimonial.rating} />
                  <p className="mt-1 text-xs text-gray-400">{testimonial.rating}/5</p>
                </td>
                <td className="px-5 py-4 max-w-md">
                  <p className="line-clamp-4 text-gray-600">{testimonial.message}</p>
                  <div className="mt-3 flex flex-wrap items-center gap-2 text-xs text-gray-400">
                    <span>{testimonial.created_at}</span>
                    <span className="rounded-full bg-gray-100 px-2 py-0.5 uppercase tracking-wide">
                      {testimonial.source === 'booking' ? 'Booking' : 'Home'}
                    </span>
                    {testimonial.is_featured && (
                      <span className="rounded-full bg-gold-pale px-2 py-0.5 text-gold-deep">Featured</span>
                    )}
                  </div>
                </td>
                <td className="px-5 py-4 text-gray-600">{testimonial.package_name ?? '-'}</td>
                <td className="px-5 py-4"><Badge status={testimonial.status} /></td>
                <td className="px-5 py-4">
                  <div className="flex flex-wrap gap-2">
                    {testimonial.status !== 'published' && (
                      <Button type="button" size="sm" variant="gold" onClick={() => updateStatus(testimonial, 'published')}>
                        Publish
                      </Button>
                    )}
                    {testimonial.status !== 'hidden' && (
                      <Button type="button" size="sm" variant="outline" onClick={() => updateStatus(testimonial, 'hidden')}>
                        Hide
                      </Button>
                    )}
                    {testimonial.status !== 'pending' && (
                      <Button type="button" size="sm" variant="ghost" onClick={() => updateStatus(testimonial, 'pending')}>
                        Pending
                      </Button>
                    )}
                    <Button type="button" size="sm" variant="outline" onClick={() => toggleFeatured(testimonial)}>
                      {testimonial.is_featured ? 'Unfeature' : 'Feature'}
                    </Button>
                    <Button type="button" size="sm" variant="danger" onClick={() => deleteTestimonial(testimonial)}>
                      Delete
                    </Button>
                  </div>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
        </div>

        <div className="flex flex-col gap-3 px-4 py-4 border-t border-gray-100 sm:flex-row sm:items-center sm:justify-between sm:px-5">
          <p className="text-xs text-gray-500 font-medium">
            Halaman {testimonials.current_page} dari {testimonials.last_page} - Menampilkan {testimonials.from ?? 0}-{testimonials.to ?? 0} dari {testimonials.total} testimoni
          </p>
          <div className="flex gap-2 overflow-x-auto pb-1 sm:pb-0">
            {testimonials.links.map((link, index) => (
              <Link
                key={index}
                href={link.url ?? '#'}
                className={`px-3 py-2 text-xs rounded-lg border transition-colors ${
                  link.active
                    ? 'bg-gold-base text-white border-gold-base font-medium'
                    : link.url
                      ? 'border-gray-300 text-gray-700 hover:bg-gray-50'
                      : 'border-gray-200 text-gray-400 cursor-not-allowed'
                }`}
                dangerouslySetInnerHTML={{ __html: link.label }}
              />
            ))}
          </div>
        </div>
      </div>
    </AdminLayout>
  )
}
