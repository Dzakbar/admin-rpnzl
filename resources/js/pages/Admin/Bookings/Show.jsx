import { useState } from 'react'
import { router } from '@inertiajs/react'
import AdminLayout from '../../../layouts/AdminLayout'
import Button from '../../../components/ui/Button'
import Badge from '../../../components/ui/Badge'
import OngkirModal from '../../../components/ui/OngkirModal'
import toast from 'react-hot-toast'

export default function BookingShow({ booking }) {
  const [shippingCost, setShippingCost] = useState(
    booking.invoice ? Number(booking.invoice.shipping_cost || 0) : 0
  )
  const [isUpdatingOngkir, setIsUpdatingOngkir] = useState(false)
  const [modalOpen, setModalOpen] = useState(false)
  const [pendingStatus, setPendingStatus] = useState(null)
  const [isSubmittingStatus, setIsSubmittingStatus] = useState(false)

  const openStatusModal = (status) => {
    setPendingStatus(status)
    setModalOpen(true)
  }

  const handleStatusConfirm = (status, ongkir) => {
    setIsSubmittingStatus(true)
    router.patch(`/admin/bookings/${booking.id}/status`, { status, shipping_cost: ongkir }, {
      onSuccess: () => {
        toast.success('Status berhasil diperbarui')
        setModalOpen(false)
        setPendingStatus(null)
        setIsSubmittingStatus(false)
      },
      onError: () => {
        toast.error('Gagal memperbarui status')
        setIsSubmittingStatus(false)
      },
    })
  }

  const handleModalCancel = () => {
    setModalOpen(false)
    setPendingStatus(null)
  }

  const updateOngkir = () => {
    if (!booking.invoice) return
    setIsUpdatingOngkir(true)
    router.patch(`/admin/invoices/${booking.invoice.id}`, { shipping_cost: shippingCost }, {
      onSuccess: () => {
        toast.success('Ongkir berhasil diperbarui')
        setIsUpdatingOngkir(false)
      },
      onError: () => {
        toast.error('Gagal memperbarui ongkir')
        setIsUpdatingOngkir(false)
      },
    })
  }

  const grandTotal = booking.invoice
    ? Number(booking.invoice.total_price) + Number(shippingCost)
    : 0

  return (
    <AdminLayout title={`Detail Booking #${booking.id.slice(0,8)}`}>
      <OngkirModal
        isOpen={modalOpen}
        status={pendingStatus}
        onConfirm={handleStatusConfirm}
        onCancel={handleModalCancel}
        isLoading={isSubmittingStatus}
      />
      <div className="max-w-5xl space-y-5">

        {/* Status & Actions */}
        <div className="bg-white rounded-xl border border-gray-100 p-4 flex flex-col gap-4 sm:p-5 md:flex-row md:items-center md:justify-between">
          <div>
            <p className="text-xs text-gray-400 mb-1">Status saat ini</p>
            <Badge status={booking.status} />
          </div>
           {booking.status === 'pending' && (
             <div className="flex flex-col gap-3 sm:flex-row">
               <Button variant="danger"  onClick={() => openStatusModal('rejected')}>
                 <i className="ti ti-x" aria-hidden="true" /> Tolak
               </Button>
               <Button variant="gold"    onClick={() => openStatusModal('confirmed')}>
                 <i className="ti ti-check" aria-hidden="true" /> Konfirmasi
               </Button>
             </div>
           )}
          {booking.status === 'confirmed' && !booking.invoice && (
            <Button variant="outline" onClick={() => router.post(`/admin/invoices/${booking.id}/generate`)}>
              <i className="ti ti-file-invoice" aria-hidden="true" /> Generate Invoice
            </Button>
          )}
        </div>

        {/* Info cards */}
        <div className="grid md:grid-cols-2 gap-5">
          <InfoCard title="Pelanggan" icon="ti-user">
            <Row label="Nama"       value={booking.user.name} />
            <Row label="Email"      value={booking.user.email} />
            <Row label="WhatsApp"   value={booking.user.whatsapp_number} />
          </InfoCard>
          <InfoCard title="Detail booking" icon="ti-clipboard-list">
            <Row label="Paket"      value={booking.package.package_name} />
            <Row label="Harga"      value={'Rp ' + Number(booking.package.price).toLocaleString('id-ID')} />
            <Row label="Tanggal"    value={booking.schedule.booking_date} />
            <Row label="Event"      value={booking.event_type} />
            <Row label="Lokasi"     value={booking.location} />
          </InfoCard>
        </div>

        {/* Notes */}
        {booking.customization_notes && (
          <div className="bg-white rounded-xl border border-gray-100 p-4 sm:p-5">
            <p className="text-xs text-gray-400 mb-2 font-medium uppercase tracking-wide">Catatan kustomisasi</p>
            <p className="text-sm text-gray-700 leading-relaxed">{booking.customization_notes}</p>
          </div>
        )}

        {/* Invoice */}
        {booking.invoice && (
          <div className="space-y-3">
            <div className="bg-gold-pale rounded-xl border border-gold-base/20 p-4 sm:p-5">
              <div className="flex flex-col gap-3 mb-4 sm:flex-row sm:items-center sm:justify-between">
                <p className="text-xs text-gold-deep font-medium">Invoice #{booking.invoice.invoice_number}</p>
                <a href={booking.invoice.pdf_url} target="_blank" rel="noreferrer">
                  <Button variant="gold" size="sm"><i className="ti ti-download" aria-hidden="true" /> Download PDF</Button>
                </a>
              </div>

              <div className="space-y-2 text-sm">
                <div className="flex flex-col gap-1 sm:flex-row sm:justify-between">
                  <span className="text-gray-400">Harga paket</span>
                  <span className="font-medium text-gray-700">Rp {Number(booking.invoice.total_price).toLocaleString('id-ID')}</span>
                </div>

                <div className="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                  <span className="text-gray-400 shrink-0">Ongkir</span>
                  <div className="flex flex-wrap items-center gap-2">
                    <span className="text-gray-400 text-xs">Rp</span>
                    <input
                      type="text"
                      inputMode="numeric"
                      value={shippingCost === 0 ? '' : shippingCost}
                      onFocus={(e) => e.target.select()}
                      onChange={(e) => {
                        const val = e.target.value.replace(/[^0-9]/g, '')
                        setShippingCost(val === '' ? 0 : Number(val))
                      }}
                      placeholder="0"
                      className="w-32 max-w-full border border-gold-base/30 rounded-lg px-3 py-1.5 text-sm text-gray-700 text-right focus:outline-none focus:border-gold-base"
                    />
                    <Button
                      variant="outline"
                      size="sm"
                      onClick={updateOngkir}
                      disabled={isUpdatingOngkir}
                    >
                      {isUpdatingOngkir ? '...' : 'Simpan'}
                    </Button>
                  </div>
                </div>

                <div className="border-t border-gold-base/20 pt-2 flex flex-col gap-1 font-semibold text-plum-800 sm:flex-row sm:justify-between">
                  <span>Grand Total</span>
                  <span>Rp {grandTotal.toLocaleString('id-ID')}</span>
                </div>
              </div>
            </div>
          </div>
        )}
      </div>
    </AdminLayout>
  )
}

function InfoCard({ title, icon, children }) {
  return (
    <div className="bg-white rounded-xl border border-gray-100 p-4 sm:p-5">
      <div className="flex items-center gap-2 mb-4">
        <i className={`ti ${icon} text-gold-base text-lg`} aria-hidden="true" />
        <h3 className="text-sm font-medium text-gray-700">{title}</h3>
      </div>
      <div className="space-y-2">{children}</div>
    </div>
  )
}

function Row({ label, value }) {
  return (
    <div className="flex flex-col gap-1 text-sm sm:flex-row sm:justify-between">
      <span className="text-gray-400">{label}</span>
      <span className="text-gray-700 font-medium break-words sm:max-w-xs sm:text-right">{value}</span>
    </div>
  )
}
