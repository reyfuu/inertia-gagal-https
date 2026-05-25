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
    User as UserIcon,
    Bell,
    CheckCircle2,
    AlertCircle
} from 'lucide-react';

export default function AuthenticatedLayout({ children, title }) {
    const { auth, flash } = usePage().props;
    const [sidebarOpen, setSidebarOpen] = useState(false);
    const [notification, setNotification] = useState(null);

    // Watch flash messages
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
        { name: 'Dashboard', icon: LayoutDashboard, href: '/dashboard', roles: ['super_admin', 'ka_prodi', 'dosen', 'mahasiswa'] },
        { name: 'Bimbingan', icon: Calendar, href: '/bimbingan', roles: ['super_admin', 'ka_prodi', 'dosen', 'mahasiswa'] },
        { name: 'Laporan Akademik', icon: FileText, href: '/laporan', roles: ['super_admin', 'ka_prodi', 'dosen', 'mahasiswa'] },
        { name: 'Laporan Mingguan', icon: BookOpen, href: '/laporan-mingguan', roles: ['super_admin', 'ka_prodi', 'dosen', 'mahasiswa'] },
        { name: 'Daftar User', icon: Users, href: '/user', roles: ['super_admin', 'ka_prodi'] },
    ];

    const currentPath = window.location.pathname;

    const filteredMenu = menuItems.filter(item => item.roles.includes(user?.role));

    const handleLogout = (e) => {
        e.preventDefault();
        router.post('/logout');
    };

    return (
        <div className="min-h-screen bg-white text-gray-900 dark:bg-slate-950 dark:text-slate-100 flex font-sans transition-colors duration-300">
            {/* Notification Toast */}
            {notification && (
                <div className={`fixed top-5 right-5 z-50 flex items-center gap-3 px-4 py-3 rounded-xl border backdrop-blur-xl shadow-lg transition-all duration-300 transform translate-y-0 scale-100 animate-bounce ${
                    notification.type === 'success' 
                    ? 'bg-emerald-950/80 border-emerald-500/35 text-emerald-300' 
                    : 'bg-rose-950/80 border-rose-500/35 text-rose-300'
                }`}>
                    {notification.type === 'success' ? (
                        <CheckCircle2 className="w-5 h-5 text-emerald-400 shrink-0" />
                    ) : (
                        <AlertCircle className="w-5 h-5 text-rose-400 shrink-0" />
                    )}
                    <span className="text-sm font-medium">{notification.message}</span>
                    <button onClick={() => setNotification(null)} className="ml-2 hover:opacity-75">
                        <X className="w-4 h-4" />
                    </button>
                </div>
            )}

            {/* Sidebar for Desktop */}
            <aside className="hidden md:flex flex-col w-64 border-r border-gray-200 dark:border-slate-800/40 bg-gray-50 dark:bg-slate-900/60 backdrop-blur-xl shrink-0 p-5 transition-colors duration-300">
                <div className="flex items-center gap-3 mb-8 px-2">
                    {/* Logo asset example: switch image based on theme */}
                    <img
                        src="/storage/logo-light.png"
                        alt="Logo"
                        className="w-9 h-9 rounded-lg block dark:hidden"
                    />
                    <img
                        src="/storage/logo-dark.png"
                        alt="Logo"
                        className="w-9 h-9 rounded-lg hidden dark:block"
                    />
                    <div>
                        <h1 className="font-bold text-lg leading-none bg-gradient-to-r from-indigo-400 to-purple-400 bg-clip-text text-transparent dark:bg-gradient-to-r dark:from-yellow-300 dark:to-pink-400">
                            TAMP APP
                        </h1>
                        <span className="text-[10px] text-slate-400 dark:text-yellow-300 font-semibold tracking-wider uppercase">Inertia Edition</span>
                    </div>
                </div>

                <nav className="flex-1 space-y-1">
                    {filteredMenu.map((item) => {
                        const isActive = currentPath.startsWith(item.href);
                        return (
                            <Link
                                key={item.name}
                                href={item.href}
                                className={`flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 ${
                                    isActive
                                        ? 'bg-indigo-100 dark:bg-indigo-600/20 border-l-4 border-indigo-500 text-indigo-700 dark:text-indigo-200 font-semibold'
                                        : 'text-gray-500 dark:text-slate-400 hover:text-gray-900 dark:hover:text-slate-200 hover:bg-gray-100 dark:hover:bg-slate-800/50'
                                }`}
                            >
                                <item.icon className={`w-5 h-5 ${isActive ? 'text-indigo-400' : 'text-slate-400'}`} />
                                {item.name}
                            </Link>
                        );
                    })}
                </nav>

                <div className="pt-5 border-t border-gray-200 dark:border-slate-800/60 flex flex-col gap-4">
                    <div className="flex items-center gap-3 px-2">
                        <div className="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-400 to-pink-400 dark:from-indigo-500 dark:to-pink-500 flex items-center justify-center font-bold text-white shadow-md">
                            {user?.name?.substring(0, 2).toUpperCase()}
                        </div>
                        <div className="overflow-hidden">
                            <h4 className="text-sm font-semibold truncate text-gray-900 dark:text-slate-200">{user?.name}</h4>
                            <span className="text-xs text-indigo-600 dark:text-indigo-400 font-medium capitalize">{role.replace('_', ' ')}</span>
                        </div>
                    </div>
                    
                    <button
                        onClick={handleLogout}
                        className="flex items-center gap-3 w-full px-4 py-3 rounded-xl text-sm font-medium text-rose-600 dark:text-rose-400 hover:text-rose-800 dark:hover:text-rose-200 hover:bg-rose-100 dark:hover:bg-rose-950/20 transition-all duration-200 border border-transparent hover:border-rose-200 dark:hover:border-rose-900/30"
                    >
                        <LogOut className="w-5 h-5" />
                        Keluar
                    </button>
                </div>
            </aside>

            {/* Mobile Sidebar Modal */}
            {sidebarOpen && (
                <div className="fixed inset-0 z-40 flex md:hidden bg-black/60 backdrop-blur-sm">
                    <div className="w-64 bg-slate-900 p-5 flex flex-col h-full animate-slide-in">
                        <div className="flex items-center justify-between mb-8">
                            <div className="flex items-center gap-3">
                                <div className="w-9 h-9 rounded-lg bg-gradient-to-tr from-indigo-500 to-purple-500 flex items-center justify-center font-bold text-white shadow-lg">
                                    T
                                </div>
                                <div>
                                    <h1 className="font-bold text-lg bg-gradient-to-r from-indigo-400 to-purple-400 bg-clip-text text-transparent">TAMP</h1>
                                </div>
                            </div>
                            <button onClick={() => setSidebarOpen(false)} className="text-slate-400 hover:text-slate-100">
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
                                        className={`flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 ${
                                            isActive
                                                ? 'bg-indigo-600/20 border-l-4 border-indigo-500 text-indigo-200 font-semibold'
                                                : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50'
                                        }`}
                                    >
                                        <item.icon className="w-5 h-5" />
                                        {item.name}
                                    </Link>
                                );
                            })}
                        </nav>

                        <div className="pt-5 border-t border-slate-800 flex flex-col gap-4">
                            <div className="flex items-center gap-3 px-2">
                                <div className="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-pink-500 flex items-center justify-center font-bold text-white">
                                    {user?.name?.substring(0, 2).toUpperCase()}
                                </div>
                                <div>
                                    <h4 className="text-sm font-semibold truncate text-slate-200">{user?.name}</h4>
                                    <span className="text-xs text-indigo-400 font-medium capitalize">{role.replace('_', ' ')}</span>
                                </div>
                            </div>
                            <button
                                onClick={handleLogout}
                                className="flex items-center gap-3 w-full px-4 py-3 rounded-xl text-sm font-medium text-rose-400 hover:text-rose-200 hover:bg-rose-950/20 transition-all duration-200"
                            >
                                <LogOut className="w-5 h-5" />
                                Keluar
                            </button>
                        </div>
                    </div>
                </div>
            )}

            {/* Main Area */}
            <div className="flex-1 flex flex-col min-w-0">
                {/* Header */}
                <header className="h-16 border-b border-gray-200 dark:border-slate-800/40 bg-white/80 dark:bg-slate-900/30 backdrop-blur-md flex items-center justify-between px-6 shrink-0 transition-colors duration-300">
                    <div className="flex items-center gap-4">
                        <ThemeSwitcher />
                        <button onClick={() => setSidebarOpen(true)} className="md:hidden text-slate-400 hover:text-slate-100">
                            <Menu className="w-6 h-6" />
                        </button>
                        <h2 className="text-lg font-bold bg-gradient-to-r from-gray-900 to-gray-500 dark:from-slate-100 dark:to-slate-300 bg-clip-text text-transparent">
                            {title}
                        </h2>
                    </div>

                    <div className="flex items-center gap-4">
                        <button className="relative w-8 h-8 rounded-full hover:bg-slate-800 flex items-center justify-center text-slate-400 hover:text-slate-200 transition-colors">
                            <Bell className="w-5 h-5" />
                            <span className="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-indigo-500"></span>
                        </button>
                        <div className="h-8 w-[1px] bg-gray-200 dark:bg-slate-800"></div>
                        <div className="flex items-center gap-3">
                            <div className="text-right hidden sm:block">
                                <p className="text-xs font-semibold text-gray-900 dark:text-slate-300">{user?.name}</p>
                                <p className="text-[10px] text-gray-500 dark:text-slate-500 font-medium capitalize">{role.replace('_', ' ')}</p>
                            </div>
                            <div className="w-8 h-8 rounded-full bg-indigo-400 dark:bg-indigo-600 flex items-center justify-center font-bold text-white text-xs">
                                {user?.name?.substring(0, 2).toUpperCase()}
                            </div>
                        </div>
                    </div>
                </header>

                {/* Page Content */}
                <main className="flex-1 overflow-auto p-6 md:p-8 bg-white dark:bg-slate-950 transition-colors duration-300">
                    {children}
                </main>
            </div>
        </div>
    );
}
