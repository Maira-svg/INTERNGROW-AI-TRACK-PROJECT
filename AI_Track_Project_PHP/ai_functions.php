<?php
/**
 * ai_functions.php
 * ------------------------------------------------------------
 * All 3 AI features implemented in pure PHP (no Python needed).
 * ------------------------------------------------------------
 */

// ==============================================================
// TASK 1: Translation (calls Google's free translate endpoint directly)
// ==============================================================
function ai_translate($text, $source, $target) {
    $text = trim($text);
    if ($text === '') {
        return ['error' => 'No text provided'];
    }

    $url = "https://translate.googleapis.com/translate_a/single?" . http_build_query([
        'client' => 'gtx',
        'sl'     => $source,
        'tl'     => $target,
        'dt'     => 't',
        'q'      => $text
    ]);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr  = curl_error($ch);
    curl_close($ch);

    if ($response === false) {
        return ['error' => 'Could not reach translation service: ' . $curlErr];
    }
    if ($httpCode != 200) {
        return ['error' => 'Translation API failed (HTTP ' . $httpCode . ')'];
    }

    $decoded = json_decode($response, true);
    if (!$decoded || !isset($decoded[0])) {
        return ['error' => 'Unexpected response from translation service'];
    }

    $translation = '';
    foreach ($decoded[0] as $part) {
        if (isset($part[0])) {
            $translation .= $part[0];
        }
    }

    return ['translation' => $translation, 'source' => $source, 'target' => $target];
}


// ==============================================================
// TASK 2: FAQ Chatbot — real TF-IDF + cosine similarity, in PHP
// ==============================================================
class RAGChatbotPHP {
    private $faqData;
    private $questions;
    private $docTermFreqs = [];
    private $idf = [];
    private $vocab = [];

    public function __construct() {
        $this->faqData = [
            "What is your return policy?" => "You can return items within 30 days of purchase.",
            "How do I track my order?" => "You will receive a tracking link via email within 24 hours.",
            "What payment methods do you accept?" => "We accept all major credit cards, PayPal, and bank transfers.",
            "How can I contact support?" => "Email us at support@example.com or call +1-800-123-4567.",
            "Do you offer international shipping?" => "Yes, we ship to over 100 countries.",
            "What is your warranty policy?" => "1-year manufacturer warranty against defects.",
            "Can I cancel my order?" => "Yes, cancel within 2 hours for a full refund.",
            "How long does shipping take?" => "Domestic: 3-5 days, International: 7-14 days.",
            "Do you have a mobile app?" => "Yes, available on both iOS and Android app stores.",
            "How do I reset my password?" => "Click 'Forgot Password' on the login page and follow the email link."
        ];
        $this->questions = array_keys($this->faqData);
        $this->buildIndex();
    }

    private function tokenize($text) {
        $text = strtolower($text);
        $text = preg_replace('/[^a-z0-9\s]/', ' ', $text);
        $tokens = preg_split('/\s+/', trim($text));
        return array_filter($tokens, fn($t) => $t !== '');
    }

    private function buildIndex() {
        $docCount = count($this->questions);
        $docFreq = []; // how many docs contain a term

        foreach ($this->questions as $i => $q) {
            $tokens = $this->tokenize($q);
            $tf = [];
            foreach ($tokens as $t) {
                $tf[$t] = ($tf[$t] ?? 0) + 1;
                $this->vocab[$t] = true;
            }
            $this->docTermFreqs[$i] = $tf;
            foreach (array_keys($tf) as $t) {
                $docFreq[$t] = ($docFreq[$t] ?? 0) + 1;
            }
        }

        foreach (array_keys($this->vocab) as $t) {
            // smoothed idf
            $this->idf[$t] = log((1 + $docCount) / (1 + $docFreq[$t])) + 1;
        }
    }

    private function vectorize($tf) {
        $vec = [];
        foreach ($tf as $term => $count) {
            $vec[$term] = $count * ($this->idf[$term] ?? 0);
        }
        return $vec;
    }

    private function cosineSim($vecA, $vecB) {
        $dot = 0.0;
        foreach ($vecA as $term => $val) {
            if (isset($vecB[$term])) {
                $dot += $val * $vecB[$term];
            }
        }
        $normA = sqrt(array_sum(array_map(fn($v) => $v * $v, $vecA)));
        $normB = sqrt(array_sum(array_map(fn($v) => $v * $v, $vecB)));
        if ($normA == 0 || $normB == 0) return 0.0;
        return $dot / ($normA * $normB);
    }

    public function getResponse($question) {
        $qTokens = $this->tokenize($question);
        $qtf = [];
        foreach ($qTokens as $t) {
            $qtf[$t] = ($qtf[$t] ?? 0) + 1;
        }
        $qVec = $this->vectorize($qtf);

        $bestScore = -1;
        $bestIdx = -1;
        foreach ($this->docTermFreqs as $i => $tf) {
            $docVec = $this->vectorize($tf);
            $score = $this->cosineSim($qVec, $docVec);
            if ($score > $bestScore) {
                $bestScore = $score;
                $bestIdx = $i;
            }
        }

        if ($bestScore > 0.25 && $bestIdx >= 0) {
            $matched = $this->questions[$bestIdx];
            return [
                'answer' => $this->faqData[$matched],
                'matched_question' => $matched,
                'score' => round($bestScore * 100, 2)
            ];
        }

        return [
            'answer' => "I couldn't find a specific answer to that. Please contact support@example.com.",
            'matched_question' => null,
            'score' => round(max($bestScore, 0) * 100, 2)
        ];
    }
}


// ==============================================================
// TASK 3: AI Music Generator (rule-based composition assistant)
// ==============================================================
function ai_generate_music($genre, $tempo, $mood) {
    $notes = ['C4', 'D4', 'E4', 'F4', 'G4', 'A4', 'B4', 'C5'];
    shuffle($notes);
    $melody = implode(' - ', array_slice($notes, 0, 8));

    $styles = [
        'classical' => 'Piano Sonata', 'jazz' => 'Modal Jazz',
        'electronic' => 'Techno', 'rock' => 'Classic Rock', 'ambient' => 'Drone'
    ];
    $elements = [
        'happy' => 'major key, upbeat tempo', 'sad' => 'minor key, slow tempo',
        'energetic' => 'fast tempo, strong rhythm', 'calm' => 'slow tempo, soft dynamics',
        'mysterious' => 'unusual scales, unexpected harmonies'
    ];
    $progressions = [
        'classical' => ['I-IV-V-I', 'I-vi-IV-V'],
        'jazz' => ['ii-V-I', 'I-vi-ii-V'],
        'electronic' => ['I-V-vi-IV', 'vi-IV-I-V'],
        'rock' => ['I-IV-V', 'I-V-vi-IV'],
        'ambient' => ['I-iii', 'I-IV']
    ];

    $chordOptions = $progressions[$genre] ?? ['I-IV-V'];

    return [
        'genre' => $genre,
        'tempo' => $tempo,
        'mood' => $mood,
        'suggested_style' => $styles[$genre] ?? 'Fusion',
        'musical_elements' => $elements[$mood] ?? 'neutral',
        'chord_progression' => $chordOptions[array_rand($chordOptions)],
        'melody_notes' => $melody,
        'midi_data' => base64_encode("MIDI for $genre")
    ];
}
