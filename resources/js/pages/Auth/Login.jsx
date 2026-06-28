import { useForm, Link } from '@inertiajs/react'
import Navbar from '../../components/Navbar'

export default function Login() {
  const { data, setData, post, processing, errors } = useForm({
    email: '',
    password: '',
    remember: false,
  })

  const submit = (e) => {
    e.preventDefault()
    post('/login')
  }

  return (
    <div className="min-h-screen bg-pink-ultra flex flex-col">
      <Navbar />
      <div className="flex-1 flex items-center justify-center p-6">
        <div className="bg-white p-8 rounded-xl shadow-sm border border-pink-light/30 w-full max-w-md">
          <h1 className="font-display text-3xl text-plum-800 text-center mb-6">Welcome Back</h1>
          
          <form onSubmit={submit} className="space-y-4">
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
              <label className="block text-sm font-medium text-gray-700 mb-1">Password</label>
              <input
                type="password"
                value={data.password}
                onChange={e => setData('password', e.target.value)}
                className="w-full border-gray-300 rounded-md shadow-sm focus:border-gold-base focus:ring focus:ring-gold-base/20 p-2 border"
                required
              />
            </div>

            <div className="flex items-center">
              <input
                type="checkbox"
                id="remember"
                checked={data.remember}
                onChange={e => setData('remember', e.target.checked)}
                className="text-gold-base focus:ring-gold-base rounded border-gray-300"
              />
              <label htmlFor="remember" className="ml-2 text-sm text-gray-600">Remember me</label>
            </div>

            <button
              type="submit"
              disabled={processing}
              className="w-full py-2.5 bg-plum-800 text-pink-ultra rounded-md hover:bg-plum-700 transition-colors disabled:opacity-50"
            >
              Login
            </button>
          </form>

          <p className="mt-6 text-center text-sm text-gray-600">
            Don't have an account? <Link href="/register" className="text-gold-deep hover:underline">Register here</Link>
          </p>
        </div>
      </div>
    </div>
  )
}
