# 📋 Nurse API Documentation

**Base URL:** `http://your-domain.com/api`  
**Auth:** All routes (except login) require `Authorization: Bearer {token}` header.

---

## 🔐 Authentication

### Login
`POST /api/auth/login`

```json
{ "email": "nurse@example.com", "password": "password" }
```
**Response:**
```json
{ "access_token": "...", "role": "nurse", "full_name": "username", "user_id": 1 }
```

### Logout
`POST /api/auth/logout`  
*Requires Bearer token.*

---

## 👤 Patients

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/patient` | List active patients (`?all=1` for all, `?search=name`) |
| POST | `/api/patient` | Register new patient |
| GET | `/api/patient/{id}` | Get patient by ID |
| PUT | `/api/patient/{id}` | Update patient |
| POST | `/api/patient/{id}/toggle-status` | Activate/deactivate (`{ "is_active": true }`) |

**Create Patient Body:**
```json
{
  "first_name": "Juan", "last_name": "Dela Cruz",
  "age": 30, "birthdate": "1994-01-15",
  "sex": "Male", "admission_date": "2025-01-01",
  "room_no": "101", "bed_no": "A",
  "address": "Manila", "religion": "Catholic",
  "chief_complaints": "Fever, cough"
}
```

---

## 🩺 Assessment Forms (ADPIE)

All 5 assessment types follow the same pattern:

| Type | URI Prefix |
|------|-----------|
| Vital Signs | `vital-signs` |
| Physical Exam | `physical-exam` |
| ADL | `adl` |
| Intake & Output | `intake-and-output` |
| Lab Values | `lab-values` |

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST/PUT | `/api/{type}` | Save/update record (runs CDSS automatically) |
| GET | `/api/{type}/patient/{patient_id}` | Get all records for patient |
| GET | `/api/{type}/{id}/assessment` | Get single record by ID |
| PUT | `/api/{type}/{id}/assessment` | Update record by ID |

---

## 🚨 Data Alerts

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/data-alert/patient/{patient_id}` | All alerts for a patient |
| GET | `/api/{component}/data-alert/patient/{patient_id}` | Alerts by component type |

---

## 🧠 ADPIE / CDSS

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/adpie/{component}/{id}` | Initialize ADPIE for a record |
| POST | `/api/adpie/analyze` | Analyze a single field |
| POST | `/api/adpie/analyze-batch` | Analyze multiple fields |
| PUT | `/api/adpie/{id}/{step}` | Save a step (`diagnosis\|planning\|intervention\|evaluation`) |

---

## 📚 Medical History

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/medical-history/patient/{patient_id}` | All history for patient |
| GET | `/api/medical-history/{type}/{id}` | Single record |
| POST/PUT | `/api/medical-history/{type}` | Create/update |
| POST/PUT | `/api/medical-history/{type}/{id}` | Update specific record |

**Types:** `present-illness` · `past-history` · `allergies` · `vaccination` · `developmental`

---

## 💊 Medication Reconciliation

Supports three prefix aliases: `medical-reconciliation`, `medication-reconciliation`, `medicalreconcilation`

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/medical-reconciliation/patient/{patient_id}` | All reconciliation data |
| GET | `/api/medical-reconciliation/{type}/{id}` | Single record |
| POST/PUT | `/api/medical-reconciliation/{type}` | Create/update |

**Types:** `current` · `home` · `changes`

---

## 💉 Medication Administration

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET/POST/PUT | `/api/medication-administration` | Save record |
| GET | `/api/medication-administration/patient/{patient_id}` | All records for patient |
| GET | `/api/medication-administration/patient/{patient_id}/time/{time}` | By time |
| GET/PUT | `/api/medication-administration/{id}` | Get/update single record |

---

## 🩻 IVs & Lines

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/ivs-and-lines/patient/{patient_id}` | All IVs for patient |
| POST/PUT | `/api/ivs-and-lines` | Save IV record |
| GET/PUT | `/api/ivs-and-lines/{id}` | Get/update single record |

---

## 🏥 Discharge Planning

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/discharge-planning/patient/{patient_id}` | Get discharge plan |
| POST/PUT | `/api/discharge-planning` | Save/update discharge plan |

---

## 🔬 Diagnostics

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/diagnostics/patient/{patient_id}` | All diagnostics for patient |
| POST | `/api/diagnostics` | Submit new diagnostic |
