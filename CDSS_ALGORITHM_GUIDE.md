# 🧠 CDSS "Strong Algorithm" Documentation & Tutorial

Welcome to the new and improved Clinical Decision Support System (CDSS). This guide explains how the "Strong Algorithm" works, how it handles **Multiple Languages** (Tagalog, Spanish, French, etc.), and how it "understands" medical text beyond simple keywords.

---

## 🚀 Key Improvements (Why it's "Stronger")

1.  **Multi-Language Support (Auto-Detect)**: Uses Google Translate API to detect the nurse's language (Tagalog, Spanish, French, etc.) and normalize it into English for analysis.
2.  **Bi-Directional Translation**: Once an alert is found, the system translates the **entire clinical alert** back into the nurse's original language.
3.  **Porter Stemming**: Reduces words to their "root" (e.g., *Coughing* -> *Cough*).
4.  **Synonym Mapping**: Understands that *HR*, *Pulse*, and *BPM* are the same thing.
5.  **Semantic Density Scoring**: Matches are scored based on how much of the rule matches the input, with huge bonuses for exact phrase matches.
6.  **Intelligent Negation**: Correctly ignores alerts when a nurse types *"No signs of..."*, *"Denies..."*, or *"Wala"*.

---

## 🛠 How the Algorithm Works (Step-by-Step)

When a nurse enters text like: **"El paciente tiene fiebre y tos"** (Spanish) or **"May mabilis na tibok ng puso"** (Tagalog)

### 1. Language Detection & Normalization
The system detects the source language and translates it to English.
*   **Spanish Input**: *"El paciente tiene fiebre y tos"* -> **English**: *"The patient has fever and cough"*
*   **Tagalog Input**: *"May mabilis na tibok ng puso"* -> **English**: *"Has a fast heartbeat"*

### 2. Tokenization & Stemming
The English text is cleaned, and suffixes are removed.
*   **Fever** -> `fever`
*   **Cough** -> `cough`
*   **Heartbeat** -> `heartbeat`

### 3. Rule Matching & Scoring
The system ranks all matching clinical rules in English (for maximum speed).
*   **Phrase Match**: Exact matches (e.g., "fast heartbeat") get a **+200 bonus**.
*   **Overlap Score**: Partial matches are scored (0-100) based on keyword density.

### 4. Reverse Translation (The Final Step)
The system takes the winning clinical alert and translates it **back** into the nurse's original language in one final step.
*   **Winner (English)**: *"Risk for infection related to high fever."*
*   **Response (Spanish)**: *"Riesgo de infección relacionado con fiebre alta."*
*   **Response (Tagalog)**: *"Panganib para sa impeksyon na may kaugnayan sa mataas na lagnat."*

---

## 📖 Tutorial: How to add new rules

Your rules are stored in `storage/app/private/`. You only need to write rules in **English**. The system handles the rest!

1.  Open a YAML file (e.g., `skin_condition.yaml`).
2.  Add a new English rule:
    ```yaml
    - keywords: ['itchy skin', 'rash']
      alert: 'Patient reports pruritus (itching). Assess for allergic reaction.'
      severity: 'warning'
    ```
3.  **The Magic**: A nurse can now type in **any language**:
    *   **Spanish**: *"Piel con picazón"* -> Matches `itchy skin` -> Returns Spanish alert.
    *   **Tagalog**: *"Makatol ang balat"* -> Matches `itchy skin` -> Returns Tagalog alert.
    *   **French**: *"Peau qui démange"* -> Matches `itchy skin` -> Returns French alert.

---

## 🧪 Testing the Multi-Language API

| Input (Nurse types...) | Detected Language | Resulting Alert |
| :--- | :--- | :--- |
| *"Nahihilo"* | Tagalog (`tl`) | Translated Dizziness Alert |
| *"Tiene mucho dolor"* | Spanish (`es`) | Translated Severe Pain Alert |
| *"Il tousse"* | French (`fr`) | Translated Cough Alert |
| *"No signs of fever"* | English (`en`) | **No Alert** (Negation logic) |

---

## 🔧 Technical Summary for Developers

*   **Base Class**: `app/Services/BaseCdssService.php`.
*   **Winner-Only Translation**: For speed, translation only happens at the very start (Input -> English) and the very end (Final Alert -> Source Lang).
*   **Caching**: Uses a static cache to avoid repeated API calls for common phrases like "No Findings".
*   **Extending**: Any service extending `BaseCdssService` automatically becomes multi-lingual.
