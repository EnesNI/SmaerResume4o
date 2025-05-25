<?php
require_once 'autoload.php';
use Smalot\PdfParser\Parser;

function extractTextFromResume($filePath, $fileType) {
    if ($fileType === 'pdf') {
        $parser = new Parser();
        $pdf = $parser->parseFile($filePath);
        return $pdf->getText();
    } elseif ($fileType === 'txt') {
        return file_get_contents($filePath);
    } else {
        return '';
    }
}

function calculateJobMatch($resumeText, $jobText) {
    $resumeWords = array_count_values(str_word_count(strtolower($resumeText), 1));
    $jobWords = array_unique(str_word_count(strtolower($jobText), 1));

    $matches = 0;
    foreach ($jobWords as $word) {
        if (isset($resumeWords[$word])) {
            $matches++;
        }
    }

    $score = ($matches / count($jobWords)) * 100;
    return round($score);
}

function detectAIContent($text) {
    $aiIndicators = [
        "passionate", "dedicated professional", "proven track record",
        "results-driven", "strong communication skills", "leveraged", 
        "utilized", "accomplished", "synergy", "empowered", "dynamic individual"
    ];

    $count = 0;
    foreach ($aiIndicators as $phrase) {
        if (stripos($text, $phrase) !== false) {
            $count++;
        }
    }

    $probability = min(100, $count * 10);
    return $probability; // return as percentage
}

function getSimilarityScore($resumeText, $jobDescription, $apiKey)
{
    $url = "https://api.cohere.ai/v1/embed";
    $headers = [
        "Authorization: Bearer $apiKey",
        "Content-Type: application/json",
        "Cohere-Version: 2022-12-06"
    ];

    $data = [
        "texts" => [$resumeText, $jobDescription],
        "model" => "embed-english-v3.0",
        "input_type" => "search_document"
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);

    if (!isset($result['embeddings']) || count($result['embeddings']) !== 2) {
        return null; // Error
    }

    // Cosine similarity calculation
    $a = $result['embeddings'][0];
    $b = $result['embeddings'][1];
    $dotProduct = 0;
    $normA = 0;
    $normB = 0;

    for ($i = 0; $i < count($a); $i++) {
        $dotProduct += $a[$i] * $b[$i];
        $normA += $a[$i] * $a[$i];
        $normB += $b[$i] * $b[$i];
    }

    $similarity = $dotProduct / (sqrt($normA) * sqrt($normB));
    return round($similarity * 100); // Return percentage
}

function analyzeWithAI($resumeText, $jobDescription) {
    $apiKey = 'fHtGUtc0nPqPdizte9rFayjFCQxZGqjrhkE4NQdh';

    $prompt = "Analyze the following resume and job description. Provide insights about skills, match quality, and suggestions.\n\nResume:\n$resumeText\n\nJob Description:\n$jobDescription";

    $data = [
        "model" => "command",  // or "command-xlarge-nightly" if available
        "prompt" => $prompt,
        "max_tokens" => 500,
        "temperature" => 0.7,
        "k" => 0,
        "stop_sequences" => ["--"],
        "return_likelihoods" => "NONE"
    ];

    $curl = curl_init("https://api.cohere.ai/v1/generate");
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $apiKey",
        "Content-Type: application/json",
        "Cohere-Version: 2022-12-06"
    ]);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($curl);

    if (curl_errno($curl)) {
        $error_msg = curl_error($curl);
        curl_close($curl);
        return "Curl error: $error_msg";
    }

    $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    if ($http_status !== 200) {
        return "API request failed with status $http_status. Response: $response";
    }

    $result = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        return "Failed to parse JSON response. Raw response: $response";
    }

    if (isset($result['generations'][0]['text'])) {
        return trim($result['generations'][0]['text']);
    } else {
        return "AI analysis failed: Unexpected response structure. Raw response: " . $response;
    }
}

function getEditingSuggestions($resumeText, $jobDescription, $matchScore, $aiScore) {
    $suggestions = [];
    
    // Analyze missing keywords
    $resumeWords = array_count_values(str_word_count(strtolower($resumeText), 1));
    $jobWords = array_unique(str_word_count(strtolower($jobDescription), 1));
    
    $missing = [];
    foreach ($jobWords as $word) {
        if (!isset($resumeWords[$word]) && strlen($word) > 3) {
            $missing[] = $word;
        }
    }

    // Suggestion 1: Keywords
    if (count($missing) > 5) {
        $topMissing = array_slice($missing, 0, 5);
        $suggestions[] = [
            'title' => 'Add Missing Keywords',
            'description' => 'Include these important keywords from the job description: ' . implode(', ', $topMissing) . '. Integrate them naturally into your experience descriptions and skills section.'
        ];
    }

    // Suggestion 2: Match Score
    if (is_numeric($matchScore) && $matchScore < 60) {
        $suggestions[] = [
            'title' => 'Improve Job Relevance',
            'description' => 'Your resume has a ' . $matchScore . '% match. Focus on highlighting experiences and skills that directly relate to the job requirements. Rewrite bullet points to emphasize relevant achievements.'
        ];
    }

    // Suggestion 3: AI Content
    if ($aiScore > 50) {
        $suggestions[] = [
            'title' => 'Make Content More Personal',
            'description' => 'Your resume shows signs of AI-generated content (' . $aiScore . '% probability). Replace generic phrases with specific, personal achievements and use more varied language to sound more authentic.'
        ];
    }

    // Suggestion 4: Skills Section
    $jobSkills = extractSkillsFromJob($jobDescription);
    if (!empty($jobSkills)) {
        $suggestions[] = [
            'title' => 'Update Skills Section',
            'description' => 'Add these relevant skills mentioned in the job posting: ' . implode(', ', array_slice($jobSkills, 0, 6)) . '. Remove outdated or irrelevant skills to make room.'
        ];
    }

    // Suggestion 5: Quantify Achievements
    if (!preg_match('/\d+%|\$\d+|\d+\+/', $resumeText)) {
        $suggestions[] = [
            'title' => 'Add Quantifiable Results',
            'description' => 'Include specific numbers, percentages, or dollar amounts to demonstrate your impact. For example: "Increased sales by 25%" or "Managed a team of 10 people".'
        ];
    }

    // Suggestion 6: Action Verbs
    $weakVerbs = ['responsible for', 'worked on', 'helped with', 'involved in'];
    $hasWeakVerbs = false;
    foreach ($weakVerbs as $verb) {
        if (stripos($resumeText, $verb) !== false) {
            $hasWeakVerbs = true;
            break;
        }
    }
    
    if ($hasWeakVerbs) {
        $suggestions[] = [
            'title' => 'Use Stronger Action Verbs',
            'description' => 'Replace weak phrases like "responsible for" and "worked on" with powerful action verbs like "led", "developed", "implemented", "optimized", or "achieved".'
        ];
    }

    // Suggestion 7: Format and Length
    $wordCount = str_word_count($resumeText);
    if ($wordCount < 200) {
        $suggestions[] = [
            'title' => 'Expand Content',
            'description' => 'Your resume seems quite brief (' . $wordCount . ' words). Add more detail about your accomplishments, projects, and relevant experiences to better showcase your qualifications.'
        ];
    } elseif ($wordCount > 800) {
        $suggestions[] = [
            'title' => 'Condense Content',
            'description' => 'Your resume is quite lengthy (' . $wordCount . ' words). Focus on the most relevant experiences and achievements. Remove outdated or less relevant information.'
        ];
    }

    // If no specific suggestions, add general ones
    if (empty($suggestions)) {
        $suggestions[] = [
            'title' => 'Optimize for ATS',
            'description' => 'Use standard section headings like "Experience", "Education", and "Skills". Avoid graphics, tables, or unusual formatting that might confuse applicant tracking systems.'
        ];
        
        $suggestions[] = [
            'title' => 'Tailor for This Role',
            'description' => 'Customize your resume further by emphasizing experiences and skills that directly match this specific job posting. Consider reordering sections to highlight the most relevant information first.'
        ];
    }

    return array_slice($suggestions, 0, 6); // Return max 6 suggestions
}

function extractSkillsFromJob($jobDescription) {
    $commonSkills = [
        'python', 'java', 'javascript', 'react', 'node.js', 'sql', 'aws', 'docker', 
        'kubernetes', 'git', 'agile', 'scrum', 'project management', 'leadership',
        'communication', 'teamwork', 'problem solving', 'analytical', 'excel',
        'powerpoint', 'salesforce', 'marketing', 'seo', 'social media', 'content',
        'design', 'photoshop', 'illustrator', 'figma', 'ui/ux', 'html', 'css',
        'machine learning', 'data analysis', 'statistics', 'tableau', 'power bi'
    ];
    
    $foundSkills = [];
    $jobLower = strtolower($jobDescription);
    
    foreach ($commonSkills as $skill) {
        if (strpos($jobLower, strtolower($skill)) !== false) {
            $foundSkills[] = $skill;
        }
    }
    
    return array_unique($foundSkills);
}
