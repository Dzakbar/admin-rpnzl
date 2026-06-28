import { Link } from '@inertiajs/react'
import { motion } from 'framer-motion'
import Navbar from '../components/Navbar'
import Footer from '../components/Footer'
import BookingCalendar from '../components/BookingCalendar'

export default function Home({ packages, gallery, contents }) {
  return (
    <>
      <Navbar />
      <HeroSection contents={contents} />
      <CalendarSection />
      <ServicesSection packages={packages} />
      <GallerySection gallery={gallery} />
      <CtaSection />
      <Footer />
    </>
  )
}

function HeroSection({ contents }) {
  return (
    <section className="relative min-h-[520px] flex items-center justify-center bg-plum-800 overflow-hidden">
      <div className="absolute inset-0 opacity-[0.06]"
        style={{ backgroundImage: 'linear-gradient(#C9A84C 1px, transparent 1px), linear-gradient(90deg, #C9A84C 1px, transparent 1px)', backgroundSize: '36px 36px' }}
      />
      <motion.div
        initial={{ opacity: 0, y: 24 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.7, ease: [0.22, 1, 0.36, 1] }}
        className="relative z-10 text-center px-6 py-20"
      >
        <p className="text-xs tracking-[5px] uppercase text-gold-light/80 mb-4">✦ Henna Art & Beauty</p>
        <h1 className="font-display text-5xl md:text-6xl font-light text-pink-ultra leading-tight mb-5"
            dangerouslySetInnerHTML={{ __html: contents.hero_title }}
        />
        <p className="text-sm text-pink-light/60 leading-relaxed mb-9 max-w-md mx-auto">
          {contents.hero_subtitle}
        </p>
        <div className="flex gap-4 justify-center">
          <Link href="/booking" className="px-9 py-3 bg-gold-base text-plum-800 text-xs tracking-widest uppercase font-medium rounded-sm hover:bg-gold-light transition-colors">
            Book a Session
          </Link>
          <a href="#gallery" className="px-9 py-3 border border-pink-light/30 text-pink-light text-xs tracking-widest uppercase rounded-sm hover:bg-white/5 transition-colors">
            View Gallery
          </a>
        </div>
      </motion.div>
    </section>
  )
}

function CalendarSection() {
  return (
    <section className="py-20 px-6 bg-white">
      <div className="max-w-4xl mx-auto grid md:grid-cols-2 gap-16 items-start">
        <div>
          <span className="text-[10px] tracking-[4px] uppercase text-gold-base mb-3 block">✦ Ketersediaan jadwal</span>
          <h2 className="font-display text-4xl font-light text-pink-deep mb-3">Cek jadwal<br/>tersedia</h2>
          <div className="w-9 h-px mb-4" style={{ background: 'linear-gradient(90deg, #E8A4BB, #C9A84C)' }} />
          <p className="text-sm text-pink-mid/70 leading-relaxed">Pilih tanggal untuk cek ketersediaan.</p>
          <BookingCalendar />
        </div>
        <div className="pt-16">
          <div className="space-y-3">
            {[
              { icon: 'ti-clock', title: 'Waktu layanan', desc: 'Sesi 1–3 jam tergantung desain. Disarankan booking H-3.' },
              { icon: 'ti-map-pin', title: 'Lokasi fleksibel', desc: 'Home visit maupun di studio kami tersedia.' },
              { icon: 'ti-brand-whatsapp', title: 'Konfirmasi via WhatsApp', desc: 'Konfirmasi otomatis setelah booking.' },
            ].map((item) => (
              <div key={item.title} className="flex gap-4 p-4 bg-pink-ultra rounded-md border border-pink-soft/20">
                <i className={`ti ${item.icon} text-gold-base text-xl mt-0.5`} aria-hidden="true" />
                <div>
                  <p className="text-sm font-medium text-pink-deep mb-1">{item.title}</p>
                  <p className="text-xs text-pink-mid/70 leading-relaxed">{item.desc}</p>
                </div>
              </div>
            ))}
          </div>
        </div>
      </div>
    </section>
  )
}

function ServicesSection({ packages }) {
  return (
    <section className="py-20 px-6 bg-pink-ultra">
      <div className="max-w-5xl mx-auto">
        <div className="text-center mb-16">
          <span className="text-[10px] tracking-[4px] uppercase text-gold-base mb-3 block">✦ Layanan Kami</span>
          <h2 className="font-display text-4xl font-light text-plum-800">Paket Henna</h2>
        </div>
        <div className="grid md:grid-cols-3 gap-8">
          {packages.map(p => (
            <div key={p.id} className="bg-white p-6 rounded-xl border border-pink-light/30 text-center">
              <h3 className="font-display text-2xl text-pink-deep mb-2">{p.package_name}</h3>
              <p className="text-xs text-pink-mid/80 mb-6 min-h-[40px]">{p.description}</p>
              <div className="text-xl font-medium text-plum-800 mb-6">
                Rp {Number(p.price).toLocaleString('id-ID')}
              </div>
              <Link href="/booking" className="block w-full py-2.5 bg-pink-light/20 text-pink-deep text-xs font-medium tracking-wider uppercase rounded-sm hover:bg-pink-light/40 transition-colors">
                Pilih Paket
              </Link>
            </div>
          ))}
        </div>
      </div>
    </section>
  )
}

function GallerySection({ gallery }) {
  return (
    <section id="gallery" className="py-20 px-6 bg-white">
      <div className="max-w-6xl mx-auto">
        <div className="text-center mb-16">
          <span className="text-[10px] tracking-[4px] uppercase text-gold-base mb-3 block">✦ Portofolio</span>
          <h2 className="font-display text-4xl font-light text-plum-800">Gallery Karya</h2>
        </div>
        <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
          {gallery.map(g => (
            <div key={g.id} className="aspect-square bg-gray-100 rounded-lg overflow-hidden group relative">
              <img src={g.image_url} alt={g.caption} className="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" />
              <div className="absolute inset-0 bg-plum-800/60 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center p-4">
                <p className="text-pink-ultra text-xs text-center">{g.caption}</p>
              </div>
            </div>
          ))}
        </div>
      </div>
    </section>
  )
}

function CtaSection() {
  return (
    <section className="py-24 px-6 bg-plum-800 text-center">
      <h2 className="font-display text-4xl text-pink-ultra mb-6">Siap mempercantik hari spesialmu?</h2>
      <Link href="/booking" className="inline-block px-10 py-3.5 bg-gold-base text-plum-800 text-sm tracking-widest uppercase font-medium rounded-sm hover:bg-gold-light transition-colors">
        Booking Sekarang
      </Link>
    </section>
  )
}
