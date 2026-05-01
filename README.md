# KRWWF Ionic + Laravel App

This repository contains:
- Ionic Vue frontend in `src/`
- Laravel backend API in `backend/`
- Token auth using Laravel Sanctum

## Run Backend

```powershell
Set-Location "C:\xampp\htdocs\krwwf-app\backend"
composer install
npm install
php artisan migrate
php artisan serve --host=127.0.0.1 --port=8000
```

## Run Frontend

```powershell
Set-Location "C:\xampp\htdocs\krwwf-app"
npm install
npm run dev -- --host 127.0.0.1 --port 5173
```

Frontend URL: `http://127.0.0.1:5173`

Backend base API URL: `http://127.0.0.1:8000/api`

## Auth + Profile Features

- Register supports `name`, required mobile number, optional email, password, and optional profile picture.
- Login supports mobile or email mode.
- Phone input uses the same `VueTelInput` behavior across auth/profile forms:
  - default country: Pakistan (`PK`)
  - placeholder: `Type mobile number`
- Profile now has two screens:
  - `/profile` (view-only profile)
  - `/profile/edit` (edit name, mobile, email, password, and picture)

## Profile Picture Storage

- Upload field name: `profile_picture`
- Stored in: `backend/public/user_pictures`
- Database column: `users.profile_picture`
- Saved filename format:
  - `name_phone_timestamp.ext`
  - example: `Ali_923001234567_1772362920.jpg`

## API Endpoints

- `POST /api/auth/register`
- `POST /api/auth/login`
- `POST /api/auth/logout` (auth required)
- `GET /api/user` (auth required)
- `PUT /api/user` (auth required)
- `POST /api/user/picture` (auth required)

