<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Menjalankan dengan Docker

Project ini menyediakan stack development dan production berbasis PHP 8.4,
MySQL 8.4, Node 22, dan Nginx.

### Development

Pastikan `.env` tersedia. Compose akan mengoverride koneksi database agar memakai
service MySQL bernama `db`, jadi `DB_HOST=127.0.0.1` di `.env` lokal tidak perlu
diubah.

```bash
cp .env.example .env # lewati bila .env sudah ada
docker compose up --build
```

Untuk startup berikutnya cukup jalankan `docker compose up -d`.

Aplikasi tersedia di <http://localhost:8000> dan Vite HMR di port `5173`.
Adminer tersedia di <http://localhost:8081>.
Migration dijalankan otomatis setelah MySQL sehat. Data contoh tetap eksplisit:

```bash
docker compose exec app php artisan db:seed
```

Perintah operasional yang umum:

```bash
docker compose exec app php artisan test
docker compose exec app php artisan migrate:status
docker compose logs -f app vite db
docker compose down
```

Database disimpan dalam named volume. `docker compose down -v` turut menghapus
database dan dependency volume, jadi gunakan hanya bila memang ingin reset total.
Port MySQL tidak dipublikasikan ke host agar tidak berbenturan dengan instalasi
lokal. Gunakan `docker compose exec db mysql -uocular -p db_ocular` untuk membuka
client MySQL di dalam stack.

Login Adminer development:

- System: `MySQL`
- Server: `db`
- Username: `ocular`
- Password: `ocular`
- Database: `db_ocular`
Credential database development Docker dapat dioverride lewat variabel
`DOCKER_DB_DATABASE`, `DOCKER_DB_USERNAME`, `DOCKER_DB_PASSWORD`, dan
`DOCKER_DB_ROOT_PASSWORD` tanpa mengubah konfigurasi database host di `.env`.

### Production

Buat environment production dan isi secret yang kuat. Samakan nilai `MYSQL_*`
dengan pasangan `DB_*` di file tersebut. `APP_KEY` dapat dibuat
dengan `docker compose exec app php artisan key:generate --show` dari development
stack, lalu salin hasilnya ke file production.

```bash
cp .env.production.example .env.production
chmod 600 .env.production
docker compose -f compose.prod.yaml up -d --build
```

Nginx tersedia secara default hanya di `127.0.0.1:8080`, untuk diteruskan oleh
reverse proxy yang menangani domain dan TLS. Migration berjalan otomatis dan
seeder tidak pernah dijalankan saat startup.

```bash
docker compose -f compose.prod.yaml ps
docker compose -f compose.prod.yaml logs -f app web db migrate
docker compose -f compose.prod.yaml exec app php artisan about
```

Jangan commit `.env` atau `.env.production`. Queue memakai mode `sync`; tambahkan
worker terpisah ketika aplikasi mulai mengirim job asynchronous.

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

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
