<?php
// chatbot_backend.php
header('Content-Type: application/json');

// Enable error reporting for debugging (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 0); // Set to 1 for debugging, 0 for production

// Environment configuration
$host = getenv('DB_HOST') ?: "localhost";
$user = getenv('DB_USER') ?: "root";
$pass = getenv('DB_PASS') ?: "";
$db = getenv('DB_NAME') ?: "ctunav";

// Session management for context and rate limiting
session_start();
if (!isset($_SESSION['chat_context'])) {
    $_SESSION['chat_context'] = [];
}

// Rate limiting - 2 seconds between requests
if (isset($_SESSION['last_request'])) {
    $elapsed = time() - $_SESSION['last_request'];
    if ($elapsed < 2) {
        die(json_encode([
            "reply" => "Please wait a moment before sending another message.",
            "links" => []
        ]));
    }
}
$_SESSION['last_request'] = time();

// Get user message with sanitization
$userMessage = isset($_POST['message']) ? trim($_POST['message']) : '';
$userMessage = filter_var($userMessage, FILTER_SANITIZE_STRING);
$userMessage = htmlspecialchars($userMessage, ENT_QUOTES, 'UTF-8');
$originalMessage = $userMessage;
$userMessage = strtolower($userMessage);

if ($userMessage === '') {
    echo json_encode([
        "reply" => "Hello! I'm CTU's virtual assistant. How can I help you today?",
        "links" => []
    ]);
    exit;
}

// Greeting detection
if (preg_match('/\b(hi|hello|hey|good morning|good afternoon|greetings|howdy)\b/', $userMessage)) {
    echo json_encode([
        "reply" => "Hello! I'm CTU's virtual assistant. How can I help you today?",
        "links" => []
    ]);
    exit;
}

// Thank you detection
if (preg_match('/\b(thank|thanks|appreciate|grateful|thx|ty)\b/', $userMessage)) {
    echo json_encode([
        "reply" => "You're welcome! Is there anything else I can help you with?",
        "links" => []
    ]);
    exit;
}

// Farewell detection
if (preg_match('/\b(bye|goodbye|see you|farewell|exit|quit|stop)\b/', $userMessage)) {
    echo json_encode([
        "reply" => "Thank you for chatting with me. Have a great day!",
        "links" => []
    ]);
    exit;
}

// Check for unreadable or gibberish input
if (isGibberish($userMessage)) {
    echo json_encode([
        "reply" => "I'm having trouble understanding your message. Could you please rephrase your question in English?",
        "links" => []
    ]);
    exit;
}

// Extract keywords from user message
$keywords = extractKeywords($userMessage);

// Check if input is too vague or has insufficient keywords
if (count($keywords) === 0 || isTooVague($userMessage, $keywords)) {
    echo json_encode([
        "reply" => "I'm not sure what you're asking about. Could you please provide more details?",
        "links" => []
    ]);
    exit;
}

// Try to find the most relevant FAQ using multiple approaches
$response = findBestResponse($userMessage, $keywords, $originalMessage);

echo json_encode($response);
exit;

// Function to detect gibberish or unreadable input
function isGibberish($message) {
    // Remove common greetings and simple words
    $cleanMessage = preg_replace('/\b(hi|hello|please|help|me|my|the|a|an|and|or|is|are|was|were|what|how|to|do|can|could|will|would|should|for|of|in|on|at|with|ct|ctu|university)\b/', '', $message);
    $cleanMessage = trim($cleanMessage);
    
    // If after removing common words, there's very little left, it's not gibberish
    if (strlen($cleanMessage) < 3) return false;
    
    // Check for excessive repetition of characters (e.g., "aaaaaaa")
    if (preg_match('/(.)\1{4,}/', $message)) return true;
    
    // Check for non-alphanumeric characters dominance
    $alphaChars = preg_replace('/[^a-z]/', '', $message);
    if (strlen($alphaChars) / strlen($message) < 0.4) return true;
    
    // Check for random keyboard mashing patterns
    $commonMistakes = ['qwerty', 'asdfgh', 'zxcvbn', '123456', 'lkjhgf', 'mnbvcx'];
    foreach ($commonMistakes as $pattern) {
        if (strpos($message, $pattern) !== false) return true;
    }
    
    // Check for minimum meaningful word count
    $words = str_word_count($message);
    if ($words < 2 && strlen($message) > 15) return true;
    
    // Check for vowel-consonant ratio (gibberish often has poor ratio)
    if (preg_match_all('/[aeiou]/i', $message) < strlen($message)/6) return true;
    
    return false;
}

// Function to check if input is too vague
function isTooVague($message, $keywords) {
    // Very short messages are likely vague
    if (strlen(trim($message)) < 4) return true;
    
    // Messages with only very common words are vague
    $commonWords = ['help', 'question', 'problem', 'issue', 'need', 'want', 'tell', 'give', 'info', 'information'];
    $uniqueKeywords = array_diff($keywords, $commonWords);
    
    if (count($uniqueKeywords) === 0) return true;
    
    // Check if message is a question without specific content
    $isQuestion = preg_match('/\?|what|how|when|where|why|who|which/', $message);
    if ($isQuestion && count($keywords) < 2) return true;
    
    return false;
}

// Function to extract keywords from user message
function extractKeywords($message) {
    // Remove common stop words
    $stopWords = ['the', 'a', 'an', 'and', 'or', 'is', 'are', 'was', 'were', 'what', 'how', 'to', 'do', 'my', 'i', 'me', 'can', 'could', 'will', 'would', 'should', 'for', 'of', 'in', 'on', 'at', 'with', 'about', 'please', 'help', 'need', 'want', 'tell', 'give', 'info', 'information', 'question', 'problem', 'issue'];
    
    // Clean the message - remove special characters but keep spaces
    $cleanMessage = preg_replace('/[^a-z\s]/', ' ', $message);
    $words = preg_split('/\s+/', $cleanMessage, -1, PREG_SPLIT_NO_EMPTY);
    
    $keywords = array_diff($words, $stopWords);
    $keywords = array_filter($keywords, function($word) {
        return strlen($word) > 2; // Keep only words with more than 2 characters
    });
    
    return array_values(array_unique($keywords));
}

// Function to correct common misspellings
function correctSpelling($word) {
    $commonMisspellings = [
        'pasword' => 'password', 'pasword' => 'password', 'forgoten' => 'forgotten',
        'transcrip' => 'transcript', 'enrolment' => 'enrollment', 'registrar' => 'registrar',
        'admission' => 'admission', 'requirement' => 'requirements', 'document' => 'documents',
        'portal' => 'portal', 'technical' => 'technical', 'grades' => 'grades',
        'login' => 'login', 'account' => 'account', 'id' => 'id', 'sis' => 'sis',
        'tor' => 'tor', 'cor' => 'cor', 'credits' => 'credits', 'scholarship' => 'scholarship'
    ];
    
    return isset($commonMisspellings[$word]) ? $commonMisspellings[$word] : $word;
}

// Function to find the best response
function findBestResponse($userMessage, $keywords, $originalMessage) {
    // Try exact match first
    $exactMatch = findExactMatch($userMessage);
    if ($exactMatch) return $exactMatch;
    
    // Try keyword matching with priority terms
    $keywordMatch = findKeywordMatch($userMessage, $keywords);
    if ($keywordMatch) return $keywordMatch;
    
    // Try spelling correction
    $spellingCorrected = trySpellingCorrection($keywords, $originalMessage);
    if ($spellingCorrected) return $spellingCorrected;
    
    // Try category-based matching
    $categoryMatch = findCategoryMatch($userMessage);
    if ($categoryMatch) return $categoryMatch;
    
    // If nothing found, provide helpful guidance
// If nothing found, provide helpful fallback with office contacts
return [
    "reply" => "I don’t have information about that 🤔. But you can reach the right office:\n\n"
             . "📌 **MIS Office** → SIS portal, login, technical issues\n"
             . "📧 mis@ctu.edu.ph | FB: facebook.com/ctumis\n\n"
             . "📌 **Registrar's Office** → TOR, enrollment, academic records\n"
             . "📧 registrar@ctu.edu.ph | FB: facebook.com/cturegistrar\n\n"
             . "📌 **Student Affairs Office (SAO)** → Lost ID, student concerns\n"
             . "📧 sao@ctu.edu.ph | FB: facebook.com/ctusao\n\n"
             . "Meanwhile, I can answer about:\n- SIS login\n- Transcript requests\n- Lost ID\n- Admission requirements\n- Enrollment process",
    "links" => []
];

}

// Function to try spelling correction
function trySpellingCorrection($keywords, $originalMessage) {
    if (empty($keywords)) return null;
    
    // Correct spelling of keywords
    $correctedKeywords = array_map('correctSpelling', $keywords);
    
    // If corrections were made, try again with corrected keywords
    if ($keywords !== $correctedKeywords) {
        $newMatch = findKeywordMatch($originalMessage, $correctedKeywords);
        if ($newMatch) {
            // Add a note about the correction
            $newMatch['reply'] = "I think you might be asking about:\n\n" . $newMatch['reply'];
            return $newMatch;
        }
    }
    
    return null;
}

// Function to find exact match
// Function to find exact match in the database
function findExactMatch($userMessage) {
    global $conn;

    $sql = "SELECT question, answer, link 
            FROM faqs 
            WHERE LOWER(TRIM(question)) = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $userMessage);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($faq = $result->fetch_assoc()) {
        $response = formatResponse($faq['answer']);
        $links = [];

        if (!empty($faq['link'])) {
            $response .= "\n\nFor more information, visit the link below:";
            $links[] = ["url" => $faq['link'], "text" => "Click here for more information"];
        }

        return ["reply" => $response, "links" => $links];
    }

    return null;
}


// Function to find keyword match
// Function to find keyword matches in the database
function findKeywordMatch($userMessage, $keywords) {
    global $conn;
    if (empty($keywords)) return null;

    // Build WHERE clause with LIKE for each keyword
    $where = [];
    $params = [];
    $types = "";
    foreach ($keywords as $word) {
        $where[] = "(LOWER(question) LIKE ? OR LOWER(answer) LIKE ?)";
        $likeWord = "%" . $word . "%";
        $params[] = $likeWord;
        $params[] = $likeWord;
        $types .= "ss";
    }

    $sql = "SELECT question, answer, link 
            FROM faqs 
            WHERE " . implode(" OR ", $where);
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    $matches = [];
    while ($faq = $result->fetch_assoc()) {
        $score = 0;
        foreach ($keywords as $word) {
            if (stripos($faq['question'] . " " . $faq['answer'], $word) !== false) {
                $score++;
            }
        }
        $matches[] = ["faq" => $faq, "score" => $score];
    }

    if (count($matches) > 0) {
        usort($matches, fn($a, $b) => $b['score'] - $a['score']);
        $topFaq = $matches[0]['faq'];

        if ($matches[0]['score'] >= 2) {
            $response = formatResponse($topFaq['answer']);
            $links = [];

            if (!empty($topFaq['link'])) {
                $response .= "\n\nFor more information, visit the link below:";
                $links[] = ["url" => $topFaq['link'], "text" => "Click here for more information"];
            }

            return ["reply" => $response, "links" => $links];
        }

        if (count($matches) > 1) {
            $response = "I found several topics that might help you. Which one are you interested in?\n\n";
            $links = [];

            foreach ($matches as $i => $match) {
                $faq = $match['faq'];
                $response .= ($i + 1) . ". " . $faq['question'] . "\n";

                if (!empty($faq['link'])) {
                    $links[] = ["url" => $faq['link'], "text" => "More info about: " . $faq['question']];
                }
            }

            $response .= "\nPlease reply with the number of your choice.";
            return ["reply" => $response, "links" => $links];
        }
    }

    return null;
}


// Function to find category-based match
function findCategoryMatch($userMessage) {
    // Define category patterns
    $categories = [
        'mis' => ['sis', 'portal', 'login', 'password', 'forgot', 'technical', 'cor', 'grades', 'online'],
        'registrar' => ['tor', 'transcript', 'enrollment', 'registration', 'admission', 'requirements', 'scholastic', 'documents', 'records'],
        'sao' => ['id', 'lost id', 'student id', 'affidavit', 'card', 'replace'],
        'admission' => ['apply', 'application', 'entrance exam', 'interview', 'transfer', 'shift program', 'requirements']
    ];
    
    // Determine the most likely category
    $bestCategory = null;
    $bestScore = 0;
    
    foreach ($categories as $category => $terms) {
        $score = 0;
        foreach ($terms as $term) {
            if (strpos($userMessage, $term) !== false) {
                $score++;
            }
        }
        
        if ($score > $bestScore) {
            $bestScore = $score;
            $bestCategory = $category;
        }
    }
    
    // If we found a category, return a helpful response
    if ($bestCategory && $bestScore > 0) {
        $links = [];
        
        switch ($bestCategory) {
            case 'mis':
                $reply = "For SIS portal issues, password reset, or viewing your COR and grades, please contact the Management Information System (MIS) office.\n\nLocation: Main Campus, Administration Building, 2nd Floor\nEmail: mis@ctu.edu.ph\nPhone: (032) 123-4567";
                $links[] = ["url" => "http://sis.ctu.edu.ph/", "text" => "Access SIS Portal"];
                $links[] = ["url" => "http://sis.ctu.edu.ph/password-reset", "text" => "Reset SIS Password"];
                break;
            case 'registrar':
                $reply = "For transcript requests, enrollment, registration, or other academic records, please contact the Registrar's Office.\n\nLocation: Main Campus, Administration Building, 1st Floor\nEmail: registrar@ctu.edu.ph\nPhone: (032) 123-4568";
                $links[] = ["url" => "https://registrar.ctu.edu.ph/", "text" => "Registrar's Office Portal"];
                break;
            case 'sao':
                $reply = "For lost ID cards or other student activity concerns, please visit the Student Affairs Office (SAO). They will assist you with the affidavit process for lost IDs.\n\nLocation: Main Campus, Student Center Building\nEmail: sao@ctu.edu.ph\nPhone: (032) 123-4569";
                break;
            case 'admission':
                $reply = "For admission requirements, applications, entrance exams, or program transfers, please contact the Admission Office.\n\nLocation: Main Campus, Administration Building, Ground Floor\nEmail: admission@ctu.edu.ph\nPhone: (032) 123-4570";
                $links[] = ["url" => "https://admission.ctu.edu.ph/", "text" => "Admission Office Portal"];
                break;
            default:
                return null;
        }
        
        return ["reply" => $reply, "links" => $links];
    }
    
    return null;
}

// Function to format response with proper spacing
function formatResponse($text) {
    // Add spacing after periods and before new lines for better readability
    $text = preg_replace('/([.!?])\s*(\w)/', '$1  $2', $text);
    
    // Ensure proper spacing for bullet points or lists
    $text = preg_replace('/\n\s*-/', "\n\n-", $text);
    
    // Replace multiple newlines with proper spacing
    $text = preg_replace('/\n{3,}/', "\n\n", $text);
    
    return $text;
}
?>