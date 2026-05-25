import React from 'react';
import { Head, useForm, Link } from '@inertiajs/react';
import { Mail, Lock, LogIn, ArrowRight } from 'lucide-react';

export default function Login() {
    const { data, setData, post, processing, errors } = useForm({
        email: '',
        password: '',
        remember: false,
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/login');
    };

    return (
        <div className="min-h-screen bg-slate-950 text-slate-100 flex items-center justify-center relative p-6 overflow-hidden font-sans">
            {/* Ambient Background Glows */}
            <div className="absolute top-1/4 left-1/4 -translate-x-1/2 -translate-y-1/2 w-96 h-96 rounded-full bg-indigo-500/10 blur-[120px] pointer-events-none"></div>
            <div className="absolute bottom-1/4 right-1/4 translate-x-1/2 translate-y-1/2 w-96 h-96 rounded-full bg-purple-500/10 blur-[120px] pointer-events-none"></div>

            <Head title="Masuk" />

            <div className="w-full max-w-md z-10">
                {/* Brand Logo */}
                <div className="flex flex-col items-center mb-8">
                    <div className="w-12 h-12 rounded-2xl bg-gradient-to-tr from-indigo-500 to-purple-500 flex items-center justify-center font-bold text-white text-xl shadow-lg shadow-indigo-500/20 mb-3">
                        T
                    </div>
                    <h2 className="text-2xl font-bold bg-gradient-to-r from-slate-100 to-slate-300 bg-clip-text text-transparent">
                        Selamat Datang di TAMP
                    </h2>
                    <p className="text-slate-400 text-sm mt-1">Silakan masuk ke akun Anda</p>
                </div>

                {/* Login Card */}
                <div className="bg-slate-900/40 border border-slate-800/60 rounded-3xl p-8 backdrop-blur-xl shadow-2xl">
                    <form onSubmit={handleSubmit} className="space-y-6">
                        {/* Email Input */}
                        <div className="space-y-2">
                            <label className="text-xs font-semibold text-slate-300 uppercase tracking-wider">Email</label>
                            <div className="relative">
                                <Mail className="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-500" />
                                <input
                                    type="email"
                                    value={data.email}
                                    onChange={e => setData('email', e.target.value)}
                                    placeholder="nama@email.com"
                                    className={`w-full bg-slate-950/50 border rounded-2xl py-3.5 pl-12 pr-4 text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-2 transition-all ${
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

                        {/* Password Input */}
                        <div className="space-y-2">
                            <div className="flex justify-between items-center">
                                <label className="text-xs font-semibold text-slate-300 uppercase tracking-wider">Password</label>
                            </div>
                            <div className="relative">
                                <Lock className="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-500" />
                                <input
                                    type="password"
                                    value={data.password}
                                    onChange={e => setData('password', e.target.value)}
                                    placeholder="••••••••"
                                    className={`w-full bg-slate-950/50 border rounded-2xl py-3.5 pl-12 pr-4 text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-2 transition-all ${
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

                        {/* Remember Me checkbox */}
                        <div className="flex items-center">
                            <input
                                id="remember_me"
                                type="checkbox"
                                checked={data.remember}
                                onChange={e => setData('remember', e.target.checked)}
                                className="w-4.5 h-4.5 rounded border-slate-800 text-indigo-600 bg-slate-950/50 focus:ring-indigo-500/20 bg-slate-950"
                            />
                            <label htmlFor="remember_me" className="ml-2 text-xs font-medium text-slate-400 cursor-pointer">
                                Ingat saya
                            </label>
                        </div>

                        {/* Submit Button */}
                        <button
                            type="submit"
                            disabled={processing}
                            className="w-full bg-gradient-to-r from-indigo-500 to-purple-500 hover:from-indigo-600 hover:to-purple-600 text-white font-bold py-3.5 px-6 rounded-2xl flex items-center justify-center gap-2 transition-all shadow-lg shadow-indigo-500/10 hover:shadow-indigo-500/20 disabled:opacity-50 cursor-pointer"
                        >
                            {processing ? (
                                <span className="inline-block w-5 h-5 border-2 border-slate-100 border-t-transparent rounded-full animate-spin"></span>
                            ) : (
                                <>
                                    <LogIn className="w-5 h-5" />
                                    Masuk Ke Akun
                                </>
                            )}
                        </button>
                    </form>

                    {/* Register Link */}
                    <div className="mt-8 text-center border-t border-slate-800/60 pt-6">
                        <p className="text-slate-400 text-sm">
                            Belum punya akun?{' '}
                            <Link
                                href="/register"
                                className="text-indigo-400 hover:text-indigo-300 font-semibold inline-flex items-center gap-1 group transition-all"
                            >
                                Daftar Sekarang
                                <ArrowRight className="w-4 h-4 transition-transform group-hover:translate-x-1" />
                            </Link>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    );
}
