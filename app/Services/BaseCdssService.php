<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Yaml\Yaml;

class BaseCdssService
{
    const CRITICAL = 'CRITICAL';
    const WARNING = 'WARNING';
    const INFO = 'INFO';
    const NONE = 'NONE';

    protected $rules = [];
    protected $rulesDirectoryName;
    protected $shouldTranslate = true;
    
    // FAST PATH: Static cache and request-scoped language detection
    protected static $translationCache = [];
    protected static $lastDetectedLang = 'en';

    // GLOBAL RED FLAGS: Universal medical emergencies (The "Catch-All")
    protected $globalRedFlags = [
        'emergency', 'urgent', 'critical', 'dying', 'death', 'dead', 'arrest', 'unconscious', 'unresponsive', 
        'bleeding', 'hemorrhage', 'choking', 'asphyxia', 'seizure', 'convulsion', 'cyanosis', 'shock', 
        'severe', 'extreme', 'worst', 'intolerable', 'agony', 'excruciating', 'suicide', 'self-harm'
    ];

    protected $stopWords = [
        'and', 'if', 'with', 'the', 'a', 'an', 'is', 'at', 'by', 'for', 'from', 'in', 'of', 'on', 'to', 'up', 'down', 'it', 'was', 'this', 'that', 'they', 'their', 'which', 'who', 'whom', 'whose', 'where', 'when', 'why', 'how', 'all', 'any', 'both', 'each', 'few', 'more', 'most', 'other', 'some', 'such', 'no', 'nor', 'not', 'only', 'own', 'same', 'so', 'than', 'too', 'very', 'can', 'will', 'just', 'should', 'now'
    ];

    protected $synonyms = [
        'hr' => 'heart rate', 'pulse' => 'heart rate', 'bpm' => 'heart rate',
        'rr' => 'respiratory rate', 'breathing' => 'respiratory rate',
        'bp' => 'blood pressure', 'spo2' => 'oxygen saturation', 'o2' => 'oxygen',
        'temp' => 'temperature', 'febrile' => 'fever', 'pain' => 'ache',
        'swelling' => 'edema', 'blue' => 'cyanosis', 'yellow' => 'jaundice',
        'pale' => 'pallor', 'difficulty' => 'hard', 'shortness' => 'dyspnea'
    ];

    public function __construct(string $rulesDirectoryName = '')
    {
        $this->rulesDirectoryName = $rulesDirectoryName;
        if (!empty($rulesDirectoryName)) {
            $this->loadRules();
        }
    }

    protected function loadRules()
    {
        $this->rules = [];
        $rulesDirectory = storage_path('app/private/' . $this->rulesDirectoryName);
        if (!File::isDirectory($rulesDirectory)) return;

        $allRules = [];
        $files = File::files($rulesDirectory);
        foreach ($files as $file) {
            if (in_array($file->getExtension(), ['yaml', 'yml'])) {
                try {
                    $parsedYaml = Yaml::parseFile($file->getPathname());
                    if (is_array($parsedYaml)) {
                        foreach ($parsedYaml as $key => $value) {
                            if (!isset($allRules[$key])) $allRules[$key] = [];
                            if (is_numeric($key)) $allRules[] = $value;
                            else $allRules[$key] = array_merge($allRules[$key], (array)$value);
                        }
                    }
                } catch (\Exception $e) {
                    Log::error("CDSS: Failed to parse YAML file: " . $file->getPathname());
                }
            }
        }
        $this->rules = $allRules;
    }

    /**
     * ADVANCED: Checks for universal "Red Flags" before specific rules.
     */
    protected function checkGlobalRedFlags($tokens)
    {
        $matches = array_intersect($tokens, $this->globalRedFlags);
        if (!empty($matches)) {
            $msg = "URGENT: General red-flag indicators detected (" . implode(', ', $matches) . "). Immediate clinical review required.";
            return [
                'alert' => $this->translateFinalAlert($msg),
                'severity' => self::CRITICAL,
                'severity_str' => 'CRITICAL'
            ];
        }
        return null;
    }

    /**
     * Translates input and stores the detected language.
     */
    public function translateAndDetect($text)
    {
        if (!$this->shouldTranslate) return $text;

        $text = trim($text);
        if (empty($text)) return $text;
        
        $cacheKey = 'detect_' . md5($text);
        if (isset(self::$translationCache[$cacheKey])) {
            self::$lastDetectedLang = self::$translationCache[$cacheKey]['lang'];
            return self::$translationCache[$cacheKey]['text'];
        }

        try {
            $response = Http::timeout(5)->get('https://translate.googleapis.com/translate_a/single', [
                'client' => 'gtx', 'sl' => 'auto', 'tl' => 'en', 'dt' => 't', 'q' => $text
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $detectedLang = $data[2] ?? 'en';
                
                $translatedText = '';
                if (isset($data[0]) && is_array($data[0])) {
                    foreach ($data[0] as $part) {
                        $translatedText .= $part[0] ?? '';
                    }
                }

                $translatedText = !empty($translatedText) ? $translatedText : $text;
                
                self::$lastDetectedLang = $detectedLang;
                self::$translationCache[$cacheKey] = ['text' => $translatedText, 'lang' => $detectedLang];
                return $translatedText;
            }
        } catch (\Exception $e) {
            Log::warning("CDSS Translation: API failed. Using original text.");
        }

        self::$lastDetectedLang = 'en';
        return $text;
    }

    /**
     * Translates the FINAL alert back to source language.
     */
    public function translateFinalAlert($text)
    {
        if (!$this->shouldTranslate || self::$lastDetectedLang === 'en' || empty($text) || $text === 'No Findings') return $text;

        $cacheKey = self::$lastDetectedLang . '_' . md5($text);
        if (isset(self::$translationCache[$cacheKey])) return self::$translationCache[$cacheKey];

        try {
            $response = Http::timeout(8)->get('https://translate.googleapis.com/translate_a/single', [
                'client' => 'gtx', 'sl' => 'en', 'tl' => self::$lastDetectedLang, 'dt' => 't', 'q' => $text
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $translated = '';
                if (isset($data[0]) && is_array($data[0])) {
                    foreach ($data[0] as $part) {
                        $translated .= $part[0] ?? '';
                    }
                }
                $translated = !empty($translated) ? $translated : $text;
                self::$translationCache[$cacheKey] = $translated;
                return $translated;
            }
        } catch (\Exception $e) {}

        return $text;
    }

    protected function robustTokenize($text)
    {
        $text = strtolower($text);
        foreach ($this->synonyms as $short => $full) {
            $text = preg_replace("/\b$short\b/", $full, $text);
        }
        $text = preg_replace('/[^\p{L}\p{N}\s-]/u', ' ', $text);
        $tokens = preg_split('/\s+/', trim($text));
        $processed = [];
        foreach ($tokens as $word) {
            if (in_array($word, $this->stopWords)) continue;
            $stemmed = $word;
            if (strlen($word) > 4) {
                if (str_ends_with($word, 'ing')) $stemmed = substr($word, 0, -3);
                elseif (str_ends_with($word, 'ed')) $stemmed = substr($word, 0, -2);
                elseif (str_ends_with($word, 'ly')) $stemmed = substr($word, 0, -2);
                elseif (str_ends_with($word, 'es')) $stemmed = substr($word, 0, -2);
                elseif (str_ends_with($word, 's') && !str_ends_with($word, 'ss')) $stemmed = substr($word, 0, -1);
            }
            $processed[] = $stemmed;
        }
        return array_unique(array_filter($processed));
    }

    public function runAnalysis($finding, $rules)
    {
        if (empty(trim($finding))) return null;

        $findingEnglish = $this->translateAndDetect($finding);
        $findingTokens = $this->robustTokenize($findingEnglish);
        
        // 1. GLOBAL RED FLAG CHECK
        $globalAlert = $this->checkGlobalRedFlags($findingTokens);
        
        $matchedRules = [];
        $negationWords = ['no', 'denies', 'without', 'not', 'none', 'absent'];

        foreach ((array) $rules as $rule) {
            $totalScore = 0;
            if (!isset($rule['keywords'])) continue;

            foreach ($rule['keywords'] as $keywordPhrase) {
                if (stripos($findingEnglish, $keywordPhrase) !== false) $totalScore += 200; 
                $keywordTokens = $this->robustTokenize($keywordPhrase);
                $intersect = array_intersect($keywordTokens, $findingTokens);
                if (!empty($intersect)) {
                    $overlapPercentage = count($intersect) / count($keywordTokens);
                    $totalScore += ($overlapPercentage * 100);
                }
            }

            if ($totalScore > 0) {
                foreach ($negationWords as $neg) {
                    if (stripos($findingEnglish, $neg) !== false) {
                        foreach ($rule['keywords'] as $kw) {
                            if (stripos($findingEnglish, $neg . " " . $kw) !== false) {
                                $totalScore = 0;
                                break 2;
                            }
                        }
                    }
                }

                if ($totalScore > 0) {
                    $matchedRules[] = [
                        'score' => $totalScore,
                        'severity' => $this->getSeverityValue($rule['severity'] ?? 'INFO'),
                        'alert' => $rule['alert'], 
                        'severity_str' => strtoupper($rule['severity'] ?? 'INFO')
                    ];
                }
            }
        }

        // 2. DECISION LOGIC
        if (empty($matchedRules)) {
            if ($globalAlert) return $globalAlert;

            // If input is long (descriptive) but no rules matched, flag for human review
            if (count($findingTokens) > 3) {
                $msg = $this->translateFinalAlert("Descriptive finding entered. Review manually for potential abnormalities.");
                return ['alert' => $msg, 'severity' => self::INFO];
            }

            return ['alert' => $this->translateFinalAlert('No Findings'), 'severity' => self::NONE];
        }

        usort($matchedRules, function ($a, $b) {
            if ($a['severity'] !== $b['severity']) return $b['severity'] <=> $a['severity'];
            return $b['score'] <=> $a['score'];
        });

        // If global flag is CRITICAL, prioritize it over non-critical specific rules
        if ($globalAlert && $globalAlert['severity'] > $matchedRules[0]['severity']) {
            return $globalAlert;
        }

        return [
            'alert' => $matchedRules[0]['alert'],
            'severity' => $matchedRules[0]['severity_str']
        ];
    }

    protected function getSeverityValue($severityStr)
    {
        return match (strtolower($severityStr)) {
            'critical' => 4, 'warning' => 3, 'info' => 2, 'none' => 1, default => 0,
        };
    }
}
