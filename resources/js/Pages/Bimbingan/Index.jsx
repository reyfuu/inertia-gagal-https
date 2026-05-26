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
    Calendar,
    MessageSquare,
    CheckCircle,
    XCircle,
    HelpCircle,
    User
} from 'lucide-react';

export default function Index({ bimbingans, mahasiswas, dosens, filters }) {
    const { auth } = usePage().props;
    const currentUser = auth.user;
    const roleName = currentUser.role;

    const [searchVal, setSearchVal] = useState(filters.search || '');
    const [modalOpen, setModalOpen] = useState(false);
    const [editMode, setEditMode] = useState(false);
    const [selectedBimbinganId, setSelectedBimbinganId] = useState(null);
    const [commentModalOpen, setCommentModalOpen] = useState(false);
    const [commentHistory, setCommentHistory] = useState([]);
    const [commentMahasiswaName, setCommentMahasiswaName] = useState('');
    const [loadingComments, setLoadingComments] = useState(false);

    const { data, setData, post, put, delete: destroy, reset, processing, errors } = useForm({
        user_id: '',
        dosen_id: '',
        topik: '',
        tanggal: new Date().toISOString().split('T')[0],
        type: 'skripsi',
        status: 'Review',
        isi: '',
        komentar: '',
    });

    const handleSearch = (e) => {
        e.preventDefault();
        router.get('/bimbingan', { search: searchVal }, { preserveState: true });
    };

    const handleOpenCreate = () => {
        reset();
        setEditMode(false);
        setSelectedBimbinganId(null);
        
        // Auto-fill student/dosen if user is mahasiswa
        if (roleName === 'mahasiswa') {
            setData(prev => ({
                ...prev,
                user_id: currentUser.id,
                status: 'Review'
            }));
        }
        setModalOpen(true);
    };

    const handleOpenEdit = (b) => {
        setEditMode(true);
        setSelectedBimbinganId(b.id);
        
        setData({
            user_id: b.user_id || '',
            dosen_id: b.dosen_id || '',
            topik: b.topik || '',
            tanggal: b.tanggal || '',
            type: b.type || 'skripsi',
            status: b.status || 'Review',
            isi: b.isi || '',
            komentar: b.komentar || '',
        });
        setModalOpen(true);
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        if (editMode) {
            put(`/bimbingan/${selectedBimbinganId}`, {
                onSuccess: () => {
                    setModalOpen(false);
                    reset();
                }
            });
        } else {
            post('/bimbingan', {
                onSuccess: () => {
                    setModalOpen(false);
                    reset();
                }
            });
        }
    };

    const handleDelete = (id) => {
        if (confirm('Apakah Anda yakin ingin menghapus log bimbingan ini?')) {
            destroy(`/bimbingan/${id}`);
        }
    };

    const handleOpenComments = async (b) => {
        setCommentModalOpen(true);
        setLoadingComments(true);
        setCommentHistory([]);
        setCommentMahasiswaName(b.user?.name || '');

        try {
            const response = await fetch(`/bimbingan/${b.id}/komentar`, {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            });

            if (!response.ok) {
                throw new Error('Gagal memuat riwayat komentar');
            }

            const data = await response.json();
            setCommentHistory(data.comments || []);
            setCommentMahasiswaName(data.mahasiswa_name || b.user?.name || '');
        } catch {
            setCommentHistory([]);
        } finally {
            setLoadingComments(false);
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
                <HelpCircle className="w-3.5 h-3.5" /> Review
            </span>
        );
    };

    return (
        <AuthenticatedLayout title="Daftar Bimbingan">
            <Head title="Daftar Bimbingan" />

            <div className="space-y-6">
                {/* Search & Actions */}
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <form onSubmit={handleSearch} className="relative w-full max-w-md">
                        <Search className="absolute left-4 top-1/2 -translate-y-1/2 w-4.5 h-4.5 text-slate-500" />
                        <input
                            type="text"
                            value={searchVal}
                            onChange={e => setSearchVal(e.target.value)}
                            placeholder="Cari topik bimbingan..."
                            className="w-full bg-white dark:bg-slate-900/40 border border-gray-200 dark:border-slate-800/60 rounded-2xl py-3 pl-11 pr-4 text-sm text-gray-900 dark:text-slate-200 placeholder-gray-400 dark:placeholder-slate-500 focus:outline-none focus:border-indigo-500"
                        />
                    </form>

                    {(roleName === 'mahasiswa' || roleName === 'super_admin' || roleName === 'ka_prodi') && (
                        <button
                            onClick={handleOpenCreate}
                            className="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-5 rounded-2xl flex items-center justify-center gap-2 transition-all shadow-md self-start sm:self-auto cursor-pointer"
                        >
                            <Plus className="w-5 h-5" />
                            Tambah Bimbingan
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
                                    <th className="py-4.5 px-6">Dosen</th>
                                    <th className="py-4.5 px-6">Topik Pertemuan</th>
                                    <th className="py-4.5 px-6">Tanggal</th>
                                    <th className="py-4.5 px-6">Jenis</th>
                                    <th className="py-4.5 px-6">Revisi</th>
                                    <th className="py-4.5 px-6">Status</th>
                                    <th className="py-4.5 px-6 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-gray-200 dark:divide-slate-800/30 text-sm text-gray-700 dark:text-slate-300">
                                {bimbingans.data.length === 0 ? (
                                    <tr>
                                        <td colSpan="8" className="py-12 text-center text-gray-500 dark:text-slate-500">
                                            Tidak ada riwayat bimbingan.
                                        </td>
                                    </tr>
                                ) : (
                                    bimbingans.data.map((b) => (
                                        <tr key={b.id} className="hover:bg-gray-50 dark:hover:bg-slate-900/10 transition-colors">
                                            <td className="py-4 px-6 font-semibold text-gray-900 dark:text-slate-200">
                                                {b.user?.name || '-'}
                                            </td>
                                            <td className="py-4 px-6 text-slate-400">
                                                {b.dosen?.name || '-'}
                                            </td>
                                            <td className="py-4 px-6">
                                                <span className="font-semibold text-gray-900 dark:text-slate-200">{b.topik}</span>
                                                {b.komentar && (
                                                    <span className="flex items-center gap-1 text-xs text-indigo-600 dark:text-indigo-400 mt-1 font-medium">
                                                        <MessageSquare className="w-3.5 h-3.5" /> Ada komentar dosen
                                                    </span>
                                                )}
                                            </td>
                                            <td className="py-4 px-6 text-slate-400">
                                                {new Date(b.tanggal).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })}
                                            </td>
                                            <td className="py-4 px-6 text-gray-600 dark:text-slate-400 uppercase text-xs font-semibold">
                                                {b.type}
                                            </td>
                                            <td className="py-4 px-6">
                                                <span className="px-2 py-0.5 rounded bg-indigo-100 border border-indigo-200 text-indigo-700 dark:bg-indigo-500/10 dark:border-indigo-500/20 dark:text-indigo-400 text-xs font-bold">
                                                    #{b.revision_count}
                                                </span>
                                            </td>
                                            <td className="py-4 px-6">
                                                {getStatusBadge(b.status)}
                                            </td>
                                            <td className="py-4 px-6 text-right">
                                                <div className="inline-flex items-center justify-end gap-2">
                                                <button
                                                    onClick={() => handleOpenComments(b)}
                                                    className="inline-flex items-center justify-center p-2 rounded-lg bg-gray-100 dark:bg-slate-800 hover:bg-indigo-100 dark:hover:bg-indigo-950 hover:text-indigo-600 dark:hover:text-indigo-300 text-gray-600 dark:text-slate-400 transition-all border border-gray-300 dark:border-slate-700/50 cursor-pointer"
                                                    title="Riwayat Komentar"
                                                >
                                                    <MessageSquare className="w-4 h-4" />
                                                </button>
                                                <button
                                                    onClick={() => handleOpenEdit(b)}
                                                    className="inline-flex items-center justify-center p-2 rounded-lg bg-gray-100 dark:bg-slate-800 hover:bg-indigo-100 dark:hover:bg-indigo-700 hover:text-indigo-600 dark:hover:text-indigo-200 text-gray-600 dark:text-slate-400 transition-all border border-gray-300 dark:border-slate-700/50 cursor-pointer"
                                                    title={roleName === 'dosen' ? 'Beri Komentar & Review' : 'Detail / Ubah'}
                                                >
                                                    <Edit className="w-4 h-4" />
                                                </button>
                                                {(roleName === 'super_admin' || roleName === 'ka_prodi' || (roleName === 'mahasiswa' && b.user_id === currentUser.id)) && (
                                                    <button
                                                        onClick={() => handleDelete(b.id)}
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

                    <Pagination paginator={bimbingans} itemLabel="bimbingan" />
                </div>
            </div>

            {/* Modal Riwayat Komentar */}
            {commentModalOpen && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
                    <div className="w-full max-w-lg bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[85vh]">
                        <div className="flex items-center justify-between border-b border-gray-200 dark:border-slate-800/60 px-6 py-4">
                            <h3 className="font-bold text-lg text-gray-900 dark:text-slate-100">Riwayat Komentar</h3>
                            <button
                                type="button"
                                onClick={() => setCommentModalOpen(false)}
                                className="text-gray-500 dark:text-slate-400 hover:text-gray-900 dark:hover:text-slate-100 p-1.5 hover:bg-gray-100 dark:hover:bg-slate-800 rounded-lg cursor-pointer"
                            >
                                <X className="w-5 h-5" />
                            </button>
                        </div>

                        <div className="flex-1 overflow-auto px-6 py-4">
                            <p className="text-sm font-medium text-gray-700 dark:text-slate-300 border-b border-gray-200 dark:border-slate-800/60 pb-3 mb-4">
                                History log
                                {commentMahasiswaName && (
                                    <span className="text-gray-500 dark:text-slate-500 font-normal"> · {commentMahasiswaName}</span>
                                )}
                            </p>

                            {loadingComments ? (
                                <div className="flex items-center justify-center py-12 text-gray-500 dark:text-slate-500 text-sm">
                                    Memuat riwayat...
                                </div>
                            ) : commentHistory.length === 0 ? (
                                <div className="rounded-xl border border-gray-200 dark:border-slate-800/60 bg-gray-50 dark:bg-slate-950/50 px-4 py-8 text-center text-sm text-gray-500 dark:text-slate-500">
                                    Belum ada komentar dari dosen pembimbing.
                                </div>
                            ) : (
                                <div className="space-y-3">
                                    {commentHistory.map((item) => (
                                        <div
                                            key={item.id}
                                            className="rounded-xl border border-gray-200 dark:border-slate-800/60 bg-gray-50 dark:bg-slate-950/80 px-4 py-3.5"
                                        >
                                            <p className="text-xs text-gray-500 dark:text-slate-500 mb-2">
                                                Bimbingan · Dosen: {item.dosen_name}
                                                {item.topik && (
                                                    <span className="text-gray-600 dark:text-slate-600"> · {item.topik}</span>
                                                )}
                                            </p>
                                            <p className="text-sm text-gray-900 dark:text-slate-100 whitespace-pre-wrap leading-relaxed">
                                                {item.komentar}
                                            </p>
                                        </div>
                                    ))}
                                </div>
                            )}
                        </div>

                        <div className="border-t border-gray-200 dark:border-slate-800/60 px-6 py-4">
                            <button
                                type="button"
                                onClick={() => setCommentModalOpen(false)}
                                className="bg-gray-100 dark:bg-slate-800 hover:bg-gray-200 dark:hover:bg-slate-700 text-gray-700 dark:text-slate-300 font-semibold py-2.5 px-5 rounded-xl text-sm transition-all cursor-pointer"
                            >
                                Batal
                            </button>
                        </div>
                    </div>
                </div>
            )}

            {/* Modal Bimbingan */}
            {modalOpen && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
                    <div className="w-full max-w-2xl bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-3xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
                        <div className="flex items-center justify-between border-b border-gray-200 dark:border-slate-800/60 px-6 py-4.5">
                            <h3 className="font-bold text-gray-900 dark:text-slate-200">
                                {editMode 
                                    ? (roleName === 'dosen' ? 'Tinjau & Komentar Bimbingan' : 'Ubah Bimbingan') 
                                    : 'Tambah Log Bimbingan'}
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
                                            value={data.user_id}
                                            onChange={e => setData('user_id', e.target.value)}
                                            className="w-full bg-gray-50 dark:bg-slate-950/50 border border-gray-300 dark:border-slate-800 rounded-xl py-2 px-3 text-sm text-gray-900 dark:text-slate-200 focus:outline-none focus:border-indigo-500"
                                        >
                                            <option value="">Pilih Mahasiswa</option>
                                            {mahasiswas.map(m => (
                                                <option key={m.id} value={m.id}>{m.name}</option>
                                            ))}
                                        </select>
                                        {errors.user_id && <p className="text-rose-400 text-xs">{errors.user_id}</p>}
                                    </div>

                                    <div className="space-y-1.5">
                                        <label className="text-xs text-gray-600 dark:text-slate-400 font-semibold uppercase">Dosen</label>
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

                            {/* Core Log Fields (Readonly for Lecturers) */}
                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div className="space-y-1.5">
                                    <label className="text-xs text-gray-600 dark:text-slate-400 font-semibold uppercase">Topik Pertemuan</label>
                                    <input
                                        type="text"
                                        value={data.topik}
                                        onChange={e => setData('topik', e.target.value)}
                                        disabled={roleName === 'dosen'}
                                        placeholder="Judul atau topik pembahasan"
                                        className="w-full bg-gray-50 dark:bg-slate-950/50 border border-gray-300 dark:border-slate-800 rounded-xl py-2.5 px-3 text-sm text-gray-900 dark:text-slate-200 focus:outline-none focus:border-indigo-500 disabled:opacity-60"
                                    />
                                    {errors.topik && <p className="text-rose-400 text-xs">{errors.topik}</p>}
                                </div>

                                <div className="space-y-1.5">
                                    <label className="text-xs text-gray-600 dark:text-slate-400 font-semibold uppercase">Tanggal</label>
                                    <input
                                        type="date"
                                        value={data.tanggal}
                                        onChange={e => setData('tanggal', e.target.value)}
                                        disabled={roleName === 'dosen'}
                                        className="w-full bg-gray-50 dark:bg-slate-950/50 border border-gray-300 dark:border-slate-800 rounded-xl py-2.5 px-3 text-sm text-gray-900 dark:text-slate-200 focus:outline-none focus:border-indigo-500 disabled:opacity-60"
                                    />
                                    {errors.tanggal && <p className="text-rose-400 text-xs">{errors.tanggal}</p>}
                                </div>
                            </div>

                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div className="space-y-1.5">
                                    <label className="text-xs text-gray-600 dark:text-slate-400 font-semibold uppercase">Jenis Bimbingan</label>
                                    <select
                                        value={data.type}
                                        onChange={e => setData('type', e.target.value)}
                                        disabled={roleName === 'dosen'}
                                        className="w-full bg-gray-50 dark:bg-slate-950/50 border border-gray-300 dark:border-slate-800 rounded-xl py-2.5 px-3 text-sm text-gray-900 dark:text-slate-200 focus:outline-none focus:border-indigo-500 disabled:opacity-60"
                                    >
                                        <option value="skripsi">Skripsi</option>
                                        <option value="proposal">Proposal</option>
                                    </select>
                                    {errors.type && <p className="text-rose-400 text-xs">{errors.type}</p>}
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
                                            <option value="Review">Review</option>
                                            <option value="Disetujui">Disetujui</option>
                                            <option value="Ditolak">Ditolak</option>
                                        </select>
                                        {errors.status && <p className="text-rose-400 text-xs">{errors.status}</p>}
                                    </div>
                                )}
                            </div>

                            {/* Isi Bimbingan */}
                            <div className="space-y-1.5">
                                <label className="text-xs text-gray-600 dark:text-slate-400 font-semibold uppercase">Isi Bimbingan</label>
                                <textarea
                                    value={data.isi}
                                    onChange={e => setData('isi', e.target.value)}
                                    disabled={roleName === 'dosen'}
                                    rows="4"
                                    placeholder="Tuliskan catatan progres atau isi bimbingan..."
                                    className="w-full bg-gray-50 dark:bg-slate-950/50 border border-gray-300 dark:border-slate-800 rounded-xl py-2.5 px-3 text-sm text-gray-900 dark:text-slate-200 focus:outline-none focus:border-indigo-500 disabled:opacity-60"
                                />
                                {errors.isi && <p className="text-rose-400 text-xs">{errors.isi}</p>}
                            </div>

                            {/* Komentar Dosen (Only editable by Dosen / Admin) */}
                            <div className="space-y-1.5">
                                <label className="text-xs text-gray-600 dark:text-slate-400 font-semibold uppercase">Komentar / Feedback Dosen</label>
                                <textarea
                                    value={data.komentar}
                                    onChange={e => setData('komentar', e.target.value)}
                                    disabled={roleName === 'mahasiswa'}
                                    rows="3"
                                    placeholder={roleName === 'mahasiswa' ? "Belum ada feedback dari Dosen Pembimbing." : "Berikan feedback atau catatan revisi..."}
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
                                {(roleName !== 'mahasiswa' || !editMode || data.status === 'Review' || data.status === 'Ditolak') && (
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
