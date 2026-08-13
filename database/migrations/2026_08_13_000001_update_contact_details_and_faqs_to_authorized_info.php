<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\ContactDetail;
use App\Models\Company;
use App\Models\Faq;

return new class extends Migration
{
    /**
     * Run the migrations to update contact_details, companies, and faqs tables on production.
     */
    public function up(): void
    {
        // 1. Update ContactDetail Table (Record ID 1 or First Record)
        try {
            DB::table('contact_details')->where('id', 1)->update([
                'address' => 'A-97 Sai Ashish, NH-8 Vadadla, Bharuch 392011',
                'phone' => '+91 76001 81931 / +91 70415 53966',
                'email' => 'srchemicalindustries9@gmail.com / marketing@srchemicalindustries.com',
                'working_hours' => 'Mon - Sat: 9:00 AM - 7:00 PM IST',
                'postal_code' => '392011',
                'whatsapp' => '+917600181931',
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            \Log::warning('Migration contact_details update error: ' . $e->getMessage());
        }

        // 2. Update Companies Table
        try {
            DB::table('companies')->where('id', 1)->update([
                'name' => 'SR Chemical Industries Limited (SRCIL)',
                'address' => 'A-97 Sai Ashish, NH-8 Vadadla, Bharuch 392011',
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            \Log::warning('Migration companies update error: ' . $e->getMessage());
        }

        // 3. Update FAQs containing old legacy contact information
        try {
            $faqs = Faq::all();
            foreach ($faqs as $faq) {
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
                }
            }
        } catch (\Exception $e) {
            \Log::warning('Migration faqs update error: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op to preserve database safety
    }
};
