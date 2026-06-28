export default function StatCard({ label, value, icon, color = 'gold', trend }) {
  const colors = {
    gold:  'bg-gold-pale text-gold-deep',
    pink:  'bg-pink-light/30 text-pink-deep',
    green: 'bg-emerald-50 text-emerald-700',
    red:   'bg-red-50 text-red-600',
  }

  return (
    <div className="bg-white rounded-xl border border-gray-100 p-5">
      <div className="flex items-start justify-between mb-3">
        <span className="text-xs text-gray-400 font-medium tracking-wide uppercase">{label}</span>
        <div className={`w-9 h-9 rounded-lg flex items-center justify-center ${colors[color]}`}>
          <i className={`ti ${icon} text-lg`} aria-hidden="true" />
        </div>
      </div>
      <div className="text-2xl font-semibold text-gray-900">{value}</div>
      {trend && <p className="text-xs text-gray-400 mt-1">{trend}</p>}
    </div>
  )
}
