import { useEffect, useState } from 'react';
import { Sun, Moon } from 'lucide-react';

const THEME_KEY = 'theme';

export default function ThemeSwitcher() {
    // Default: light mode
    const [theme, setTheme] = useState(() => {
        if (typeof window !== 'undefined') {
            return localStorage.getItem(THEME_KEY) || 'light';
        }
        return 'light';
    });

    useEffect(() => {
        const root = document.documentElement;
        if (theme === 'dark') {
            root.classList.add('dark');
        } else {
            root.classList.remove('dark');
        }
        localStorage.setItem(THEME_KEY, theme);
    }, [theme]);

    const toggleTheme = () => {
        setTheme((prev) => (prev === 'dark' ? 'light' : 'dark'));
    };

    const isDark = theme === 'dark';

    return (
        <button
            onClick={toggleTheme}
            aria-label={isDark ? 'Beralih ke Mode Terang' : 'Beralih ke Mode Gelap'}
            title={isDark ? 'Beralih ke Mode Terang' : 'Beralih ke Mode Gelap'}
            className={`
                relative flex items-center gap-2 px-3 py-1.5 rounded-xl text-sm font-medium
                border transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-400
                ${isDark
                    ? 'bg-slate-800 border-slate-700 text-slate-300 hover:bg-slate-700 hover:text-white'
                    : 'bg-slate-100 border-slate-200 text-slate-600 hover:bg-slate-200 hover:text-slate-900'
                }
            `}
        >
            {isDark ? (
                <Moon className="w-4 h-4 text-indigo-400" />
            ) : (
                <Sun className="w-4 h-4 text-amber-500" />
            )}
            <span className="hidden sm:inline">
                {isDark ? 'Dark' : 'Light'}
            </span>
        </button>
    );
}
