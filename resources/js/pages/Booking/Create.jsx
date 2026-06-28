import { useForm, usePage } from '@inertiajs/react'
import Navbar from '../../components/Navbar'
import Footer from '../../components/Footer'

export default function CreateBooking({ packages }) {
  const { url } = usePage()
  const params = new URLSearchParams(url.split('?')[1])
  const defaultDate = params.get('date') || ''

  const { data, setData, post, processing, errors } = useForm({
    package_id: '',
    booking_date: defaultDate,
    event_type: '',
    location: '',
    customization_notes: '',
  })

  const submit = (e) => {
    e.preventDefault()
    post('/booking')
  }

  return (
    <div className="min-h-screen bg-pink-ultra flex flex-col">
      <Navbar />
      
      <main className="flex-1 max-w-3xl mx-auto w-full px-6 py-12">
        <h1 className="font-display text-4xl text-plum-800 text-center mb-2">Book a Session</h1>
        <p className="text-center text-gray-500 mb-10 text-sm">Please fill out the form below to request a booking.</p>

        <form onSubmit={submit} className="bg-white p-8 rounded-xl shadow-sm border border-pink-light/30 space-y-6">
          <div className="grid md:grid-cols-2 gap-6">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Package</label>
              <select
                value={data.package_id}
                onChange={e => setData('package_id', e.target.value)}
                className="w-full border-gray-300 rounded-md shadow-sm focus:border-gold-base focus:ring focus:ring-gold-base/20 p-2 border"
                required
              >
                <option value="">Select a package...</option>
                {packages.map(p => (
                  <option key={p.id} value={p.id}>{p.package_name} - Rp {Number(p.price).toLocaleString('id-ID')}</option>
                ))}
              </select>
              {errors.package_id && <div className="text-red-500 text-xs mt-1">{errors.package_id}</div>}
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Date</label>
              <input
                type="date"
                value={data.booking_date}
                onChange={e => setData('booking_date', e.target.value)}
                className="w-full border-gray-300 rounded-md shadow-sm focus:border-gold-base focus:ring focus:ring-gold-base/20 p-2 border"
                required
              />
              {errors.booking_date && <div className="text-red-500 text-xs mt-1">{errors.booking_date}</div>}
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Event Type</label>
              <input
                type="text"
                placeholder="e.g. Wedding, Engagement, Party"
                value={data.event_type}
                onChange={e => setData('event_type', e.target.value)}
                className="w-full border-gray-300 rounded-md shadow-sm focus:border-gold-base focus:ring focus:ring-gold-base/20 p-2 border"
                required
              />
              {errors.event_type && <div className="text-red-500 text-xs mt-1">{errors.event_type}</div>}
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Location</label>
              <input
                type="text"
                placeholder="Full address for home visit / Studio"
                value={data.location}
                onChange={e => setData('location', e.target.value)}
                className="w-full border-gray-300 rounded-md shadow-sm focus:border-gold-base focus:ring focus:ring-gold-base/20 p-2 border"
                required
              />
              {errors.location && <div className="text-red-500 text-xs mt-1">{errors.location}</div>}
            </div>
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Customization Notes (Optional)</label>
            <textarea
              rows="4"
              placeholder="Any specific design requests or details?"
              value={data.customization_notes}
              onChange={e => setData('customization_notes', e.target.value)}
              className="w-full border-gray-300 rounded-md shadow-sm focus:border-gold-base focus:ring focus:ring-gold-base/20 p-2 border"
            ></textarea>
            {errors.customization_notes && <div className="text-red-500 text-xs mt-1">{errors.customization_notes}</div>}
          </div>

          <div className="pt-4 border-t border-gray-100 flex justify-end">
            <button
              type="submit"
              disabled={processing}
              className="px-8 py-3 bg-plum-800 text-pink-ultra rounded-md hover:bg-plum-700 transition-colors disabled:opacity-50 font-medium tracking-wide"
            >
              Submit Booking
            </button>
          </div>
        </form>
      </main>

      <Footer />
    </div>
  )
}
