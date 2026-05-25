import { router } from '@inertiajs/react';
import { ChevronLeft, ChevronRight } from 'lucide-react';

function stripHtml(label) {
    return String(label).replace(/<[^>]*>/g, '').trim();
}

function isEllipsis(label) {
    const text = stripHtml(label);
    return text === '...' || text.includes('…');
}

export default function Pagination({ paginator, itemLabel = 'data' }) {
    if (!paginator?.links || paginator.links.length <= 3) {
        return null;
    }

    const links = paginator.links;
    const lastIndex = links.length - 1;

    const visit = (url) => {
        if (url) {
            router.get(url, {}, { preserveState: true, preserveScroll: true });
        }
    };

    const btnBase =
        'inline-flex items-center justify-center rounded-lg border text-xs font-semibold transition-all cursor-pointer disabled:opacity-30 disabled:cursor-not-allowed';
    const btnNav =
        `${btnBase} h-9 w-9 border-slate-800/80 bg-slate-900/40 text-slate-400 hover:bg-slate-800 hover:text-slate-200`;
    const btnPage = (active) =>
        `${btnBase} h-9 min-w-9 px-2 ${
            active
                ? 'bg-indigo-600 border-indigo-500 text-white shadow-md'
                : 'border-slate-800/80 bg-slate-900/40 text-slate-400 hover:bg-slate-800'
        }`;

    return (
        <div className="border-t border-slate-800/50 px-6 py-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <span className="text-xs text-slate-500 shrink-0">
                Menampilkan {paginator.from || 0} sampai {paginator.to || 0} dari {paginator.total} {itemLabel}
            </span>

            <nav className="flex items-center justify-end gap-1.5" aria-label="Navigasi halaman">
                {links.map((link, idx) => {
                    if (idx === 0) {
                        return (
                            <button
                                key={idx}
                                type="button"
                                onClick={() => visit(link.url)}
                                disabled={!link.url}
                                className={btnNav}
                                title="Sebelumnya"
                                aria-label="Halaman sebelumnya"
                            >
                                <ChevronLeft className="w-4 h-4" />
                            </button>
                        );
                    }

                    if (idx === lastIndex) {
                        return (
                            <button
                                key={idx}
                                type="button"
                                onClick={() => visit(link.url)}
                                disabled={!link.url}
                                className={btnNav}
                                title="Selanjutnya"
                                aria-label="Halaman selanjutnya"
                            >
                                <ChevronRight className="w-4 h-4" />
                            </button>
                        );
                    }

                    if (isEllipsis(link.label)) {
                        return (
                            <span
                                key={idx}
                                className="inline-flex h-9 min-w-9 items-center justify-center text-xs text-slate-500"
                            >
                                …
                            </span>
                        );
                    }

                    const pageLabel = stripHtml(link.label);

                    return (
                        <button
                            key={idx}
                            type="button"
                            onClick={() => visit(link.url)}
                            disabled={!link.url}
                            className={btnPage(link.active)}
                            aria-label={`Halaman ${pageLabel}`}
                            aria-current={link.active ? 'page' : undefined}
                        >
                            {pageLabel}
                        </button>
                    );
                })}
            </nav>
        </div>
    );
}
