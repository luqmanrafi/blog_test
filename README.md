# Blog API

Blog API dibuat dengan tujuan untuk menyelesaikan tugas technical test.

## Fitur
* Manajemen User (Registrasi, Login)
* Otentikasi dan Otorisasi menggunakan JWT via `php-open-source-saver/jwt-auth`
* CRUD untuk Artikel (Dengan pencarian dan pagination)
* CRUD untuk Kategori (Authorization hanya untuk Admin)
* Sistem Role User (Admin, Writer, User)

## Requirement
Berikut adalah requirement dari aplikasi ini [here](requirement) 

## Langkah-Langkah Instalasi & Menjalankan Aplikasi

Berikut adalah panduan untuk menjalankan aplikasi ini di environment lokal:

**1. Clone Repository:**

   ```bash
   git clone https://github.com/luqmanrafi/blog_test.git
   cd blog_test
   ```
**2. Install dependencies:**
```bash
composer install
```

**3. Copy file .env**
```bash
cp .env.example .env
```

**4. Setup .env:**
```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT= [PORT_DATABASE]
DB_DATABASE= [NAMA_DATABASE]
DB_USERNAME= [USERNAME_DATABASE]
DB_PASSWORD= [PASSWORD_DATABASE]
```

**5. Generate Application key:**
```bash
php artisan key:generate
```

**6. Generate JWT secret**
```bash
php artisan jwt:secret
```

**7. Migrate & Seeding Database**
```bash
php artisan migrate:fresh --seed
```

**8. Jalankan server**
```bash
php artisan serve
```

## Link video
Link video demonstrasi [here](https://drive.google.com/file/d/1iDTNlpIwt318Rbrtw_aRJEjpLRROa5Rj/view?usp=sharing)
## Link RESTful API documentation
https://documenter.getpostman.com/view/34883889/2sB2qgedy2
