
# DLabs Laravel

Proyek ini bertujuan untuk membangun aplikasi web yang aman dan skalabel dengan menggunakan Laravel sebagai framework utama, JWT (JSON Web Tokens) untuk autentikasi dan otorisasi, Redis untuk caching dan manajemen session, serta MySQL untuk penyimpanan data.

## Prerequiresite
- IDE / Text Editor
- Composer. (See [installation](https://www.hostinger.co.id/tutorial/cara-install-composer))
- MySQL. (See [installation](https://dev.mysql.com/doc/mysql-installation-excerpt/5.7/en/))
- Redis. (See [installation](https://redis.io/docs/latest/operate/oss_and_stack/install/install-redis/))

## Installation

Sebelum menjalankan projek ini pastikan php yang digunakan minimal versi 8.2. 

Berikut tahap untuk setup projek :
- Clone this repository
```
  git clone https://github.com/marifsulaksono/dlabs-laravel.git
```
- Masuk ke direktori projek
```
cd dlabs-laravel
```
- Instal dependency laravel menggunakan perintah
```
composer install
```
- Copy `.env.example` menjadi `.env` dengan perintah
```
cp .env.example .env
```
- Konfigurasi Database
Sesuaikan konfigurasi database pada file `.env`
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=core_laravel_11_venturo
DB_USERNAME=root
DB_PASSWORD=

REDIS_CLIENT=predis
REDIS_PREFIX=
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

JWT_SECRET=your_jwt_secret
JWT_ALGO=HS256
JWT_SHOW_BLACKLIST_EXCEPTION=true
```
- Generate table menggunakan migration
```
php artisan migrate
```
- Generate data menggunakan seeder
```
php artisan db:seed
```
- Generate token jwt
```
php artisan jwt:secret
```
- Menjalankan projek laravel
```
php artisan serve
```

Jika telah menjalankan seed, kita bisa menggunakan akun dengan credential berikut untuk melakukan login:
```
Email: admin@gmail.com
Password: Admin#1234
```

## Perintah Sebelum Commit

Pastikan untuk menjalankan perintah berikut sebelum melakukan commit agar kode tetap konsisten dengan standar yang ditentukan:
```
vendor/bin/pint
``` 

# Contact
For more information or to report issues, please contact me at:

* [LinkedIn](https://www.linkedin.com/in/marifsulaksono/)
* [Email](mailto:marifsulaksono@gmail.com)