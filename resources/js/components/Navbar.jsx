import { Link, usePage } from '@inertiajs/react'

export default function Navbar() {
  const { auth } = usePage().props

  return (
    <nav className="h-20 flex items-center justify-between px-6 md:px-12 bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-pink-light/30">
      <Link href="/" className="font-display text-2xl text-plum-800 tracking-widest uppercase">
        RPNZL <span className="text-gold-base">Art</span>
      </Link>
      <div className="flex items-center gap-6 text-sm">
        <a href="/#gallery" className="text-gray-600 hover:text-plum-800 transition-colors">Gallery</a>
        {auth.user?.role === 'admin' ? (
          <Link href="/admin" className="px-5 py-2 bg-plum-800 text-pink-ultra rounded-sm hover:bg-plum-700 transition-colors">
            Dashboard
          </Link>
        ) : auth.user ? (
          <Link href="/booking/status" className="px-5 py-2 bg-pink-light/20 text-pink-deep rounded-sm hover:bg-pink-light/40 transition-colors">
            My Bookings
          </Link>
        ) : (
          <Link href="/login" className="px-5 py-2 bg-plum-800 text-pink-ultra rounded-sm hover:bg-plum-700 transition-colors">
            Login
          </Link>
        )}
      </div>
    </nav>
  )
}
