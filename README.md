# Laravel Project

Project Laravel ini adalah aplikasi berbasis Laravel yang siap dijalankan dengan langkah-langkah setup sederhana.

## Cara Setup

Ikuti langkah berikut untuk menjalankan project Laravel ini di komputer lokal kamu:

1. Clone repository ini:
    ```bash
    git clone https://github.com/deriana/pos-laravel.git
    ```

2. Masuk ke folder project:
    ```bash
    cd pos-laravel
    ```

3. Install dependencies menggunakan Composer:
    ```bash
    composer install
    ```

4. Generate application key:
    ```bash
    php artisan key:generate
    ```

5. Salin file konfigurasi `.env.example` menjadi `.env`:
    ```bash
    cp .env.example .env
    ```

6. Buat symbolic link untuk storage agar file upload dapat diakses:
    ```bash
    php artisan storage:link
    ```

7. Jalankan server development Laravel:
    ```bash
    php artisan serve
    ```

8. Buka browser dan akses:
    ```
    http://localhost:8000
    ```

## Requirements

- PHP >= 8.x
- Composer
- Database (MySQL/PostgreSQL/SQLite) sesuai konfigurasi `.env`

## License

Project ini menggunakan lisensi MIT.

---

Terima kasih sudah menggunakan project ini! Jika ada pertanyaan atau masalah, silakan buka issue di repository ini.
