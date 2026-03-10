# 📋 Doctor API Documentation

**Base URL:** `http://your-domain.com/api/doctor`  
**Auth:** All routes require `Authorization: Bearer {token}` header + **doctor role**.  
**Login first:** `POST /api/auth/login` → use returned `access_token`.

---

## 🔐 Authentication

### Login
`POST /api/auth/login`
```json
{ "email": "doctor@example.com", "password": "password" }
```
**Response:**
```json
{ "access_token": "...", "role": "doctor", "full_name": "dr_username", "user_id": 2 }
```

---

## 📊 Dashboard Stats

### Get Stats
`GET /api/doctor/stats`

**Response:**
```json
{
  "total_patients": 120,
  "active_patients": 45,
  "today_updates": 12
}
```

---

## 📋 Recent Forms Feed

### Get Recent Forms
`GET /api/doctor/recent-forms`

| Query Param | Type | Default | Description |
|-------------|------|---------|-------------|
| `type` | string | `all` | Form type filter: `vital-signs`, `physical-exam`, `adl`, `intake-output`, `lab-values`, `medication`, `ivs-lines` |
| `patient` | string | `""` | Filter by patient name (partial match) |
| `date` | date | `""` | Filter by date `YYYY-MM-DD` |
| `page` | int | `1` | Page number |
| `per_page` | int | `20` | Results per page (max 50) |

**Response:**
```json
{
  "data": [
    {
      "type": "Vital Signs",
      "type_key": "vital-signs",
      "patient_id": 7,
      "patient_name": "Dela Cruz, Juan M.",
      "time": "2025-03-10 08:45:00",
      "record_id": 42
    }
  ],
  "total": 150,
  "page": 1,
  "per_page": 20,
  "last_page": 8
}
```

---

## 📅 Today's Updates

### Get Today's Updates
`GET /api/doctor/today-updates`

**Response:**
```json
{
  "data": [
    {
      "type": "Lab Values",
      "type_key": "lab-values",
      "patient_id": 3,
      "patient_name": "Santos, Maria T.",
      "time": "2025-03-10 09:10:00",
      "record_id": 18
    }
  ],
  "total": 7
}
```

---

## 👥 Patients

### List All Patients (Total Patients)
`GET /api/doctor/patients`

| Query Param | Description |
|-------------|-------------|
| `search` | Search by first/last name |

**Response:** Array of patient objects sorted alphabetically.

```json
[
  {
    "id": 7,
    "patient_id": 7,
    "first_name": "Juan",
    "last_name": "Dela Cruz",
    "name": "Dela Cruz, Juan M.",
    "age": 30,
    "sex": "Male",
    "room_no": "101",
    "bed_no": "A",
    "admission_date": "2025-01-01T00:00:00.000000Z",
    "is_active": true,
    "days_admitted": 68
  }
]
```

### List Active Patients
`GET /api/doctor/patients/active`

Returns only currently admitted patients, sorted by most recent admission. Each object includes `days_admitted` field.

---

## 🧑‍⚕️ Patient Details

### Get Patient Details
`GET /api/doctor/patient/{id}`

**Response:** Full patient object including:
- Personal info (name, age, sex, birthdate, birthplace, religion, ethnicity, address)
- Admission info (admission_date, room_no, bed_no, is_active, days_admitted)
- Contact info (contact_name, contact_relationship, contact_number arrays)
- Chief complaints

```json
{
  "id": 7,
  "patient_id": 7,
  "name": "Dela Cruz, Juan M.",
  "first_name": "Juan",
  "last_name": "Dela Cruz",
  "middle_name": "Mayuga",
  "age": 30,
  "sex": "Male",
  "admission_date": "2025-01-01T00:00:00.000000Z",
  "days_admitted": 68,
  "room_no": "101",
  "bed_no": "A",
  "is_active": true,
  "chief_complaints": "Fever, cough"
}
```

---

## 📂 Patient Form Records

### Get Patient Forms by Type
`GET /api/doctor/patient/{patient_id}/forms/{type}`

| Path Param | Values |
|------------|--------|
| `type` | `vital-signs` · `physical-exam` · `adl` · `intake-output` · `lab-values` · `medication` · `ivs-lines` |

**Response:** Array of records for that form type. For assessment types (`vital-signs`, `physical-exam`, `adl`, `intake-output`, `lab-values`), each record includes its associated `nursing_diagnoses` object.

**Example:**
```
GET /api/doctor/patient/7/forms/vital-signs
GET /api/doctor/patient/7/forms/lab-values
GET /api/doctor/patient/7/forms/medication
```

**Response (with nursing diagnoses):**
```json
[
  {
    "id": 42,
    "patient_id": 7,
    "temperature": "37.5",
    "heart_rate": "82",
    "alerts": "Normal vital signs.",
    "updated_at": "2025-03-10T08:45:00.000000Z",
    "nursing_diagnoses": {
      "id": 8,
      "diagnosis": "...",
      "planning": "...",
      "intervention": "...",
      "evaluation": "..."
    }
  }
]
```

---

## ⚠️ Error Responses

| Status | Meaning |
|--------|---------|
| `401` | Missing or invalid token |
| `403` | Authenticated but not a doctor |
| `404` | Resource not found |
| `422` | Invalid form type parameter |
