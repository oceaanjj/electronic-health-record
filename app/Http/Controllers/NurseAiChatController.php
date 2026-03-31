<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use App\Models\Patient;

class NurseAiChatController extends Controller
{
    private const FEATURE_MAP = [
        [
            'name' => 'Register Patient',
            'route' => 'patients.create',
            'path' => '/patients/create',
            'keywords' => ['register patient', 'add patient', 'new patient', 'create patient', 'admit patient'],
        ],
        [
            'name' => 'Home',
            'route' => 'nurse-home',
            'path' => '/nurse',
            'keywords' => ['home', 'dashboard'],
        ],
        [
            'name' => 'Demographic Profile',
            'route' => 'patients.index',
            'path' => '/patients',
            'keywords' => ['demographic', 'profile', 'patient list', 'patients'],
        ],
        [
            'name' => 'Medical History',
            'route' => 'medical-history',
            'path' => '/medical-history',
            'keywords' => ['medical history', 'history'],
        ],
        [
            'name' => 'Physical Exam',
            'route' => 'physical-exam.index',
            'path' => '/physical-exam',
            'keywords' => ['physical exam', 'physical'],
        ],
        [
            'name' => 'Vital Signs',
            'route' => 'vital-signs.show',
            'path' => '/vital-signs',
            'keywords' => ['vital signs', 'vitals', 'bp', 'temperature'],
        ],
        [
            'name' => 'Intake and Output',
            'route' => 'io.show',
            'path' => '/intake-and-output',
            'keywords' => ['intake', 'output', 'i&o', 'io'],
        ],
        [
            'name' => 'Activities of Daily Living',
            'route' => 'adl.show',
            'path' => '/adl',
            'keywords' => ['adl', 'activities of daily living'],
        ],
        [
            'name' => 'Lab Values',
            'route' => 'lab-values.index',
            'path' => '/lab-values',
            'keywords' => ['lab', 'lab values', 'cbc'],
        ],
        [
            'name' => 'Diagnostics',
            'route' => 'diagnostics.index',
            'path' => '/diagnostics',
            'keywords' => ['diagnostics', 'diagnosis'],
        ],
        [
            'name' => 'IVs and Lines',
            'route' => 'ivs-and-lines',
            'path' => '/ivs-and-lines',
            'keywords' => ['iv', 'ivs', 'lines'],
        ],
        [
            'name' => 'Medication Administration',
            'route' => 'medication-administration',
            'path' => '/medication-administration',
            'keywords' => ['medication administration', 'administer', 'medication'],
        ],
        [
            'name' => 'Medication Reconciliation',
            'route' => 'medication-reconciliation',
            'path' => '/medication-reconciliation',
            'keywords' => ['medication reconciliation', 'reconciliation'],
        ],
    ];

    private const FEATURE_GUIDES = [
        'patients.create' => "1) Open Register Patient.\n2) Fill in demographic and contact details.\n3) Verify required fields.\n4) Submit to create the patient record.",
        'patients.index' => "1) Open Demographic Profile.\n2) Use search to find patients by name.\n3) Select a patient to review profile details.",
        'medical-history' => "1) Open Medical History.\n2) Select the patient.\n3) Encode prior conditions, surgeries, allergies, and relevant history.\n4) Save changes.",
        'physical-exam.index' => "1) Open Physical Exam.\n2) Choose the patient.\n3) Enter assessment findings.\n4) Save the exam entry.",
        'vital-signs.show' => "1) Open Vital Signs.\n2) Select patient and enter BP, pulse, temperature, and respirations.\n3) Review values.\n4) Save.",
        'io.show' => "1) Open Intake and Output.\n2) Select patient.\n3) Record fluid intake and output values.\n4) Save and monitor balance.",
        'adl.show' => "1) Open Activities of Daily Living.\n2) Select patient.\n3) Document ADL status and assistance level.\n4) Save.",
        'lab-values.index' => "1) Open Lab Values.\n2) Select patient.\n3) Record or review laboratory results.\n4) Save updates.",
        'diagnostics.index' => "1) Open Diagnostics.\n2) Select patient.\n3) Add diagnostic notes/results.\n4) Save the entry.",
        'ivs-and-lines' => "1) Open IVs and Lines.\n2) Select patient.\n3) Record IV/line type, site, and status.\n4) Save.",
        'medication-administration' => "1) Open Medication Administration.\n2) Select patient.\n3) Encode medication, dose, route, and time.\n4) Confirm and save.",
        'medication-reconciliation' => "1) Open Medication Reconciliation.\n2) Select patient.\n3) Compare current vs prior meds.\n4) Resolve differences and save.",
        'nurse-home' => "Open Home to see nurse dashboard shortcuts and quick access to all nursing modules.",
    ];

    public function ask(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:500'],
        ]);

        $message = trim((string) $validated['message']);
        $apiKey = (string) config('services.gemini.api_key');
        $model = (string) config('services.gemini.model', 'gemini-2.0-flash');
        $offline = $this->offlineResponse($message);

        // Prioritize offline intents even when Gemini is enabled.
        if (!empty($offline['actions']) || !empty($offline['confirm_action']) || !empty($offline['force_offline'])) {
            return response()->json($offline);
        }

        if ($apiKey === '') {
            return response()->json($offline);
        }

        // Filter out off-topic questions to save tokens
        if (!$this->isRelevantTopic($message)) {
            return response()->json([
                'reply' => 'I\'m here to help with healthcare topics and nurse app features. Please ask about medical care, patient management, or how to use this app.',
                'actions' => [],
                'confirm_action' => null,
            ]);
        }

        $prompt = $this->buildPrompt($message);
        $cacheKey = $this->aiCacheKey($message, $model);
        $cachedAnswer = Cache::get($cacheKey);
        if (is_string($cachedAnswer) && trim($cachedAnswer) !== '') {
            return response()->json([
                'reply' => $cachedAnswer,
                'actions' => [],
                'confirm_action' => null,
                'is_ai' => true,
                'from_cache' => true,
            ]);
        }

        try {
            $response = Http::timeout(25)
                ->acceptJson()
                ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt],
                            ],
                        ],
                    ],
                    'generationConfig' => [
                        'maxOutputTokens' => 800,
                        'temperature' => 0.7,
                    ],
                ]);
        } catch (\Throwable $e) {
            return response()->json([
                'reply' => 'Internet connection failed. Please try again.',
                'actions' => [],
                'confirm_action' => null,
                'is_ai' => false,
            ]);
        }

        if (!$response->successful()) {
            return response()->json($offline);
        }

        $parts = data_get($response->json(), 'candidates.0.content.parts', []);
        $answer = collect($parts)
            ->pluck('text')
            ->filter(fn ($text) => is_string($text) && trim($text) !== '')
            ->implode("\n");

        $finishReason = (string) data_get($response->json(), 'candidates.0.finishReason', '');
        if ($finishReason === 'MAX_TOKENS' && $answer !== '') {
            $continuationPrompt = $prompt
                . "\n\nPartial answer already shown:\n"
                . $answer
                . "\n\nContinue exactly where it stopped. Do not repeat earlier text. Finish the answer completely.";

            try {
                $continuationResponse = Http::timeout(25)
                    ->acceptJson()
                    ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", [
                        'contents' => [
                            [
                                'parts' => [
                                    ['text' => $continuationPrompt],
                                ],
                            ],
                        ],
                        'generationConfig' => [
                            'maxOutputTokens' => 500,
                            'temperature' => 0.7,
                        ],
                    ]);
            } catch (\Throwable $e) {
                $continuationResponse = null;
            }

            if ($continuationResponse && $continuationResponse->successful()) {
                $continuationParts = data_get($continuationResponse->json(), 'candidates.0.content.parts', []);
                $continuationAnswer = collect($continuationParts)
                    ->pluck('text')
                    ->filter(fn ($text) => is_string($text) && trim($text) !== '')
                    ->implode("\n");

                if ($continuationAnswer !== '') {
                    $answer .= "\n" . $continuationAnswer;
                }
            }
        }

        if ($answer === '') {
            return response()->json($offline);
        }

        Cache::put($cacheKey, $answer, now()->addHours(24));

        return response()->json([
            'reply' => $answer,
            'actions' => [],
            'confirm_action' => null,
            'is_ai' => true,
        ]);
    }

    private function aiCacheKey(string $message, string $model): string
    {
        $normalized = $this->normalizeAiQuestion($message);
        return 'nurse_ai_answer:' . md5(strtolower($model) . '|' . $normalized);
    }

    private function normalizeAiQuestion(string $message): string
    {
        $text = strtolower($message);
        $text = preg_replace('/[^a-z0-9\s]/', ' ', $text) ?? '';
        $text = preg_replace('/\s+/', ' ', trim($text)) ?? '';

        // remove filler words so similar questions can reuse cached answers
        $tokens = explode(' ', $text);
        $stopwords = [
            'what', 'is', 'are', 'the', 'a', 'an', 'please', 'can', 'you', 'tell', 'me',
            'about', 'of', 'for', 'to', 'in', 'on', 'at', 'do', 'does', 'how', 'i', 'need',
        ];

        $tokens = array_values(array_filter($tokens, function ($token) use ($stopwords) {
            return $token !== '' && !in_array($token, $stopwords, true);
        }));

        $synonyms = [
            'teenager' => 'teen',
            'teenagers' => 'teen',
            'adolescent' => 'teen',
            'adolescents' => 'teen',
            'bp' => 'blood pressure',
            'hr' => 'heart rate',
            'yrs' => 'years',
            'yr' => 'year',
        ];

        foreach ($tokens as &$token) {
            if (isset($synonyms[$token])) {
                $token = $synonyms[$token];
            }
        }
        unset($token);

        sort($tokens);
        return implode(' ', $tokens);
    }

    private function isRelevantTopic(string $message): bool
    {
        $normalized = strtolower($message);

        // Healthcare & medical keywords (ALLOWED)
        $healthcareKeywords = [
            'patient', 'health', 'medical', 'diagnosis', 'symptom', 'treatment', 'medication',
            'vitals', 'vital signs', 'vital', 'blood pressure', 'bp', 'temperature', 'heart rate', 
            'pulse', 'oxygen', 'spo2', 'respiration', 'rr', 'hr', 'temp',
            'nursing', 'nurse', 'care', 'doctor', 'physician', 'hospital', 'clinical',
            'disease', 'condition', 'infection', 'pain', 'fever', 'wound', 'injury',
            'prescription', 'dosage', 'drug', 'therapy', 'surgery', 'procedure',
            'lab', 'test', 'examination', 'assessment', 'monitor', 'observation',
            'ehr', 'record', 'chart', 'documentation', 'triage', 'emergency',
            'diabetes', 'hypertension', 'cardiac', 'respiratory', 'neurological',
            'normal', 'range', 'level', 'rate', 'value', 'reading', 'measurement',
            'abnormal', 'high', 'low', 'elevated', 'decreased', 'increased',
            // Anatomy & physiology
            'body', 'anatomy', 'organ', 'bone', 'muscle', 'tissue', 'cell', 'blood',
            'skin', 'eye', 'ear', 'nose', 'throat', 'mouth', 'teeth', 'hair',
            'height', 'weight', 'bmi', 'growth', 'development', 'puberty', 'aging',
            // Demographics & characteristics
            'male', 'female', 'age', 'gender', 'race', 'ethnicity', 'asian', 'filipino',
            'caucasian', 'african', 'hispanic', 'indigenous', 'genetic', 'hereditary',
            // General health topics
            'nutrition', 'diet', 'vitamin', 'mineral', 'exercise', 'fitness', 'sleep',
            'mental health', 'psychology', 'stress', 'anxiety', 'depression',
            'pregnancy', 'maternal', 'pediatric', 'geriatric', 'child', 'infant', 'elderly',
            'allergy', 'immune', 'vaccine', 'immunization', 'antibody',
        ];

        // App feature keywords (ALLOWED)
        $appKeywords = [
            'feature', 'page', 'route', 'navigate', 'open', 'access', 'how to use',
            'where is', 'find', 'show me', 'dashboard', 'menu', 'app', 'system',
        ];

        // Off-topic keywords (BLOCKED)
        $blockedKeywords = [
            'president', 'politics', 'election', 'government', 'congress', 'senate',
            'write code', 'create code', 'program', 'javascript', 'python', 'php code',
            'movie', 'film', 'actor', 'actress', 'celebrity', 'entertainment',
            'sport', 'football', 'basketball', 'soccer', 'game', 'match',
            'weather', 'forecast', 'climate today',
            'stock', 'invest', 'crypto', 'bitcoin', 'finance', 'market',
            'joke', 'funny', 'meme', 'story', 'novel',
        ];

        // Check if blocked topic
        foreach ($blockedKeywords as $blocked) {
            if (str_contains($normalized, $blocked)) {
                return false;
            }
        }

        // Check if healthcare or app related
        foreach ($healthcareKeywords as $keyword) {
            if (str_contains($normalized, $keyword)) {
                return true;
            }
        }

        foreach ($appKeywords as $keyword) {
            if (str_contains($normalized, $keyword)) {
                return true;
            }
        }

        // Short questions likely generic (block)
        if (str_word_count($normalized) <= 3 && !str_contains($normalized, 'what') && !str_contains($normalized, 'how')) {
            return false;
        }

        // Default: allow if not explicitly blocked
        return true;
    }

    private function buildPrompt(string $message): string
    {
        $pages = collect(self::FEATURE_MAP)
            ->take(10)
            ->pluck('name')
            ->implode(', ');

        return "Healthcare assistant for nurses. Answer briefly about medical topics, patient care, or app features. Available pages: {$pages}.\nQ: {$message}\nA:";
    }

    private function offlineResponse(string $message): array
    {
        $normalized = strtolower($message);

        // Treat direct name searches like "search for rex" as patient lookup.
        $implicitPatientName = $this->extractImplicitPatientSearchName($message);
        if ($implicitPatientName !== null && $implicitPatientName !== '') {
            return $this->searchPatientRecord($implicitPatientName);
        }

        if ($this->isPatientOfflineIntent($normalized)) {
            $patientName = $this->extractPatientName($message);

            if ($patientName !== null && $patientName !== '') {
                return $this->searchPatientRecord($patientName);
            }

            return [
                'reply' => 'Patient requests are handled offline using your database. Please include a patient name (example: "is there a record for Juan Dela Cruz").',
                'actions' => [[
                    'label' => 'View Patient List',
                    'url' => '/patients',
                ]],
                'confirm_action' => null,
                'force_offline' => true,
            ];
        }
        
        // Check for patient record queries
        if (preg_match('/\b(is there|find|search|show|get|lookup|check)\s+(a\s+)?(record|patient|data)\s+(for|of|named|about)\s+(.+)/i', $message, $matches)) {
            $patientName = trim($matches[5]);
            return $this->searchPatientRecord($patientName);
        }
        
        // Check if asking about a specific patient by name
        if (preg_match('/\bpatient\s+(.+?)\s+(record|data|information|details|history)/i', $message, $matches)) {
            $patientName = trim($matches[1]);
            return $this->searchPatientRecord($patientName);
        }

        $featureHelp = $this->featureHelpResponse($normalized);
        if ($featureHelp !== null) {
            return $featureHelp;
        }
        
        // Check if this is a NAVIGATION intent (not an information question)
        $isNavigationIntent = str_contains($normalized, 'open')
            || str_contains($normalized, 'go to')
            || str_contains($normalized, 'take me to')
            || str_contains($normalized, 'show me the page')
            || str_contains($normalized, 'navigate')
            || str_contains($normalized, 'access')
            || preg_match('/\b(where is|find|register|create|add|view|see|check)\s+(the\s+)?(page|patient|diagnostic|medication|lab|vital)/i', $message)
            || preg_match('/\bi (want to|need to|would like to)\s+(register|create|add|view|see|check|open)/i', $message);
        
        // Information questions should NOT trigger navigation
        $isInformationQuestion = str_contains($normalized, 'what is')
            || str_contains($normalized, 'what are')
            || str_contains($normalized, 'how to')
            || str_contains($normalized, 'why')
            || str_contains($normalized, 'when')
            || str_contains($normalized, 'explain')
            || str_contains($normalized, 'tell me about')
            || str_contains($normalized, 'normal')
            || str_contains($normalized, 'range')
            || str_contains($normalized, 'rate')
            || preg_match('/\b(bp|blood pressure|heart rate|temperature|pulse)\s+(for|of|in|rate|range|normal)/i', $message);
        
        $showAll = str_contains($normalized, 'all pages')
            || str_contains($normalized, 'all features')
            || str_contains($normalized, 'show all')
            || str_contains($normalized, 'list all');

        if ($showAll) {
            return [
                'reply' => 'Here are the main pages I can help you open quickly.',
                'actions' => collect(self::FEATURE_MAP)
                    ->take(8)
                    ->map(fn ($item) => [
                        'label' => 'Open ' . $item['name'],
                        'url' => $item['path'],
                    ])
                    ->values()
                    ->all(),
            ];
        }

        // Only do navigation matching if it's clearly a navigation intent AND not an information question
        if ($isNavigationIntent && !$isInformationQuestion) {
            $bestMatch = $this->findBestFeatureMatch($normalized);

            if ($bestMatch) {
                return [
                    'reply' => "Got it. Tap below to open {$bestMatch['name']}.",
                    'actions' => [[
                        'label' => 'Open ' . $bestMatch['name'],
                        'url' => $bestMatch['path'],
                    ]],
                    'confirm_action' => null,
                ];
            }
        }

        // Typo suggestions only for navigation intents
        if ($isNavigationIntent && !$isInformationQuestion) {
            $suggestion = $this->findTypoSuggestion($normalized);
            if ($suggestion) {
                return [
                    'reply' => "Did you mean you want to {$suggestion['intent']}? Reply \"yes\" to continue.",
                    'actions' => [],
                    'confirm_action' => [
                        'label' => 'Open ' . $suggestion['feature']['name'],
                        'url' => $suggestion['feature']['path'],
                        'intent' => $suggestion['intent'],
                    ],
                ];
            }
        }

        return [
            'reply' => "Hi! I'm your AI assistant. You can ask me anything about your EHR workflow.",
            'actions' => [],
            'confirm_action' => null,
        ];
    }

    private function findBestFeatureMatch(string $normalized): ?array
    {
        // Strong intent rules to avoid mixed results (e.g. "write diagnosis for patient" => Diagnostics only).
        $intentRules = [
            ['route' => 'diagnostics.index', 'phrases' => ['write diagnosis', 'diagnosis', 'diagnostics', 'show diagnostics', 'open diagnostics']],
            ['route' => 'patients.create', 'phrases' => ['register patient', 'i want to register patient', 'i want to register', 'register', 'add patient', 'new patient', 'create patient', 'admit patient']],
            ['route' => 'patients.index', 'phrases' => ['patient list', 'patient profile', 'demographic profile', 'find patient']],
            ['route' => 'lab-values.index', 'phrases' => ['lab values', 'lab result', 'cbc', 'laboratory']],
            ['route' => 'vital-signs.show', 'phrases' => ['vital signs', 'vitals', 'blood pressure', 'temperature', 'pulse']],
            ['route' => 'medication-administration', 'phrases' => ['give medication', 'administer medication', 'medication administration']],
            ['route' => 'medication-reconciliation', 'phrases' => ['medication reconciliation', 'reconcile medication']],
            ['route' => 'io.show', 'phrases' => ['intake and output', 'i&o', 'fluid balance']],
            ['route' => 'adl.show', 'phrases' => ['adl', 'activities of daily living']],
            ['route' => 'physical-exam.index', 'phrases' => ['physical exam', 'physical assessment']],
            ['route' => 'medical-history', 'phrases' => ['medical history', 'patient history']],
            ['route' => 'ivs-and-lines', 'phrases' => ['iv', 'ivs and lines', 'intravenous line']],
            ['route' => 'nurse-home', 'phrases' => ['home', 'dashboard']],
        ];

        foreach ($intentRules as $rule) {
            foreach ($rule['phrases'] as $phrase) {
                if (str_contains($normalized, $phrase)) {
                    return $this->featureByRoute($rule['route']);
                }
            }
        }

        $bestFeature = null;
        $bestScore = 0;

        foreach (self::FEATURE_MAP as $feature) {
            $score = 0;
            foreach ($feature['keywords'] as $keyword) {
                $keywordLower = strtolower($keyword);
                if (str_contains($normalized, $keywordLower)) {
                    $score += strlen($keywordLower) >= 8 ? 3 : 1;
                }
            }

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestFeature = $feature;
            }
        }

        return $bestScore > 0 ? $bestFeature : null;
    }

    private function featureByRoute(string $route): ?array
    {
        foreach (self::FEATURE_MAP as $feature) {
            if ($feature['route'] === $route) {
                return $feature;
            }
        }

        return null;
    }

    private function findTypoSuggestion(string $normalized): ?array
    {
        $intentRules = [
            ['route' => 'patients.create', 'intent' => 'register patient', 'phrases' => ['register patient', 'i want to register patient', 'i want to register', 'register', 'add patient', 'new patient', 'create patient', 'admit patient']],
            ['route' => 'diagnostics.index', 'intent' => 'open diagnostics', 'phrases' => ['write diagnosis', 'diagnosis', 'diagnostics', 'show diagnostics', 'open diagnostics']],
            ['route' => 'vital-signs.show', 'intent' => 'open vital signs', 'phrases' => ['vital signs', 'vitals', 'blood pressure', 'temperature', 'pulse']],
            ['route' => 'lab-values.index', 'intent' => 'open lab values', 'phrases' => ['lab values', 'lab result', 'cbc', 'laboratory']],
            ['route' => 'patients.index', 'intent' => 'open demographic profile', 'phrases' => ['patient list', 'patient profile', 'demographic profile', 'find patient']],
        ];

        $messageWords = $this->normalizeWords($normalized);
        if (count($messageWords) === 0) {
            return null;
        }

        foreach ($intentRules as $rule) {
            foreach ($rule['phrases'] as $phrase) {
                if ($this->isApproximatePhraseMatch($messageWords, $this->normalizeWords($phrase))) {
                    $feature = $this->featureByRoute($rule['route']);
                    if ($feature) {
                        return [
                            'intent' => $rule['intent'],
                            'feature' => $feature,
                        ];
                    }
                }
            }
        }

        return null;
    }

    private function normalizeWords(string $text): array
    {
        $clean = preg_replace('/[^a-z0-9\\s]/', ' ', strtolower($text));
        $parts = preg_split('/\\s+/', trim((string) $clean)) ?: [];

        return array_values(array_filter($parts, fn ($word) => $word !== ''));
    }

    private function isApproximatePhraseMatch(array $messageWords, array $phraseWords): bool
    {
        if (count($phraseWords) === 0) {
            return false;
        }

        foreach ($phraseWords as $phraseWord) {
            $matched = false;
            foreach ($messageWords as $messageWord) {
                if ($messageWord === $phraseWord) {
                    $matched = true;
                    break;
                }

                $maxDistance = strlen($phraseWord) >= 7 ? 2 : 1;
                if (levenshtein($messageWord, $phraseWord) <= $maxDistance) {
                    $matched = true;
                    break;
                }
            }

            if (!$matched) {
                return false;
            }
        }

        return true;
    }

    private function searchPatientRecord(string $name): array
    {
        // Search for patient by name (case-insensitive, partial match)
        // Search in first_name, last_name, or middle_name
        $patients = Patient::where(function ($query) use ($name) {
                $query->whereRaw('LOWER(first_name) LIKE ?', ['%' . strtolower($name) . '%'])
                      ->orWhereRaw('LOWER(last_name) LIKE ?', ['%' . strtolower($name) . '%'])
                      ->orWhereRaw('LOWER(middle_name) LIKE ?', ['%' . strtolower($name) . '%']);
            })
            ->limit(5)
            ->get(['patient_id', 'first_name', 'last_name', 'middle_name', 'birthdate', 'sex', 'age']);

        if ($patients->isEmpty()) {
            return [
                'reply' => "There is no record for \"{$name}\". You can check the patient list or register a new patient.",
                'actions' => [
                    [
                        'label' => 'View Patient List',
                        'url' => '/patients',
                    ],
                    [
                        'label' => 'Register New Patient',
                        'url' => '/patients/create',
                    ],
                ],
                'confirm_action' => null,
                'force_offline' => true,
            ];
        }

        if ($patients->count() === 1) {
            $patient = $patients->first();
            $displayName = $patient->name; // Uses getNameAttribute accessor
            $age = $patient->age ?? 'N/A';
            $sex = ucfirst($patient->sex ?? 'N/A');
            
            return [
                'reply' => "Patient record found.\nName: {$displayName}\nSex: {$sex}\nAge: {$age}\n\nTap below to view the full record.",
                'actions' => [[
                    'label' => 'View Patient Record',
                    'url' => route('patients.show', $patient->patient_id),
                ]],
                'confirm_action' => null,
                'force_offline' => true,
            ];
        }

        // Multiple matches found
        $actions = $patients->map(function ($patient) {
            $displayName = $patient->name; // Uses getNameAttribute accessor
            $age = $patient->age ?? 'N/A';
            $sex = ucfirst($patient->sex ?? 'N/A');
            return [
                'label' => "{$displayName} ({$sex}, {$age} yrs)",
                'url' => route('patients.show', $patient->patient_id),
            ];
        })->toArray();

        return [
            'reply' => "Found {$patients->count()} patients matching \"{$name}\". Select one below:",
            'actions' => $actions,
            'confirm_action' => null,
            'force_offline' => true,
        ];
    }

    private function isPatientOfflineIntent(string $normalized): bool
    {
        return str_contains($normalized, 'patient')
            || str_contains($normalized, 'patients')
            || str_contains($normalized, 'record')
            || str_contains($normalized, 'records')
            || str_contains($normalized, 'chart')
            || str_contains($normalized, 'demographic profile');
    }

    private function extractPatientName(string $message): ?string
    {
        $patterns = [
            '/\b(?:record|records)\s+(?:for|of|about)\s+(.+)$/i',
            '/\b(?:patient|patients)\s+(?:named|name is|name|for)\s+(.+)$/i',
            '/\b(?:search|find|lookup|check|show|get)\s+(?:for\s+)?(?:patient|record|records)\s+(.+)$/i',
            '/\bis there\s+(?:a\s+)?(?:patient\s+)?(?:record|records)\s+(?:for|of)\s+(.+)$/i',
            '/\bpatient\s+(.+?)\s+(?:record|records|data|information|details|history)\b/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $message, $matches)) {
                $name = trim((string) ($matches[1] ?? ''));
                $name = preg_replace('/\?+$/', '', $name);
                $name = trim((string) $name);
                if ($name !== '') {
                    return $name;
                }
            }
        }

        return null;
    }

    private function extractImplicitPatientSearchName(string $message): ?string
    {
        $patterns = [
            '/^\s*(?:search|find|lookup|check|show|get)\s+for\s+([a-z][a-z\-\'.\s]{1,60})\s*$/i',
            '/^\s*(?:search|find|lookup|check|show|get)\s+([a-z][a-z\-\'.\s]{1,60})\s*$/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $message, $matches)) {
                $candidate = trim((string) ($matches[1] ?? ''));
                $candidate = preg_replace('/\?+$/', '', $candidate);
                $candidate = trim((string) $candidate);
                if ($candidate === '') {
                    continue;
                }

                // Ignore obvious non-name search terms.
                $blocked = [
                    'diagnostics', 'diagnostic', 'labs', 'lab', 'vitals', 'vital signs',
                    'medical history', 'intake and output', 'adl', 'home', 'dashboard',
                    'page', 'route', 'feature', 'patient', 'record', 'records',
                ];

                $candidateLower = strtolower($candidate);
                if (in_array($candidateLower, $blocked, true)) {
                    return null;
                }

                return $candidate;
            }
        }

        return null;
    }

    private function featureHelpResponse(string $normalized): ?array
    {
        $isHowTo = str_contains($normalized, 'how to')
            || str_contains($normalized, 'how do i')
            || str_contains($normalized, 'how can i')
            || str_contains($normalized, 'guide')
            || str_contains($normalized, 'steps')
            || str_contains($normalized, 'help me use')
            || str_contains($normalized, 'how to use');

        if (!$isHowTo) {
            return null;
        }

        $feature = $this->findBestFeatureMatch($normalized);
        if (!$feature) {
            return null;
        }

        $guide = self::FEATURE_GUIDES[$feature['route']] ?? null;
        if (!$guide) {
            return null;
        }

        return [
            'reply' => "How to use {$feature['name']}:\n{$guide}",
            'actions' => [[
                'label' => 'Open ' . $feature['name'],
                'url' => $feature['path'],
            ]],
            'confirm_action' => null,
        ];
    }
}
