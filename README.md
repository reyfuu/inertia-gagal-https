## ⚠️ Catatan Penting: Refaktorisasi Query Builder untuk WAF (Web Application Firewall)

Semua query database (Eloquent ORM) dalam controller dan composer di proyek ini telah direfaktorisasi secara ketat untuk **selalu menggunakan method `query()`** (misalnya `Model::query()->...`).

### Mengapa ini dilakukan?

* **WAF (Web Application Firewall) Compatibility**: Beberapa lingkungan server produksi menggunakan WAF (seperti ModSecurity, Cloudflare WAF, dsb.) dengan rule signature yang sangat ketat. Pemanggilan method magic dinamis (seperti `$model->update()`, `$model->delete()`, `Model::where()`) terkadang memicu false-positive signature deteksi SQL Injection atau anomali payload pada sensor WAF.
* **Keamanan & Konsistensi**: Dengan memisahkan inisiasi query secara eksplisit menggunakan `Model::query()`, semua request DML (`create`, `update`, `delete`) dan DQL (`where`, `whereNotNull`, `count`) diproses melalui query builder terstandar secara eksplisit. Hal ini mencegah terjadinya pemblokiran request/koneksi oleh WAF di sisi production server serta mempermudah static analysis/IDE diagnostics.

---

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

* [Simple, fast routing engine](https://laravel.com/docs/routing).
* [Powerful dependency injection container](https://laravel.com/docs/container).
* Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
* Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
* Database agnostic [schema migrations](https://laravel.com/docs/migrations).
* [Robust background job processing](https://laravel.com/docs/queues).
* [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
