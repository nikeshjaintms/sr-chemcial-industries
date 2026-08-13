<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ContactDetail;
use App\Models\Company;
use App\Models\Faq;

echo "==================================================\n";
echo "UPDATING DATABASE CONTACT DETAILS & FAQS\n";
echo "==================================================\n";

// 1. Update ContactDetail table
$contact = ContactDetail::firstOrNew(['id' => 1]);
$contact->address = 'A-97 Sai Ashish, NH-8 Vadadla, Bharuch 392011';
$contact->phone = '+91 76001 81931 / +91 70415 53966';
$contact->email = 'srchemicalindustries9@gmail.com / marketing@srchemicalindustries.com';
$contact->working_hours = 'Mon - Sat: 9:00 AM - 7:00 PM IST';
$contact->save();

echo "✅ ContactDetail record ID 1 updated:\n";
echo "   Address: {$contact->address}\n";
echo "   Phone: {$contact->phone}\n";
echo "   Email: {$contact->email}\n";

// 2. Update Company table
$company = Company::firstOrNew(['id' => 1]);
$company->name = 'SR Chemical Industries Limited (SRCIL)';
$company->address = 'A-97 Sai Ashish, NH-8 Vadadla, Bharuch 392011';
$company->save();

echo "\n✅ Company record ID 1 updated:\n";
echo "   Address: {$company->address}\n";

// 3. Update FAQs
$faqs = Faq::all();
$updatedFaqs = 0;

foreach ($faqs as $faq) {
    $needSave = false;
    $ans = $faq->answer;

    if (str_contains($ans, 'Bhavani') || str_contains($ans, 'GF-10') || str_contains($ans, 'Zadeshwar') || str_contains($ans, '99047') || str_contains($ans, '76988') || str_contains($ans, 'puregrade') || str_contains($ans, 'sales@srchemical.com')) {
        $ans = str_replace(
            ['GF-10, Bhavani Shopping Complex, Zadeshwar, Bharuch - 392015, Gujarat, India', 'GF-10, Bhavani Shopping Complex, Nr. Hotel NyayMandir, Zadeshwar', 'GF-10, Bhavani Shopping Complex, Zadeshwar'],
            'A-97 Sai Ashish, NH-8 Vadadla, Bharuch 392011',
            $ans
        );
        $ans = str_replace(
            ['+91 99047 88479 or +91 76988 81819', '+91 99047 88479', '+91 76988 81819'],
            '+91 76001 81931 or +91 70415 53966',
            $ans
        );
        $ans = str_replace(
            ['marketing@puregrade.in or sales@srchemical.com', 'marketing@puregrade.in', 'sales@srchemical.com'],
            'srchemicalindustries9@gmail.com or marketing@srchemicalindustries.com',
            $ans
        );
        $faq->answer = $ans;
        $faq->save();
        $updatedFaqs++;
        echo "   Updated FAQ ID {$faq->id}: '{$faq->question}'\n";
    }
}

echo "\n✅ Total FAQs updated: {$updatedFaqs}\n";
