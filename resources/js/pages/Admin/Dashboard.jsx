import AdminLayout from '../../layouts/AdminLayout'
import StatCard from '../../components/ui/StatCard'
import Badge from '../../components/ui/Badge'
import { Link } from '@inertiajs/react'
import { BarChart, Bar, XAxis, YAxis, Tooltip, ResponsiveContainer, CartesianGrid } from 'recharts'

export default function Dashboard({ stats, salesChart, recentBookings }) {
  const formatRp = (val) => 'Rp ' + Number(val).toLocaleString('id-ID')

  return (
    <AdminLayout title="Dashboard">
      {/* Stat cards */}
      <div className="grid grid-cols-2 xl:grid-cols-4 gap-4 mb-8">
        <StatCard label="Booking pending"     value={stats.pending_bookings}       icon="ti-clock"        color="red" />
        <StatCard label="Booking bulan ini"   value={stats.bookings_this_month}    icon="ti-calendar"     color="pink" />
        <StatCard label="Total revenue"       value={formatRp(stats.total_revenue)} icon="ti-currency-dollar" color="gold" />
        <StatCard label="Jadwal tersedia"     value={`${stats.available_dates} hari`} icon="ti-check"    color="green" />
      </div>

      <div className="grid lg:grid-cols-3 gap-6">
        {/* Sales chart */}
        <div className="lg:col-span-2 bg-white rounded-xl border border-gray-100 p-6">
          <h2 className="text-sm font-medium text-gray-700 mb-6">Grafik penjualan (6 bulan terakhir)</h2>
          <ResponsiveContainer width="100%" height={240}>
            <BarChart data={salesChart} barSize={28}>
              <CartesianGrid strokeDasharray="3 3" stroke="#f3f4f6" />
              <XAxis dataKey="label" tick={{ fontSize: 11, fill: '#9ca3af' }} axisLine={false} tickLine={false} />
              <YAxis tick={{ fontSize: 11, fill: '#9ca3af' }} axisLine={false} tickLine={false}
                     tickFormatter={(v) => 'Rp' + (v/1000).toFixed(0) + 'k'} />
              <Tooltip formatter={(v) => formatRp(v)} contentStyle={{ fontSize: 12, borderRadius: 8 }} />
              <Bar dataKey="total" fill="#C9A84C" radius={[4,4,0,0]} />
            </BarChart>
          </ResponsiveContainer>
        </div>

        {/* Recent bookings */}
        <div className="bg-white rounded-xl border border-gray-100 p-6">
          <div className="flex items-center justify-between mb-5">
            <h2 className="text-sm font-medium text-gray-700">Booking terbaru</h2>
            <Link href="/admin/bookings" className="text-xs text-gold-base hover:text-gold-deep">Lihat semua →</Link>
          </div>
          <div className="space-y-4">
            {recentBookings.map((b) => (
              <Link key={b.id} href={`/admin/bookings/${b.id}`} className="flex items-center gap-3 hover:bg-gray-50 -mx-2 px-2 py-1.5 rounded-lg transition-colors">
                <div className="w-8 h-8 rounded-full bg-pink-light/50 flex items-center justify-center text-pink-deep text-xs font-medium flex-shrink-0">
                  {b.user_name[0]}
                </div>
                <div className="flex-1 min-w-0">
                  <p className="text-sm font-medium text-gray-800 truncate">{b.user_name}</p>
                  <p className="text-xs text-gray-400">{b.package_name} · {b.date}</p>
                </div>
                <Badge status={b.status} />
              </Link>
            ))}
          </div>
        </div>
      </div>
    </AdminLayout>
  )
}
