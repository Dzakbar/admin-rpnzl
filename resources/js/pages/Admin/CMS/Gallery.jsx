import { useState } from 'react'
import { router, useForm } from '@inertiajs/react'
import AdminLayout from '../../../layouts/AdminLayout'
import Button from '../../../components/ui/Button'
import toast from 'react-hot-toast'

export default function Gallery({ gallery }) {
  const [showUpload, setShowUpload] = useState(false)
  const { data, setData, post, processing, reset } = useForm({
    image: null, caption: '', category: 'white'
  })

  const submit = (e) => {
    e.preventDefault()
    post('/admin/cms/gallery', {
      onSuccess: () => {
        toast.success('Foto berhasil diupload')
        reset()
        setShowUpload(false)
      }
    })
  }

  const destroy = (id) => {
    if (confirm('Hapus foto ini?')) {
      router.delete(`/admin/cms/gallery/${id}`, {
        onSuccess: () => toast.success('Foto dihapus')
      })
    }
  }

  return (
    <AdminLayout title="Kelola Gallery">
      <div className="flex mb-6 sm:justify-end">
        <Button onClick={() => setShowUpload(true)} variant="gold"><i className="ti ti-upload" /> Upload Foto</Button>
      </div>

      <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4">
        {gallery.map(g => (
          <div key={g.id} className="relative group rounded-xl overflow-hidden aspect-square bg-gray-100">
            <img src={g.image_url} alt={g.caption} className="w-full h-full object-cover" />
            <div className="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col justify-between p-3">
              <div className="flex justify-end">
                <button onClick={() => destroy(g.id)} className="w-8 h-8 rounded-full bg-white/20 hover:bg-red-500 text-white flex items-center justify-center transition-colors">
                  <i className="ti ti-trash" />
                </button>
              </div>
              <div>
                <span className="text-[10px] bg-gold-base text-plum-800 px-2 py-0.5 rounded-full font-medium">{g.category}</span>
                <p className="text-white text-xs mt-1 truncate">{g.caption}</p>
              </div>
            </div>
          </div>
        ))}
        {gallery.length === 0 && (
          <div className="col-span-full py-12 text-center text-gray-500 border-2 border-dashed border-gray-200 rounded-xl">
            Belum ada foto di gallery.
          </div>
        )}
      </div>

      {showUpload && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
          <div className="bg-white rounded-2xl w-full max-w-sm max-h-[92vh] overflow-hidden shadow-xl">
            <div className="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
              <h3 className="text-lg font-medium">Upload Foto</h3>
              <button onClick={() => setShowUpload(false)} className="text-gray-400 hover:text-gray-600"><i className="ti ti-x" /></button>
            </div>
            <form onSubmit={submit} className="p-4 space-y-4 overflow-y-auto max-h-[calc(92vh-65px)] sm:p-6">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">File Foto</label>
                <input type="file" onChange={e => setData('image', e.target.files[0])} required className="w-full border border-gray-300 rounded-md p-1.5 text-sm" accept="image/*" />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                <select value={data.category} onChange={e => setData('category', e.target.value)} required className="w-full border-gray-300 rounded-md shadow-sm p-2 border text-sm">
                  <option value="white">White Henna</option>
                  <option value="nude-semi-gold">Nude Semi Gold Henna</option>
                  <option value="maroon">Henna Maroon</option>
                  <option value="pink-rose">Pink Rose Henna</option>
                </select>
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Caption</label>
                <input type="text" value={data.caption} onChange={e => setData('caption', e.target.value)} className="w-full border-gray-300 rounded-md shadow-sm p-2 border text-sm" />
              </div>
              <div className="pt-4 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <Button type="button" variant="ghost" onClick={() => setShowUpload(false)}>Batal</Button>
                <Button type="submit" variant="primary" disabled={processing}>Upload</Button>
              </div>
            </form>
          </div>
        </div>
      )}
    </AdminLayout>
  )
}
