<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\ChatbotEngineService;
use App\Models\ContactDetail;
use App\Models\Company;
use App\Models\Faq;

$service = new ChatbotEngineService();

echo "==================================================\n";
echo "1. TESTING CHATBOT ENGINE CONTACT RESPONSES\n";
echo "==================================================\n";

$testQueries = [
    "contact",
    "contact details",
    "address",
    "location",
    "phone",
    "email",
    "How can I contact SR Chemical Industries?",
    "bulk order",
];

$oldPatterns = [
    'GF-10',
    'Bhavani',
    'NyayMandir',
    'Zadeshwar',
    '99047',
    '76988',
    'puregrade',
    'sales@srchemical.com',
];

$allClean = true;

foreach ($testQueries as $q) {
    $res = $service->processQuery($q, 'test_session_123');
    $msg = $res['message'] ?? '';
    
    echo "Query: '{$q}'\n";
    echo "--------------------------------------------------\n";
    echo $msg . "\n\n";

    foreach ($oldPatterns as $old) {
        if (str_contains($msg, $old)) {
            echo "❌ OLD VALUE DETECTED IN CHATBOT RESPONSE: '{$old}' for query '{$q}'!\n";
            $allClean = false;
        }
    }
}

echo "==================================================\n";
echo "2. AUDITING DATABASE TABLES FOR LEGACY CONTACT VALUES\n";
echo "==================================================\n";

$dbClean = true;

$contact = ContactDetail::first();
echo "ContactDetail DB Address: " . ($contact ? $contact->address : 'NONE') . "\n";
echo "ContactDetail DB Phone: " . ($contact ? $contact->phone : 'NONE') . "\n";
echo "ContactDetail DB Email: " . ($contact ? $contact->email : 'NONE') . "\n";

foreach ($oldPatterns as $old) {
    if ($contact && (str_contains($contact->address, $old) || str_contains($contact->phone, $old) || str_contains($contact->email, $old))) {
        echo "❌ OLD VALUE IN CONTACT_DETAILS TABLE: '{$old}'\n";
        $dbClean = false;
    }
}

$faqs = Faq::all();
foreach ($faqs as $f) {
    $str = $f->question . ' ' . $f->answer;
    foreach ($oldPatterns as $old) {
        if (str_contains($str, $old)) {
            echo "❌ OLD VALUE IN FAQ ID {$f->id}: '{$old}'\n";
            $dbClean = false;
        }
    }
}

echo "\n==================================================\n";
if ($allClean && $dbClean) {
    echo "✅ ALL CHATBOT RESPONSES AND DATABASE RECORDS ARE 100% CLEAN AND ACCURATE!\n";
} else {
    echo "❌ TEST FAILED — OLD VALUES STILL DETECTED!\n";
    exit(1);
}
