import { useState } from 'react'
import { router, useForm } from '@inertiajs/react'
import AdminLayout from '../../../layouts/AdminLayout'
import Button from '../../../components/ui/Button'
import toast from 'react-hot-toast'

export default function Packages({ packages }) {
  const [editing, setEditing] = useState(null)
  
  const { data, setData, post, processing, reset, clearErrors } = useForm({
    package_name: '', description: '', price: '', status: 'active', image: null
  })

  const openForm = (p = null) => {
    setEditing(p ? p.id : 'new')
    clearErrors()
    if (p) {
      setData({
        package_name: p.package_name,
        description: p.description,
        price: p.price,
        status: p.status,
        image: null
      })
    } else {
      reset()
    }
  }

  const closeForm = () => {
    setEditing(null)
    reset()
    clearErrors()
  }

  const submit = (e) => {
    e.preventDefault()
    if (editing === 'new') {
      post('/admin/cms/packages', {
        onSuccess: () => { toast.success('Paket ditambahkan'); closeForm() }
      })
    } else {
      router.post(`/admin/cms/packages/${editing}`, {
        _method: 'put',
        ...data,
      }, {
        onSuccess: () => { toast.success('Paket diperbarui'); closeForm() }
      })
    }
  }

  const destroy = (id) => {
    if (confirm('Hapus paket ini?')) {
      router.delete(`/admin/cms/packages/${id}`, {
        onSuccess: () => toast.success('Paket dihapus')
      })
    }
  }

  return (
    <AdminLayout title="Kelola Paket Layanan">
      <div className="flex justify-end mb-4">
        <Button onClick={() => openForm()} variant="gold"><i className="ti ti-plus" /> Tambah Paket</Button>
      </div>

      <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        {packages.map(p => (
          <div key={p.id} className="bg-white rounded-xl border border-gray-100 overflow-hidden flex flex-col">
            {p.image_url ? (
              <img src={p.image_url} alt={p.package_name} className="h-40 w-full object-cover" />
            ) : (
              <div className="h-40 bg-gray-100 flex items-center justify-center text-gray-400">
                <i className="ti ti-photo-off text-3xl" aria-hidden="true" />
              </div>
            )}
            <div className="p-5 flex-1 flex flex-col">
              <div className="flex justify-between items-start mb-2">
                <h3 className="font-display text-xl text-plum-800 font-medium">{p.package_name}</h3>
                <span className={`text-[10px] px-2 py-0.5 rounded-full font-medium ${p.status === 'active' ? 'bg-emerald-50 text-emerald-600' : 'bg-gray-100 text-gray-500'}`}>
                  {p.status === 'active' ? 'Aktif' : 'Nonaktif'}
                </span>
              </div>
              <p className="text-sm text-gray-500 mb-4 flex-1">{p.description}</p>
              <div className="text-lg font-medium text-gold-deep mb-4">Rp {Number(p.price).toLocaleString('id-ID')}</div>
              <div className="flex gap-2">
                <Button variant="outline" size="sm" onClick={() => openForm(p)} className="flex-1">Edit</Button>
                <Button variant="ghost" size="sm" onClick={() => destroy(p.id)} className="text-red-500 hover:text-red-600"><i className="ti ti-trash" /></Button>
              </div>
            </div>
          </div>
        ))}
      </div>

      {editing && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
          <div className="bg-white rounded-2xl w-full max-w-lg overflow-hidden shadow-xl">
            <div className="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
              <h3 className="text-lg font-medium">{editing === 'new' ? 'Tambah Paket' : 'Edit Paket'}</h3>
              <button onClick={closeForm} className="text-gray-400 hover:text-gray-600"><i className="ti ti-x" /></button>
            </div>
            <form onSubmit={submit} className="p-6 space-y-4">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Nama Paket</label>
                <input type="text" value={data.package_name} onChange={e => setData('package_name', e.target.value)} required className="w-full border-gray-300 rounded-md shadow-sm p-2 border" />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Harga</label>
                <input type="number" value={data.price} onChange={e => setData('price', e.target.value)} required className="w-full border-gray-300 rounded-md shadow-sm p-2 border" />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea value={data.description} onChange={e => setData('description', e.target.value)} required rows="3" className="w-full border-gray-300 rounded-md shadow-sm p-2 border" />
              </div>
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Status</label>
                  <select value={data.status} onChange={e => setData('status', e.target.value)} className="w-full border-gray-300 rounded-md shadow-sm p-2 border">
                    <option value="active">Aktif</option>
                    <option value="inactive">Nonaktif</option>
                  </select>
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Gambar (Opsional)</label>
                  <input type="file" onChange={e => setData('image', e.target.files[0])} className="w-full border border-gray-300 rounded-md p-1.5 text-sm" accept="image/*" />
                </div>
              </div>
              <div className="pt-4 flex justify-end gap-3">
                <Button type="button" variant="ghost" onClick={closeForm}>Batal</Button>
                <Button type="submit" variant="primary" disabled={processing}>Simpan</Button>
              </div>
            </form>
          </div>
        </div>
      )}
    </AdminLayout>
  )
}
