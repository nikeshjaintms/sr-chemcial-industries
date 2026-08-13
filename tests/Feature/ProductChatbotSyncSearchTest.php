<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Category;
use App\Services\SearchService;
use App\Services\ChatbotEngineService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductChatbotSyncSearchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        if (Product::count() === 0) {
            Product::create([
                'name' => 'Nitric Acid',
                'slug' => 'gnfc-nitric-acid',
                'chemical_name' => 'Nitric Acid (HNO3)',
                'cas_number' => '7697-37-2',
                'hsn_code' => '28080010',
                'purity' => '68% / 60%',
                'packaging' => '35 Kg Carboys / Tanker',
                'description' => 'High purity Nitric Acid supplied for chemical synthesis and industrial applications.',
                'status' => true,
                'product_url' => '/products/gnfc-nitric-acid',
            ]);

            Product::create([
                'name' => 'Caustic Soda Flakes (NaOH)',
                'slug' => 'gacl-caustic-soda-flakes-naoh',
                'chemical_name' => 'Sodium Hydroxide (NaOH)',
                'cas_number' => '1310-73-2',
                'hsn_code' => '28151110',
                'purity' => '99% Min',
                'packaging' => '50 Kg HDPE Bags',
                'description' => 'High purity Caustic Soda Flakes used in soap, detergent, and textile processing.',
                'status' => true,
                'product_url' => '/products/gacl-caustic-soda-flakes-naoh',
            ]);

            Product::create([
                'name' => 'Caustic Soda Lye (NaOH)',
                'slug' => 'gacl-caustic-soda-lye-naoh',
                'chemical_name' => 'Sodium Hydroxide Solution (NaOH)',
                'cas_number' => '1310-73-2',
                'hsn_code' => '28151200',
                'purity' => '48%',
                'packaging' => 'Bulk Tanker',
                'description' => 'Industrial liquid Caustic Soda Lye.',
                'status' => true,
                'product_url' => '/products/gacl-caustic-soda-lye-naoh',
            ]);

            Product::create([
                'name' => 'Hydrogen Peroxide',
                'slug' => 'gacl-hydrogen-peroxide',
                'chemical_name' => 'Hydrogen Peroxide (H2O2)',
                'cas_number' => '7722-84-1',
                'hsn_code' => '28470000',
                'purity' => '50% / 35%',
                'packaging' => '30 Kg Carboys',
                'description' => 'Eco-friendly bleaching agent and disinfectant.',
                'status' => true,
                'product_url' => '/products/gacl-hydrogen-peroxide',
            ]);

            Product::create([
                'name' => 'Methylene Chloride (MDC)',
                'slug' => 'gacl-methylene-chloride-mdc',
                'chemical_name' => 'Dichloromethane (CH2Cl2)',
                'cas_number' => '75-09-2',
                'hsn_code' => '29031200',
                'purity' => '99.9% Pure',
                'packaging' => '250 Kg Drums',
                'description' => 'High purity solvent for pharma and industrial extraction.',
                'status' => true,
                'product_url' => '/products/gacl-methylene-chloride-mdc',
            ]);

            Product::create([
                'name' => 'Boric Acid',
                'slug' => 'dmcc-boric-acid',
                'chemical_name' => 'Boric Acid (H3BO3)',
                'cas_number' => '10043-35-3',
                'hsn_code' => '28100020',
                'purity' => '99.5% Min',
                'packaging' => '50 Kg Bags',
                'description' => 'Industrial grade Boric Acid.',
                'status' => true,
                'product_url' => '/products/dmcc-boric-acid',
            ]);

            Product::create([
                'name' => 'Formic Acid',
                'slug' => 'gnfc-formic-acid',
                'chemical_name' => 'Formic Acid (CH2O2)',
                'cas_number' => '64-18-6',
                'hsn_code' => '29151100',
                'purity' => '85% Grade',
                'packaging' => '35 Kg Carboys',
                'description' => 'High grade Formic Acid for leather and textile dyeing.',
                'status' => true,
                'product_url' => '/products/gnfc-formic-acid',
            ]);
        }
    }

    /**
     * Test exact product search returns ONLY the exact product.
     */
    public function test_exact_product_search_returns_only_exact_product(): void
    {
        $res1 = SearchService::search('Nitric Acid');
        $this->assertEquals(1, $res1['priority']);
        $this->assertEquals(1, $res1['count']);
        $this->assertCount(1, $res1['products']);
        $this->assertStringContainsString('Nitric Acid', $res1['products'][0]->name);

        $res2 = SearchService::search('Caustic Soda Flakes');
        $this->assertEquals(1, $res2['count']);
        $this->assertStringContainsString('Caustic Soda Flakes', $res2['products'][0]->name);

        $res3 = SearchService::search('Hydrogen Peroxide');
        $this->assertEquals(1, $res3['count']);
        $this->assertStringContainsString('Hydrogen Peroxide', $res3['products'][0]->name);
    }

    /**
     * Test case-insensitive search normalization.
     */
    public function test_case_insensitive_search(): void
    {
        $upperRes = SearchService::search('NITRIC ACID');
        $this->assertEquals(1, $upperRes['count']);
        $this->assertStringContainsString('Nitric Acid', $upperRes['products'][0]->name);

        $lowerRes = SearchService::search('nitric acid');
        $this->assertEquals(1, $lowerRes['count']);
        $this->assertStringContainsString('Nitric Acid', $lowerRes['products'][0]->name);
    }

    /**
     * Test extra spaces normalization.
     */
    public function test_extra_spaces_normalization_search(): void
    {
        $spacesRes = SearchService::search('   NITRIC    ACID   ');
        $this->assertEquals(1, $spacesRes['count']);
        $this->assertStringContainsString('Nitric Acid', $spacesRes['products'][0]->name);
    }

    /**
     * Test partial search returns relevant products when exact match does not exist.
     */
    public function test_partial_product_name_search(): void
    {
        $res = SearchService::search('Caustic Soda');
        $this->assertGreaterThan(0, $res['count']);
        foreach ($res['products'] as $p) {
            $this->assertStringContainsString('Caustic Soda', $p->name);
        }
    }

    /**
     * Test typo search works as final fallback.
     */
    public function test_typo_search_fallback(): void
    {
        $res = SearchService::search('Nitirct Acid');
        $this->assertEquals(7, $res['priority']);
        $this->assertEquals(1, $res['count']);
        $this->assertStringContainsString('Nitric Acid', $res['products'][0]->name);
    }

    /**
     * Test chemical formula synonym mapping (HNO3, NaOH, MDC, IPA).
     */
    public function test_chemical_formula_synonym_search(): void
    {
        $hno3Res = SearchService::search('HNO3');
        $this->assertGreaterThan(0, $hno3Res['count']);
        $this->assertStringContainsString('Nitric Acid', $hno3Res['products'][0]->name);

        $naohRes = SearchService::search('NaOH');
        $this->assertGreaterThan(0, $naohRes['count']);
        $this->assertStringContainsString('Caustic Soda', $naohRes['products'][0]->name);

        $mdcRes = SearchService::search('MDC');
        $this->assertGreaterThan(0, $mdcRes['count']);
        $this->assertStringContainsString('Methylene Chloride', $mdcRes['products'][0]->name);
    }

    /**
     * Test Admin Panel product CRUD automatic sync with Chatbot.
     */
    public function test_admin_product_crud_automatic_sync(): void
    {
        $uniqueName = 'Alpha Special Chemical ' . rand(1000, 9999);
        $updatedName = 'Beta Premium Substance ' . rand(1000, 9999);

        // 1. Create Product in DB (simulating Admin Store)
        $product = Product::create([
            'name' => $uniqueName,
            'slug' => \Illuminate\Support\Str::slug($uniqueName),
            'description' => 'Test Chemical Product for Chatbot Sync Verification.',
            'status' => true,
            'product_url' => '/products/' . \Illuminate\Support\Str::slug($uniqueName),
        ]);

        // Immediate Chatbot Search
        $engine = new ChatbotEngineService();
        $response1 = $engine->processQuery($uniqueName, 'test_session_sync');
        $this->assertEquals('success', $response1['status']);
        $this->assertEquals('product', $response1['card_type']);
        $this->assertEquals($uniqueName, $response1['product']['name']);

        // 2. Edit Product in DB (simulating Admin Update)
        $product->update([
            'name' => $updatedName,
            'slug' => \Illuminate\Support\Str::slug($updatedName),
        ]);

        // Immediate Chatbot Search for new name
        $response2 = $engine->processQuery($updatedName, 'test_session_sync');
        $this->assertEquals('success', $response2['status']);
        $this->assertEquals('product', $response2['card_type']);
        $this->assertEquals($updatedName, $response2['product']['name']);

        // Immediate Chatbot Search for old name should no longer return exact match for this product
        $oldRes = SearchService::search($uniqueName);
        if ($oldRes['count'] > 0) {
            $this->assertNotEquals($product->id, $oldRes['products'][0]->id);
        }

        // 3. Delete Product in DB (simulating Admin Destroy)
        $product->delete();

        // Search deleted product name should no longer find it
        $deletedRes = SearchService::search($updatedName);
        if ($deletedRes['count'] > 0) {
            $this->assertNotEquals($product->id, $deletedRes['products'][0]->id);
        }
    }

    /**
     * Test Chatbot API POST endpoint /api/chatbot/chat.
     */
    public function test_chatbot_api_endpoint(): void
    {
        $response = $this->postJson('/api/chatbot/chat', [
            'message' => 'Nitric Acid',
            'session_id' => 'api_test_session_1'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'card_type' => 'product',
        ]);
        $response->assertJsonPath('product.name', 'Nitric Acid');
    }
}
