# Unified ADPIE & CDSS Process Documentation

This document outlines the workflow for the Clinical Decision Support System (CDSS) and the Nursing Process (ADPIE) implementation.

## 1. Trigger Point (Assessment Blade)
The process begins in any of the assessment views:
*   `physical-exam.blade.php`
*   `vital-signs.blade.php`
*   `lab-values.blade.php`
*   `intake-and-output.blade.php`
*   `act-of-daily-living.blade.php`

### The CDSS Button
Instead of a simple link, the CDSS button is now a **submit button**:
```html
<button type="submit" name="action" value="cdss" class="button-default cdss-btn">CDSS</button>
```
**Why?** This ensures that any findings typed by the nurse are saved to the database *before* navigating to the ADPIE process, preventing data loss.

---

## 2. Assessment Controller Logic
When the assessment form is submitted with `action=cdss`, the corresponding controller (`PhysicalExamController`, `VitalSignsController`, etc.) handles the save and then redirects:

```php
if ($request->input('action') === 'cdss') {
    return redirect()->route('nursing-diagnosis.process', [
        'component' => 'physical-exam',
        'id' => $record->id
    ]);
}
```

---

## 3. Routing & URL Structure

The system uses a unified routing structure defined in `routes/web.php` under the `adpie` prefix.

### Key Unified Routes
| Method | Route Name | URL Pattern | Description |
| :--- | :--- | :--- | :--- |
| **GET** | `nursing-diagnosis.process` | `/adpie/{component}/process/{id}` | The main entry point that renders the unified 4-step wizard. |
| **POST** | `nursing-diagnosis.storeFullProcess` | `/adpie/{component}/process/{id}` | Saves data for any/all steps and redirects back to the current step. |

### CDSS & AJAX Routes
| Method | Route Name | URL Pattern | Description |
| :--- | :--- | :--- | :--- |
| **POST** | `nursing-diagnosis.analyze-field` | `/adpie/analyze-step` | Handles live typing analysis for a single textarea. |
| **POST** | `nursing-diagnosis.analyze-batch-field` | `/adpie/analyze-batch-step` | Analyzes multiple fields at once (used for initial page load). |

### Legacy/Redirect Routes
These routes are maintained for backward compatibility but now perform an internal redirect to the `.process` route:
*   `nursing-diagnosis.start`
*   `nursing-diagnosis.showPlanning`
*   `nursing-diagnosis.showIntervention`
*   `nursing-diagnosis.showEvaluation`

---

## 4. Unified ADPIE Router
The request hits `NursingDiagnosisController@showProcess`. This is the "brain" of the unified system.

1.  It identifies the correct **Component Service** (e.g., `PhysicalExamComponent`).
2.  It calls `getProcessData()` to prepare session alerts and retrieve patient info.
3.  It returns the single, unified view: `resources/views/adpie/process.blade.php`.

---

## 5. The Unified Process View (`process.blade.php`)
This file uses a **Slider/Wizard** interface to show all 4 steps of ADPIE on one page without full page reloads.

### Data Persistence (Submit Button)
When a user clicks "SUBMIT" on any step (Planning, Intervention, etc.), the form submits via POST to `nursing-diagnosis.storeFullProcess`.

*   **Hidden Input:** `<input type="hidden" name="current_step" id="current_step_input" value="1">`
*   **Controller Action:** The controller saves all fields and redirects back with the `current_step` in the session.
*   **JavaScript Auto-Resume:** On reload, JS reads the session and automatically slides the user back to the step they were just on.

---

## 6. Dynamic CDSS Alerts (`adpie-alert.js`)
The system provides real-time clinical recommendations as the nurse types.

### Live Analysis Workflow
1.  **Event Listener:** `adpie-alert.js` attaches listeners to all `.cdss-input` fields.
2.  **Step Awareness:** When the user clicks "Next" or "Back", a `cdss:step-changed` event is dispatched to re-initialize listeners for the newly visible fields.
3.  **API Call:** As typing stops (debounce), a request is sent to `NursingDiagnosisController@analyzeDiagnosisField`.
4.  **Banner Update:** The system finds the specific banner for that step (e.g., `#recommendation-planning`) and updates it with the CDSS recommendation.
5.  **Modal Interaction:** Clicking "View Details" on the banner calls `window.openRecommendationModal()` to show the full, formatted clinical guidance.

---

## 7. Directory Cleanup
Because all logic is now in `process.blade.php` and the dynamic partials in `views/adpie/partials/`, the following directories are now **redundant** and safe to delete:
*   `resources/views/adpie/physical-exam/`
*   `resources/views/adpie/vital-signs/`
*   `resources/views/adpie/lab-values/`
*   `resources/views/adpie/intake-and-output/`
*   `resources/views/adpie/adl/`

*Note: Component classes have been updated to redirect any legacy links targeting these folders to the new unified process.*
