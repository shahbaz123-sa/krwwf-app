# ERP App + Laravel API Auth

This workspace now contains:
- Ionic Vue frontend in `src/`
- Laravel backend in `backend/`
- Token auth with Laravel Sanctum (`/api/auth/*`)

## 1) Backend setup (Laravel)

```powershell
Set-Location "C:\xampp\htdocs\erpApp\backend"
php artisan migrate
php artisan serve
```

API runs at `http://127.0.0.1:8000` by default.

## 2) Frontend setup (Ionic Vue)

Create `.env` in project root from `.env.example` and keep `VITE_API_BASE_URL` aligned with your target.

```powershell
Set-Location "C:\xampp\htdocs\erpApp"
npm run dev
```

## Auth endpoints
- `POST /api/auth/register`
- `POST /api/auth/login`
- `POST /api/auth/logout` (Bearer token required)
- `GET /api/user` (Bearer token required)

## Database
Default backend database is SQLite (`backend/database/database.sqlite`).

To use MySQL/XAMPP instead, update `backend/.env`:
- `DB_CONNECTION=mysql`
- `DB_HOST=127.0.0.1`
- `DB_PORT=3306`
- `DB_DATABASE=<your_db_name>`
- `DB_USERNAME=root`
- `DB_PASSWORD=`

Then run:

```powershell
Set-Location "C:\xampp\htdocs\erpApp\backend"
php artisan migrate
```

