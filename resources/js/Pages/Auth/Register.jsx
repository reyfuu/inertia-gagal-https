import React from 'react';
import { Head, useForm, Link } from '@inertiajs/react';
import { Mail, Lock, User, Hash, Send, ChevronRight, ArrowLeft } from 'lucide-react';

export default function Register() {
    const { data, setData, post, processing, errors } = useForm({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        npm: '',
        telegram_chat_id: '',
        kategori: 'skripsi',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/register');
    };

    return (
        <div className="min-h-screen bg-slate-950 text-slate-100 flex items-center justify-center relative p-6 overflow-hidden font-sans">
            {/* Ambient Background Glows */}
            <div className="absolute top-1/4 left-1/4 -translate-x-1/2 -translate-y-1/2 w-96 h-96 rounded-full bg-indigo-500/10 blur-[120px] pointer-events-none"></div>
            <div className="absolute bottom-1/4 right-1/4 translate-x-1/2 translate-y-1/2 w-96 h-96 rounded-full bg-purple-500/10 blur-[120px] pointer-events-none"></div>

            <Head title="Pendaftaran" />

            <div className="w-full max-w-lg z-10 py-8">
                {/* Brand Logo */}
                <div className="flex flex-col items-center mb-8">
                    <Link href="/" className="flex items-center gap-2 text-slate-400 hover:text-slate-200 text-xs font-semibold mb-4 self-start px-2 py-1 bg-slate-900/60 rounded-lg border border-slate-800/40 transition-colors">
                        <ArrowLeft className="w-4 h-4" /> Kembali ke Masuk
                    </Link>
                    <div className="w-12 h-12 rounded-2xl bg-gradient-to-tr from-indigo-500 to-purple-500 flex items-center justify-center font-bold text-white text-xl shadow-lg mb-3">
                        T
                    </div>
                    <h2 className="text-2xl font-bold bg-gradient-to-r from-slate-100 to-slate-300 bg-clip-text text-transparent">
                        Registrasi Mahasiswa
                    </h2>
                    <p className="text-slate-400 text-sm mt-1">Daftarkan akun mahasiswa Anda di TAMP</p>
                </div>

                {/* Register Card */}
                <div className="bg-slate-900/40 border border-slate-800/60 rounded-3xl p-8 backdrop-blur-xl shadow-2xl">
                    <form onSubmit={handleSubmit} className="space-y-5">
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {/* Name Input */}
                            <div className="space-y-2">
                                <label className="text-xs font-semibold text-slate-300 uppercase tracking-wider">Nama Lengkap</label>
                                <div className="relative">
                                    <User className="absolute left-4 top-1/2 -translate-y-1/2 w-4.5 h-4.5 text-slate-500" />
                                    <input
                                        type="text"
                                        value={data.name}
                                        onChange={e => setData('name', e.target.value)}
                                        placeholder="Nama Lengkap"
                                        className={`w-full bg-slate-950/50 border rounded-2xl py-3 pl-11 pr-4 text-sm text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-2 transition-all ${
                                            errors.name 
                                            ? 'border-rose-500/50 focus:ring-rose-500/20' 
                                            : 'border-slate-800 focus:border-indigo-500 focus:ring-indigo-500/20'
                                        }`}
                                    />
                                </div>
                                {errors.name && (
                                    <p className="text-rose-400 text-xs mt-1 font-medium">{errors.name}</p>
                                )}
                            </div>

                            {/* NPM Input */}
                            <div className="space-y-2">
                                <label className="text-xs font-semibold text-slate-300 uppercase tracking-wider">NPM</label>
                                <div className="relative">
                                    <Hash className="absolute left-4 top-1/2 -translate-y-1/2 w-4.5 h-4.5 text-slate-500" />
                                    <input
                                        type="text"
                                        value={data.npm}
                                        onChange={e => setData('npm', e.target.value)}
                                        placeholder="NPM Anda"
                                        className={`w-full bg-slate-950/50 border rounded-2xl py-3 pl-11 pr-4 text-sm text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-2 transition-all ${
                                            errors.npm 
                                            ? 'border-rose-500/50 focus:ring-rose-500/20' 
                                            : 'border-slate-800 focus:border-indigo-500 focus:ring-indigo-500/20'
                                        }`}
                                    />
                                </div>
                                {errors.npm && (
                                    <p className="text-rose-400 text-xs mt-1 font-medium">{errors.npm}</p>
                                )}
                            </div>
                        </div>

                        {/* Email Input */}
                        <div className="space-y-2">
                            <label className="text-xs font-semibold text-slate-300 uppercase tracking-wider">Email</label>
                            <div className="relative">
                                <Mail className="absolute left-4 top-1/2 -translate-y-1/2 w-4.5 h-4.5 text-slate-500" />
                                <input
                                    type="email"
                                    value={data.email}
                                    onChange={e => setData('email', e.target.value)}
                                    placeholder="nama@student.ukdc.ac.id"
                                    className={`w-full bg-slate-950/50 border rounded-2xl py-3 pl-11 pr-4 text-sm text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-2 transition-all ${
                                        errors.email 
                                        ? 'border-rose-500/50 focus:ring-rose-500/20' 
                                        : 'border-slate-800 focus:border-indigo-500 focus:ring-indigo-500/20'
                                    }`}
                                />
                            </div>
                            {errors.email && (
                                <p className="text-rose-400 text-xs mt-1 font-medium">{errors.email}</p>
                            )}
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {/* Kategori Input */}
                            <div className="space-y-2">
                                <label className="text-xs font-semibold text-slate-300 uppercase tracking-wider">Kategori</label>
                                <select
                                    value={data.kategori}
                                    onChange={e => setData('kategori', e.target.value)}
                                    className="w-full bg-slate-950/50 border border-slate-800 rounded-2xl py-3 px-4 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:border-indigo-500 focus:ring-indigo-500/20 transition-all cursor-pointer"
                                >
                                    <option value="skripsi" className="bg-slate-900">Skripsi</option>
                                    <option value="magang" className="bg-slate-900">Magang</option>
                                </select>
                                {errors.kategori && (
                                    <p className="text-rose-400 text-xs mt-1 font-medium">{errors.kategori}</p>
                                )}
                            </div>

                            {/* Telegram Chat ID */}
                            <div className="space-y-2">
                                <label className="text-xs font-semibold text-slate-300 uppercase tracking-wider">Telegram Chat ID</label>
                                <div className="relative">
                                    <Send className="absolute left-4 top-1/2 -translate-y-1/2 w-4.5 h-4.5 text-slate-500" />
                                    <input
                                        type="text"
                                        value={data.telegram_chat_id}
                                        onChange={e => setData('telegram_chat_id', e.target.value)}
                                        placeholder="Chat ID Telegram"
                                        className={`w-full bg-slate-950/50 border rounded-2xl py-3 pl-11 pr-4 text-sm text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-2 transition-all ${
                                            errors.telegram_chat_id 
                                            ? 'border-rose-500/50 focus:ring-rose-500/20' 
                                            : 'border-slate-800 focus:border-indigo-500 focus:ring-indigo-500/20'
                                        }`}
                                    />
                                </div>
                                {errors.telegram_chat_id && (
                                    <p className="text-rose-400 text-xs mt-1 font-medium">{errors.telegram_chat_id}</p>
                                )}
                            </div>
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {/* Password Input */}
                            <div className="space-y-2">
                                <label className="text-xs font-semibold text-slate-300 uppercase tracking-wider">Password</label>
                                <div className="relative">
                                    <Lock className="absolute left-4 top-1/2 -translate-y-1/2 w-4.5 h-4.5 text-slate-500" />
                                    <input
                                        type="password"
                                        value={data.password}
                                        onChange={e => setData('password', e.target.value)}
                                        placeholder="Min 6 karakter"
                                        className={`w-full bg-slate-950/50 border rounded-2xl py-3 pl-11 pr-4 text-sm text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-2 transition-all ${
                                            errors.password 
                                            ? 'border-rose-500/50 focus:ring-rose-500/20' 
                                            : 'border-slate-800 focus:border-indigo-500 focus:ring-indigo-500/20'
                                        }`}
                                    />
                                </div>
                                {errors.password && (
                                    <p className="text-rose-400 text-xs mt-1 font-medium">{errors.password}</p>
                                )}
                            </div>

                            {/* Password Confirmation */}
                            <div className="space-y-2">
                                <label className="text-xs font-semibold text-slate-300 uppercase tracking-wider">Konfirmasi Password</label>
                                <div className="relative">
                                    <Lock className="absolute left-4 top-1/2 -translate-y-1/2 w-4.5 h-4.5 text-slate-500" />
                                    <input
                                        type="password"
                                        value={data.password_confirmation}
                                        onChange={e => setData('password_confirmation', e.target.value)}
                                        placeholder="Ulangi password"
                                        className="w-full bg-slate-950/50 border border-slate-800 rounded-2xl py-3 pl-11 pr-4 text-sm text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-2 focus:border-indigo-500 focus:ring-indigo-500/20 transition-all"
                                    />
                                </div>
                            </div>
                        </div>

                        {/* Submit Button */}
                        <button
                            type="submit"
                            disabled={processing}
                            className="w-full bg-gradient-to-r from-indigo-500 to-purple-500 hover:from-indigo-600 hover:to-purple-600 text-white font-bold py-3.5 px-6 rounded-2xl flex items-center justify-center gap-2 transition-all shadow-lg hover:shadow-indigo-500/20 disabled:opacity-50 mt-4 cursor-pointer"
                        >
                            {processing ? (
                                <span className="inline-block w-5 h-5 border-2 border-slate-100 border-t-transparent rounded-full animate-spin"></span>
                            ) : (
                                <>
                                    Daftar Sekarang
                                    <ChevronRight className="w-5 h-5" />
                                </>
                            )}
                        </button>
                    </form>

                    {/* Login Link */}
                    <div className="mt-6 text-center border-t border-slate-800/60 pt-5">
                        <p className="text-slate-400 text-sm">
                            Sudah memiliki akun?{' '}
                            <Link href="/" className="text-indigo-400 hover:text-indigo-300 font-semibold transition-all">
                                Masuk ke akun
                            </Link>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    );
}
