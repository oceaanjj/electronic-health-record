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
| POST | `/api/patient/{id}/toggle-status` | Activate/deactivate patient |

---

### Register a New Patient

`POST /api/patient`

#### Required Fields

| DB Column | Type | Validation | Notes |
|---|---|---|---|
| `first_name` | string | required, max 255 | Auto-capitalized (e.g. `"maria"` → `"Maria"`) |
| `last_name` | string | required, max 255 | Auto-capitalized |
| `age` | number | required, min 0 | Decimals allowed — e.g. `0.5` = 6 months old |
| `birthdate` | date string | required, `YYYY-MM-DD` | e.g. `"1990-03-15"` |
| `sex` | string | required | Must be exactly `"Male"`, `"Female"`, or `"Other"` |
| `admission_date` | date string | required, `YYYY-MM-DD` | e.g. `"2026-03-12"` |

#### Optional Fields

| DB Column | Type | Validation | Notes |
|---|---|---|---|
| `middle_name` | string | nullable, max 255 | Auto-capitalized |
| `address` | string | nullable, max 500 | Full home address |
| `birthplace` | string | nullable, max 255 | City/town of birth |
| `religion` | string | nullable, max 255 | e.g. `"Catholic"` |
| `ethnicity` | string | nullable, max 255 | e.g. `"Filipino"` |
| `chief_complaints` | string | nullable | Free-text chief complaints on admission |
| `room_no` | string | nullable, max 50 | e.g. `"101"` |
| `bed_no` | string | nullable, max 50 | e.g. `"A"` |
| `contact_name` | array of strings | nullable | e.g. `["Juan Santos"]` |
| `contact_relationship` | array of strings | nullable | e.g. `["Spouse"]` |
| `contact_number` | array of strings | nullable | e.g. `["09171234567"]` |

> ⚠️ `contact_name`, `contact_relationship`, and `contact_number` are stored as JSON arrays in the database. Always send them as arrays, even with a single contact.

#### Minimum Request Body
```json
{
  "first_name": "Juan",
  "last_name": "Dela Cruz",
  "age": 30,
  "birthdate": "1994-01-15",
  "sex": "Male",
  "admission_date": "2026-03-12"
}
```

#### Full Request Body (all fields)
```json
{
  "first_name": "Juan",
  "last_name": "Dela Cruz",
  "middle_name": "Reyes",
  "age": 30,
  "birthdate": "1994-01-15",
  "sex": "Male",
  "admission_date": "2026-03-12",
  "address": "123 Rizal St., Quezon City",
  "birthplace": "Manila",
  "religion": "Catholic",
  "ethnicity": "Filipino",
  "chief_complaints": "Fever, cough, shortness of breath",
  "room_no": "101",
  "bed_no": "A",
  "contact_name": ["Maria Dela Cruz"],
  "contact_relationship": ["Spouse"],
  "contact_number": ["09171234567"]
}
```

#### Success Response (201)
```json
{
  "message": "Patient registered successfully",
  "patient": {
    "id": "P-2026-0042",
    "patient_id": "P-2026-0042",
    "first_name": "Juan",
    "last_name": "Dela Cruz",
    "middle_name": "Reyes",
    "age": 30,
    "birthdate": "1994-01-15",
    "sex": "Male",
    "admission_date": "2026-03-12T00:00:00.000000Z",
    "address": "123 Rizal St., Quezon City",
    "birthplace": "Manila",
    "religion": "Catholic",
    "ethnicity": "Filipino",
    "chief_complaints": "Fever, cough, shortness of breath",
    "room_no": "101",
    "bed_no": "A",
    "contact_name": ["Maria Dela Cruz"],
    "contact_relationship": ["Spouse"],
    "contact_number": ["09171234567"],
    "is_active": true,
    "user_id": 5,
    "created_at": "2026-03-12T14:00:00.000000Z",
    "updated_at": "2026-03-12T14:00:00.000000Z"
  }
}
```

#### Validation Error Response (422)
```json
{
  "message": "The sex field must be one of: Male, Female, Other.",
  "errors": {
    "sex": ["The sex field must be one of: Male, Female, Other."]
  }
}
```

---

### List Patients

`GET /api/patient`

| Query Param | Description |
|---|---|
| `search` | Filter by `first_name`, `last_name`, or `patient_id` |
| `all` | Include deactivated patients — e.g. `?all=1` |

---

### Get a Single Patient

`GET /api/patient/{patient_id}`

Returns the full patient object with all DB columns.

---

### Update a Patient

`PUT /api/patient/{patient_id}`

Send only the fields you want to change. Uses the same DB column names as the create endpoint. All fields are optional (partial update).

```json
{
  "room_no": "205",
  "bed_no": "B",
  "chief_complaints": "Updated: fever and chills"
}
```

**Success Response (200):**
```json
{
  "message": "Patient updated successfully",
  "patient": { "...all patient fields..." }
}
```

---

### Activate / Deactivate a Patient

`POST /api/patient/{patient_id}/toggle-status`

```json
{ "is_active": true }
```

Use `true` to activate, `false` to deactivate (soft-delete).

**Success Response (200):**
```json
{ "message": "Patient Activated successfully" }
```

---

## 🩺 Assessment Forms (ADPIE)

Each clinical component has its own dedicated documentation with complete endpoint, field, CDSS alert, and ADPIE/Nursing Diagnosis instructions:

| Component | Documentation File | DB Table | API Prefix |
|-----------|-------------------|----------|------------|
| 🫀 Vital Signs | `API_DOCS_NURSE_VITAL_SIGNS.md` | `vital_signs` | `/api/vital-signs` |
| 🩻 Physical Exam | `api_docs_physicalexam.md` | `physical_exams` | `/api/physical-exam` |
| 🧍 ADL (Activities of Daily Living) | `API_DOCS_NURSE_ADL.md` | `act_of_daily_living` | `/api/adl` |
| 💧 Intake & Output | `API_DOCS_NURSE_INTAKE_OUTPUT.md` | `intake_and_outputs` | `/api/intake-and-output` |
| 🔬 Lab Values | `API_DOCS_NURSE_LAB_VALUES.md` | `lab_values` | `/api/lab-values` |

All components share the same endpoint pattern:

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/{component}` | Save/update record (runs CDSS automatically) |
| GET | `/api/{component}/patient/{patient_id}` | Get all records for patient |
| GET | `/api/{component}/{id}/assessment` | Get single record by ID |
| PUT | `/api/{component}/{id}/assessment` | Update record by ID (re-runs CDSS) |
| GET | `/api/{component}/data-alert/patient/{patient_id}` | Get latest CDSS alert for patient |
| GET | `/api/adpie/{component}/{id}` | Initialize ADPIE/Nursing Diagnosis record |
| POST | `/api/adpie/analyze` | Analyze a single ADPIE field via CDSS |
| POST | `/api/adpie/analyze-batch` | Analyze multiple ADPIE fields at once |
| PUT | `/api/adpie/{id}/{step}` | Save an ADPIE step (`diagnosis\|planning\|intervention\|evaluation`) |

---

## 🚨 Data Alerts

Returns the latest CDSS alert(s) for a patient across all components or for a specific one.

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/data-alert/patient/{patient_id}` | All alerts for a patient (all components) |
| GET | `/api/{component}/data-alert/patient/{patient_id}` | Alert for a specific component |

Valid `{component}` values: `vital-signs` · `physical-exam` · `adl` · `intake-and-output` · `lab-values`

**GET all alerts response:**
```json
{
  "vital_signs": "⚠️ WARNING: Low-grade fever detected.",
  "physical_exam": "Jaundice — Suggests liver disease or hemolysis.",
  "adl": "Risk for falls related to impaired physical mobility.",
  "intake_and_output": "🚨 CRITICAL: Severe oliguria. Immediate renal assessment required.",
  "lab_values": "Leukopenia — Significant risk of infection."
}
```

Fields with no alerts return `"No findings."`.

> For full details on component-specific data alerts, see the dedicated documentation files.

---

## 🧠 ADPIE / CDSS

The ADPIE (Nursing Diagnosis) workflow applies to all assessment components. It follows 4 steps: **Diagnosis → Planning → Intervention → Evaluation**. CDSS recommendations are generated automatically when each step is saved.

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/adpie/{component}/{id}` | Initialize ADPIE for a record |
| POST | `/api/adpie/analyze` | Analyze a single ADPIE field via CDSS |
| POST | `/api/adpie/analyze-batch` | Analyze multiple ADPIE fields at once |
| PUT | `/api/adpie/{id}/{step}` | Save a step (`diagnosis\|planning\|intervention\|evaluation`) |

Valid `{component}` values: `vital-signs` · `physical-exam` · `adl` · `intake-and-output` · `lab-values`

> For full ADPIE request/response details, CDSS alert fields (`diagnosis_alert`, `planning_alert`, `intervention_alert`, `evaluation_alert`), and the `nursing_diagnoses` table schema, see the dedicated documentation files for each component.

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
