import { useState } from 'react'
import { Link, usePage } from '@inertiajs/react'
import { AnimatePresence, motion } from 'framer-motion'
import { Toaster } from 'react-hot-toast'
import clsx from 'clsx'

const NAV = [
  { label: 'Dashboard',  href: '/admin',           icon: 'ti-dashboard' },
  { label: 'Booking',    href: '/admin/bookings',  icon: 'ti-calendar-check' },
  { label: 'Testimoni',  href: '/admin/testimonials', icon: 'ti-star' },
  { label: 'Jadwal',     href: '/admin/schedule',  icon: 'ti-calendar-event' },
  { label: 'Invoice',    href: '/admin/invoices',  icon: 'ti-file-invoice' },
  {
    label: 'CMS',
    icon: 'ti-layout',
    children: [
      { label: 'Paket Layanan', href: '/admin/cms/packages' },
      { label: 'Gallery',       href: '/admin/cms/gallery' },
    ],
  },
  { label: 'Users', href: '/admin/users', icon: 'ti-users' },
]

export default function AdminLayout({ children, title }) {
  const { url, props }   = usePage()
  const { auth, flash }  = props
  const [cmsOpen, setCmsOpen] = useState(url.startsWith('/admin/cms'))
  const [sidebarOpen, setSidebarOpen] = useState(true)
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false)

  const closeMobileMenu = () => setMobileMenuOpen(false)

  return (
    <div className="flex min-h-screen bg-gray-50">
      <Toaster position="top-right" />

      {mobileMenuOpen && (
        <button
          type="button"
          aria-label="Tutup menu admin"
          className="fixed inset-0 z-30 bg-plum-900/45 backdrop-blur-sm lg:hidden"
          onClick={closeMobileMenu}
        />
      )}

      {/* Sidebar */}
      <aside className={clsx(
        'fixed inset-y-0 left-0 z-40 flex flex-col bg-plum-800 transition-all duration-300 flex-shrink-0',
        'w-72 max-w-[82vw] shadow-2xl lg:sticky lg:top-0 lg:h-screen lg:max-w-none lg:shadow-none',
        mobileMenuOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
        sidebarOpen ? 'lg:w-60' : 'lg:w-16'
      )}>
        {/* Logo */}
        <div className="flex items-center gap-3 px-4 py-5 border-b border-white/10">
          <span className={clsx(
            'text-gold-base text-xl font-display font-light tracking-widest',
            !sidebarOpen && 'lg:hidden'
          )}>
            RPNZL Art
          </span>
          <span className={clsx(
            'hidden text-gold-base text-xl font-display font-light tracking-widest',
            !sidebarOpen && 'lg:inline'
          )}>
            R
          </span>
          <button
            type="button"
            onClick={closeMobileMenu}
            className="ml-auto inline-flex h-9 w-9 items-center justify-center rounded-md text-pink-light/60 hover:bg-white/10 hover:text-pink-light lg:hidden"
            aria-label="Tutup menu"
          >
            <i className="ti ti-x text-xl" aria-hidden="true" />
          </button>
        </div>

        {/* Nav */}
        <nav className="flex-1 p-3 space-y-1">
          {NAV.map((item) => {
            if (item.children) {
              return (
                <div key={item.label}>
                  <button
                    onClick={() => setCmsOpen(!cmsOpen)}
                    className="w-full flex items-center gap-3 px-3 py-2.5 rounded-md text-pink-light/70 hover:text-pink-light hover:bg-white/5 transition-colors text-sm"
                  >
                    <i className={`ti ${item.icon} text-base`} aria-hidden="true" />
                    <span className={clsx('flex-1 text-left', !sidebarOpen && 'lg:hidden')}>{item.label}</span>
                    <i className={clsx(
                      'ti text-xs transition-transform',
                      !sidebarOpen && 'lg:hidden',
                      cmsOpen ? 'ti-chevron-up' : 'ti-chevron-down'
                    )} aria-hidden="true" />
                  </button>
                  <AnimatePresence>
                    {cmsOpen && (
                      <motion.div
                        initial={{ height: 0, opacity: 0 }}
                        animate={{ height: 'auto', opacity: 1 }}
                        exit={{ height: 0, opacity: 0 }}
                        transition={{ duration: 0.2 }}
                        className={clsx('overflow-hidden', !sidebarOpen && 'lg:hidden')}
                      >
                        {item.children.map((child) => (
                          <Link
                            key={child.href}
                            href={child.href}
                            onClick={closeMobileMenu}
                            className={clsx(
                              'flex items-center gap-2 pl-10 pr-3 py-2 rounded-md text-sm transition-colors',
                              url === child.href
                                ? 'text-gold-base bg-white/8'
                                : 'text-pink-light/50 hover:text-pink-light'
                            )}
                          >
                            <i className="ti ti-point text-xs" aria-hidden="true" />
                            {child.label}
                          </Link>
                        ))}
                      </motion.div>
                    )}
                  </AnimatePresence>
                </div>
              )
            }

            return (
              <Link
                key={item.href}
                href={item.href}
                onClick={closeMobileMenu}
                className={clsx(
                  'flex items-center gap-3 px-3 py-2.5 rounded-md text-sm transition-colors',
                  url === item.href
                    ? 'bg-gold-base/20 text-gold-base'
                    : 'text-pink-light/70 hover:text-pink-light hover:bg-white/5'
                )}
              >
                <i className={`ti ${item.icon} text-base`} aria-hidden="true" />
                <span className={clsx(!sidebarOpen && 'lg:hidden')}>{item.label}</span>
              </Link>
            )
          })}
        </nav>

        {/* User info */}
        <div className={clsx('p-3 border-t border-white/10', !sidebarOpen && 'lg:hidden')}>
            <div className="flex items-center gap-3 px-3 py-2">
              <div className="w-8 h-8 rounded-full bg-gold-base/20 flex items-center justify-center text-gold-base text-xs font-medium">
                {auth.user?.name?.[0] ?? 'A'}
              </div>
              <div className="flex-1 min-w-0">
                <p className="text-xs text-pink-light truncate">{auth.user?.name}</p>
                <p className="text-xs text-pink-light/40">Admin</p>
              </div>
              <Link
                href="/logout"
                method="post"
                as="button"
                className="text-pink-light/40 hover:text-pink-light"
              >
                <i className="ti ti-logout text-base" aria-hidden="true" />
              </Link>
            </div>
          </div>
      </aside>

      {/* Main */}
      <div className="flex-1 flex flex-col min-w-0">
        {/* Topbar */}
        <header className="sticky top-0 z-20 h-14 bg-white border-b border-gray-100 flex items-center px-4 sm:px-6 gap-3 sm:gap-4">
          <button
            onClick={() => setMobileMenuOpen(true)}
            className="inline-flex h-9 w-9 items-center justify-center rounded-md text-gray-400 hover:bg-gray-50 hover:text-gray-600 lg:hidden"
            aria-label="Buka menu"
          >
            <i className="ti ti-menu-2 text-xl" aria-hidden="true" />
          </button>
          <button
            onClick={() => setSidebarOpen(!sidebarOpen)}
            className="hidden h-9 w-9 items-center justify-center rounded-md text-gray-400 hover:bg-gray-50 hover:text-gray-600 lg:inline-flex"
            aria-label="Toggle sidebar"
          >
            <i className="ti ti-menu-2 text-xl" aria-hidden="true" />
          </button>
          <h1 className="font-medium text-gray-800 text-sm flex-1 truncate">{title}</h1>
        </header>

        {/* Flash messages */}
        {flash?.success && (
          <div className="mx-4 mt-4 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg text-sm flex items-center gap-2 sm:mx-6">
            <i className="ti ti-check" aria-hidden="true" />
            {flash.success}
          </div>
        )}

        {/* Page content */}
        <motion.main
          key={url}
          initial={{ opacity: 0, y: 8 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.3, ease: [0.22, 1, 0.36, 1] }}
          className="flex-1 p-4 sm:p-6"
        >
          {children}
        </motion.main>
      </div>
    </div>
  )
}
