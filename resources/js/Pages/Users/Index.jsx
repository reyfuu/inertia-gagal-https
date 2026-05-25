import React, { useState } from 'react';
import { Head, useForm, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import Pagination from '@/Components/Pagination';
import { 
    Search, 
    Plus, 
    Edit, 
    Trash2, 
    X, 
    Shield, 
    CheckCircle, 
    AlertTriangle,
    Mail,
    Lock,
    User as UserIcon,
} from 'lucide-react';

export default function Index({ users, roles, dosens, filters }) {
    const [searchVal, setSearchVal] = useState(filters.search || '');
    const [modalOpen, setModalOpen] = useState(false);
    const [editMode, setEditMode] = useState(false);
    const [editingUserId, setEditingUserId] = useState(null);

    const { data, setData, post, put, delete: destroy, reset, processing, errors } = useForm({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        role_id: roles[0]?.id || '',
        status: 'active',
        npm: '',
        nidn: '',
        angkatan: '',
        kategori: 'skripsi',
        dosen_pembimbing_id: '',
        telegram_chat_id: '',
    });

    const selectedRole = roles.find(r => r.id === parseInt(data.role_id))?.name || '';
    const isMahasiswa = selectedRole === 'mahasiswa';
    const isDosenOrKaprodi = selectedRole === 'dosen' || selectedRole === 'ka_prodi';
    const isAdmin = selectedRole === 'super_admin';

    const handleSearch = (e) => {
        e.preventDefault();
        router.get('/user', { search: searchVal }, { preserveState: true });
    };

    const handleOpenCreate = () => {
        reset();
        setEditMode(false);
        setEditingUserId(null);
        setModalOpen(true);
    };

    const handleOpenEdit = (user) => {
        setEditMode(true);
        setEditingUserId(user.id);
        
        setData({
            name: user.name || '',
            email: user.email || '',
            password: '',
            password_confirmation: '',
            role_id: user.roles[0]?.id || roles[0]?.id || '',
            status: user.status || 'active',
            npm: user.npm || '',
            nidn: user.nidn || '',
            angkatan: user.angkatan || '',
            kategori: user.kategori || 'skripsi',
            dosen_pembimbing_id: user.dosen_pembimbing_id || '',
            telegram_chat_id: user.telegram_chat_id || '',
        });
        setModalOpen(true);
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        if (editMode) {
            put(`/user/${editingUserId}`, {
                onSuccess: () => {
                    setModalOpen(false);
                    reset();
                }
            });
        } else {
            post('/user', {
                onSuccess: () => {
                    setModalOpen(false);
                    reset();
                }
            });
        }
    };

    const handleDelete = (userId) => {
        if (confirm('Apakah Anda yakin ingin menghapus user ini?')) {
            destroy(`/user/${userId}`);
        }
    };

    return (
        <AuthenticatedLayout title="Daftar User">
            <Head title="Daftar User" />

            {/* [SKILL] Mulai dark/light mode support di halaman Users */}
            <div className="space-y-6">
                {/* Search & Actions Bar */}
                {/* [SKILL] Bar pencarian dan aksi responsif mode */}
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <form onSubmit={handleSearch} className="relative w-full max-w-md">
                        <Search className="absolute left-4 top-1/2 -translate-y-1/2 w-4.5 h-4.5 text-slate-500" />
                        {/* [SKILL] Input responsif mode */}
                        <input
                            type="text"
                            value={searchVal}
                            onChange={e => setSearchVal(e.target.value)}
                            placeholder="Cari user (nama, email, npm)..."
                            className="w-full bg-white text-gray-900 placeholder-gray-400 border border-gray-300 rounded-2xl py-3 pl-11 pr-4 text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/20 dark:bg-slate-900/40 dark:text-slate-200 dark:placeholder-slate-500 dark:border-slate-800/60 transition-colors"
                        />
                    </form>

                    {/* [SKILL] Tombol tambah user responsif mode */}
                    <button
                        onClick={handleOpenCreate}
                        className="bg-indigo-100 text-indigo-800 hover:bg-indigo-200 font-bold py-3 px-5 rounded-2xl flex items-center justify-center gap-2 transition-all shadow-md hover:shadow-indigo-500/20 self-start sm:self-auto cursor-pointer dark:bg-indigo-600 dark:text-white dark:hover:bg-indigo-700"
                    >
                        <Plus className="w-5 h-5" />
                        Tambah User
                    </button>
                </div>

                {/* Users Table */}
                {/* [SKILL] Card tabel responsif mode */}
                <div className="bg-white border border-gray-200 rounded-3xl overflow-hidden shadow-lg dark:bg-slate-900/30 dark:border-slate-800/40 transition-colors">
                    <div className="overflow-x-auto">
                        {/* [SKILL] Tabel responsif mode */}
                        <table className="w-full text-left border-collapse">
                            <thead>
                                <tr className="border-b border-gray-200 bg-gray-50 text-gray-500 text-xs font-semibold uppercase tracking-wider dark:border-slate-800/50 dark:bg-slate-900/20 dark:text-slate-400">
                                    <th className="py-4.5 px-6">Nama Lengkap</th>
                                    <th className="py-4.5 px-6">Email</th>
                                    <th className="py-4.5 px-6">Roles</th>
                                    <th className="py-4.5 px-6">Status</th>
                                    <th className="py-4.5 px-6 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-gray-100 text-sm text-gray-700 dark:divide-slate-800/30 dark:text-slate-300">
                                {users.data.length === 0 ? (
                                    <tr>
                                        <td colSpan="5" className="py-12 text-center text-slate-500">
                                            Tidak ada data user.
                                        </td>
                                    </tr>
                                ) : (
                                    users.data.map((u) => (
                                        <tr key={u.id} className="hover:bg-slate-900/10 transition-colors">
                                            <td className="py-4 px-6 font-semibold text-gray-900 dark:text-slate-200">
                                                {u.name}
                                                {/* [SKILL] Info NPM/NIDN responsif mode */}
                                                {u.npm && <span className="block text-xs font-medium text-gray-500 dark:text-slate-500 mt-0.5">NPM: {u.npm}</span>}
                                                {u.nidn && <span className="block text-xs font-medium text-gray-500 dark:text-slate-500 mt-0.5">NIDN: {u.nidn}</span>}
                                            </td>
                                            <td className="py-4 px-6 text-gray-500 dark:text-slate-400">{u.email}</td>
                                            <td className="py-4 px-6">
                                                {/* [SKILL] Badge role responsif mode */}
                                                <span className="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-purple-100 text-purple-800 border border-purple-200 capitalize dark:bg-purple-500/10 dark:text-purple-400 dark:border-purple-500/20">
                                                    <Shield className="w-3 h-3" />
                                                    {u.roles[0]?.name?.replace('_', ' ') || '-'}
                                                </span>
                                            </td>
                                            <td className="py-4 px-6">
                                                {/* [SKILL] Badge status responsif mode */}
                                                <span className={`inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-semibold border ${
                                                    u.status === 'active' 
                                                    ? 'bg-emerald-100 text-emerald-800 border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20' 
                                                    : 'bg-rose-100 text-rose-800 border-rose-200 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-500/20'
                                                }`}>
                                                    {u.status === 'active' ? <CheckCircle className="w-3.5 h-3.5" /> : <AlertTriangle className="w-3.5 h-3.5" />}
                                                    <span className="capitalize">{u.status}</span>
                                                </span>
                                            </td>
                                            <td className="py-4 px-6 text-right space-x-2">
                                                <button
                                                    onClick={() => handleOpenEdit(u)}
                                                    className="inline-flex items-center justify-center p-2 rounded-lg bg-slate-800 hover:bg-indigo-650 hover:text-indigo-200 text-slate-400 transition-all border border-slate-700/50 cursor-pointer"
                                                    title="Ubah"
                                                >
                                                    <Edit className="w-4 h-4" />
                                                </button>
                                                <button
                                                    onClick={() => handleDelete(u.id)}
                                                    className="inline-flex items-center justify-center p-2 rounded-lg bg-slate-800 hover:bg-rose-950 hover:text-rose-300 text-slate-400 transition-all border border-slate-700/50 cursor-pointer"
                                                    title="Hapus"
                                                >
                                                    <Trash2 className="w-4 h-4" />
                                                </button>
                                            </td>
                                        </tr>
                                    ))
                                )}
                            </tbody>
                        </table>
                    </div>

                    <Pagination paginator={users} itemLabel="pengguna" />
                </div>
            </div>

            {/* Modal Form */}
            {modalOpen && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
                    <div className="w-full max-w-2xl bg-slate-900 border border-slate-800 rounded-3xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
                        <div className="flex items-center justify-between border-b border-slate-800/60 px-6 py-4.5 bg-slate-900/50">
                            <h3 className="font-bold text-slate-200">{editMode ? 'Ubah Pengguna' : 'Tambah Pengguna Baru'}</h3>
                            <button onClick={() => setModalOpen(false)} className="text-slate-400 hover:text-slate-100 p-1.5 hover:bg-slate-800 rounded-lg">
                                <X className="w-5 h-5" />
                            </button>
                        </div>

                        <form onSubmit={handleSubmit} className="flex-1 overflow-auto p-6 space-y-4">
                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                {/* Role Select */}
                                <div className="space-y-1.5">
                                    <label className="text-xs font-semibold text-slate-400 uppercase tracking-wider">Role</label>
                                    <select
                                        value={data.role_id}
                                        onChange={e => setData('role_id', e.target.value)}
                                        className="w-full bg-slate-950/50 border border-slate-800 rounded-xl py-2.5 px-3 text-sm text-slate-200 focus:outline-none focus:border-indigo-500 transition-all"
                                    >
                                        {roles.map(r => (
                                            <option key={r.id} value={r.id} className="bg-slate-900 capitalize">{r.name.replace('_', ' ')}</option>
                                        ))}
                                    </select>
                                    {errors.role_id && <p className="text-rose-400 text-xs">{errors.role_id}</p>}
                                </div>

                                {/* Status Select */}
                                <div className="space-y-1.5">
                                    <label className="text-xs font-semibold text-slate-400 uppercase tracking-wider">Status</label>
                                    <select
                                        value={data.status}
                                        onChange={e => setData('status', e.target.value)}
                                        className="w-full bg-slate-950/50 border border-slate-800 rounded-xl py-2.5 px-3 text-sm text-slate-200 focus:outline-none focus:border-indigo-500 transition-all"
                                    >
                                        <option value="active" className="bg-slate-900">Active</option>
                                        <option value="inactive" className="bg-slate-900">Inactive</option>
                                        <option value="suspended" className="bg-slate-900">Suspended</option>
                                    </select>
                                    {errors.status && <p className="text-rose-400 text-xs">{errors.status}</p>}
                                </div>
                            </div>

                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                {/* Name Input */}
                                <div className="space-y-1.5">
                                    <label className="text-xs font-semibold text-slate-400 uppercase tracking-wider">Nama Lengkap</label>
                                    <div className="relative">
                                        <UserIcon className="absolute left-3 top-1/2 -translate-y-1/2 w-4.5 h-4.5 text-slate-500" />
                                        <input
                                            type="text"
                                            value={data.name}
                                            onChange={e => setData('name', e.target.value)}
                                            placeholder="Nama Lengkap"
                                            className="w-full bg-slate-950/50 border border-slate-800 rounded-xl py-2.5 pl-10 pr-3 text-sm text-slate-200 focus:outline-none focus:border-indigo-500"
                                        />
                                    </div>
                                    {errors.name && <p className="text-rose-400 text-xs">{errors.name}</p>}
                                </div>

                                {/* Email Input */}
                                <div className="space-y-1.5">
                                    <label className="text-xs font-semibold text-slate-400 uppercase tracking-wider">Email</label>
                                    <div className="relative">
                                        <Mail className="absolute left-3 top-1/2 -translate-y-1/2 w-4.5 h-4.5 text-slate-500" />
                                        <input
                                            type="email"
                                            value={data.email}
                                            onChange={e => setData('email', e.target.value)}
                                            placeholder="nama@email.com"
                                            className="w-full bg-slate-950/50 border border-slate-800 rounded-xl py-2.5 pl-10 pr-3 text-sm text-slate-200 focus:outline-none focus:border-indigo-500"
                                        />
                                    </div>
                                    {errors.email && <p className="text-rose-400 text-xs">{errors.email}</p>}
                                </div>
                            </div>

                            {/* Conditionally Render Fields based on selected Role */}
                            {isMahasiswa && (
                                <div className="p-4 bg-slate-950/30 border border-slate-850 rounded-2xl space-y-4 animate-fade-in">
                                    <h4 className="text-xs font-bold text-indigo-400 tracking-wider uppercase">Data Mahasiswa</h4>
                                    
                                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div className="space-y-1.5">
                                            <label className="text-xs text-slate-400">NPM</label>
                                            <input
                                                type="text"
                                                value={data.npm}
                                                onChange={e => setData('npm', e.target.value)}
                                                className="w-full bg-slate-950/60 border border-slate-800 rounded-xl py-2 px-3 text-sm text-slate-200 focus:outline-none focus:border-indigo-500"
                                            />
                                            {errors.npm && <p className="text-rose-400 text-xs">{errors.npm}</p>}
                                        </div>

                                        <div className="space-y-1.5">
                                            <label className="text-xs text-slate-400">Angkatan</label>
                                            <input
                                                type="text"
                                                value={data.angkatan}
                                                onChange={e => setData('angkatan', e.target.value)}
                                                placeholder="Contoh: 2022"
                                                className="w-full bg-slate-950/60 border border-slate-800 rounded-xl py-2 px-3 text-sm text-slate-200 focus:outline-none focus:border-indigo-500"
                                            />
                                            {errors.angkatan && <p className="text-rose-400 text-xs">{errors.angkatan}</p>}
                                        </div>
                                    </div>

                                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div className="space-y-1.5">
                                            <label className="text-xs text-slate-400">Kategori</label>
                                            <select
                                                value={data.kategori}
                                                onChange={e => setData('kategori', e.target.value)}
                                                className="w-full bg-slate-950/60 border border-slate-800 rounded-xl py-2 px-3 text-sm text-slate-200 focus:outline-none focus:border-indigo-500"
                                            >
                                                <option value="skripsi">Skripsi</option>
                                                <option value="magang">Magang</option>
                                            </select>
                                            {errors.kategori && <p className="text-rose-400 text-xs">{errors.kategori}</p>}
                                        </div>

                                        <div className="space-y-1.5">
                                            <label className="text-xs text-slate-400">Dosen Pembimbing</label>
                                            <select
                                                value={data.dosen_pembimbing_id}
                                                onChange={e => setData('dosen_pembimbing_id', e.target.value)}
                                                className="w-full bg-slate-950/60 border border-slate-800 rounded-xl py-2 px-3 text-sm text-slate-200 focus:outline-none focus:border-indigo-500"
                                            >
                                                <option value="">Pilih Dosen</option>
                                                {dosens.map(d => (
                                                    <option key={d.id} value={d.id}>{d.name}</option>
                                                ))}
                                            </select>
                                            {errors.dosen_pembimbing_id && <p className="text-rose-400 text-xs">{errors.dosen_pembimbing_id}</p>}
                                        </div>
                                    </div>
                                </div>
                            )}

                            {isDosenOrKaprodi && (
                                <div className="p-4 bg-slate-950/30 border border-slate-850 rounded-2xl space-y-4 animate-fade-in">
                                    <h4 className="text-xs font-bold text-indigo-400 tracking-wider uppercase">Data Dosen / Kaprodi</h4>
                                    
                                    <div className="space-y-1.5">
                                        <label className="text-xs text-slate-400 font-semibold">NIDN</label>
                                        <input
                                            type="text"
                                            value={data.nidn}
                                            onChange={e => setData('nidn', e.target.value)}
                                            className="w-full bg-slate-950/60 border border-slate-800 rounded-xl py-2 px-3 text-sm text-slate-200 focus:outline-none focus:border-indigo-500"
                                        />
                                        {errors.nidn && <p className="text-rose-400 text-xs">{errors.nidn}</p>}
                                    </div>
                                </div>
                            )}

                            {!isAdmin && (
                                <div className="space-y-1.5">
                                    <label className="text-xs font-semibold text-slate-400 uppercase tracking-wider">Telegram Chat ID</label>
                                    <input
                                        type="text"
                                        value={data.telegram_chat_id}
                                        onChange={e => setData('telegram_chat_id', e.target.value)}
                                        className="w-full bg-slate-950/50 border border-slate-800 rounded-xl py-2.5 px-3 text-sm text-slate-200 focus:outline-none focus:border-indigo-500"
                                    />
                                    {errors.telegram_chat_id && <p className="text-rose-400 text-xs">{errors.telegram_chat_id}</p>}
                                </div>
                            )}

                            {/* Passwords */}
                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div className="space-y-1.5">
                                    <label className="text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                        Password {editMode && <span className="text-[10px] text-slate-500 lowercase">(biarkan kosong jika tidak diubah)</span>}
                                    </label>
                                    <div className="relative">
                                        <Lock className="absolute left-3 top-1/2 -translate-y-1/2 w-4.5 h-4.5 text-slate-500" />
                                        <input
                                            type="password"
                                            value={data.password}
                                            onChange={e => setData('password', e.target.value)}
                                            placeholder="Minimal 6 karakter"
                                            className="w-full bg-slate-950/50 border border-slate-800 rounded-xl py-2.5 pl-10 pr-3 text-sm text-slate-200 focus:outline-none focus:border-indigo-500"
                                        />
                                    </div>
                                    {errors.password && <p className="text-rose-400 text-xs">{errors.password}</p>}
                                </div>

                                <div className="space-y-1.5">
                                    <label className="text-xs font-semibold text-slate-400 uppercase tracking-wider">Konfirmasi Password</label>
                                    <div className="relative">
                                        <Lock className="absolute left-3 top-1/2 -translate-y-1/2 w-4.5 h-4.5 text-slate-500" />
                                        <input
                                            type="password"
                                            value={data.password_confirmation}
                                            onChange={e => setData('password_confirmation', e.target.value)}
                                            placeholder="Ulangi password"
                                            className="w-full bg-slate-950/50 border border-slate-800 rounded-xl py-2.5 pl-10 pr-3 text-sm text-slate-200 focus:outline-none focus:border-indigo-500"
                                        />
                                    </div>
                                </div>
                            </div>

                            <div className="border-t border-slate-800/60 pt-4 flex justify-end gap-3">
                                <button
                                    type="button"
                                    onClick={() => setModalOpen(false)}
                                    className="bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold py-2.5 px-4 rounded-xl text-sm transition-all"
                                >
                                    Batal
                                </button>
                                <button
                                    type="submit"
                                    disabled={processing}
                                    className="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-5 rounded-xl text-sm transition-all shadow-md hover:shadow-indigo-500/10 disabled:opacity-50"
                                >
                                    {processing ? 'Menyimpan...' : 'Simpan'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </AuthenticatedLayout>
    );
}
