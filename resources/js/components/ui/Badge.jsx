import clsx from 'clsx'

const styles = {
  pending:   'bg-amber-50 text-amber-700 border-amber-200',
  confirmed: 'bg-emerald-50 text-emerald-700 border-emerald-200',
  rejected:  'bg-red-50 text-red-600 border-red-200',
  done:      'bg-blue-50 text-blue-700 border-blue-200',
  active:    'bg-emerald-50 text-emerald-700 border-emerald-200',
  inactive:  'bg-gray-50 text-gray-500 border-gray-200',
  available: 'bg-emerald-50 text-emerald-700 border-emerald-200',
  limited:   'bg-amber-50 text-amber-700 border-amber-200',
  fully_booked: 'bg-red-50 text-red-600 border-red-200',
  blocked:   'bg-gray-50 text-gray-500 border-gray-200',
}

const labels = {
  pending: 'Menunggu', confirmed: 'Dikonfirmasi',
  rejected: 'Ditolak', done: 'Selesai',
  active: 'Aktif', inactive: 'Nonaktif',
  available: 'Tersedia',
  limited: 'Terbatas',
  fully_booked: 'Penuh',
  blocked: 'Diblokir',
}

export default function Badge({ status }) {
  return (
    <span className={clsx(
      'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border',
      styles[status] ?? 'bg-gray-100 text-gray-600'
    )}>
      {labels[status] ?? status}
    </span>
  )
}
