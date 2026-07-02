import React, { useState } from 'react';
import { Head, useForm, router, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import Pagination from '@/Components/Pagination';
import { 
    Search, 
    Plus, 
    Edit, 
    Trash2, 
    X, 
    ExternalLink,
    FileText,
    CheckCircle,
    AlertCircle,
    Clock,
    MessageCircle
} from 'lucide-react';

export default function Index({ laporans, mahasiswas, dosens, filters }) {
    const { auth } = usePage().props;
    const currentUser = auth.user;
    const roleName = currentUser.role;
    const kategori = currentUser.kategori;

    // Tentukan jenis laporan yang diizinkan berdasarkan kategori mahasiswa
    const allowedTypes = roleName !== 'mahasiswa'
        ? ['skripsi', 'proposal', 'magang']
        : kategori === 'magang'
            ? ['magang']
            : ['skripsi', 'proposal'];
    const defaultType = allowedTypes[0];

    const [searchVal, setSearchVal] = useState(filters.search || '');
    const [modalOpen, setModalOpen] = useState(false);
    const [editMode, setEditMode] = useState(false);
    const [selectedLaporanId, setSelectedLaporanId] = useState(null);

    const { data, setData, post, put, delete: destroy, reset, processing, errors } = useForm({
        mahasiswa_id: '',
        dosen_id: '',
        judul: '',
        type: defaultType,
        dokumen: '',
        status: 'pending',
        tanggal_mulai: new Date().toISOString().split('T')[0],
        deskripsi: '',
        komentar: '',
    });

    const handleSearch = (e) => {
        e.preventDefault();
        router.get('/laporan', { search: searchVal }, { preserveState: true });
    };

    const handleOpenCreate = () => {
        reset();
        setEditMode(false);
        setSelectedLaporanId(null);
        if (roleName === 'mahasiswa') {
            setData(prev => ({
                ...prev,
                mahasiswa_id: currentUser.id,
                dosen_id: currentUser.dosen_pembimbing_id || '',
                type: defaultType,
                status: 'pending'
            }));
        }
        setModalOpen(true);
    };

    const handleOpenEdit = (l) => {
        setEditMode(true);
        setSelectedLaporanId(l.id);
        
        setData({
            mahasiswa_id: l.mahasiswa_id || '',
            dosen_id: l.dosen_id || '',
            judul: l.judul || '',
            type: l.type || 'skripsi',
            dokumen: l.dokumen || '',
            status: l.status || 'pending',
            tanggal_mulai: l.tanggal_mulai || '',
            deskripsi: l.deskripsi || '',
            komentar: l.komentar || '',
        });
        setModalOpen(true);
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        if (editMode) {
            put(`/laporan/${selectedLaporanId}`, {
                onSuccess: () => {
                    setModalOpen(false);
                    reset();
                }
            });
        } else {
            post('/laporan', {
                onSuccess: () => {
                    setModalOpen(false);
                    reset();
                }
            });
        }
    };

    const handleDelete = (id) => {
        if (confirm('Apakah Anda yakin ingin menghapus laporan akademik ini?')) {
            destroy(`/laporan/${id}`);
        }
    };

    const getStatusBadge = (status) => {
        const s = status?.toLowerCase() || '';
        if (s === 'disetujui') {
            return (
                <span className="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-semibold bg-emerald-100 text-emerald-700 border border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20">
                    <CheckCircle className="w-3.5 h-3.5" /> Disetujui
                </span>
            );
        }
        if (s === 'revisi') {
            return (
                <span className="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-semibold bg-rose-100 text-rose-700 border border-rose-200 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-500/20">
                    <AlertCircle className="w-3.5 h-3.5" /> Revisi
                </span>
            );
        }
        return (
            <span className="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-semibold bg-amber-100 text-amber-700 border border-amber-200 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20">
                <Clock className="w-3.5 h-3.5" /> Pending
            </span>
        );
    };

    return (
        <AuthenticatedLayout title="Laporan Akademik">
            <Head title="Laporan Akademik" />

            <div className="space-y-6">
                {/* Search & Actions */}
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <form onSubmit={handleSearch} className="relative w-full max-w-md">
                        <Search className="absolute left-4 top-1/2 -translate-y-1/2 w-4.5 h-4.5 text-slate-500" />
                        <input
                            type="text"
                            value={searchVal}
                            onChange={e => setSearchVal(e.target.value)}
                            placeholder="Cari judul laporan..."
                            className="w-full bg-white dark:bg-slate-900/40 border border-gray-200 dark:border-slate-800/60 rounded-2xl py-3 pl-11 pr-4 text-sm text-gray-900 dark:text-slate-200 placeholder-gray-400 dark:placeholder-slate-500 focus:outline-none focus:border-indigo-500"
                        />
                    </form>

                    {(roleName === 'mahasiswa' || roleName === 'super_admin' || roleName === 'ka_prodi') && (
                        <button
                            onClick={handleOpenCreate}
                            className="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-5 rounded-2xl flex items-center justify-center gap-2 transition-all shadow-md self-start sm:self-auto cursor-pointer"
                        >
                            <Plus className="w-5 h-5" />
                            Tambah Laporan
                        </button>
                    )}
                </div>

                {/* Table */}
                <div className="bg-white dark:bg-slate-900/30 border border-gray-200 dark:border-slate-800/40 rounded-3xl overflow-hidden shadow-lg animate-fade-in">
                    <div className="overflow-x-auto">
                        <table className="w-full text-left border-collapse">
                            <thead>
                                <tr className="border-b border-gray-200 dark:border-slate-800/50 bg-gray-50 dark:bg-slate-900/20 text-gray-600 dark:text-slate-400 text-xs font-semibold uppercase tracking-wider">
                                    <th className="py-4.5 px-6">Mahasiswa</th>
                                    <th className="py-4.5 px-6">Dosen Pembimbing</th>
                                    <th className="py-4.5 px-6">Judul Laporan</th>
                                    <th className="py-4.5 px-6">Dokumen</th>
                                    <th className="py-4.5 px-6">Jenis</th>
                                    <th className="py-4.5 px-6">Mulai</th>
                                    <th className="py-4.5 px-6">Status</th>
                                    <th className="py-4.5 px-6 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-gray-200 dark:divide-slate-800/30 text-sm text-gray-700 dark:text-slate-300">
                                {laporans.data.length === 0 ? (
                                    <tr>
                                        <td colSpan="8" className="py-12 text-center text-gray-500 dark:text-slate-500">
                                            Tidak ada riwayat laporan akademik.
                                        </td>
                                    </tr>
                                ) : (
                                    laporans.data.map((l) => (
                                        <tr key={l.id} className="hover:bg-gray-50 dark:hover:bg-slate-900/10 transition-colors">
                                            <td className="py-4 px-6 font-semibold text-gray-900 dark:text-slate-200">
                                                {l.mahasiswa?.name || '-'}
                                            </td>
                                            <td className="py-4 px-6 text-slate-400">
                                                {l.dosen?.name || '-'}
                                            </td>
                                            <td className="py-4 px-6">
                                                <span className="font-semibold text-gray-900 dark:text-slate-200">{l.judul}</span>
                                                {l.komentar && (
                                                    <span className="flex items-center gap-1 text-xs text-indigo-600 dark:text-indigo-400 mt-1 font-medium">
                                                        <MessageCircle className="w-3.5 h-3.5" /> Ada revisi/catatan
                                                    </span>
                                                )}
                                            </td>
                                            <td className="py-4 px-6">
                                                <a 
                                                    href={l.dokumen} 
                                                    target="_blank" 
                                                    rel="noopener noreferrer"
                                                    className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-indigo-100 text-indigo-700 border border-indigo-200 dark:bg-indigo-500/10 dark:text-indigo-400 dark:border-indigo-500/20 hover:bg-indigo-200 dark:hover:bg-indigo-600/20 transition-all cursor-pointer"
                                                >
                                                    <ExternalLink className="w-3.5 h-3.5" /> Link File
                                                </a>
                                            </td>
                                            <td className="py-4 px-6 text-gray-600 dark:text-slate-400 uppercase text-xs font-bold">
                                                {l.type}
                                            </td>
                                            <td className="py-4 px-6 text-slate-400">
                                                {new Date(l.tanggal_mulai).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })}
                                            </td>
                                            <td className="py-4 px-6">
                                                {getStatusBadge(l.status)}
                                            </td>
                                            <td className="py-4 px-6">
                                                <div className="flex items-center gap-2 justify-end">
                                                <button
                                                    onClick={() => handleOpenEdit(l)}
                                                    className="inline-flex items-center justify-center p-2 rounded-lg bg-gray-100 dark:bg-slate-800 hover:bg-indigo-100 dark:hover:bg-indigo-650 hover:text-indigo-600 dark:hover:text-indigo-200 text-gray-600 dark:text-slate-400 transition-all border border-gray-300 dark:border-slate-700/50 cursor-pointer"
                                                    title={roleName === 'dosen' ? 'Beri Catatan & Review' : 'Detail / Ubah'}
                                                >
                                                    <Edit className="w-4 h-4" />
                                                </button>
                                                {(roleName === 'super_admin' || roleName === 'ka_prodi' || (roleName === 'mahasiswa' && l.mahasiswa_id === currentUser.id)) && (
                                                    <button
                                                        onClick={() => handleDelete(l.id)}
                                                        className="inline-flex items-center justify-center p-2 rounded-lg bg-gray-100 dark:bg-slate-800 hover:bg-rose-100 dark:hover:bg-rose-950 hover:text-rose-600 dark:hover:text-rose-300 text-gray-600 dark:text-slate-400 transition-all border border-gray-300 dark:border-slate-700/50 cursor-pointer"
                                                        title="Hapus"
                                                    >
                                                        <Trash2 className="w-4 h-4" />
                                                    </button>
                                                )}
                                                </div>
                                            </td>
                                        </tr>
                                    ))
                                )}
                            </tbody>
                        </table>
                    </div>

                    <Pagination paginator={laporans} itemLabel="laporan" />
                </div>
            </div>

            {/* Modal Laporan */}
            {modalOpen && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
                    <div className="w-full max-w-2xl bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-3xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
                        <div className="flex items-center justify-between border-b border-gray-200 dark:border-slate-800/60 px-6 py-4.5">
                            <h3 className="font-bold text-gray-900 dark:text-slate-200">
                                {editMode 
                                    ? (roleName === 'dosen' ? 'Review & Tinjau Laporan' : 'Ubah Laporan Akademik') 
                                    : 'Tambah Laporan Akademik Baru'}
                            </h3>
                            <button onClick={() => setModalOpen(false)} className="text-gray-500 dark:text-slate-400 hover:text-gray-900 dark:hover:text-slate-100 p-1.5 hover:bg-gray-100 dark:hover:bg-slate-800 rounded-lg">
                                <X className="w-5 h-5" />
                            </button>
                        </div>

                        <form onSubmit={handleSubmit} className="flex-1 overflow-auto p-6 space-y-4">
                            {/* Student / Dosen Selection (Only visible to Admin) */}
                            {roleName !== 'mahasiswa' && roleName !== 'dosen' && (
                                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div className="space-y-1.5">
                                        <label className="text-xs text-gray-600 dark:text-slate-400 font-semibold uppercase">Mahasiswa</label>
                                        <select
                                            value={data.mahasiswa_id}
                                            onChange={e => setData('mahasiswa_id', e.target.value)}
                                            className="w-full bg-gray-50 dark:bg-slate-950/50 border border-gray-300 dark:border-slate-800 rounded-xl py-2 px-3 text-sm text-gray-900 dark:text-slate-200 focus:outline-none focus:border-indigo-500"
                                        >
                                            <option value="">Pilih Mahasiswa</option>
                                            {mahasiswas.map(m => (
                                                <option key={m.id} value={m.id}>{m.name}</option>
                                            ))}
                                        </select>
                                        {errors.mahasiswa_id && <p className="text-rose-400 text-xs">{errors.mahasiswa_id}</p>}
                                    </div>

                                    <div className="space-y-1.5">
                                        <label className="text-xs text-gray-600 dark:text-slate-400 font-semibold uppercase">Dosen Pembimbing</label>
                                        <select
                                            value={data.dosen_id}
                                            onChange={e => setData('dosen_id', e.target.value)}
                                            className="w-full bg-gray-50 dark:bg-slate-950/50 border border-gray-300 dark:border-slate-800 rounded-xl py-2 px-3 text-sm text-gray-900 dark:text-slate-200 focus:outline-none focus:border-indigo-500"
                                        >
                                            <option value="">Pilih Dosen</option>
                                            {dosens.map(d => (
                                                <option key={d.id} value={d.id}>{d.name}</option>
                                            ))}
                                        </select>
                                        {errors.dosen_id && <p className="text-rose-400 text-xs">{errors.dosen_id}</p>}
                                    </div>
                                </div>
                            )}

                            {/* Core Log Fields */}
                            <div className="space-y-1.5">
                                <label className="text-xs text-gray-600 dark:text-slate-400 font-semibold uppercase">Judul Laporan</label>
                                <input
                                    type="text"
                                    value={data.judul}
                                    onChange={e => setData('judul', e.target.value)}
                                    disabled={roleName === 'dosen'}
                                    placeholder="Masukkan judul skripsi, proposal, atau magang"
                                    className="w-full bg-gray-50 dark:bg-slate-950/50 border border-gray-300 dark:border-slate-800 rounded-xl py-2.5 px-3 text-sm text-gray-900 dark:text-slate-200 focus:outline-none focus:border-indigo-500 disabled:opacity-60"
                                />
                                {errors.judul && <p className="text-rose-400 text-xs">{errors.judul}</p>}
                            </div>

                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div className="space-y-1.5">
                                    <label className="text-xs text-gray-600 dark:text-slate-400 font-semibold uppercase">Jenis Laporan</label>
                                    <select
                                        value={data.type}
                                        onChange={e => setData('type', e.target.value)}
                                        disabled={roleName === 'dosen'}
                                        className="w-full bg-gray-50 dark:bg-slate-950/50 border border-gray-300 dark:border-slate-800 rounded-xl py-2.5 px-3 text-sm text-gray-900 dark:text-slate-200 focus:outline-none focus:border-indigo-500 disabled:opacity-60"
                                    >
                                        {allowedTypes.includes('skripsi') && <option value="skripsi">Skripsi</option>}
                                        {allowedTypes.includes('proposal') && <option value="proposal">Proposal</option>}
                                        {allowedTypes.includes('magang') && <option value="magang">Magang</option>}
                                    </select>
                                    {errors.type && <p className="text-rose-400 text-xs">{errors.type}</p>}
                                </div>

                                <div className="space-y-1.5">
                                    <label className="text-xs text-gray-600 dark:text-slate-400 font-semibold uppercase">Tanggal Mulai</label>
                                    <input
                                        type="date"
                                        value={data.tanggal_mulai}
                                        onChange={e => setData('tanggal_mulai', e.target.value)}
                                        disabled={roleName === 'dosen'}
                                        className="w-full bg-gray-50 dark:bg-slate-950/50 border border-gray-300 dark:border-slate-800 rounded-xl py-2.5 px-3 text-sm text-gray-900 dark:text-slate-200 focus:outline-none focus:border-indigo-500 disabled:opacity-60"
                                    />
                                    {errors.tanggal_mulai && <p className="text-rose-400 text-xs">{errors.tanggal_mulai}</p>}
                                </div>
                            </div>

                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div className="space-y-1.5">
                                    <label className="text-xs text-gray-600 dark:text-slate-400 font-semibold uppercase">Link Dokumen (URL)</label>
                                    <input
                                        type="text"
                                        value={data.dokumen}
                                        onChange={e => setData('dokumen', e.target.value)}
                                        disabled={roleName === 'dosen'}
                                        placeholder="https://drive.google.com/..."
                                        className="w-full bg-gray-50 dark:bg-slate-950/50 border border-gray-300 dark:border-slate-800 rounded-xl py-2.5 px-3 text-sm text-gray-900 dark:text-slate-200 focus:outline-none focus:border-indigo-500 disabled:opacity-60"
                                    />
                                    {errors.dokumen && <p className="text-rose-400 text-xs">{errors.dokumen}</p>}
                                </div>

                                {/* Status selection for Dosen / Admin */}
                                {(roleName === 'dosen' || roleName === 'super_admin' || roleName === 'ka_prodi') && (
                                    <div className="space-y-1.5">
                                        <label className="text-xs text-gray-600 dark:text-slate-400 font-semibold uppercase">Status Persetujuan</label>
                                        <select
                                            value={data.status}
                                            onChange={e => setData('status', e.target.value)}
                                            className="w-full bg-gray-50 dark:bg-slate-950/50 border border-gray-300 dark:border-slate-850 rounded-xl py-2.5 px-3 text-sm text-gray-900 dark:text-slate-200 focus:outline-none focus:border-indigo-500"
                                        >
                                            <option value="pending">Pending</option>
                                            <option value="disetujui">Disetujui</option>
                                            <option value="revisi">Revisi</option>
                                        </select>
                                        {errors.status && <p className="text-rose-400 text-xs">{errors.status}</p>}
                                    </div>
                                )}
                            </div>

                            {/* Deskripsi */}
                            <div className="space-y-1.5">
                                <label className="text-xs text-gray-600 dark:text-slate-400 font-semibold uppercase">Deskripsi Laporan</label>
                                <textarea
                                    value={data.deskripsi}
                                    onChange={e => setData('deskripsi', e.target.value)}
                                    disabled={roleName === 'dosen'}
                                    rows="3"
                                    placeholder="Tambahkan penjelasan singkat mengenai laporan akademik ini..."
                                    className="w-full bg-gray-50 dark:bg-slate-950/50 border border-gray-300 dark:border-slate-800 rounded-xl py-2.5 px-3 text-sm text-gray-900 dark:text-slate-200 focus:outline-none focus:border-indigo-500 disabled:opacity-60"
                                />
                                {errors.deskripsi && <p className="text-rose-400 text-xs">{errors.deskripsi}</p>}
                            </div>

                            {/* Komentar Dosen */}
                            <div className="space-y-1.5">
                                <label className="text-xs text-gray-600 dark:text-slate-400 font-semibold uppercase">Feedback / Catatan Dosen</label>
                                <textarea
                                    value={data.komentar}
                                    onChange={e => setData('komentar', e.target.value)}
                                    disabled={roleName === 'mahasiswa'}
                                    rows="3"
                                    placeholder={roleName === 'mahasiswa' ? "Belum ada feedback dari Dosen Pembimbing." : "Tulis feedback atau detail catatan revisi di sini..."}
                                    className="w-full bg-gray-50 dark:bg-slate-950/50 border border-gray-300 dark:border-slate-800 rounded-xl py-2.5 px-3 text-sm text-gray-900 dark:text-slate-200 focus:outline-none focus:border-indigo-500 disabled:opacity-60"
                                />
                                {errors.komentar && <p className="text-rose-400 text-xs">{errors.komentar}</p>}
                            </div>

                            {/* Action Buttons */}
                            <div className="border-t border-gray-200 dark:border-slate-800/60 pt-4 flex justify-end gap-3">
                                <button
                                    type="button"
                                    onClick={() => setModalOpen(false)}
                                    className="bg-gray-100 dark:bg-slate-800 hover:bg-gray-200 dark:hover:bg-slate-700 text-gray-700 dark:text-slate-300 font-bold py-2.5 px-4 rounded-xl text-sm transition-all"
                                >
                                    Tutup
                                </button>
                                {(roleName !== 'mahasiswa' || !editMode || data.status === 'pending' || data.status === 'revisi') && (
                                    <button
                                        type="submit"
                                        disabled={processing}
                                        className="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-5 rounded-xl text-sm transition-all shadow-md disabled:opacity-50"
                                    >
                                        {processing ? 'Menyimpan...' : 'Simpan'}
                                    </button>
                                )}
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </AuthenticatedLayout>
    );
}
