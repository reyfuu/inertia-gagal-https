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
    BookOpen,
    CheckCircle,
    XCircle,
    Clock,
    ExternalLink,
    Filter
} from 'lucide-react';

export default function Index({ laporanMingguans, filterMahasiswas, mahasiswas, filters }) {
    const { auth } = usePage().props;
    const currentUser = auth.user;
    const roleName = currentUser.role;

    const [searchVal, setSearchVal] = useState(filters.search || '');
    const [selectedMhsFilter, setSelectedMhsFilter] = useState(filters.mahasiswa_id || '');
    const [modalOpen, setModalOpen] = useState(false);
    const [editMode, setEditMode] = useState(false);
    const [selectedId, setSelectedId] = useState(null);

    const { data, setData, post, put, delete: destroy, reset, processing, errors } = useForm({
        mahasiswa_id: '',
        week: '1',
        isi: '',
        status: 'pending',
    });

    const handleSearch = (e) => {
        e.preventDefault();
        applyFilters(searchVal, selectedMhsFilter);
    };

    const handleMhsFilterChange = (e) => {
        const val = e.target.value;
        setSelectedMhsFilter(val);
        applyFilters(searchVal, val);
    };

    const applyFilters = (search, mhsId) => {
        router.get('/laporan-mingguan', { 
            search: search || undefined, 
            mahasiswa_id: mhsId || undefined 
        }, { preserveState: true });
    };

    const handleOpenCreate = () => {
        reset();
        setEditMode(false);
        setSelectedId(null);
        if (roleName === 'mahasiswa') {
            setData(prev => ({
                ...prev,
                mahasiswa_id: currentUser.id,
                status: 'pending'
            }));
        }
        setModalOpen(true);
    };

    const handleOpenEdit = (lm) => {
        setEditMode(true);
        setSelectedId(lm.id);
        
        setData({
            mahasiswa_id: lm.mahasiswa_id || '',
            week: lm.week || '1',
            isi: lm.isi || '',
            status: lm.status || 'pending',
        });
        setModalOpen(true);
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        if (editMode) {
            put(`/laporan-mingguan/${selectedId}`, {
                onSuccess: () => {
                    setModalOpen(false);
                    reset();
                }
            });
        } else {
            post('/laporan-mingguan', {
                onSuccess: () => {
                    setModalOpen(false);
                    reset();
                }
            });
        }
    };

    const handleDelete = (id) => {
        if (confirm('Apakah Anda yakin ingin menghapus laporan mingguan ini?')) {
            destroy(`/laporan-mingguan/${id}`);
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
        if (s === 'ditolak') {
            return (
                <span className="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-semibold bg-rose-100 text-rose-700 border border-rose-200 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-500/20">
                    <XCircle className="w-3.5 h-3.5" /> Ditolak
                </span>
            );
        }
        return (
            <span className="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-semibold bg-amber-100 text-amber-700 border border-amber-200 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20">
                <Clock className="w-3.5 h-3.5" /> Pending
            </span>
        );
    };

    const isUrl = (str) => {
        try {
            new URL(str);
            return true;
        } catch (_) {
            return false;
        }
    };

    return (
        <AuthenticatedLayout title="Laporan Mingguan">
            <Head title="Laporan Mingguan" />

            <div className="space-y-6">
                {/* Search & Actions */}
                <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div className="flex flex-col sm:flex-row gap-3 flex-1 max-w-2xl">
                        <form onSubmit={handleSearch} className="relative flex-1">
                            <Search className="absolute left-4 top-1/2 -translate-y-1/2 w-4.5 h-4.5 text-slate-500" />
                            <input
                                type="text"
                                value={searchVal}
                                onChange={e => setSearchVal(e.target.value)}
                                placeholder="Cari Minggu Ke..."
                                className="w-full bg-white dark:bg-slate-900/40 border border-gray-200 dark:border-slate-800/60 rounded-2xl py-3 pl-11 pr-4 text-sm text-gray-900 dark:text-slate-200 placeholder-gray-400 dark:placeholder-slate-500 focus:outline-none focus:border-indigo-500"
                            />
                        </form>

                        {/* Mahasiswa filter for lecturers / admins */}
                        {roleName !== 'mahasiswa' && (
                            <div className="relative">
                                <Filter className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-indigo-400 pointer-events-none" />
                                <select
                                    value={selectedMhsFilter}
                                    onChange={handleMhsFilterChange}
                                    className="bg-white dark:bg-slate-900/40 border border-gray-200 dark:border-slate-800/60 rounded-2xl py-3 pl-9 pr-8 text-sm text-gray-900 dark:text-slate-200 focus:outline-none focus:border-indigo-500 appearance-none cursor-pointer"
                                >
                                    <option value="" className="bg-white dark:bg-slate-900">Semua Mahasiswa</option>
                                    {filterMahasiswas.map(m => (
                                        <option key={m.id} value={m.id} className="bg-white dark:bg-slate-900">{m.name}</option>
                                    ))}
                                </select>
                            </div>
                        )}
                    </div>

                    {(roleName === 'mahasiswa' || roleName === 'super_admin' || roleName === 'ka_prodi') && (
                        <button
                            onClick={handleOpenCreate}
                            className="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-5 rounded-2xl flex items-center justify-center gap-2 transition-all shadow-md self-start md:self-auto cursor-pointer"
                        >
                            <Plus className="w-5 h-5" />
                            Tambah Laporan Mingguan
                        </button>
                    )}
                </div>

                {/* Table */}
                <div className="bg-white dark:bg-slate-900/30 border border-gray-200 dark:border-slate-800/40 rounded-3xl overflow-hidden shadow-lg animate-fade-in">
                    <div className="overflow-x-auto">
                        <table className="w-full text-left border-collapse">
                            <thead>
                                <tr className="border-b border-gray-200 dark:border-slate-800/50 bg-gray-50 dark:bg-slate-900/20 text-gray-600 dark:text-slate-400 text-xs font-semibold uppercase tracking-wider">
                                    <th className="py-4.5 px-6">Minggu Ke</th>
                                    <th className="py-4.5 px-6">Mahasiswa</th>
                                    <th className="py-4.5 px-6">Dosen</th>
                                    <th className="py-4.5 px-6">Isi / Tautan</th>
                                    <th className="py-4.5 px-6">Status</th>
                                    <th className="py-4.5 px-6 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-gray-200 dark:divide-slate-800/30 text-sm text-gray-700 dark:text-slate-300">
                                {laporanMingguans.data.length === 0 ? (
                                    <tr>
                                        <td colSpan="6" className="py-12 text-center text-gray-500 dark:text-slate-500">
                                            Tidak ada riwayat laporan mingguan.
                                        </td>
                                    </tr>
                                ) : (
                                    laporanMingguans.data.map((lm) => (
                                        <tr key={lm.id} className="hover:bg-gray-50 dark:hover:bg-slate-900/10 transition-colors">
                                            <td className="py-4 px-6 font-extrabold text-gray-900 dark:text-slate-100">
                                                Minggu ke-{lm.week}
                                            </td>
                                            <td className="py-4 px-6 font-semibold text-gray-900 dark:text-slate-200">
                                                {lm.mahasiswa?.name || '-'}
                                            </td>
                                            <td className="py-4 px-6 text-gray-600 dark:text-slate-400">
                                                {lm.dosen?.name || '-'}
                                            </td>
                                            <td className="py-4 px-6">
                                                {isUrl(lm.isi) ? (
                                                    <a 
                                                        href={lm.isi} 
                                                        target="_blank" 
                                                        rel="noopener noreferrer"
                                                        className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-indigo-100 text-indigo-700 border border-indigo-200 dark:bg-indigo-500/10 dark:text-indigo-400 dark:border-indigo-500/20 hover:bg-indigo-200 dark:hover:bg-indigo-600/20 transition-all cursor-pointer"
                                                    >
                                                        <ExternalLink className="w-3.5 h-3.5" /> Lihat Dokumen
                                                    </a>
                                                ) : (
                                                    <span className="text-gray-600 dark:text-slate-350 line-clamp-2 max-w-xs">{lm.isi}</span>
                                                )}
                                            </td>
                                            <td className="py-4 px-6">
                                                {getStatusBadge(lm.status)}
                                            </td>
                                            <td className="py-4 px-6 text-right space-x-2">
                                                <button
                                                    onClick={() => handleOpenEdit(lm)}
                                                    className="inline-flex items-center justify-center p-2 rounded-lg bg-gray-100 dark:bg-slate-800 hover:bg-indigo-100 dark:hover:bg-indigo-650 hover:text-indigo-600 dark:hover:text-indigo-200 text-gray-600 dark:text-slate-400 transition-all border border-gray-300 dark:border-slate-700/50 cursor-pointer"
                                                    title={roleName === 'dosen' ? 'Review & Setujui' : 'Detail / Ubah'}
                                                >
                                                    <Edit className="w-4 h-4" />
                                                </button>
                                                {(roleName === 'super_admin' || roleName === 'ka_prodi' || (roleName === 'mahasiswa' && lm.mahasiswa_id === currentUser.id)) && (
                                                    <button
                                                        onClick={() => handleDelete(lm.id)}
                                                        className="inline-flex items-center justify-center p-2 rounded-lg bg-gray-100 dark:bg-slate-800 hover:bg-rose-100 dark:hover:bg-rose-950 hover:text-rose-600 dark:hover:text-rose-300 text-gray-600 dark:text-slate-400 transition-all border border-gray-300 dark:border-slate-700/50 cursor-pointer"
                                                        title="Hapus"
                                                    >
                                                        <Trash2 className="w-4 h-4" />
                                                    </button>
                                                )}
                                            </td>
                                        </tr>
                                    ))
                                )}
                            </tbody>
                        </table>
                    </div>

                    <Pagination paginator={laporanMingguans} itemLabel="laporan mingguan" />
                </div>
            </div>

            {/* Modal Laporan Mingguan */}
            {modalOpen && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
                    <div className="w-full max-w-2xl bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-3xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
                        <div className="flex items-center justify-between border-b border-gray-200 dark:border-slate-800/60 px-6 py-4.5">
                            <h3 className="font-bold text-gray-900 dark:text-slate-200">
                                {editMode 
                                    ? (roleName === 'dosen' ? 'Review & Tinjau Laporan Mingguan' : 'Ubah Laporan Mingguan') 
                                    : 'Tambah Laporan Mingguan Baru'}
                            </h3>
                            <button onClick={() => setModalOpen(false)} className="text-gray-500 dark:text-slate-400 hover:text-gray-900 dark:hover:text-slate-100 p-1.5 hover:bg-gray-100 dark:hover:bg-slate-800 rounded-lg">
                                <X className="w-5 h-5" />
                            </button>
                        </div>

                        <form onSubmit={handleSubmit} className="flex-1 overflow-auto p-6 space-y-4">
                            {/* Student Selection (Only visible to Admin) */}
                            {roleName !== 'mahasiswa' && roleName !== 'dosen' && (
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
                            )}

                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div className="space-y-1.5">
                                    <label className="text-xs text-gray-600 dark:text-slate-400 font-semibold uppercase">Minggu Ke</label>
                                    <input
                                        type="number"
                                        min="1"
                                        value={data.week}
                                        onChange={e => setData('week', e.target.value)}
                                        disabled={roleName === 'dosen'}
                                        placeholder="Misal: 1, 2, 3"
                                        className="w-full bg-gray-50 dark:bg-slate-950/50 border border-gray-300 dark:border-slate-800 rounded-xl py-2.5 px-3 text-sm text-gray-900 dark:text-slate-200 focus:outline-none focus:border-indigo-500 disabled:opacity-60"
                                    />
                                    {errors.week && <p className="text-rose-400 text-xs">{errors.week}</p>}
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
                                            <option value="ditolak">Ditolak</option>
                                        </select>
                                        {errors.status && <p className="text-rose-400 text-xs">{errors.status}</p>}
                                    </div>
                                )}
                            </div>

                            {/* Isi / Tautan Laporan */}
                            <div className="space-y-1.5">
                                <label className="text-xs text-gray-600 dark:text-slate-400 font-semibold uppercase">Isi / Tautan Laporan Mingguan</label>
                                <textarea
                                    value={data.isi}
                                    onChange={e => setData('isi', e.target.value)}
                                    disabled={roleName === 'dosen'}
                                    rows="5"
                                    placeholder="Masukkan penjelasan progres mingguan Anda atau tautkan URL dokumen bimbingan di Google Drive..."
                                    className="w-full bg-gray-50 dark:bg-slate-950/50 border border-gray-300 dark:border-slate-800 rounded-xl py-2.5 px-3 text-sm text-gray-900 dark:text-slate-200 focus:outline-none focus:border-indigo-500 disabled:opacity-60"
                                />
                                {errors.isi && <p className="text-rose-400 text-xs">{errors.isi}</p>}
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
                                {(roleName !== 'mahasiswa' || !editMode || data.status === 'pending' || data.status === 'ditolak') && (
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
