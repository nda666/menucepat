# APP ABSENSI IRIS

[![Actions Status](https://github.com/nda666/menucepat/workflows/testing/badge.svg)](https://github.com/nda666/menucepat/actions)

## Getting Started
- copy .env.example dan rename menjadi .env isi DB_DATABASE,DB_USERNAME,DB_PASSWORD sesuai server mu.
    ```console
    cp .env.example .env
    ```
- Generate laravel app key
    ```console
    php artisan key:generate
    ```
- Jalankan composer install
    ```console
    composer install
    ```
- Jalankan migrate
    ```console
    php artisan migrate
    ```
- Jalankan Admin Seeder
    ```console
    php artisan db:seed AdminSeeder
    ```

### Testing
- copy .env.example dan rename menjadi .env.testing
    ```console
    cp .env.example .env.testing
    ```
- Isi DB_DATABASE,DB_USERNAME,DB_PASSWORD pada file .env.testing. **Selalu buat database terpisah untuk testing**. Contoh:
    ```
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=menucepat-testing
    DB_USERNAME=DBUSERNAME
    DB_PASSWORD=DBPASSWORD
    ```
- Untuk menjalankan Testing
    ```console
    vendor/bin/phpunit --testdox
    ```