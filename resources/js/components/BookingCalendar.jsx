import { useState, useEffect } from 'react'
import { Link, usePage } from '@inertiajs/react'
import axios from 'axios'

const MONTHS_ID = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember']
const DAYS_ID   = ['Min','Sen','Sel','Rab','Kam','Jum','Sab']

export default function BookingCalendar() {
  const { auth } = usePage().props
  const [view, setView]         = useState(new Date())
  const [unavailable, setUnavail] = useState([])
  const [selected, setSelected]   = useState(null)
  const [loading, setLoading]     = useState(false)

  useEffect(() => {
    setLoading(true)
    axios.get('/api/schedules/unavailable', {
      params: { year: view.getFullYear(), month: view.getMonth() + 1 }
    }).then(res => {
      setUnavail(res.data.dates)
    }).finally(() => setLoading(false))
  }, [view])

  const firstDay    = new Date(view.getFullYear(), view.getMonth(), 1).getDay()
  const daysInMonth = new Date(view.getFullYear(), view.getMonth() + 1, 0).getDate()
  const today       = new Date(); today.setHours(0,0,0,0)

  const dateStr = (d) => `${view.getFullYear()}-${String(view.getMonth()+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`

  return (
    <div className="mt-6 border border-gold-base/20 rounded-md overflow-hidden">
      {/* Header */}
      <div className="bg-plum-800 px-4 py-3 flex items-center justify-between">
        <button onClick={() => setView(new Date(view.getFullYear(), view.getMonth()-1, 1))} className="text-gold-light/70 hover:text-gold-light text-lg">‹</button>
        <span className="text-xs tracking-[3px] uppercase text-gold-light">
          {MONTHS_ID[view.getMonth()]} {view.getFullYear()}
        </span>
        <button onClick={() => setView(new Date(view.getFullYear(), view.getMonth()+1, 1))} className="text-gold-light/70 hover:text-gold-light text-lg">›</button>
      </div>

      {/* Days header */}
      <div className="grid grid-cols-7 bg-gold-pale px-3 py-2 border-b border-gold-base/10">
        {DAYS_ID.map(d => <span key={d} className="text-center text-[9px] tracking-wider uppercase text-gold-deep/70 font-medium">{d}</span>)}
      </div>

      {/* Grid */}
      <div className="grid grid-cols-7 p-3 gap-1">
        {Array(firstDay).fill(null).map((_, i) => <div key={i} />)}
        {Array.from({ length: daysInMonth }, (_, i) => i + 1).map(d => {
          const ds = dateStr(d)
          const isPast     = new Date(view.getFullYear(), view.getMonth(), d) < today
          const isUnavail  = unavailable.includes(ds)
          const isToday    = d === today.getDate() && view.getMonth() === today.getMonth() && view.getFullYear() === today.getFullYear()
          const isSelected = selected === ds

          return (
            <button
              key={d}
              disabled={isPast || isUnavail}
              onClick={() => setSelected(ds)}
              className={[
                'aspect-square flex items-center justify-center text-xs rounded-sm transition-colors',
                isPast     && 'text-gray-300 cursor-default',
                isUnavail  && 'bg-pink-light text-pink-deep cursor-not-allowed',
                isSelected && 'bg-gold-base text-plum-800 font-medium',
                isToday && !isSelected && 'border border-gold-base text-gold-deep',
                !isPast && !isUnavail && !isSelected && 'bg-pink-ultra/50 text-pink-deep hover:bg-gold-pale',
              ].filter(Boolean).join(' ')}
            >
              {d}
            </button>
          )
        })}
      </div>

      {/* Legend */}
      <div className="flex gap-4 px-4 pb-3 pt-1">
        <span className="flex items-center gap-1.5 text-[10px] text-gray-400"><span className="w-3 h-3 rounded-sm bg-pink-ultra/50 border border-gray-100 inline-block" />Tersedia</span>
        <span className="flex items-center gap-1.5 text-[10px] text-gray-400"><span className="w-3 h-3 rounded-sm bg-pink-light inline-block" />Penuh</span>
      </div>

      {/* CTA */}
      {selected && (
        <div className="border-t border-gold-base/10 p-3 bg-gold-pale/50">
          <p className="text-xs text-gold-deep mb-2">Tanggal dipilih: <strong>{selected}</strong></p>
          {auth.user
            ? <Link href={`/booking?date=${selected}`} className="block w-full text-center py-2.5 bg-gold-base text-plum-800 text-xs font-medium tracking-wider uppercase rounded-sm hover:bg-gold-light transition-colors">Lanjut Booking →</Link>
            : <Link href="/login" className="block w-full text-center py-2.5 bg-plum-800 text-pink-ultra text-xs font-medium tracking-wider uppercase rounded-sm hover:bg-plum-700 transition-colors">Login untuk booking →</Link>
          }
        </div>
      )}
    </div>
  )
}
