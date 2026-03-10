# 🚀 Pull Request: Doctor Dashboard, Patient Details, Route Separation & Mobile API

## 📌 Summary

This PR adds major new features to the EHR system: a full **Doctor Dashboard** with patient tracking, a **Patient Details** page, clean **role-based route separation**, and a complete **REST API** for Doctor and Admin roles to support the connected mobile app.

---

## ✨ New Features

### 1. 🏥 Doctor Dashboard

- Replaced the placeholder doctor page with a fully functional dashboard
- Stats cards: Total Patients, Active Patients, Today's Updates
- Recent Forms feed showing all nurse submissions across all accounts
- PDF generation button still available
- Charts and live data sourced from the database

### 2. 🧑‍⚕️ Patient Details Page

- Clickable patient rows in **Total Patients** and **Active Patients** stat pages now navigate to a dedicated patient details page (`/doctor/patient/{id}`)
- Displays: patient avatar (initial letter), name, status badge, age/sex/room/admission pills
- Cards for: personal info, admission details, emergency contacts, chief complaints
- Quick-link grid to all form records for that patient

### 3. 📋 Recent Forms — Row Click Fix

- Fixed a bug where clicking a row in the Recent Forms table did nothing
- Root cause: Blade's `{{ }}` was HTML-encoding quotes in `onclick` attributes, breaking them silently
- Fixed by using `{!! !!}` for raw HTML attribute output on both desktop rows and mobile cards

### 4. 🗂️ Route Separation by Role

Routes are now cleanly split into three files instead of one monolithic `web.php`:

| File                | Middleware              |
| ------------------- | ----------------------- |
| `routes/doctor.php` | `auth`, `can:is-doctor` |
| `routes/nurse.php`  | `auth`, `can:is-nurse`  |
| `routes/admin.php`  | `auth`, `can:is-admin`  |

`routes/web.php` now only contains auth routes and `require` statements.

> **Bug fixed:** `Route [vital-signs.cdss] not defined` — restored the duplicate `cdss` route name in the nurse vital-signs group that was lost during separation.

### 5. 📱 Mobile API — Doctor & Admin Endpoints

New API controllers and routes for the mobile app:

**Doctor API** (`/api/doctor/*` — requires `role:doctor`)
| Endpoint | Description |
|----------|-------------|
| `GET /api/doctor/stats` | Dashboard counts |
| `GET /api/doctor/recent-forms` | Paginated form feed (filterable) |
| `GET /api/doctor/today-updates` | Today's form submissions |
| `GET /api/doctor/patients` | All patients (searchable) |
| `GET /api/doctor/patients/active` | Active/admitted patients |
| `GET /api/doctor/patient/{id}` | Full patient details |
| `GET /api/doctor/patient/{id}/forms/{type}` | Patient's records by form type |

**Admin API** (`/api/admin/*` — requires `role:admin`)
| Endpoint | Description |
|----------|-------------|
| `GET /api/admin/stats` | System-wide counts |
| `GET /api/admin/users` | List users (filterable by role) |
| `GET /api/admin/users/{id}` | Single user details |
| `POST /api/admin/users` | Register new user |
| `PATCH /api/admin/users/{id}/role` | Update user role |
| `GET /api/admin/audit-logs` | Paginated audit log |

**Role middleware:** New `CheckRole` middleware registered as `role` alias — returns clean `403 JSON` if role doesn't match.

**API routes** are also split:

- `routes/api/nurse.php`
- `routes/api/doctor.php`
- `routes/api/admin.php`

### 6. 📖 API Documentation

Three separate documentation files added to the repo root:

| File                 | Covers                               |
| -------------------- | ------------------------------------ |
| `API_DOCS_NURSE.md`  | All 11 nurse endpoint groups         |
| `API_DOCS_DOCTOR.md` | All 7 doctor endpoints with examples |
| `API_DOCS_ADMIN.md`  | All 6 admin endpoints with examples  |

---

## 📁 Files Changed / Added

### New Files

```
routes/doctor.php
routes/nurse.php
routes/admin.php
routes/api/nurse.php
routes/api/doctor.php
routes/api/admin.php
app/Http/Controllers/Api/Doctor/DoctorApiController.php
app/Http/Controllers/Api/Admin/AdminApiController.php
app/Http/Middleware/CheckRole.php
resources/views/doctor/patient-details.blade.php
API_DOCS_NURSE.md
API_DOCS_DOCTOR.md
API_DOCS_ADMIN.md
```

### Modified Files

```
routes/web.php                                         — stripped to auth + requires
routes/api.php                                         — stripped to auth + requires
bootstrap/app.php                                      — registered 'role' middleware alias
app/Http/Controllers/Doctor/ReportController.php       — added patientDetails(), updated fromMap
resources/views/doctor/recent-forms.blade.php          — fixed onclick {{ }} → {!! !!}
resources/views/doctor/stats/total-patients.blade.php  — onclick → patient-details
resources/views/doctor/stats/active-patients.blade.php — onclick → patient-details
```

---

## 🛠️ Setup Instructions for Team

> **No `npm install` or `npm run build` is needed** — no new frontend packages were added.  
> All changes are backend (PHP/Laravel) and Blade views only.

### Steps to pull and run:

```bash
# 1. Pull the latest changes
git pull origin main

# 2. No new Composer packages were added, but run this to be safe
composer install

# 3. Clear Laravel caches (REQUIRED — route structure changed)
php artisan route:clear
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# 4. Run migrations if any are pending (check with --pretend first)
php artisan migrate --pretend
php artisan migrate

# 5. Start the dev server as usual
php artisan serve

# 6. (Optional) If you work on frontend assets
npm run dev        # development with hot reload
npm run build      # production build
```

### ⚠️ Important Notes

- **Route caches must be cleared** — `routes/web.php` and `routes/api.php` were restructured. Old route caches will break the app.
- **No database schema changes** — no new migrations. Existing data is safe.
- **No new npm packages** — `node_modules` does not need to be reinstalled.
- **API tokens** — the new `/api/doctor/*` and `/api/admin/*` routes require a Sanctum token. Login via `POST /api/auth/login` first, then pass the `access_token` as a `Bearer` header. Role must match (`doctor` or `admin`).

---

## 🧪 Testing Checklist

- [x] Doctor dashboard loads and shows correct stats
- [x] Clicking a patient row in Total/Active Patients goes to Patient Details page
- [x] Patient Details page shows all info correctly
- [x] Recent Forms table rows are clickable and navigate correctly
- [x] All nurse routes still work (vital signs, physical exam, ADL, etc.)
- [x] `vital-signs.cdss` no longer throws a route error
- [x] Admin panel routes still work
- [ ] `POST /api/auth/login` returns a token
- [ ] `GET /api/doctor/stats` returns 403 for non-doctor tokens
- [ ] `GET /api/admin/stats` returns 403 for non-admin tokens
- [ ] Doctor and Admin API endpoints return correct data with valid tokens
