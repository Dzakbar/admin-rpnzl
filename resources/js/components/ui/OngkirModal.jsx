import { useState } from 'react'
import Button from './Button'

export default function OngkirModal({ isOpen, status, onConfirm, onCancel, isLoading }) {
  const [ongkir, setOngkir] = useState('')

  const handleConfirm = () => {
    onConfirm(status, Number(ongkir || 0))
    setOngkir('')
  }

  const handleCancel = () => {
    onCancel()
    setOngkir('')
  }

  if (!isOpen) return null

  const statusLabel = status === 'confirmed' ? 'mengkonfirmasi' : 'menolak'

  return (
    <div className="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
      <div className="bg-white rounded-2xl max-w-sm w-full mx-4 p-6 shadow-lg">
        <h3 className="text-lg font-semibold text-gray-800 mb-2">
          Konfirmasi {statusLabel === 'mengkonfirmasi' ? 'Booking' : 'Penolakan'}
        </h3>
        <p className="text-sm text-gray-600 mb-6">
          {statusLabel === 'mengkonfirmasi' 
            ? 'Masukkan biaya ongkir sebelum mengkonfirmasi booking ini.'
            : 'Masukkan biaya ongkir sebelum menolak booking ini.'}
        </p>

        <div className="mb-6">
          <label className="block text-sm font-medium text-gray-700 mb-2">
            Biaya Ongkir
          </label>
          <div className="flex items-center gap-2">
            <span className="text-gray-600">Rp</span>
            <input
              type="text"
              inputMode="numeric"
              value={ongkir === '' ? '' : ongkir}
              onChange={(e) => {
                const val = e.target.value.replace(/[^0-9]/g, '')
                setOngkir(val === '' ? '' : val)
              }}
              onFocus={(e) => e.target.select()}
              placeholder="0"
              autoFocus
              className="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none focus:border-gold-base focus:ring-1 focus:ring-gold-base"
            />
          </div>
          <p className="text-xs text-gray-500 mt-2">
            Boleh diisi 0 untuk pengiriman gratis
          </p>
        </div>

        <div className="flex gap-3">
          <Button
            variant="outline"
            className="flex-1"
            onClick={handleCancel}
            disabled={isLoading}
          >
            Batal
          </Button>
          <Button
            variant={status === 'confirmed' ? 'gold' : 'danger'}
            className="flex-1"
            onClick={handleConfirm}
            disabled={isLoading}
          >
            {isLoading ? '...' : 'Lanjutkan'}
          </Button>
        </div>
      </div>
    </div>
  )
}
