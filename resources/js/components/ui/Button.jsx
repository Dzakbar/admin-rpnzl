import { motion } from 'framer-motion'
import clsx from 'clsx'

const variants = {
  gold:    'bg-gold-base text-plum-800 hover:bg-gold-light border-gold-base',
  primary: 'bg-plum-800 text-pink-ultra hover:bg-plum-700 border-plum-800',
  danger:  'bg-red-500 text-white hover:bg-red-600 border-red-500',
  outline: 'bg-transparent text-plum-800 border-gray-200 hover:bg-gray-50',
  ghost:   'bg-transparent text-gray-500 border-transparent hover:bg-gray-100',
}

export default function Button({ children, variant = 'primary', size = 'md', className, disabled, ...props }) {
  const sizes = { sm: 'px-3 py-1.5 text-xs', md: 'px-4 py-2 text-sm', lg: 'px-6 py-3 text-sm' }

  return (
    <motion.button
      whileTap={{ scale: 0.97 }}
      disabled={disabled}
      className={clsx(
        'inline-flex items-center gap-2 rounded-md border font-medium tracking-wide transition-colors',
        'disabled:opacity-50 disabled:cursor-not-allowed',
        variants[variant], sizes[size], className
      )}
      {...props}
    >
      {children}
    </motion.button>
  )
}
