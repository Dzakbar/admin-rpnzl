export default function Footer() {
  return (
    <footer className="bg-plum-900 text-pink-ultra/50 py-12 px-6 text-center text-sm">
      <div className="font-display text-2xl text-pink-ultra tracking-widest uppercase mb-4">
        RPNZL <span className="text-gold-base">Art</span>
      </div>
      <p className="mb-8">Professional Henna Artist based in Indonesia.</p>
      <p>&copy; {new Date().getFullYear()} RPNZL Art. All rights reserved.</p>
    </footer>
  )
}
