import AdminLayout from '../../../layouts/AdminLayout'
import { Link } from '@inertiajs/react'

export default function InvoicesIndex({ invoices }) {
  return (
    <AdminLayout title="Invoice">
      <div className="bg-white rounded-xl border border-gray-100 overflow-hidden">
        <table className="w-full text-sm">
          <thead className="bg-gray-50 border-b border-gray-100">
            <tr>
              {['No Invoice', 'Tanggal', 'Pelanggan', 'Paket', 'Total', 'Ongkir', 'Grand Total', 'Aksi'].map(h => (
                <th key={h} className="text-left px-5 py-3.5 text-xs font-medium text-gray-500 tracking-wide">{h}</th>
              ))}
            </tr>
          </thead>
          <tbody className="divide-y divide-gray-50">
            {invoices.data.length === 0 ? (
              <tr><td colSpan="8" className="text-center py-6 text-gray-500">Belum ada invoice</td></tr>
            ) : invoices.data.map(i => (
              <tr key={i.id} className="hover:bg-gray-50/50 transition-colors">
                <td className="px-5 py-4 font-medium text-plum-800">{i.invoice_number}</td>
                <td className="px-5 py-4 text-gray-600">{i.invoice_date}</td>
                <td className="px-5 py-4 text-gray-800">{i.user_name}</td>
                <td className="px-5 py-4 text-gray-600">{i.package_name}</td>
                <td className="px-5 py-4 text-gray-600">Rp {Number(i.total_price).toLocaleString('id-ID')}</td>
                <td className="px-5 py-4 text-gray-600">Rp {Number(i.shipping_cost || 0).toLocaleString('id-ID')}</td>
                <td className="px-5 py-4 font-medium text-plum-800">Rp {Number(i.grand_total).toLocaleString('id-ID')}</td>
                <td className="px-5 py-4">
                  <a
                    href={`/admin/invoices/${i.id}/download`}
                    target="_blank"
                    rel="noreferrer"
                    className="text-gold-deep hover:text-gold-base text-xs font-medium"
                  >
                    Unduh PDF
                  </a>
                </td>
              </tr>
            ))}
          </tbody>
        </table>

        {/* Pagination */}
        <div className="flex items-center justify-between px-5 py-4 border-t border-gray-100">
          <p className="text-xs text-gray-400">
            Menampilkan {invoices.from || 0}–{invoices.to || 0} dari {invoices.total}
          </p>
          <div className="flex gap-1">
            {invoices.links.map((link, idx) => (
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
