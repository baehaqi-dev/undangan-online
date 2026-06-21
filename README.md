# Undangan Online — Wedding Invitation REST API

REST API untuk platform undangan pernikahan digital, dibangun dengan Laravel 13. Mendukung manajemen undangan, tamu, RSVP, notifikasi WhatsApp otomatis, dan caching untuk performa tinggi.

## ✨ Fitur

- **Autentikasi** — Register, login, logout menggunakan Laravel Sanctum (token-based)
- **CRUD Undangan** — Kelola data pernikahan (mempelai, tanggal, lokasi, dll)
- **CRUD Tamu & RSVP** — Generate link RSVP unik per tamu, submit kehadiran tanpa login
- **Notifikasi WhatsApp** — Dikirim otomatis lewat background queue (Redis)
- **Caching** — Redis caching pada endpoint publik dengan cache invalidation otomatis
- **Rate Limiting** — Proteksi anti-spam pada endpoint RSVP publik
- **Statistik & Export** — Dashboard ringkasan RSVP, export data tamu ke CSV
- **Authorization** — Policy-based, memastikan user hanya bisa kelola undangan miliknya sendiri

## 🛠️ Tech Stack

- **Framework:** Laravel 13
- **Database:** MySQL
- **Cache & Queue:** Redis
- **Auth:** Laravel Sanctum
- **Testing:** Pest PHP

## 📋 Prasyarat

- PHP >= 8.2
- Composer
- MySQL
- Redis

## 🚀 Instalasi

```bash
# Clone repository
git clone https://github.com/USERNAME/undangan-online.git
cd undangan-online

# Install dependencies
composer install

# Setup environment
cp .env.example .env
php artisan key:generate

# Konfigurasi database di .env
DB_DATABASE=undangan_online
DB_USERNAME=root
DB_PASSWORD=

# Konfigurasi queue & cache
QUEUE_CONNECTION=redis
CACHE_STORE=redis

# Jalankan migration
php artisan migrate

# Jalankan server (termasuk queue worker)
composer run dev
```

## 🧪 Testing

```bash
php artisan test
```

20 test mencakup autentikasi, CRUD undangan dengan otorisasi, dan flow RSVP publik.

## 📁 Struktur Database

```
users
  └─< invitations
        ├─< galleries
        ├─< guests
        │     └─< rsvps
        └─< rsvps
```

## 📡 API Documentation

Koleksi Postman tersedia di [`postman_collection.json`](./postman_collection.json) — import ke Postman untuk mencoba seluruh endpoint.

### Endpoint Utama

| Method | Endpoint | Deskripsi | Auth |
|--------|----------|-----------|------|
| POST | `/api/v1/register` | Registrasi user baru | - |
| POST | `/api/v1/login` | Login | - |
| POST | `/api/v1/logout` | Logout | ✅ |
| GET\|POST\|PUT\|DELETE | `/api/v1/invitations` | CRUD undangan | ✅ |
| GET\|POST | `/api/v1/invitations/{id}/guests` | CRUD tamu | ✅ |
| GET | `/api/v1/invitations/{id}/statistics` | Statistik RSVP | ✅ |
| GET | `/api/v1/invitations/{id}/export-guests` | Export CSV | ✅ |
| GET | `/api/v1/rsvp/{token}` | Lihat undangan via token | - |
| POST | `/api/v1/rsvp/{token}` | Submit RSVP | - |

## 🏗️ Prinsip Desain

Project ini menerapkan beberapa prinsip software engineering:

- **SOLID** — Separation of Concerns lewat Form Request, Resource, Policy, dan Controller yang masing-masing punya tanggung jawab tunggal
- **DRY** — Logic validasi terpusat di Form Request, format response terpusat di API Resource
- **Fat Model, Skinny Controller** — Controller fokus sebagai penghubung request-response

## 👤 Author

Baehaqi — [LinkedIn](#) | [GitHub](#)