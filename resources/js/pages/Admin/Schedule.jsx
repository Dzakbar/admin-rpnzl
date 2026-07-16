import { useMemo, useState } from 'react'
import { router, useForm } from '@inertiajs/react'
import AdminLayout from '../../layouts/AdminLayout'
import Button from '../../components/ui/Button'
import Badge from '../../components/ui/Badge'
import toast from 'react-hot-toast'

const MONTHS = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember']

const STATUS_OPTIONS = [
  { value: 'available', label: 'Tersedia' },
  { value: 'limited', label: 'Terbatas' },
  { value: 'fully_booked', label: 'Penuh' },
  { value: 'blocked', label: 'Diblokir' },
]

const STATUS_HINTS = {
  available: 'Slot bisa dipilih customer.',
  limited: 'Slot masih bisa dipilih, tapi ditandai terbatas.',
  fully_booked: 'Slot tampil penuh dan tidak bisa dipilih.',
  blocked: 'Slot ditutup dan tidak bisa dipilih.',
}

const emptyForm = { date: '', time: '09:00', status: 'available', notes: '' }

export default function Schedule({ schedules, year, month }) {
  const [editingId, setEditingId] = useState(null)
  const { data, setData, post, put, processing, errors, reset, clearErrors } = useForm(emptyForm)

  const groupedSchedules = useMemo(() => {
    return schedules.reduce((groups, slot) => {
      if (!groups[slot.booking_date]) groups[slot.booking_date] = []
      groups[slot.booking_date].push(slot)
      return groups
    }, {})
  }, [schedules])

  const changeMonth = (delta) => {
    let nextMonth = month + delta
    let nextYear = year

    if (nextMonth > 12) { nextMonth = 1; nextYear++ }
    if (nextMonth < 1)  { nextMonth = 12; nextYear-- }

    router.get('/admin/schedule', { year: nextYear, month: nextMonth }, { preserveState: true })
  }

  const submitSlot = (e) => {
    e.preventDefault()

    const options = {
      preserveScroll: true,
      onSuccess: () => {
        toast.success(editingId ? 'Slot berhasil diperbarui' : 'Slot berhasil disimpan')
        cancelEdit()
      },
    }

    if (editingId) {
      put(`/admin/schedule/${editingId}`, options)
      return
    }

    post('/admin/schedule', options)
  }

  const editSlot = (slot) => {
    clearErrors()
    setEditingId(slot.id)
    setData({
      date: slot.booking_date,
      time: slot.booking_time,
      status: slot.configured_status ?? slot.status,
      notes: slot.notes ?? '',
    })
  }

  const cancelEdit = () => {
    setEditingId(null)
    reset()
    clearErrors()
  }

  const deleteSlot = (slot) => {
    if (!confirm(`Hapus slot ${slot.booking_date} jam ${slot.booking_time}?`)) return

    router.delete(`/admin/schedule/${slot.id}`, {
      preserveScroll: true,
      onSuccess: () => toast.success('Slot berhasil dihapus'),
    })
  }

  const openSlot = (slot) => {
    router.put(`/admin/schedule/${slot.id}`, {
      date: slot.booking_date,
      time: slot.booking_time,
      status: 'available',
      notes: slot.notes ?? '',
    }, {
      preserveScroll: true,
      onSuccess: () => toast.success('Slot dibuka'),
    })
  }

  return (
    <AdminLayout title="Manajemen Jadwal">
      <div className="grid lg:grid-cols-3 gap-6">
        <div className="lg:col-span-2 bg-white rounded-xl border border-gray-100 p-4 sm:p-5">
          <div className="flex flex-col gap-2 mb-6 bg-gray-50 p-2 rounded-lg sm:flex-row sm:items-center sm:justify-between">
            <button onClick={() => changeMonth(-1)} className="px-3 py-2 hover:bg-gray-200 rounded text-sm">
              &lt; Sebelumnya
            </button>
            <h2 className="font-medium text-gray-800 text-center">{MONTHS[month - 1]} {year}</h2>
            <button onClick={() => changeMonth(1)} className="px-3 py-2 hover:bg-gray-200 rounded text-sm">
              Selanjutnya &gt;
            </button>
          </div>

          <div className="mb-5">
            <p className="text-sm font-medium text-gray-800">Slot kalender owner</p>
            <p className="text-xs text-gray-500 mt-1">
              Tanggal yang belum punya slot akan kosong dan tidak bisa dipilih customer.
            </p>
          </div>

          {Object.keys(groupedSchedules).length === 0 ? (
            <div className="border border-dashed border-gray-200 rounded-lg p-8 text-center">
              <p className="text-sm font-medium text-gray-700">Belum ada slot di bulan ini.</p>
              <p className="text-xs text-gray-500 mt-1">Tambahkan tanggal dan jam dari form di kanan.</p>
            </div>
          ) : (
            <div className="space-y-5">
              {Object.entries(groupedSchedules).map(([date, slots]) => (
                <div key={date}>
                  <div className="flex flex-wrap items-center gap-3 mb-2">
                    <p className="font-medium text-gray-900">{date}</p>
                    <span className="text-xs text-gray-400">{slots.length} slot</span>
                  </div>
                  <div className="space-y-2">
                    {slots.map(slot => (
                      <div key={slot.id} className="flex flex-col md:flex-row md:items-center justify-between gap-3 p-3 border border-gray-100 rounded-lg">
                        <div className="min-w-0">
                          <div className="flex flex-wrap items-center gap-2">
                            <p className="font-medium text-gray-800">{slot.booking_time}</p>
                            <Badge status={slot.status} />
                            {slot.configured_status !== slot.status && (
                              <span className="text-[11px] text-gray-400">Owner: {slot.configured_status}</span>
                            )}
                          </div>
                          <p className="text-xs text-gray-500 mt-1">
                            {slot.booking_count} booking aktif, {slot.remaining_slots} slot tersisa
                            {slot.notes ? ` - ${slot.notes}` : ''}
                          </p>
                        </div>
                        <div className="flex flex-wrap items-center gap-3 shrink-0 md:justify-end">
                          {slot.status !== 'available' && slot.booking_count === 0 && (
                            <button onClick={() => openSlot(slot)} className="text-xs font-medium text-emerald-600 hover:text-emerald-700">
                              Buka
                            </button>
                          )}
                          <button onClick={() => editSlot(slot)} className="text-xs font-medium text-plum-800 hover:text-plum-600">
                            Edit
                          </button>
                          <button onClick={() => deleteSlot(slot)} className="text-xs font-medium text-red-500 hover:text-red-600">
                            Hapus
                          </button>
                        </div>
                      </div>
                    ))}
                  </div>
                </div>
              ))}
            </div>
          )}
        </div>

        <div>
          <div className="bg-white rounded-xl border border-gray-100 p-4 sm:p-5 lg:sticky lg:top-20">
            <div className="flex items-start justify-between gap-3 mb-4">
              <div>
                <h3 className="text-sm font-medium text-gray-800">{editingId ? 'Edit Slot' : 'Tambah Slot'}</h3>
                <p className="text-xs text-gray-500 mt-1">Atur tanggal, jam, dan status ketersediaan.</p>
              </div>
              {editingId && (
                <button type="button" onClick={cancelEdit} className="text-xs text-gray-400 hover:text-gray-600">
                  Batal
                </button>
              )}
            </div>

            <form onSubmit={submitSlot} className="space-y-4">
              <div>
                <label className="block text-xs font-medium text-gray-700 mb-1">Tanggal</label>
                <input
                  type="date"
                  value={data.date}
                  onChange={e => setData('date', e.target.value)}
                  className="w-full border-gray-300 rounded-md shadow-sm text-sm p-2 border focus:border-gold-base focus:ring focus:ring-gold-base/20"
                  required
                />
                {errors.date && <p className="text-xs text-red-500 mt-1">{errors.date}</p>}
              </div>

              <div>
                <label className="block text-xs font-medium text-gray-700 mb-1">Jam</label>
                <input
                  type="time"
                  value={data.time}
                  onChange={e => setData('time', e.target.value)}
                  className="w-full border-gray-300 rounded-md shadow-sm text-sm p-2 border focus:border-gold-base focus:ring focus:ring-gold-base/20"
                  required
                />
                {errors.time && <p className="text-xs text-red-500 mt-1">{errors.time}</p>}
              </div>

              <div>
                <label className="block text-xs font-medium text-gray-700 mb-1">Status Slot</label>
                <select
                  value={data.status}
                  onChange={e => setData('status', e.target.value)}
                  className="w-full border-gray-300 rounded-md shadow-sm text-sm p-2 border focus:border-gold-base focus:ring focus:ring-gold-base/20"
                  required
                >
                  {STATUS_OPTIONS.map(status => (
                    <option key={status.value} value={status.value}>{status.label}</option>
                  ))}
                </select>
                <p className="text-[11px] text-gray-400 mt-1">{STATUS_HINTS[data.status]}</p>
                {errors.status && <p className="text-xs text-red-500 mt-1">{errors.status}</p>}
              </div>

              <div>
                <label className="block text-xs font-medium text-gray-700 mb-1">Catatan</label>
                <input
                  type="text"
                  value={data.notes}
                  onChange={e => setData('notes', e.target.value)}
                  placeholder="Misal: Studio only, crew terbatas"
                  className="w-full border-gray-300 rounded-md shadow-sm text-sm p-2 border focus:border-gold-base focus:ring focus:ring-gold-base/20"
                />
                {errors.notes && <p className="text-xs text-red-500 mt-1">{errors.notes}</p>}
              </div>

              {(errors.schedule || errors.date) && (
                <p className="text-xs text-red-500">{errors.schedule ?? errors.date}</p>
              )}

              <Button type="submit" variant="primary" disabled={processing} className="w-full justify-center">
                {editingId ? 'Simpan Perubahan' : 'Simpan Slot'}
              </Button>
            </form>
          </div>
        </div>
      </div>
    </AdminLayout>
  )
}
