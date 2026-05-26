# TAMP APP — Inertia Edition

Aplikasi manajemen bimbingan akademik mahasiswa berbasis **Laravel + Inertia.js + React**.

---

## Tech Stack

| Layer      | Teknologi                          |
|------------|------------------------------------|
| Backend    | Laravel 13, PHP 8.3                |
| Frontend   | React 19, Inertia.js 2             |
| Styling    | Tailwind CSS 4, Lucide React       |
| Build Tool | Vite 8                             |
| Database   | SQLite / MySQL                     |

---

## Menjalankan Project

```bash
# Install semua dependency
composer install
npm install

# Setup env
cp .env.example .env
php artisan key:generate
php artisan migrate

# Development (jalankan semua sekaligus)
npm run dev:full
```

Buka browser di: **http://localhost:5174**

---

## NPM Packages

### Dependencies
| Package | Versi | Keterangan |
|---------|-------|------------|
| `@inertiajs/react` | ^3.2.0 | Adapter Inertia untuk React |
| `@vitejs/plugin-react` | ^6.0.2 | Plugin React untuk Vite |
| `lucide-react` | ^1.16.0 | Icon library |
| `react` | ^19.2.6 | UI library |
| `react-dom` | ^19.2.6 | React DOM renderer |

### Dev Dependencies
| Package | Versi | Keterangan |
|---------|-------|------------|
| `@tailwindcss/vite` | ^4.0.0 | Plugin Tailwind CSS untuk Vite |
| `concurrently` | ^9.0.1 | Jalankan beberapa perintah sekaligus |
| `laravel-vite-plugin` | ^3.1 | Plugin Vite untuk Laravel |
| `tailwindcss` | ^4.0.0 | Utility-first CSS framework |
| `vite` | ^8.0.0 | Build tool & dev server |

---

## Composer Packages

### Require (Production)
| Package | Versi | Keterangan |
|---------|-------|------------|
| `php` | ^8.3 | PHP runtime |
| `inertiajs/inertia-laravel` | ^2.0 | Adapter Inertia untuk Laravel |
| `laravel/framework` | ^13.7 | Laravel framework |
| `laravel/tinker` | ^3.0 | REPL interaktif untuk Laravel |
| `tightenco/ziggy` | ^2.6 | Named routes Laravel di JavaScript |

### Require-dev (Development)
| Package | Versi | Keterangan |
|---------|-------|------------|
| `barryvdh/laravel-debugbar` | ^4.2 | Debug toolbar untuk development |
| `barryvdh/laravel-ide-helper` | ^3.7 | Helper IDE (autocomplete, type hints) |
| `fakerphp/faker` | ^1.23 | Generator data palsu untuk seeder |
| `laravel/pail` | ^1.2.5 | Log viewer real-time |
| `laravel/pint` | ^1.27 | PHP code style fixer |
| `mockery/mockery` | ^1.6 | Mocking library untuk testing |
| `nunomaduro/collision` | ^8.6 | Error reporting yang lebih informatif |
| `phpunit/phpunit` | ^12.5.12 | Framework testing PHP |

---

## Catatan Teknis

### WAF Compatibility
Semua query database menggunakan `Model::query()->...` secara eksplisit untuk menghindari false-positive pada WAF (Web Application Firewall) seperti ModSecurity atau Cloudflare WAF di environment production.

### Dark / Light Mode
Theme toggle tersedia di header. Preferensi disimpan di `localStorage`. Default: **light mode**.

---

## License

MIT
