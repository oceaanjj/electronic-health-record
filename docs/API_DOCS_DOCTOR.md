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
  "total_patients":  120,
  "active_patients": 45,
  "today_updates":   12,
  "unread_count":    7
}
```

| Field | Description |
|-------|-------------|
| `total_patients` | All registered patients |
| `active_patients` | Currently admitted patients |
| `today_updates` | Forms updated today across all types |
| `unread_count` | Recent forms not yet marked as read by this doctor |

---

## 📋 Recent Forms Feed

### Get Recent Forms
`GET /api/doctor/recent-forms`

| Query Param | Type | Default | Description |
|-------------|------|---------|-------------|
| `type` | string | `all` | Form type: `vital-signs` · `physical-exam` · `adl` · `intake-output` · `lab-values` · `medication` · `ivs-lines` |
| `read` | string | `all` | Read status filter: `all` · `unread` · `read` |
| `patient` | string | `""` | Filter by patient name (partial match) |
| `date` | date | `""` | Filter by date `YYYY-MM-DD` |
| `page` | int | `1` | Page number |
| `per_page` | int | `20` | Results per page (max 50) |

**Response:**
```json
{
  "data": [
    {
      "type":         "Vital Signs",
      "type_key":     "vital-signs",
      "patient_id":   7,
      "patient_name": "Dela Cruz, Juan M.",
      "time":         "2026-03-11 08:45:00",
      "record_id":    42,
      "is_read":      false,
      "is_today":     true
    }
  ],
  "total":     150,
  "page":      1,
  "per_page":  20,
  "last_page": 8
}
```

| Field | Description |
|-------|-------------|
| `is_read` | `true` if this doctor has marked this form as read |
| `is_today` | `true` if the form was last updated today |
| `record_id` | Use this with `POST /api/doctor/mark-read` to mark as read |

**Filter examples:**
```
GET /api/doctor/recent-forms?read=unread
GET /api/doctor/recent-forms?read=read&type=vital-signs
GET /api/doctor/recent-forms?date=2026-03-11&patient=Cruz
```

---

## ✅ Mark Form as Read

### Mark as Read
`POST /api/doctor/mark-read`

Use `record_id` and `model_class` from the recent-forms or today-updates feed.

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `model_type` | string | ✅ | Full PHP model class name (see table below) |
| `model_id` | integer | ✅ | The `record_id` from the feed |

**Allowed `model_type` values:**

| Form Type | `model_type` value |
|-----------|-------------------|
| Vital Signs | `App\Models\Vitals` |
| Physical Exam | `App\Models\PhysicalExam` |
| ADL | `App\Models\ActOfDailyLiving` |
| Intake & Output | `App\Models\IntakeAndOutput` |
| Lab Values | `App\Models\LabValues` |
| Medication Administration | `App\Models\MedicationAdministration` |
| IVs & Lines | `App\Models\IvsAndLine` |

**Request body:**
```json
{
  "model_type": "App\\Models\\Vitals",
  "model_id":   42
}
```

**Response:**
```json
{ "success": true, "message": "Marked as read." }
```

> **Tip:** Call this immediately when the doctor opens/views a form item. Subsequent calls for the same form simply update `read_at` (idempotent).

**React Native example:**
```js
await fetch(`${BASE_URL}/api/doctor/mark-read`, {
  method: 'POST',
  headers: {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
  body: JSON.stringify({
    model_type: 'App\\Models\\Vitals',
    model_id: item.record_id,
  }),
});
```

**Flutter example:**
```dart
await http.post(
  Uri.parse('$baseUrl/api/doctor/mark-read'),
  headers: {
    'Authorization': 'Bearer $token',
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
  body: jsonEncode({
    'model_type': r'App\Models\Vitals',
    'model_id': item['record_id'],
  }),
);
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
      "type":         "Lab Values",
      "type_key":     "lab-values",
      "patient_id":   3,
      "patient_name": "Santos, Maria T.",
      "time":         "2026-03-12 09:10:00",
      "record_id":    18,
      "is_read":      false
    }
  ],
  "total": 7
}
```

> `is_read` works the same as in Recent Forms — use `POST /api/doctor/mark-read` to mark items read.

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
    "admission_date": "2026-01-01T00:00:00.000000Z",
    "is_active": true,
    "days_admitted": 70
  }
]
```

### List Active Patients
`GET /api/doctor/patients/active`

Returns only currently admitted patients, sorted by most recent admission. Each object includes `days_admitted`.

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
  "admission_date": "2026-01-01T00:00:00.000000Z",
  "days_admitted": 70,
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

**Response:** Array of records for that form type. For `vital-signs`, `physical-exam`, `adl`, `intake-output`, and `lab-values`, each record includes its `nursing_diagnoses` object.

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
    "hr": "82",
    "alerts": "Normal vital signs.",
    "updated_at": "2026-03-12T08:45:00.000000Z",
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
| `422` | Validation failed (invalid form type or model_type) |

