<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ContactDetail;
use App\Models\Company;
use App\Models\Faq;

echo "==================================================\n";
echo "1. AUDITING CONTACT_DETAILS TABLE\n";
echo "==================================================\n";

$contacts = ContactDetail::all();
echo "Total ContactDetail Records: " . $contacts->count() . "\n\n";

foreach ($contacts as $c) {
    echo "ID: {$c->id}\n";
    echo "Address: {$c->address}\n";
    echo "Phone: {$c->phone}\n";
    echo "Email: {$c->email}\n";
    echo "Working Hours: {$c->working_hours}\n";
    echo "Map Embed URL: {$c->map_embed_url}\n";
    echo "--------------------------------------------------\n";
}

echo "\n==================================================\n";
echo "2. AUDITING COMPANY TABLE\n";
echo "==================================================\n";

$companies = Company::all();
echo "Total Company Records: " . $companies->count() . "\n\n";

foreach ($companies as $comp) {
    echo "ID: {$comp->id} | Name: {$comp->name}\n";
    echo "Address: {$comp->address}\n";
    echo "Phone: {$comp->phone}\n";
    echo "Email: {$comp->email}\n";
    echo "--------------------------------------------------\n";
}

echo "\n==================================================\n";
echo "3. AUDITING FAQS FOR OLD CONTACT DETAILS\n";
echo "==================================================\n";

$faqs = Faq::all();
foreach ($faqs as $f) {
    $str = $f->question . ' ' . $f->answer;
    if (str_contains($str, 'Bhavani') || str_contains($str, '99047') || str_contains($str, 'puregrade') || str_contains($str, 'sales@srchemical.com') || str_contains($str, '76988')) {
        echo "FAQ ID: {$f->id} | Question: {$f->question}\n";
        echo "Answer: {$f->answer}\n";
        echo "--------------------------------------------------\n";
    }
}
