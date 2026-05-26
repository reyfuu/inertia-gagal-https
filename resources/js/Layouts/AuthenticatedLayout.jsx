import React, { useState, useEffect } from 'react';
import ThemeSwitcher from '../Components/ThemeSwitcher';
import { Link, usePage, router } from '@inertiajs/react';
import {
    LayoutDashboard,
    BookOpen,
    FileText,
    Calendar,
    Users,
    LogOut,
    Menu,
    X,
    Bell,
    CheckCircle2,
    AlertCircle
} from 'lucide-react';

// [SKILL] Authenticated Layout — full light/dark mode support
export default function AuthenticatedLayout({ children, title }) {
    const { auth, flash } = usePage().props;
    const [sidebarOpen, setSidebarOpen] = useState(false);
    const [notification, setNotification] = useState(null);

    useEffect(() => {
        if (flash?.success) {
            setNotification({ type: 'success', message: flash.success });
            const timer = setTimeout(() => setNotification(null), 5000);
            return () => clearTimeout(timer);
        } else if (flash?.error) {
            setNotification({ type: 'error', message: flash.error });
            const timer = setTimeout(() => setNotification(null), 5000);
            return () => clearTimeout(timer);
        }
    }, [flash]);

    const user = auth.user;
    const role = user?.role || 'Guest';

    const menuItems = [
        { name: 'Dashboard',        icon: LayoutDashboard, href: '/dashboard',        roles: ['super_admin', 'ka_prodi', 'dosen', 'mahasiswa'] },
        { name: 'Bimbingan',        icon: Calendar,        href: '/bimbingan',        roles: ['super_admin', 'ka_prodi', 'dosen', 'mahasiswa'] },
        { name: 'Laporan Akademik', icon: FileText,        href: '/laporan',          roles: ['super_admin', 'ka_prodi', 'dosen', 'mahasiswa'] },
        { name: 'Laporan Mingguan', icon: BookOpen,        href: '/laporan-mingguan', roles: ['super_admin', 'ka_prodi', 'dosen', 'mahasiswa'] },
        { name: 'Daftar User',      icon: Users,           href: '/user',             roles: ['super_admin', 'ka_prodi'] },
    ];

    const currentPath = window.location.pathname;
    const filteredMenu = menuItems.filter(item => item.roles.includes(user?.role));

    const handleLogout = (e) => {
        e.preventDefault();
        router.post('/logout');
    };

    return (
        // [SKILL] Root container — light gray in light, deep navy in dark
        <div className="min-h-screen bg-slate-100 dark:bg-[#0b0f1a] text-slate-800 dark:text-slate-200 flex font-sans">

            {/* ── Notification Toast ───────────────────── */}
            {notification && (
                // [SKILL] Toast — solid tinted bg in light, glass in dark
                <div className={`fixed top-5 right-5 z-50 flex items-center gap-3 px-4 py-3 rounded-xl border shadow-lg transition-all duration-300 ${
                    notification.type === 'success'
                        ? 'bg-emerald-50 border-emerald-200 text-emerald-800 dark:bg-emerald-950/80 dark:border-emerald-500/30 dark:text-emerald-300'
                        : 'bg-rose-50 border-rose-200 text-rose-800 dark:bg-rose-950/80 dark:border-rose-500/30 dark:text-rose-300'
                }`}>
                    {notification.type === 'success'
                        ? <CheckCircle2 className="w-5 h-5 text-emerald-500 shrink-0" />
                        : <AlertCircle  className="w-5 h-5 text-rose-500 shrink-0" />
                    }
                    <span className="text-sm font-medium">{notification.message}</span>
                    <button onClick={() => setNotification(null)} className="ml-2 hover:opacity-70">
                        <X className="w-4 h-4" />
                    </button>
                </div>
            )}

            {/* ── Desktop Sidebar ──────────────────────── */}
            {/* [SKILL] Sidebar — white solid in light, translucent glass in dark */}
            <aside className="hidden md:flex flex-col w-64 shrink-0 p-5 transition-colors duration-200
                              bg-white border-r border-slate-200 shadow-sm
                              dark:bg-slate-900/60 dark:border-slate-800/40 dark:backdrop-blur-xl dark:shadow-none">

                {/* Brand */}
                <div className="flex items-center gap-3 mb-8 px-2">
                    <div className="w-9 h-9 rounded-xl bg-gradient-to-tr from-indigo-500 to-purple-500 flex items-center justify-center font-bold text-white text-sm shadow-md shadow-indigo-500/20 shrink-0">
                        T
                    </div>
                    <div>
                        <h1 className="font-bold text-base leading-none bg-gradient-to-r from-indigo-600 to-purple-600 dark:from-indigo-400 dark:to-purple-400 bg-clip-text text-transparent">
                            TAMP APP
                        </h1>
                        <span className="text-[10px] text-slate-400 dark:text-slate-500 font-semibold tracking-wider uppercase">
                            Inertia Edition
                        </span>
                    </div>
                </div>

                {/* Nav */}
                <nav className="flex-1 space-y-1">
                    {filteredMenu.map((item) => {
                        const isActive = currentPath.startsWith(item.href);
                        return (
                            <Link
                                key={item.name}
                                href={item.href}
                                // [SKILL] Active nav item — indigo tint in both modes
                                className={`flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-150 ${
                                    isActive
                                        ? 'bg-indigo-50 border-l-4 border-indigo-500 text-indigo-700 font-semibold dark:bg-indigo-600/15 dark:text-indigo-300 dark:border-indigo-500'
                                        : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100 dark:text-slate-400 dark:hover:text-slate-200 dark:hover:bg-slate-800/50'
                                }`}
                            >
                                <item.icon className={`w-5 h-5 shrink-0 ${isActive ? 'text-indigo-500' : 'text-slate-400 dark:text-slate-500'}`} />
                                {item.name}
                            </Link>
                        );
                    })}
                </nav>

                {/* User + Logout */}
                <div className="pt-5 border-t border-slate-200 dark:border-slate-800/60 flex flex-col gap-3">
                    <div className="flex items-center gap-3 px-2">
                        <div className="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-400 to-pink-400 flex items-center justify-center font-bold text-white text-sm shadow-sm shrink-0">
                            {user?.name?.substring(0, 2).toUpperCase()}
                        </div>
                        <div className="overflow-hidden">
                            <h4 className="text-sm font-semibold truncate text-slate-800 dark:text-slate-200">{user?.name}</h4>
                            <span className="text-xs text-indigo-600 dark:text-indigo-400 font-medium capitalize">{role.replace('_', ' ')}</span>
                        </div>
                    </div>

                    {/* [SKILL] Logout button — rose tint on hover */}
                    <button
                        onClick={handleLogout}
                        className="flex items-center gap-3 w-full px-4 py-2.5 rounded-xl text-sm font-medium
                                   text-slate-500 dark:text-slate-400
                                   hover:text-rose-600 dark:hover:text-rose-400
                                   hover:bg-rose-50 dark:hover:bg-rose-950/20
                                   border border-transparent hover:border-rose-200 dark:hover:border-rose-900/30
                                   transition-all duration-150"
                    >
                        <LogOut className="w-5 h-5" />
                        Keluar
                    </button>
                </div>
            </aside>

            {/* ── Mobile Sidebar Modal ─────────────────── */}
            {sidebarOpen && (
                <div className="fixed inset-0 z-40 flex md:hidden bg-black/40 dark:bg-black/60 backdrop-blur-sm" onClick={() => setSidebarOpen(false)}>
                    <div className="w-64 bg-white dark:bg-slate-900 p-5 flex flex-col h-full shadow-2xl" onClick={e => e.stopPropagation()}>

                        <div className="flex items-center justify-between mb-8">
                            <h1 className="font-bold text-base bg-gradient-to-r from-indigo-600 to-purple-600 dark:from-indigo-400 dark:to-purple-400 bg-clip-text text-transparent">
                                TAMP APP
                            </h1>
                            <button onClick={() => setSidebarOpen(false)} className="text-slate-400 hover:text-slate-700 dark:hover:text-slate-100 transition-colors">
                                <X className="w-6 h-6" />
                            </button>
                        </div>

                        <nav className="flex-1 space-y-1">
                            {filteredMenu.map((item) => {
                                const isActive = currentPath.startsWith(item.href);
                                return (
                                    <Link
                                        key={item.name}
                                        href={item.href}
                                        onClick={() => setSidebarOpen(false)}
                                        className={`flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-150 ${
                                            isActive
                                                ? 'bg-indigo-50 border-l-4 border-indigo-500 text-indigo-700 font-semibold dark:bg-indigo-600/15 dark:text-indigo-300'
                                                : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100 dark:text-slate-400 dark:hover:text-slate-200 dark:hover:bg-slate-800/50'
                                        }`}
                                    >
                                        <item.icon className={`w-5 h-5 shrink-0 ${isActive ? 'text-indigo-500' : 'text-slate-400'}`} />
                                        {item.name}
                                    </Link>
                                );
                            })}
                        </nav>

                        <div className="pt-5 border-t border-slate-200 dark:border-slate-800 flex flex-col gap-3">
                            <div className="flex items-center gap-3 px-2">
                                <div className="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-400 to-pink-400 flex items-center justify-center font-bold text-white text-sm shrink-0">
                                    {user?.name?.substring(0, 2).toUpperCase()}
                                </div>
                                <div className="overflow-hidden">
                                    <h4 className="text-sm font-semibold truncate text-slate-800 dark:text-slate-200">{user?.name}</h4>
                                    <span className="text-xs text-indigo-600 dark:text-indigo-400 font-medium capitalize">{role.replace('_', ' ')}</span>
                                </div>
                            </div>
                            <button
                                onClick={handleLogout}
                                className="flex items-center gap-3 w-full px-4 py-2.5 rounded-xl text-sm font-medium text-slate-500 hover:text-rose-600 hover:bg-rose-50 dark:hover:text-rose-400 dark:hover:bg-rose-950/20 transition-all"
                            >
                                <LogOut className="w-5 h-5" />
                                Keluar
                            </button>
                        </div>
                    </div>
                </div>
            )}

            {/* ── Main Area ────────────────────────────── */}
            <div className="flex-1 flex flex-col min-w-0">

                {/* Header */}
                {/* [SKILL] Header — white opaque in light, translucent glass in dark */}
                <header className="h-16 border-b border-slate-200 dark:border-slate-800/40
                                   bg-white/85 dark:bg-slate-900/30
                                   backdrop-blur-md
                                   flex items-center justify-between px-6 shrink-0 transition-colors duration-200">
                    <div className="flex items-center gap-4">
                        {/* [SKILL] ThemeSwitcher — JANGAN DIHAPUS */}
                        <ThemeSwitcher />
                        <button
                            onClick={() => setSidebarOpen(true)}
                            className="md:hidden text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 transition-colors"
                        >
                            <Menu className="w-6 h-6" />
                        </button>
                        <h2 className="text-base font-bold text-slate-800 dark:text-slate-100">
                            {title}
                        </h2>
                    </div>

                    <div className="flex items-center gap-4">
                        <button className="relative w-8 h-8 rounded-full flex items-center justify-center
                                           text-slate-400 dark:text-slate-500
                                           hover:bg-slate-100 dark:hover:bg-slate-800
                                           hover:text-slate-700 dark:hover:text-slate-300
                                           transition-colors">
                            <Bell className="w-5 h-5" />
                            <span className="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-indigo-500" />
                        </button>

                        <div className="h-8 w-px bg-slate-200 dark:bg-slate-800" />

                        {/* User chip */}
                        <div className="flex items-center gap-3">
                            <div className="text-right hidden sm:block">
                                <p className="text-xs font-semibold text-slate-700 dark:text-slate-300">{user?.name}</p>
                                <p className="text-[10px] text-slate-400 dark:text-slate-500 font-medium capitalize">{role.replace('_', ' ')}</p>
                            </div>
                            <div className="w-8 h-8 rounded-full bg-indigo-500 flex items-center justify-center font-bold text-white text-xs">
                                {user?.name?.substring(0, 2).toUpperCase()}
                            </div>
                        </div>
                    </div>
                </header>

                {/* Page Content */}
                {/* [SKILL] Main — light gray bg in light, transparent (inherits root) in dark */}
                <main className="flex-1 overflow-auto p-6 md:p-8 bg-slate-100 dark:bg-transparent transition-colors duration-200">
                    {children}
                </main>
            </div>
        </div>
    );
}
