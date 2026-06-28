import { useForm, Link } from '@inertiajs/react'
import Navbar from '../../components/Navbar'

export default function Register() {
  const { data, setData, post, processing, errors } = useForm({
    name: '',
    email: '',
    whatsapp_number: '',
    password: '',
    password_confirmation: '',
  })

  const submit = (e) => {
    e.preventDefault()
    post('/register')
  }

  return (
    <div className="min-h-screen bg-pink-ultra flex flex-col">
      <Navbar />
      <div className="flex-1 flex items-center justify-center p-6">
        <div className="bg-white p-8 rounded-xl shadow-sm border border-pink-light/30 w-full max-w-md">
          <h1 className="font-display text-3xl text-plum-800 text-center mb-6">Create Account</h1>
          
          <form onSubmit={submit} className="space-y-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
              <input
                type="text"
                value={data.name}
                onChange={e => setData('name', e.target.value)}
                className="w-full border-gray-300 rounded-md shadow-sm focus:border-gold-base focus:ring focus:ring-gold-base/20 p-2 border"
                required
              />
              {errors.name && <div className="text-red-500 text-xs mt-1">{errors.name}</div>}
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Email</label>
              <input
                type="email"
                value={data.email}
                onChange={e => setData('email', e.target.value)}
                className="w-full border-gray-300 rounded-md shadow-sm focus:border-gold-base focus:ring focus:ring-gold-base/20 p-2 border"
                required
              />
              {errors.email && <div className="text-red-500 text-xs mt-1">{errors.email}</div>}
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">WhatsApp Number</label>
              <input
                type="text"
                value={data.whatsapp_number}
                onChange={e => setData('whatsapp_number', e.target.value)}
                className="w-full border-gray-300 rounded-md shadow-sm focus:border-gold-base focus:ring focus:ring-gold-base/20 p-2 border"
                required
              />
              {errors.whatsapp_number && <div className="text-red-500 text-xs mt-1">{errors.whatsapp_number}</div>}
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Password</label>
              <input
                type="password"
                value={data.password}
                onChange={e => setData('password', e.target.value)}
                className="w-full border-gray-300 rounded-md shadow-sm focus:border-gold-base focus:ring focus:ring-gold-base/20 p-2 border"
                required
              />
              {errors.password && <div className="text-red-500 text-xs mt-1">{errors.password}</div>}
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
              <input
                type="password"
                value={data.password_confirmation}
                onChange={e => setData('password_confirmation', e.target.value)}
                className="w-full border-gray-300 rounded-md shadow-sm focus:border-gold-base focus:ring focus:ring-gold-base/20 p-2 border"
                required
              />
            </div>

            <button
              type="submit"
              disabled={processing}
              className="w-full py-2.5 bg-plum-800 text-pink-ultra rounded-md hover:bg-plum-700 transition-colors disabled:opacity-50 mt-4"
            >
              Register
            </button>
          </form>

          <p className="mt-6 text-center text-sm text-gray-600">
            Already have an account? <Link href="/login" className="text-gold-deep hover:underline">Login here</Link>
          </p>
        </div>
      </div>
    </div>
  )
}
