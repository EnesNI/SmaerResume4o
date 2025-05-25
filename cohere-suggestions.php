<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Get the input data
$input = json_decode(file_get_contents('php://input'), true);
$text = $input['text'] ?? '';
$section = $input['section'] ?? '';

// Your Cohere API key (replace with your actual key)
$cohere_api_key = 'YOUR_COHERE_API_KEY_HERE';

if (empty($cohere_api_key) || $cohere_api_key === 'YOUR_COHERE_API_KEY_HERE') {
    // Return mock suggestions if no API key is set
    echo json_encode([
        'success' => true,
        'suggestions' => getMockSuggestions($text, $section)
    ]);
    exit;
}

function getCohereSuggestions($text, $section, $api_key) {
    $prompt = generatePrompt($text, $section);
    
    $data = [
        'model' => 'command-r-plus',
        'prompt' => $prompt,
        'max_tokens' => 500,
        'temperature' => 0.7,
        'k' => 0,
        'stop_sequences' => [],
        'return_likelihoods' => 'NONE'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.cohere.ai/v1/generate');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $api_key,
        'Content-Type: application/json',
        'Cohere-Version: 2022-12-06'
    ]);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code === 200) {
        $result = json_decode($response, true);
        return parseCohereSuggestions($result['generations'][0]['text']);
    } else {
        throw new Exception('Cohere API error: ' . $response);
    }
}

function generatePrompt($text, $section) {
    $section_context = getSectionContext($section);
    
    return "You are a professional resume writing expert. Analyze the following {$section_context} text from a resume and provide 2-3 specific improvement suggestions.

Original text: \"{$text}\"

For each suggestion, provide:
1. A clear title describing the improvement
2. A brief explanation of why this improvement helps
3. The improved version of the text

Format your response as JSON with this structure:
[
  {
    \"title\": \"Improvement title\",
    \"description\": \"Why this helps\",
    \"improved_text\": \"The improved version\"
  }
]

Focus on making the text more impactful, specific, and quantifiable. Use strong action verbs and include metrics where possible.";
}

function getSectionContext($section) {
    $contexts = [
        'name' => 'name',
        'title' => 'job title',
        'contact' => 'contact information',
        'summary' => 'professional summary',
        'job-title' => 'job title',
        'company' => 'company information',
        'job-description' => 'job description',
        'education' => 'education',
        'summary-title' => 'section heading',
        'experience-title' => 'section heading',
        'education-title' => 'section heading'
    ];
    
    return $contexts[$section] ?? 'resume section';
}

function parseCohereSuggestions($response_text) {
    // Try to extract JSON from the response
    $json_start = strpos($response_text, '[');
    $json_end = strrpos($response_text, ']') + 1;
    
    if ($json_start !== false && $json_end !== false) {
        $json_text = substr($response_text, $json_start, $json_end - $json_start);
        $suggestions = json_decode($json_text, true);
        
        if ($suggestions && is_array($suggestions)) {
            return $suggestions;
        }
    }
    
    // Fallback to mock suggestions if parsing fails
    return getMockSuggestions('', '');
}

function getMockSuggestions($text, $section) {
    $suggestions = [];
    
    switch ($section) {
        case 'summary':
            $suggestions = [
                [
                    'title' => 'Add Quantifiable Achievements',
                    'description' => 'Include specific numbers and metrics to demonstrate your impact and make your summary more compelling.',
                    'improved_text' => 'Experienced Software Engineer with 5+ years of expertise in full-stack development, successfully delivering 15+ web applications and improving system performance by 40%.'
                ],
                [
                    'title' => 'Highlight Key Technologies',
                    'description' => 'Mention your core technical skills early to catch recruiters\' attention and pass ATS filters.',
                    'improved_text' => 'Experienced Software Engineer with 5+ years of expertise in full-stack development using React, Node.js, and AWS, with a proven track record of building scalable applications.'
                ]
            ];
            break;
            
        case 'job-description':
            $suggestions = [
                [
                    'title' => 'Use Strong Action Verbs',
                    'description' => 'Start with powerful action verbs to make your achievements more impactful and engaging.',
                    'improved_text' => 'Architected and implemented microservices architecture serving 1M+ users, resulting in 50% improved scalability and 30% reduced response times.'
                ],
                [
                    'title' => 'Add Business Impact',
                    'description' => 'Connect your technical work to business outcomes to show your value to potential employers.',
                    'improved_text' => 'Led development of microservices architecture serving 1M+ users, contributing to 25% increase in user engagement and $2M annual revenue growth.'
                ]
            ];
            break;
            
        case 'contact':
            $suggestions = [
                [
                    'title' => 'Add Professional Portfolio',
                    'description' => 'Include a link to your portfolio or GitHub to showcase your work and technical skills.',
                    'improved_text' => 'john.smith@email.com | +1 (555) 123-4567 | San Francisco, CA | linkedin.com/in/johnsmith | github.com/johnsmith | portfolio.johnsmith.dev'
                ]
            ];
            break;
            
        default:
            $suggestions = [
                [
                    'title' => 'Improve Content',
                    'description' => 'Make this section more specific and impactful to better showcase your qualifications.',
                    'improved_text' => 'Enhanced version: ' . $text . ' with measurable results and specific achievements.'
                ]
            ];
    }
    
    return $suggestions;
}

try {
    if (empty($text)) {
        throw new Exception('No text provided');
    }
    
    $suggestions = getCohereSuggestions($text, $section, $cohere_api_key);
    
    echo json_encode([
        'success' => true,
        'suggestions' => $suggestions
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'suggestions' => getMockSuggestions($text, $section)
    ]);
}
?>
