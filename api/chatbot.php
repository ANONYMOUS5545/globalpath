<?php
require_once '../includes/config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$message = trim($input['message'] ?? '');
$history = array_slice($input['history'] ?? [], 0, 10);

if (!$message) {
    echo json_encode(['success' => false, 'reply' => 'No message received.']);
    exit;
}

$db = getDB();
startSecureSession();
$userId = $_SESSION['user_id'] ?? null;
$sessionId = session_id();

$systemPrompt = "You are PathBot, the AI assistant for Global Path Africa - a platform helping African students and professionals access international scholarships, jobs abroad, visa guidance, and online foreign language classes.

Key information:
- Website: Global Path Africa (globalpathAfrica.org)
- WhatsApp support: +254 792 579 974
- Email: info@globalpathAfrica.org

Membership plans:
- Free: Browse scholarships and jobs (no application)
- Premium: $9.99/month or $89.99/year - apply to jobs, priority support, application tracker
- Premium Plus: $19.99/month or $179.99/year - everything in Premium plus first access to new jobs, CV review, and dedicated support
- Scholarship Application Support: $49.99 one-time
- Visa Application Support: $79.99 one-time

Language classes:
- French
- German
- English exam prep
- Dutch
- Swedish

Jobs: UN Jobs, World Bank, WHO, African Development Bank, NGOs worldwide.

Payment methods accepted: Stripe, PayPal, Flutterwave, M-Pesa, Bank Transfer.

Escalation: If a user has a specific account issue, payment problem, or complex application question, tell them to WhatsApp +254792579974 or email info@globalpathAfrica.org and set escalated=true.

Keep responses concise, warm, and helpful. Use simple markdown for formatting and encourage users to explore the platform.";

$messages = [];
foreach ($history as $item) {
    if (in_array($item['role'] ?? '', ['user', 'assistant'], true)) {
        $messages[] = ['role' => $item['role'], 'content' => $item['content']];
    }
}
$messages[] = ['role' => 'user', 'content' => $message];

$escalated = false;
$reply = '';

try {
    $apiKey = ANTHROPIC_API_KEY;

    if ($apiKey === 'YOUR_ANTHROPIC_API_KEY') {
        $lower = strtolower($message);

        if (strpos($lower, 'erasmus') !== false) {
            $reply = "**Erasmus+** is the EU's flagship scholarship programme. It offers fully funded masters opportunities for African students with tuition support, living support and travel funding. Browse our scholarships page for current listings.";
        } elseif (strpos($lower, 'daad') !== false) {
            $reply = "**DAAD** supports many postgraduate opportunities in Germany for African professionals. Our scholarships page is the best place to check the latest active DAAD-linked listings.";
        } elseif (strpos($lower, 'chevening') !== false) {
            $reply = "**Chevening** is the UK government's global scholarship programme for future leaders. It usually targets master's study and is one of the key opportunities we track.";
        } elseif (strpos($lower, 'premium') !== false || strpos($lower, 'membership') !== false || strpos($lower, 'plan') !== false) {
            $reply = "We have 3 membership tiers:\n\n**Free** - browse scholarships and jobs\n**Premium** - $9.99/month - apply to jobs and get priority support\n**Premium Plus** - $19.99/month - first access to new jobs plus CV review\n\nWe also offer one-time support services:\n**Scholarship Support** - $49.99\n**Visa Support** - $79.99";
        } elseif (strpos($lower, 'visa') !== false) {
            $reply = "We provide visa guidance for Germany, the UK, the USA, France, Canada, the Netherlands, Belgium and Sweden.\n\nYou can also purchase **Visa Application Support** for $79.99 one-time to get document review, proof-of-funds guidance and interview preparation.";
        } elseif (strpos($lower, 'language') !== false || strpos($lower, 'french') !== false || strpos($lower, 'german') !== false || strpos($lower, 'ielts') !== false || strpos($lower, 'toefl') !== false || strpos($lower, 'dutch') !== false || strpos($lower, 'swedish') !== false) {
            $reply = "We now offer **online foreign language tutoring** for French, German, English exam prep, Dutch and Swedish.\n\nThese classes are useful for scholarship interviews, visa confidence, academic communication and relocation preparation.";
        } elseif (strpos($lower, 'payment') !== false || strpos($lower, 'pay') !== false || strpos($lower, 'mpesa') !== false) {
            $reply = "We accept multiple payment methods:\n- **Stripe**\n- **PayPal**\n- **Flutterwave**\n- **M-Pesa**\n- **Bank Transfer**\n\nAll payments are processed securely.";
        } elseif (strpos($lower, 'job') !== false || strpos($lower, 'work') !== false) {
            $reply = "We list international jobs at organisations like the **UN**, **World Bank**, **WHO**, **African Development Bank**, and global NGOs. Premium members can apply directly, while Premium Plus members get earlier access to selected roles.";
        } elseif (strpos($lower, 'whatsapp') !== false || strpos($lower, 'contact') !== false || strpos($lower, 'help') !== false || strpos($lower, 'support') !== false) {
            $reply = "Our support team is available on WhatsApp: **+254 792 579 974**\n\nOr email us at: info@globalpathAfrica.org";
            $escalated = true;
        } elseif (strpos($lower, 'account') !== false || strpos($lower, 'register') !== false || strpos($lower, 'sign up') !== false) {
            $reply = "Creating an account is **free and fast**. With a free account you can browse scholarships and jobs, use PathBot AI, save your country preference and explore visa and language resources.";
        } else {
            $reply = "I'm here to help with **scholarships**, **jobs abroad**, **visa guidance**, **online language classes**, and **membership plans**. You can also WhatsApp us directly on **+254 792 579 974**.";
        }
    } else {
        $ch = curl_init('https://api.anthropic.com/v1/messages');
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'x-api-key: ' . $apiKey,
                'anthropic-version: 2023-06-01'
            ],
            CURLOPT_POSTFIELDS => json_encode([
                'model' => 'claude-3-haiku-20240307',
                'max_tokens' => 500,
                'system' => $systemPrompt,
                'messages' => $messages
            ]),
            CURLOPT_TIMEOUT => 15,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response && $httpCode === 200) {
            $data = json_decode($response, true);
            $reply = $data['content'][0]['text'] ?? 'I could not process that right now.';
            if (strpos(strtolower($reply), 'whatsapp') !== false && strpos(strtolower($message), 'problem') !== false) {
                $escalated = true;
            }
        } else {
            $reply = 'I am having trouble connecting right now. Please WhatsApp us at +254 792 579 974 for immediate help.';
        }
    }

    try {
        $db->prepare("INSERT INTO support_messages (user_id,session_id,message,reply,is_escalated,status) VALUES (?,?,?,?,?,'replied')")
           ->execute([$userId, $sessionId, $message, $reply, $escalated ? 1 : 0]);
    } catch (Exception $e) {
    }

    echo json_encode(['success' => true, 'reply' => $reply, 'escalated' => $escalated]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'reply' => 'Service temporarily unavailable. Please WhatsApp +254 792 579 974']);
}
