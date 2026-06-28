import AdminLayout from '../../../layouts/AdminLayout'
import { Link } from '@inertiajs/react'

export default function UsersIndex({ users }) {
  return (
    <AdminLayout title="Pelanggan">
      <div className="bg-white rounded-xl border border-gray-100 overflow-hidden">
        <table className="w-full text-sm">
          <thead className="bg-gray-50 border-b border-gray-100">
            <tr>
              {['Nama', 'Email', 'WhatsApp', 'Total Booking', 'Bergabung'].map(h => (
                <th key={h} className="text-left px-5 py-3.5 text-xs font-medium text-gray-500 tracking-wide">{h}</th>
              ))}
            </tr>
          </thead>
          <tbody className="divide-y divide-gray-50">
            {users.data.length === 0 ? (
              <tr><td colSpan="5" className="text-center py-6 text-gray-500">Belum ada pelanggan</td></tr>
            ) : users.data.map(u => (
              <tr key={u.id} className="hover:bg-gray-50/50 transition-colors">
                <td className="px-5 py-4 font-medium text-gray-800">{u.name}</td>
                <td className="px-5 py-4 text-gray-600">{u.email}</td>
                <td className="px-5 py-4 text-gray-600">{u.whatsapp_number}</td>
                <td className="px-5 py-4 text-gray-600">
                  <span className="inline-flex items-center justify-center px-2 py-1 rounded bg-pink-ultra text-pink-deep text-xs font-medium min-w-[2rem]">
                    {u.bookings_count}
                  </span>
                </td>
                <td className="px-5 py-4 text-gray-600">{u.created_at}</td>
              </tr>
            ))}
          </tbody>
        </table>

        {/* Pagination */}
        <div className="flex items-center justify-between px-5 py-4 border-t border-gray-100">
          <p className="text-xs text-gray-400">
            Menampilkan {users.from || 0}–{users.to || 0} dari {users.total}
          </p>
          <div className="flex gap-1">
            {users.links.map((link, idx) => (
              <Link key={idx} href={link.url ?? '#'}
                className={`px-3 py-1.5 text-xs rounded-md border transition-colors ${
                  link.active ? 'bg-gold-base text-plum-800 border-gold-base' : 'border-gray-200 text-gray-500 hover:bg-gray-50'
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
