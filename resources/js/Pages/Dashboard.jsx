import React from 'react';
import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { 
    Users, 
    Calendar, 
    FileText, 
    BookOpen,
    ArrowUpRight,
    TrendingUp,
    FolderCheck,
    Clock
} from 'lucide-react';

export default function Dashboard({ stats, recentBimbingan, recentLaporan }) {
    const today = new Date().toLocaleDateString('id-ID', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });

    const getStatusClass = (status) => {
        const s = status?.toLowerCase() || '';
        if (s === 'disetujui' || s === 'active') return 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20';
        if (s === 'revisi' || s === 'ditolak' || s === 'inactive' || s === 'suspended') return 'bg-rose-500/10 text-rose-400 border-rose-500/20';
        if (s === 'review' || s === 'pending') return 'bg-amber-500/10 text-amber-400 border-amber-500/20';
        return 'bg-slate-500/10 text-slate-400 border-slate-500/20';
    };

    return (
        <AuthenticatedLayout title="Dashboard">
            <Head title="Dashboard" />

            <div className="space-y-8">
                {/* Welcome Card */}
                <div className="relative bg-gradient-to-r from-indigo-900/60 to-purple-900/40 border border-indigo-500/15 rounded-3xl p-6 md:p-8 overflow-hidden shadow-xl shadow-indigo-500/5">
                    <div className="absolute -right-10 -top-10 w-40 h-40 bg-indigo-500/10 rounded-full blur-2xl pointer-events-none"></div>
                    <div className="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <span className="text-xs font-semibold uppercase tracking-wider text-indigo-400 bg-indigo-500/10 px-3 py-1 rounded-full border border-indigo-500/20">
                                {today}
                            </span>
                            <h3 className="text-2xl md:text-3xl font-extrabold mt-3 bg-gradient-to-r from-slate-100 to-indigo-200 bg-clip-text text-transparent">
                                Dashboard Akademik TAMP
                            </h3>
                            <p className="text-slate-400 text-sm mt-1.5 max-w-xl">
                                Pantau kemajuan bimbingan skripsi, proposal, magang, dan laporan mingguan mahasiswa dengan mudah dan terintegrasi.
                            </p>
                        </div>
                    </div>
                </div>

                {/* Stats Grid */}
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    {/* Stat Card: Users (For Admin) */}
                    {stats.total_users !== undefined && stats.total_users > 0 && (
                        <div className="bg-slate-900/35 border border-slate-800/60 rounded-2xl p-5 flex items-center justify-between hover:border-slate-700/60 transition-all hover:translate-y-[-2px]">
                            <div>
                                <p className="text-slate-500 text-xs font-semibold uppercase tracking-wider">Total Pengguna</p>
                                <h4 className="text-2xl font-bold mt-1 text-slate-100">{stats.total_users}</h4>
                                <span className="text-[10px] text-slate-400 mt-1 block">Sistem Terintegrasi</span>
                            </div>
                            <div className="w-12 h-12 rounded-xl bg-slate-800/60 flex items-center justify-center text-slate-300 shadow-inner">
                                <Users className="w-6 h-6 text-indigo-400" />
                            </div>
                        </div>
                    )}

                    {/* Stat Card: Bimbingan */}
                    <div className="bg-slate-900/35 border border-slate-800/60 rounded-2xl p-5 flex items-center justify-between hover:border-slate-700/60 transition-all hover:translate-y-[-2px]">
                        <div>
                            <p className="text-slate-500 text-xs font-semibold uppercase tracking-wider">Total Bimbingan</p>
                            <h4 className="text-2xl font-bold mt-1 text-slate-100">{stats.total_bimbingan}</h4>
                            <span className="text-[10px] text-slate-400 mt-1 block">Pertemuan Log</span>
                        </div>
                        <div className="w-12 h-12 rounded-xl bg-slate-800/60 flex items-center justify-center text-slate-300 shadow-inner">
                            <Calendar className="w-6 h-6 text-emerald-400" />
                        </div>
                    </div>

                    {/* Stat Card: Laporan Akademik */}
                    <div className="bg-slate-900/35 border border-slate-800/60 rounded-2xl p-5 flex items-center justify-between hover:border-slate-700/60 transition-all hover:translate-y-[-2px]">
                        <div>
                            <p className="text-slate-500 text-xs font-semibold uppercase tracking-wider">Laporan Akademik</p>
                            <h4 className="text-2xl font-bold mt-1 text-slate-100">{stats.total_laporan}</h4>
                            <span className="text-[10px] text-slate-400 mt-1 block">Skripsi & Magang</span>
                        </div>
                        <div className="w-12 h-12 rounded-xl bg-slate-800/60 flex items-center justify-center text-slate-300 shadow-inner">
                            <FileText className="w-6 h-6 text-amber-400" />
                        </div>
                    </div>

                    {/* Stat Card: Laporan Mingguan */}
                    <div className="bg-slate-900/35 border border-slate-800/60 rounded-2xl p-5 flex items-center justify-between hover:border-slate-700/60 transition-all hover:translate-y-[-2px]">
                        <div>
                            <p className="text-slate-500 text-xs font-semibold uppercase tracking-wider">Laporan Mingguan</p>
                            <h4 className="text-2xl font-bold mt-1 text-slate-100">{stats.total_laporan_mingguan}</h4>
                            <span className="text-[10px] text-slate-400 mt-1 block">Log Mingguan</span>
                        </div>
                        <div className="w-12 h-12 rounded-xl bg-slate-800/60 flex items-center justify-center text-slate-300 shadow-inner">
                            <BookOpen className="w-6 h-6 text-purple-400" />
                        </div>
                    </div>
                </div>

                {/* Tables / Recent Activity section */}
                <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    {/* Recent Bimbingan */}
                    <div className="bg-slate-900/30 border border-slate-800/40 rounded-3xl p-6 backdrop-blur-md shadow-lg flex flex-col h-[400px]">
                        <div className="flex items-center justify-between mb-5">
                            <div className="flex items-center gap-2">
                                <Clock className="w-5 h-5 text-indigo-400" />
                                <h3 className="font-bold text-slate-200">Bimbingan Terbaru</h3>
                            </div>
                            <Link href="/bimbingan" className="text-xs text-indigo-400 hover:text-indigo-300 inline-flex items-center gap-1 font-semibold group">
                                Lihat Semua
                                <ArrowUpRight className="w-4 h-4 transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5" />
                            </Link>
                        </div>
                        
                        <div className="flex-1 overflow-auto space-y-3 pr-2 scrollbar-thin">
                            {recentBimbingan.length === 0 ? (
                                <div className="h-full flex flex-col items-center justify-center text-slate-500">
                                    <Calendar className="w-8 h-8 mb-2 opacity-40" />
                                    <p className="text-sm">Belum ada riwayat bimbingan.</p>
                                </div>
                            ) : (
                                recentBimbingan.map((b) => (
                                    <div key={b.id} className="bg-slate-950/40 border border-slate-800/50 p-4 rounded-xl flex items-start justify-between gap-4 transition-all hover:bg-slate-900/20">
                                        <div className="space-y-1">
                                            <p className="text-xs text-slate-500 font-semibold">{new Date(b.tanggal).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })}</p>
                                            <h4 className="text-sm font-bold text-slate-300 truncate max-w-[200px] sm:max-w-[300px]">{b.topik}</h4>
                                            <p className="text-xs text-slate-400">
                                                Mhs: <span className="text-slate-300 font-medium">{b.user?.name || '-'}</span>
                                            </p>
                                        </div>
                                        <span className={`text-[10px] font-bold px-2 py-1 rounded-md border ${getStatusClass(b.status)}`}>
                                            {b.status}
                                        </span>
                                    </div>
                                ))
                            )}
                        </div>
                    </div>

                    {/* Recent Laporan */}
                    <div className="bg-slate-900/30 border border-slate-800/40 rounded-3xl p-6 backdrop-blur-md shadow-lg flex flex-col h-[400px]">
                        <div className="flex items-center justify-between mb-5">
                            <div className="flex items-center gap-2">
                                <FolderCheck className="w-5 h-5 text-emerald-400" />
                                <h3 className="font-bold text-slate-200">Laporan Akademik Terbaru</h3>
                            </div>
                            <Link href="/laporan" className="text-xs text-indigo-400 hover:text-indigo-300 inline-flex items-center gap-1 font-semibold group">
                                Lihat Semua
                                <ArrowUpRight className="w-4 h-4 transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5" />
                            </Link>
                        </div>
                        
                        <div className="flex-1 overflow-auto space-y-3 pr-2 scrollbar-thin">
                            {recentLaporan.length === 0 ? (
                                <div className="h-full flex flex-col items-center justify-center text-slate-500">
                                    <FileText className="w-8 h-8 mb-2 opacity-40" />
                                    <p className="text-sm">Belum ada riwayat laporan akademik.</p>
                                </div>
                            ) : (
                                recentLaporan.map((l) => (
                                    <div key={l.id} className="bg-slate-950/40 border border-slate-800/50 p-4 rounded-xl flex items-start justify-between gap-4 transition-all hover:bg-slate-900/20">
                                        <div className="space-y-1">
                                            <p className="text-xs text-slate-500 font-semibold capitalize">Kategori: {l.type}</p>
                                            <h4 className="text-sm font-bold text-slate-300 truncate max-w-[200px] sm:max-w-[300px]">{l.judul}</h4>
                                            <p className="text-xs text-slate-400">
                                                Mhs: <span className="text-slate-300 font-medium">{l.mahasiswa?.name || '-'}</span>
                                            </p>
                                        </div>
                                        <span className={`text-[10px] font-bold px-2 py-1 rounded-md border ${getStatusClass(l.status)}`}>
                                            {l.status}
                                        </span>
                                    </div>
                                ))
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
