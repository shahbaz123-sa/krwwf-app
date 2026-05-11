# AGENTS.md

This document provides essential information for AI agents working on this codebase.

## Architecture

This is a monolithic repository containing two main parts: a frontend application and a backend API.

-   **Frontend**: An Ionic Vue application located in the `src/` directory. It's a Single Page Application (SPA) that handles the user interface and user interactions.
-   **Backend**: A Laravel PHP application located in the `backend/` directory. It serves as a RESTful API that the frontend consumes for data and authentication.

### Frontend (`src/`)

-   **Framework**: [Ionic](https://ionicframework.com/) with [Vue.js](https://vuejs.org/).
-   **UI Components**: Reusable UI components are in `src/components/`.
-   **Pages/Views**: Application pages are located in `src/views/`.
-   **Routing**: Client-side routing is managed by `vue-router` in `src/router/index.ts`.
-   **State Management & Composables**: Reusable logic and state management are found in `src/composables/`. For example, `useTheme.ts` manages the application's theme.
-   **API Services**: Code for making API requests to the backend is located in `src/services/`.
-   **API Base URL Handling**: The frontend uses `src/services/api.ts` to resolve the API base URL. It supports:
    - `.env` variables (`VITE_API_BASE_URL`, `VITE_API_BASE_URL_ANDROID`) for environment-specific overrides
    - Defaults to `http://localhost/krwwf-app/backend/public/api` for web/XAMPP
    - Uses `10.0.2.2` for Android emulators, and requires LAN IP for real devices
    - See comments in `src/services/api.ts` for details
-   **Profile Editing**: The profile edit UI (`src/views/EditProfilePage.vue`) is a multi-step form supporting extended user fields (profession, company, experience, skills, role in community, blood group, interests, short bio, LinkedIn, etc.).

### Backend (`backend/`)

-   **Framework**: [Laravel](https://laravel.com/).
-   **API Routes**: All API endpoints are defined in `backend/routes/api.php`.
-   **Controllers**: Business logic for handling API requests resides in `backend/app/Http/Controllers/`.
-   **Models**: Database models (Eloquent ORM) are in `backend/app/Models/`. For example, the `User` model is in `backend/app/Models/User.php`.
-   **Database Migrations**: Database schema is managed through migrations in `backend/database/migrations/`.
-   **Asset Build**: Uses Vite for asset building (`vite.config.js`).

## Developer Workflow

### Running the Application

To run the full application, you need to start both the frontend and backend servers.

**1. Run Backend Server:**

```powershell
Set-Location "C:\xampp\htdocs\krwwf-app\backend"
composer install
npm install
php artisan migrate
php artisan serve --host=127.0.0.1 --port=8000
```

**2. Run Frontend Server:**

```powershell
Set-Location "C:\xampp\htdocs\krwwf-app"
npm install
npm run dev -- --host 127.0.0.1 --port 5173
```

-   Frontend is accessible at: `http://127.0.0.1:5173`
-   Backend API base URL is: `http://127.0.0.1:8000/api` (see also API Base URL Handling above for mobile/Android specifics)
-   If frontend requests should go to `php artisan serve`, set `VITE_API_BASE_URL=http://127.0.0.1:8000/api`; otherwise frontend defaults to the XAMPP path in `src/services/api.ts`.

### Testing

-   **Backend**: PHPUnit tests are located in `backend/tests/`. Run them with `php artisan test` from the `backend` directory.
-   **Frontend**: E2E tests are in `tests/e2e/` and unit tests are in `tests/unit/`.
-   Frontend test commands (run from repo root): `npm run test:unit` and `npm run test:e2e`.

## Conventions and Patterns

### Environment Variables

-   The frontend supports `.env` files for configuring API endpoints. Use `VITE_API_BASE_URL` for web/dev and `VITE_API_BASE_URL_ANDROID` for Android real devices.

### Authentication

-   Authentication is handled by **Laravel Sanctum**, which provides token-based authentication.
-   The frontend sends login/register requests to the backend, and upon success, the backend returns a token.
-   Login is mode-based: send `login_with` as `mobile` or `email` (`mobile` mode requires `country_code` + `mobile_number`; `email` mode requires `email`).
-   The frontend stores this token and includes it in the `Authorization` header for all subsequent authenticated API requests as a Bearer token.
-   Authenticated routes on the backend are protected by the `auth:sanctum` middleware.

### Profile Picture Handling

-   Profile pictures are uploaded via a `POST` request to `/api/user/picture`.
-   The field name for the uploaded file must be `profile_picture`.
-   Files are stored in the `backend/public/user_pictures/` directory.
-   The filename is generated using the format: `name_phone_timestamp.ext` (e.g., `Ali_923001234567_1772362920.jpg`).
-   The path to the picture is stored in the `profile_picture` column of the `users` table.

### API Endpoints

Here are some of the key API endpoints:

-   `POST /api/auth/register`: User registration.
-   `POST /api/auth/login`: User login.
-   `POST /api/auth/logout`: User logout (requires authentication).
-   `GET /api/user`: Get the current authenticated user's profile (requires authentication).
-   `PUT /api/user`: Update the current authenticated user's profile (requires authentication).
-   `POST /api/user/picture`: Upload a new profile picture for the authenticated user (requires authentication).

### User Profile Fields

-   User profiles support additional fields: `member_id`, location, profession, company, experience, skills, role in community, blood group, interests, short bio, `linkedin_profile`, and date of birth. See `src/services/auth.ts` and `backend/app/Http/Controllers/AuthController.php` for details.
