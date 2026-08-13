<?php

namespace Database\Seeders;

require_once __DIR__ . '/../bootstrap.php';

use Illuminate\Database\Capsule\Manager as DB;
use App\Models\Company;
use App\Models\Category;
use App\Models\Product;
use App\Models\Blog;
use App\Models\Faq;
use App\Models\Certification;
use App\Models\ContactDetail;
use App\Models\ExportCountry;

class MySQLHierarchicalSeeder
{
    public static function run()
    {
        echo "Starting MySQL Database Migration & Seeding...\n";

        // 1. Drop existing tables safely
        DB::schema()->dropIfExists('chat_histories');
        DB::schema()->dropIfExists('products');
        DB::schema()->dropIfExists('categories');
        DB::schema()->dropIfExists('companies');
        DB::schema()->dropIfExists('blogs');
        DB::schema()->dropIfExists('faqs');
        DB::schema()->dropIfExists('certifications');
        DB::schema()->dropIfExists('contact_details');
        DB::schema()->dropIfExists('export_countries');

        // Companies table
        DB::schema()->create('companies', function ($table) {
            $table->id();
            $table->string('name');
            $table->string('short_name');
            $table->string('tagline');
            $table->text('about');
            $table->text('mission');
            $table->text('vision');
            $table->string('address');
            $table->string('phone_primary');
            $table->string('phone_secondary');
            $table->string('email_primary');
            $table->string('email_secondary');
            $table->string('website_url');
            $table->string('logo_url');
            $table->text('services');
            $table->text('highlights');
            $table->text('logistics_info');
            $table->text('compliance_info');
            $table->timestamps();
        });

        // Hierarchical Categories table
        DB::schema()->create('categories', function ($table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('type')->default('Category');
            $table->enum('level', ['root', 'main_category', 'sub_category', 'sub_sub_category'])->default('main_category');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->foreign('parent_id')->references('id')->on('categories')->onDelete('cascade');
            $table->text('description')->nullable();
            $table->string('image_url')->nullable();
            $table->timestamps();
        });

        // Products table
        DB::schema()->create('products', function ($table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('brand')->nullable();
            $table->string('chemical_name')->nullable();
            $table->string('cas_number')->nullable();
            $table->string('hsn_code')->nullable();
            $table->string('purity')->nullable();
            $table->string('packaging')->nullable();
            $table->text('description');
            $table->text('features')->nullable();
            $table->text('applications')->nullable();
            $table->text('specifications')->nullable();
            $table->text('storage_info')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
            $table->string('image_url')->nullable();
            $table->string('msds_url')->nullable();
            $table->string('specification_url')->nullable();
            $table->string('product_url');
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });

        // Blogs table
        DB::schema()->create('blogs', function ($table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('summary');
            $table->text('content');
            $table->string('category')->nullable();
            $table->string('author')->default('SRCIL Editorial Team');
            $table->string('read_time')->default('5 min read');
            $table->string('published_at')->nullable();
            $table->string('image_url')->nullable();
            $table->string('url');
            $table->timestamps();
        });

        // FAQs table
        DB::schema()->create('faqs', function ($table) {
            $table->id();
            $table->text('question');
            $table->text('answer');
            $table->string('category')->default('General');
            $table->text('keywords')->nullable();
            $table->timestamps();
        });

        // Certifications table
        DB::schema()->create('certifications', function ($table) {
            $table->id();
            $table->string('title');
            $table->string('issuer');
            $table->text('description');
            $table->string('document_url')->nullable();
            $table->string('icon')->nullable();
            $table->timestamps();
        });

        // Contact Details table
        DB::schema()->create('contact_details', function ($table) {
            $table->id();
            $table->string('office_name');
            $table->string('address');
            $table->string('city');
            $table->string('state');
            $table->string('country');
            $table->string('postal_code');
            $table->string('phone');
            $table->string('email');
            $table->string('whatsapp')->nullable();
            $table->string('working_hours')->nullable();
            $table->string('google_map_url')->nullable();
            $table->timestamps();
        });

        // Export Countries table
        DB::schema()->create('export_countries', function ($table) {
            $table->id();
            $table->string('name');
            $table->string('code');
            $table->string('region');
            $table->string('flag_emoji')->nullable();
            $table->text('details')->nullable();
            $table->timestamps();
        });

        // Chat History table
        DB::schema()->create('chat_histories', function ($table) {
            $table->id();
            $table->string('session_id');
            $table->text('user_query');
            $table->text('bot_response');
            $table->string('matched_intent')->nullable();
            $table->unsignedBigInteger('context_product_id')->nullable();
            $table->text('metadata')->nullable();
            $table->timestamps();
        });

        echo "Database schema created successfully.\n";

        // 2. Seed Company Info
        Company::create([
            'name' => 'SR Chemical Industries Limited (SRCIL)',
            'short_name' => 'SRCIL / Pure Grade Exim',
            'tagline' => 'Global Trade. Trusted Quality. Connecting domestic and international industries with high-purity chemicals.',
            'about' => 'SR Chemical Industries Limited (SRCIL) is a premier chemical supplier, importer, exporter, and distributor based in Bharuch, Gujarat, India.',
            'mission' => 'To empower worldwide manufacturing through reliable chemical supply, absolute transparency, stringent quality verification, and sustainable industrial trade practices.',
            'vision' => 'To be recognized globally as the most trusted partner for high-purity industrial chemicals, solvents, and raw material logistics.',
            'address' => 'GF-10, Bhavani Shopping Complex, Nr. Hotel NyayMandir, Zadeshwar, Bharuch - 392015, Gujarat, India',
            'phone_primary' => '+91 99047 88479',
            'phone_secondary' => '+91 76988 81819',
            'email_primary' => 'marketing@puregrade.in',
            'email_secondary' => 'sales@srchemical.com',
            'website_url' => 'https://srchemical.com',
            'logo_url' => 'assets/img/added/blue-logo.png',
            'services' => json_encode([
                'Global Import & Export of Hazardous & Non-Hazardous Chemicals',
                'Domestic Indian Market Distribution with Fast Dispatch',
                'Bulk Order Procurement & Custom Packaging Solutions',
                'End-to-End Hazardous Material Logistics & Tanker Transport'
            ]),
            'highlights' => json_encode([
                '25+ Global Trade Partners',
                '100% Verified Pure Grade Quality Standard',
                'Dedicated Hazardous & Bulk Cargo Logistics Network'
            ]),
            'logistics_info' => 'Full movement support for bulk liquid chemicals via dedicated tankers.',
            'compliance_info' => 'Strict adherence to international safety standards, ISO certifications, REACH guidelines.'
        ]);

        // 3. Seed 5-Tier Category Tree
        // LEVEL 0: ROOT
        $root = Category::create([
            'name' => 'SR Chemical Catalog Root',
            'slug' => 'root-chemical-catalog',
            'type' => 'Root',
            'level' => 'root',
            'parent_id' => null,
            'description' => 'Root catalog for all chemical product lines.',
            'image_url' => 'assets/img/added/blue-logo.png'
        ]);

        // LEVEL 1: MAIN CATEGORIES
        $mainGACL = Category::create([
            'name' => 'GACL Products',
            'slug' => 'gacl-products',
            'type' => 'Main Category',
            'level' => 'main_category',
            'parent_id' => $root->id,
            'description' => 'Gujarat Alkalies and Chemicals Limited products portfolio.',
            'image_url' => 'assets/img/added/product/Caustic-Soda-Flakes-NaOH.jpg'
        ]);

        $mainOrganic = Category::create([
            'name' => 'Organic Products',
            'slug' => 'organic-products',
            'type' => 'Main Category',
            'level' => 'main_category',
            'parent_id' => $root->id,
            'description' => 'High purity organic chemical intermediates and aromatic compounds.',
            'image_url' => 'assets/img/added/product/Mono-Chloro-Benzene.jpg'
        ]);

        $mainDMCC = Category::create([
            'name' => 'DMCC Products',
            'slug' => 'dmcc-products',
            'type' => 'Main Category',
            'level' => 'main_category',
            'parent_id' => $root->id,
            'description' => 'Boron chemicals and sulfur derivatives manufactured by DMCC.',
            'image_url' => 'assets/img/added/product/Boric-Acid.jpg'
        ]);

        $mainGNFC = Category::create([
            'name' => 'GNFC Products',
            'slug' => 'gnfc-products',
            'type' => 'Main Category',
            'level' => 'main_category',
            'parent_id' => $root->id,
            'description' => 'Gujarat Narmada Valley Fertilizers & Chemicals portfolio.',
            'image_url' => 'assets/img/added/product/Acetic-Acid.jpg'
        ]);

        $mainSolvents = Category::create([
            'name' => 'Industrial Solvents',
            'slug' => 'industrial-solvents',
            'type' => 'Main Category',
            'level' => 'main_category',
            'parent_id' => $root->id,
            'description' => 'High purity solvent range for paints, coatings, pharma, and cleaning.',
            'image_url' => 'assets/img/added/product/Methylene-Chloride-MDC.jpg'
        ]);

        $mainCoal = Category::create([
            'name' => 'Coal & Energy Products',
            'slug' => 'coal-products',
            'type' => 'Main Category',
            'level' => 'main_category',
            'parent_id' => $root->id,
            'description' => 'Bio-coal and imported coal energy commodities.',
            'image_url' => 'assets/img/added/product/Bio-Coal.jpg'
        ]);

        // LEVEL 2: SUB CATEGORIES & LEVEL 3: SUB SUB CATEGORIES

        // --- GACL SUB & SUB-SUB CATEGORIES ---
        $subGaclAcid = Category::create([
            'name' => 'ACID Products',
            'slug' => 'gacl-acid-products',
            'type' => 'Sub Category',
            'level' => 'sub_category',
            'parent_id' => $mainGACL->id,
            'description' => 'Industrial mineral acids.'
        ]);
        $subSubGaclAcid = Category::create([
            'name' => 'Industrial Acid Compounds',
            'slug' => 'industrial-acid-compounds',
            'type' => 'Sub Sub Category',
            'level' => 'sub_sub_category',
            'parent_id' => $subGaclAcid->id,
            'description' => 'Hydrochloric, Nitric, Phosphoric, Sulphuric, Acetic, Formic acids.'
        ]);

        $subChlorAlkali = Category::create([
            'name' => 'Chlor-Alkali Chemicals',
            'slug' => 'gacl-chlor-alkali-chemicals',
            'type' => 'Sub Category',
            'level' => 'sub_category',
            'parent_id' => $mainGACL->id,
            'description' => 'Caustic Soda, Chlorine, Bleaching agents.'
        ]);
        $subSubCausticSoda = Category::create([
            'name' => 'Caustic Soda Derivatives',
            'slug' => 'caustic-soda-derivatives',
            'type' => 'Sub Sub Category',
            'level' => 'sub_sub_category',
            'parent_id' => $subChlorAlkali->id,
            'description' => 'Flakes, Lye, Prills.'
        ]);
        $subSubChlorineBleach = Category::create([
            'name' => 'Chlorine & Bleaching Products',
            'slug' => 'chlorine-bleaching-products',
            'type' => 'Sub Sub Category',
            'level' => 'sub_sub_category',
            'parent_id' => $subChlorAlkali->id,
            'description' => 'Liquid Chlorine, Bleaching Powder, Sodium Hypochlorite.'
        ]);

        $subHydrogenPeroxide = Category::create([
            'name' => 'Hydrogen & Peroxide Chemicals',
            'slug' => 'gacl-hydrogen-peroxide-chemicals',
            'type' => 'Sub Category',
            'level' => 'sub_category',
            'parent_id' => $mainGACL->id,
            'description' => 'Hydrogen Peroxide, Hydrazine Hydrate.'
        ]);
        $subSubPeroxide = Category::create([
            'name' => 'Peroxide Derivatives',
            'slug' => 'peroxide-derivatives',
            'type' => 'Sub Sub Category',
            'level' => 'sub_sub_category',
            'parent_id' => $subHydrogenPeroxide->id,
            'description' => 'Peroxide oxidants and hydrazine.'
        ]);

        $subChloromethane = Category::create([
            'name' => 'Chloromethane Chemicals',
            'slug' => 'gacl-chloromethane-chemicals',
            'type' => 'Sub Category',
            'level' => 'sub_category',
            'parent_id' => $mainGACL->id,
            'description' => 'Chlorinated methane compounds.'
        ]);
        $subSubChloromethane = Category::create([
            'name' => 'Methane Chlorides',
            'slug' => 'methane-chlorides',
            'type' => 'Sub Sub Category',
            'level' => 'sub_sub_category',
            'parent_id' => $subChloromethane->id,
            'description' => 'MDC, Chloroform, Carbon Tetrachloride, Methyl Chloride.'
        ]);

        $subPotassium = Category::create([
            'name' => 'Potassium Chemicals',
            'slug' => 'gacl-potassium-chemicals',
            'type' => 'Sub Category',
            'level' => 'sub_category',
            'parent_id' => $mainGACL->id,
            'description' => 'Potassium based chemicals.'
        ]);
        $subSubPotassium = Category::create([
            'name' => 'Potassium Compounds',
            'slug' => 'potassium-compounds',
            'type' => 'Sub Sub Category',
            'level' => 'sub_sub_category',
            'parent_id' => $subPotassium->id,
            'description' => 'Caustic Potash Flakes, Lye, Potassium Carbonate.'
        ]);

        $subAluminium = Category::create([
            'name' => 'Aluminium Based Chemicals',
            'slug' => 'gacl-aluminium-based-chemicals',
            'type' => 'Sub Category',
            'level' => 'sub_category',
            'parent_id' => $mainGACL->id,
            'description' => 'Aluminium chloride coagulants.'
        ]);
        $subSubAluminium = Category::create([
            'name' => 'Aluminium Chlorides & Coagulants',
            'slug' => 'aluminium-chlorides-coagulants',
            'type' => 'Sub Sub Category',
            'level' => 'sub_sub_category',
            'parent_id' => $subAluminium->id,
            'description' => 'Anhydrous Aluminium Chloride, PAC Powder & Liquid.'
        ]);

        $subWaterTreatment = Category::create([
            'name' => 'Water Treatment Chemicals',
            'slug' => 'gacl-water-treatment-chemicals',
            'type' => 'Sub Category',
            'level' => 'sub_category',
            'parent_id' => $mainGACL->id,
            'description' => 'Water treatment salts and coagulants.'
        ]);
        $subSubWaterTreatment = Category::create([
            'name' => 'Effluent & Water Treatment Agents',
            'slug' => 'effluent-water-treatment-agents',
            'type' => 'Sub Sub Category',
            'level' => 'sub_sub_category',
            'parent_id' => $subWaterTreatment->id,
            'description' => 'Industrial Salt, Water Treatment agents.'
        ]);

        $subPhosphate = Category::create([
            'name' => 'Phosphate Chemicals',
            'slug' => 'gacl-phosphate-chemicals',
            'type' => 'Sub Category',
            'level' => 'sub_category',
            'parent_id' => $mainGACL->id,
            'description' => 'Phosphates and Phosphoric Acids.'
        ]);
        $subSubPhosphate = Category::create([
            'name' => 'Phosphate Salts & Acids',
            'slug' => 'phosphate-salts-acids',
            'type' => 'Sub Sub Category',
            'level' => 'sub_sub_category',
            'parent_id' => $subPhosphate->id,
            'description' => 'Food Grade Phosphoric Acid, Phosphate Salts.'
        ]);

        $subOtherSpecialty = Category::create([
            'name' => 'Other Specialty Chemicals',
            'slug' => 'gacl-other-specialty-chemicals',
            'type' => 'Sub Category',
            'level' => 'sub_category',
            'parent_id' => $mainGACL->id,
            'description' => 'Specialty auxiliaries and chlor-organics.'
        ]);
        $subSubOtherSpecialty = Category::create([
            'name' => 'Chlorinated Auxiliaries',
            'slug' => 'chlorinated-auxiliaries',
            'type' => 'Sub Sub Category',
            'level' => 'sub_sub_category',
            'parent_id' => $subOtherSpecialty->id,
            'description' => 'Chlorinated Paraffin, Benzyl Chloride, Sodium Chlorate, Monochloro Acetic Acid.'
        ]);

        // --- ORGANIC PRODUCTS SUB & SUB-SUB CATEGORIES ---
        $subChlorobenzene = Category::create([
            'name' => 'Chlorobenzene & Nitro Compounds',
            'slug' => 'chlorobenzene-nitro-compounds',
            'type' => 'Sub Category',
            'level' => 'sub_category',
            'parent_id' => $mainOrganic->id,
            'description' => 'Substituted chlorobenzenes and nitrobenzene derivatives.'
        ]);
        $subSubChlorobenzenes = Category::create([
            'name' => 'Chlorobenzenes Group',
            'slug' => 'chlorobenzenes-group',
            'type' => 'Sub Sub Category',
            'level' => 'sub_sub_category',
            'parent_id' => $subChlorobenzene->id,
            'description' => 'MCB, PDCB, ODCB, TCB.'
        ]);
        $subSubNitroChlorobenzene = Category::create([
            'name' => 'Nitro Chlorobenzenes & Nitrobenzene',
            'slug' => 'nitro-chlorobenzenes-nitrobenzene',
            'type' => 'Sub Sub Category',
            'level' => 'sub_sub_category',
            'parent_id' => $subChlorobenzene->id,
            'description' => '2,4 DNCB, 2,5 DNCB, 3,4 DCNB, PNCB, ONCB, MNCB, Nitrobenzene.'
        ]);

        $subAnilineToluidine = Category::create([
            'name' => 'Aniline & Toluidine Derivatives',
            'slug' => 'aniline-toluidine-derivatives',
            'type' => 'Sub Category',
            'level' => 'sub_category',
            'parent_id' => $mainOrganic->id,
            'description' => 'Chloroanilines, Anisidines, Toluidines.'
        ]);
        $subSubChloroAnilines = Category::create([
            'name' => 'Chloro Anilines Group',
            'slug' => 'chloro-anilines-group',
            'type' => 'Sub Sub Category',
            'level' => 'sub_sub_category',
            'parent_id' => $subAnilineToluidine->id,
            'description' => 'PCA, MCA, OCA, 2,5 DCA, 3,4 DCA, 2,4,5 TCA.'
        ]);
        $subSubAnisidinesToluidines = Category::create([
            'name' => 'Anisidines & Toluidines Group',
            'slug' => 'anisidines-toluidines-group',
            'type' => 'Sub Sub Category',
            'level' => 'sub_sub_category',
            'parent_id' => $subAnilineToluidine->id,
            'description' => 'OA, PA, PT, OT, MT.'
        ]);

        $subInorganicIntermediates = Category::create([
            'name' => 'Inorganic Intermediates & Chlorides',
            'slug' => 'inorganic-intermediates-chlorides',
            'type' => 'Sub Category',
            'level' => 'sub_category',
            'parent_id' => $mainOrganic->id,
            'description' => 'Calcium Chlorides, Benzene, Spent Acids.'
        ]);
        $subSubCalciumBenzene = Category::create([
            'name' => 'Calcium Chlorides & Refinery Benzene',
            'slug' => 'calcium-chlorides-refinery-benzene',
            'type' => 'Sub Sub Category',
            'level' => 'sub_sub_category',
            'parent_id' => $subInorganicIntermediates->id,
            'description' => 'Calcium Chloride Prills/Brine, Benzene Pure, Spent Sulphuric Acid.'
        ]);

        // --- DMCC SUB & SUB-SUB CATEGORIES ---
        $subBoron = Category::create([
            'name' => 'Boron Chemicals',
            'slug' => 'dmcc-boron-chemicals',
            'type' => 'Sub Category',
            'level' => 'sub_category',
            'parent_id' => $mainDMCC->id,
            'description' => 'Borax and Boric Acid compounds.'
        ]);
        $subSubBorates = Category::create([
            'name' => 'Borates & Boric Acids',
            'slug' => 'borates-boric-acids',
            'type' => 'Sub Sub Category',
            'level' => 'sub_sub_category',
            'parent_id' => $subBoron->id,
            'description' => 'Borax Decahydrate, Borax Pentahydrate, Technical Boric Acid, Special Quality Boric Acid.'
        ]);

        $subSulfur = Category::create([
            'name' => 'Sulfur Products',
            'slug' => 'dmcc-sulfur-products',
            'type' => 'Sub Category',
            'level' => 'sub_category',
            'parent_id' => $mainDMCC->id,
            'description' => 'Oleum and Specialty Sulfurs.'
        ]);
        $subSubOleumSulfur = Category::create([
            'name' => 'Oleum & Sulfuric Derivatives',
            'slug' => 'oleum-sulfuric-derivatives',
            'type' => 'Sub Sub Category',
            'level' => 'sub_sub_category',
            'parent_id' => $subSulfur->id,
            'description' => 'Oleum 23%, Oleum 65%, Battery Grade Sulfuric Acid, Commercial Grade Sulfuric Acid.'
        ]);

        // --- GNFC SUB & SUB-SUB CATEGORIES ---
        $subGnfcIntermediates = Category::create([
            'name' => 'GNFC Organic Intermediates',
            'slug' => 'gnfc-organic-intermediates',
            'type' => 'Sub Category',
            'level' => 'sub_category',
            'parent_id' => $mainGNFC->id,
            'description' => 'GNFC industrial chemicals.'
        ]);
        $subSubGnfcOrganics = Category::create([
            'name' => 'GNFC Industrial Chemicals Group',
            'slug' => 'gnfc-industrial-chemicals-group',
            'type' => 'Sub Sub Category',
            'level' => 'sub_sub_category',
            'parent_id' => $subGnfcIntermediates->id,
            'description' => 'Formic Acid, Acetic Acid, Urea, Capsol, Methyl Formate, Nitric Acid, Ethyl Acetate, Methanol, OTD, MTD, Aniline, Calcium Carbonate, Nitrogen Liquid.'
        ]);

        // --- INDUSTRIAL SOLVENTS SUB & SUB-SUB CATEGORIES ---
        $subSpecialtySolvents = Category::create([
            'name' => 'Specialty Solvents & Esters',
            'slug' => 'specialty-solvents-esters',
            'type' => 'Sub Category',
            'level' => 'sub_category',
            'parent_id' => $mainSolvents->id,
            'description' => 'Solvents for coatings, pharma, degreasing.'
        ]);
        $subSubSolventsEsters = Category::create([
            'name' => 'Coatings & Pharma Solvents',
            'slug' => 'coatings-pharma-solvents',
            'type' => 'Sub Sub Category',
            'level' => 'sub_sub_category',
            'parent_id' => $subSpecialtySolvents->id,
            'description' => 'IPA, Butyl Acetate, NC Thinner, Paint & Coating Solvents, Pharma Solvents, Cleaning Degreasing Solvents.'
        ]);

        // --- COAL SUB & SUB-SUB CATEGORIES ---
        $subIndustrialCoals = Category::create([
            'name' => 'Industrial Coals & Biomass',
            'slug' => 'industrial-coals-biomass',
            'type' => 'Sub Category',
            'level' => 'sub_category',
            'parent_id' => $mainCoal->id,
            'description' => 'Bio-coal and imported coal.'
        ]);
        $subSubBioImportedCoal = Category::create([
            'name' => 'Bio & Imported Coal Energy',
            'slug' => 'bio-imported-coal-energy',
            'type' => 'Sub Sub Category',
            'level' => 'sub_sub_category',
            'parent_id' => $subIndustrialCoals->id,
            'description' => 'Bio-Coal, Indonesian Coal, South African Coal, Screen Coal.'
        ]);

        echo "5-Tier Category Hierarchy created successfully.\n";

        // 4. Helper function to seed product cleanly
        $createProduct = function($data, $subSubCategoryId) {
            Product::create([
                'name' => $data['name'],
                'slug' => $data['slug'],
                'brand' => $data['brand'] ?? 'SRCIL Standard',
                'chemical_name' => $data['chemical_name'] ?? $data['name'],
                'cas_number' => $data['cas_number'] ?? 'N/A',
                'hsn_code' => $data['hsn_code'] ?? '2915',
                'purity' => $data['purity'] ?? 'Technical Grade',
                'packaging' => $data['packaging'] ?? 'Standard Drums / HDPE Bags',
                'description' => $data['description'],
                'features' => json_encode($data['features'] ?? ['High Purity Standard', 'Reliable Supply']),
                'applications' => json_encode($data['applications'] ?? ['Industrial Manufacturing']),
                'specifications' => json_encode($data['specifications'] ?? ['Assay' => 'Standard Grade']),
                'storage_info' => $data['storage_info'] ?? 'Store in cool dry warehouse.',
                'category_id' => $subSubCategoryId,
                'image_url' => $data['image_url'] ?? 'assets/img/added/product/' . $data['slug'] . '.jpg',
                'msds_url' => $data['msds_url'] ?? 'contact.php',
                'specification_url' => $data['product_url'],
                'product_url' => $data['product_url'],
                'is_featured' => $data['is_featured'] ?? false
            ]);
        };

        // 5. Seed All Products mapped under Level 3 (Sub Sub Category)
        echo "Seeding Products under Level 3 Sub Sub Categories...\n";

        // --- GACL PRODUCTS ---
        $createProduct([
            'name' => 'Caustic Soda Flakes',
            'slug' => 'caustic-soda-flakes',
            'brand' => 'GRASIM INDUSTRIES LIMITED',
            'chemical_name' => 'Sodium Hydroxide (NaOH)',
            'cas_number' => '1310-73-2',
            'hsn_code' => '28151110',
            'purity' => '99.0% Min Purity',
            'packaging' => '50 Kg HDPE Bags with inner liner',
            'description' => 'Caustic Soda Flakes is a strongly alkaline white solid flake compound used in pulp & paper, textiles, soap, and alumina refining.',
            'features' => ['High purity white crystalline solid flakes', 'Exothermic reaction in water', 'Excellent saponification'],
            'applications' => ['Textile processing & mercerizing', 'Pulp & paper manufacturing', 'Soap & detergent manufacturing', 'Water treatment pH correction'],
            'specifications' => ['NaOH' => '99.0% Min', 'Na2CO3' => '0.4% Max', 'NaCl' => '0.03% Max'],
            'storage_info' => 'Store in sealed bags in cool dry warehouse away from moisture and acids.',
            'image_url' => 'assets/img/added/product/Caustic-Soda-Flakes-NaOH.jpg',
            'msds_url' => 'assets/pdf/MSDC/CAUSTIC-SODA-FLAKES.pdf',
            'product_url' => 'caustic-soda-flakes.php',
            'is_featured' => true
        ], $subSubCausticSoda->id);

        $createProduct([
            'name' => 'Caustic Soda Lye',
            'slug' => 'caustic-soda-lye',
            'brand' => 'EPIGRAL / GACL',
            'chemical_name' => 'Sodium Hydroxide Solution (NaOH)',
            'cas_number' => '1310-73-2',
            'hsn_code' => '28151200',
            'purity' => '48% to 50% Liquid Solution',
            'packaging' => 'Bulk Road Tankers / 250 Kg HDPE Barrels',
            'description' => 'Ready-to-use liquid Sodium Hydroxide Lye (48%-50%) for automated dosing in water neutralization, textiles, and chemical plants.',
            'features' => ['Pre-dissolved liquid ready for automated dosing', 'High commercial purity', 'Supplied in dedicated tankers'],
            'applications' => ['Industrial water treatment and pH balancing', 'Textile dyeing processes', 'Detergent manufacturing'],
            'specifications' => ['NaOH Content' => '48.0% - 50.0% Min', 'Na2CO3' => '0.2% Max', 'NaCl' => '0.1% Max'],
            'storage_info' => 'Store in dedicated rubber-lined or stainless steel tanks.',
            'image_url' => 'assets/img/added/product/Caustic-Soda-Lye-NaOH.jpg',
            'msds_url' => 'assets/pdf/MSDC/CAUSTIC-SODA-LYE gacl.pdf',
            'product_url' => 'caustic-soda-lye.php',
            'is_featured' => true
        ], $subSubCausticSoda->id);

        $createProduct([
            'name' => 'Caustic Soda Prills',
            'slug' => 'caustic-soda-prills',
            'brand' => 'GACL / Reliance',
            'chemical_name' => 'Sodium Hydroxide Micro-spheres (NaOH)',
            'cas_number' => '1310-73-2',
            'hsn_code' => '28151110',
            'purity' => '99.0% Min Purity',
            'packaging' => '25 Kg / 50 Kg Moisture-Proof Bags',
            'description' => 'Free-flowing spherical micro-prills of Sodium Hydroxide engineered for non-dusty handling and rapid dissolution.',
            'features' => ['Dust-free spherical prills', 'Rapid uniform dissolution rate', 'Free-flowing structure'],
            'applications' => ['Pharmaceutical formulation', 'Water treatment feeder systems', 'Specialty soap formulations'],
            'specifications' => ['NaOH Content' => '99.0% Min', 'Particle Size' => '0.5mm - 1.0mm micro-spheres'],
            'storage_info' => 'Keep sealed in original packaging in a cool dry warehouse.',
            'image_url' => 'assets/img/added/product/Caustic-Soda-Prills-NaOH.jpg',
            'msds_url' => 'assets/pdf/MSDC/CAUSTIC-SODA-PRILLS.pdf',
            'product_url' => 'caustic-soda-prills.php',
            'is_featured' => false
        ], $subSubCausticSoda->id);

        $createProduct([
            'name' => 'Liquid Chlorine',
            'slug' => 'liquid-chlorine',
            'brand' => 'GACL / EPIGRAL',
            'chemical_name' => 'Chlorine (Cl2)',
            'cas_number' => '7782-50-5',
            'hsn_code' => '28011000',
            'purity' => '99.5% Min Liquid Chlorine',
            'packaging' => '900 Kg Tonner Cylinders',
            'description' => 'Compressed liquefied chlorine gas supplied in tonner cylinders for water chlorination, PVC manufacturing, and organic synthesis.',
            'features' => ['Liquefied elemental chlorine', 'High oxidative and bleaching efficacy'],
            'applications' => ['Municipal water chlorination and disinfection', 'PVC and chlorinated solvent synthesis'],
            'specifications' => ['Purity' => '99.5% Min', 'Moisture' => '50 ppm Max'],
            'storage_info' => 'Store in tonner cylinders in cool well-ventilated dedicated shed.',
            'image_url' => 'assets/img/added/product/Liquid-Chlorine.jpg',
            'msds_url' => 'contact.php',
            'product_url' => 'liquid-chlorine.php'
        ], $subSubChlorineBleach->id);

        $createProduct([
            'name' => 'Sodium Hypochlorite',
            'slug' => 'sodium-hypochlorite',
            'brand' => 'GACL / Standard Grade',
            'chemical_name' => 'Sodium Hypochlorite Solution (NaOCl)',
            'cas_number' => '7681-52-9',
            'hsn_code' => '28289010',
            'purity' => '10% - 12% Available Chlorine',
            'packaging' => '50 Kg HDPE Carboys / Tankers',
            'description' => 'Clear pale yellow liquid bleach solution widely used for industrial disinfection, water treatment, and textile bleaching.',
            'features' => ['High available chlorine output', 'Strong biocidal disinfectant action'],
            'applications' => ['Water disinfection & bleaching', 'Textile color stripping', 'Sanitization'],
            'specifications' => ['Available Chlorine' => '10.0% - 12.0% Min', 'Free Alkali' => '1.0% Max'],
            'storage_info' => 'Store in cool dark location in vented container.',
            'image_url' => 'assets/img/added/product/Sodium-Hypochlorite.jpg',
            'product_url' => 'sodium-hypochlorite.php'
        ], $subSubChlorineBleach->id);

        $createProduct([
            'name' => 'Stable Bleaching Powder (SBP)',
            'slug' => 'stable-bleaching-powder',
            'brand' => 'GACL / Grasim',
            'chemical_name' => 'Calcium Hypochlorite [Ca(ClO)2]',
            'cas_number' => '7778-54-3',
            'hsn_code' => '28281010',
            'purity' => '34% Min Available Chlorine',
            'packaging' => '25 Kg / 50 Kg HDPE Drums with PE Liner',
            'description' => 'White dry powder formulation of Calcium Hypochlorite engineered for water disinfection, sanitation, and textile bleaching.',
            'features' => ['High stability dry powder', '34% available chlorine content'],
            'applications' => ['Drinking water sterilization', 'Epidemic sanitization and textile bleaching'],
            'specifications' => ['Available Chlorine' => '34.0% Min', 'Moisture' => '0.3% Max'],
            'storage_info' => 'Store in airtight drums in cool dry store.',
            'image_url' => 'assets/img/added/product/Stable-Bleaching-Powder.jpg',
            'product_url' => 'stable-bleaching-powder.php'
        ], $subSubChlorineBleach->id);

        // --- HYDROGEN & PEROXIDE ---
        $createProduct([
            'name' => 'Hydrogen Peroxide (H2O2 50%)',
            'slug' => 'hydrogen-peroxide',
            'brand' => 'National Peroxide / Asian Peroxide',
            'chemical_name' => 'Hydrogen Peroxide (H2O2)',
            'cas_number' => '7722-84-1',
            'hsn_code' => '28470000',
            'purity' => '50% w/w Industrial Grade',
            'packaging' => '30 Kg / 50 Kg Vent-Cap HDPE Drums / Tankers',
            'description' => 'Eco-friendly oxidizing and bleaching agent decomposing into water and oxygen.',
            'features' => ['Eco-clean oxidant yielding only H2O and O2', 'High bleaching efficiency'],
            'applications' => ['Textile fiber bleaching', 'Pulp & paper bleaching', 'Effluent COD reduction'],
            'specifications' => ['H2O2 Concentration' => '50.0% Min', 'Stability' => '98.5% Min'],
            'storage_info' => 'Store in cool ventilated space away from direct sunlight.',
            'image_url' => 'assets/img/added/product/Hydrogen-Peroxide-H2O2.jpg',
            'product_url' => 'hydrogen-peroxide.php',
            'is_featured' => true
        ], $subSubPeroxide->id);

        $createProduct([
            'name' => 'Hydrazine Hydrate (80% / 64%)',
            'slug' => 'hydrazine-hydrate',
            'brand' => 'GACL / Arkema',
            'chemical_name' => 'Hydrazine Monohydrate (N2H4·H2O)',
            'cas_number' => '7803-57-8',
            'hsn_code' => '28251020',
            'purity' => '80% & 64% Technical Grade',
            'packaging' => '200 Kg HDPE Drums / ISO Tanks',
            'description' => 'Powerful reducing agent used in boiler water oxygen scavenging, pharma synthesis, and agrochemicals.',
            'features' => ['Zero-residue oxygen scavenger for high pressure boilers', 'High reactivity'],
            'applications' => ['Boiler feed water oxygen treatment', 'Pharma & agrochemical intermediates'],
            'specifications' => ['Hydrazine Hydrate' => '80.0% Min / 64.0% Min', 'Non-Volatile Residue' => '0.01% Max'],
            'storage_info' => 'Store in tightly sealed drums away from heat and oxidants.',
            'image_url' => 'assets/img/added/product/Hydrazine-Hydrate.jpg',
            'product_url' => 'hydrazine-hydrate.php'
        ], $subSubPeroxide->id);

        // --- CHLOROMETHANE ---
        $createProduct([
            'name' => 'Methylene Chloride (MDC)',
            'slug' => 'methylene-chloride',
            'brand' => 'EPIGRAL / SRF',
            'chemical_name' => 'Dichloromethane (CH2Cl2)',
            'cas_number' => '75-09-2',
            'hsn_code' => '29031200',
            'purity' => '99.9% Pure Solvent Grade',
            'packaging' => '250 Kg Steel Drums / ISO Tank Containers',
            'description' => 'Clear volatile chlorinated solvent with high solvency power for pharma extraction, paint stripping, foam blowing.',
            'features' => ['99.9% high purity low moisture solvent', 'High volatility with low boiling point'],
            'applications' => ['Pharma API reaction medium & extraction', 'Paint remover formulations', 'Foam blowing agent'],
            'specifications' => ['Assay' => '99.90% Min', 'Water Content' => '0.03% Max'],
            'storage_info' => 'Store in sealed tight steel drums below 30°C.',
            'image_url' => 'assets/img/added/product/Methylene-Chloride-MDC.jpg',
            'product_url' => 'methylene-chloride.php',
            'is_featured' => true
        ], $subSubChloromethane->id);

        $createProduct([
            'name' => 'Chloroform (Trichloromethane)',
            'slug' => 'chloroform',
            'brand' => 'GACL / EPIGRAL',
            'chemical_name' => 'Trichloromethane (CHCl3)',
            'cas_number' => '67-66-3',
            'hsn_code' => '29031300',
            'purity' => '99.5% Technical Grade',
            'packaging' => '280 Kg Steel Drums',
            'description' => 'High solvency chlorinated solvent utilized as precursor for refrigerant R-22 and pharma extractions.',
            'features' => ['High density solvent', 'Non-flammable chlorinated liquid'],
            'applications' => ['Pharma API extraction & synthesis', 'Fluorocarbon refrigerant manufacture'],
            'specifications' => ['Purity' => '99.5% Min', 'Acidity' => '0.001% Max'],
            'storage_info' => 'Store in dark steel drums away from sunlight.',
            'image_url' => 'assets/img/added/product/Chloroform.jpg',
            'product_url' => 'chloroform.php'
        ], $subSubChloromethane->id);

        $createProduct([
            'name' => 'Carbon Tetrachloride (CTC)',
            'slug' => 'carbon-tetrachloride',
            'brand' => 'GACL / Standard',
            'chemical_name' => 'Tetrachloromethane (CCl4)',
            'cas_number' => '56-23-5',
            'hsn_code' => '29031400',
            'purity' => '99.5% Industrial Grade',
            'packaging' => 'Steel Drums / Tankers',
            'description' => 'Specialized non-flammable organic solvent and chemical synthesis raw material.',
            'features' => ['High density clear organic liquid', 'Non-flammable'],
            'applications' => ['Chemical synthesis raw material', 'Specialty laboratory solvent'],
            'specifications' => ['Assay' => '99.5% Min', 'Moisture' => '0.02% Max'],
            'storage_info' => 'Store in cool ventilated chemical warehouse.',
            'image_url' => 'assets/img/added/product/Carbon-Tetrachloride.jpg',
            'product_url' => 'carbon-tetrachloride.php'
        ], $subSubChloromethane->id);

        $createProduct([
            'name' => 'Methyl Chloride',
            'slug' => 'methyl-chloride',
            'brand' => 'GACL Industrial',
            'chemical_name' => 'Chloromethane (CH3Cl)',
            'cas_number' => '74-87-3',
            'hsn_code' => '29031100',
            'purity' => '99.8% Compressed Gas',
            'packaging' => 'Pressurized Cylinders / Tankers',
            'description' => 'Liquefied compressed gas used in silicone polymer manufacturing, methyl cellulose, and butyl rubber.',
            'features' => ['Ultra-pure liquefied compressed gas'],
            'applications' => ['Silicone fluids and resins synthesis', 'Methylation agent in organic synthesis'],
            'specifications' => ['Purity' => '99.8% Min', 'Moisture' => '50 ppm Max'],
            'storage_info' => 'Store in dedicated pressure cylinders in cool outdoor shade.',
            'image_url' => 'assets/img/added/product/Methyl-Chloride.jpg',
            'product_url' => 'methyl-chloride.php'
        ], $subSubChloromethane->id);

        // --- POTASSIUM CHEMICALS ---
        $createProduct([
            'name' => 'Caustic Potash Flakes (KOH)',
            'slug' => 'caustic-potash-flakes',
            'brand' => 'GACL / UNID',
            'chemical_name' => 'Potassium Hydroxide (KOH)',
            'cas_number' => '1310-58-3',
            'hsn_code' => '28152000',
            'purity' => '90% Min White Flakes',
            'packaging' => '25 Kg / 50 Kg HDPE Bags',
            'description' => 'Strong alkaline potassium base in flake form used for liquid soap, potassium salts, and battery electrolytes.',
            'features' => ['90% pure white solid flakes', 'Stronger solubility than NaOH'],
            'applications' => ['Potassium soap and detergent production', 'Fertilizer & agrochemical synthesis', 'Battery electrolytes'],
            'specifications' => ['KOH' => '90.0% Min', 'K2CO3' => '0.5% Max'],
            'storage_info' => 'Store sealed in moisture-proof HDPE bags.',
            'image_url' => 'assets/img/added/product/Caustic-Potash-Flakes.jpg',
            'product_url' => 'caustic-potash-flakes.php'
        ], $subSubPotassium->id);

        $createProduct([
            'name' => 'Caustic Potash Lye',
            'slug' => 'caustic-potash-lye',
            'brand' => 'GACL Liquid Grade',
            'chemical_name' => 'Potassium Hydroxide Solution (KOH)',
            'cas_number' => '1310-58-3',
            'hsn_code' => '28152000',
            'purity' => '48% to 50% Liquid Solution',
            'packaging' => 'Road Tankers / 250 Kg HDPE Drums',
            'description' => 'Ready-to-use liquid Potassium Hydroxide Lye for liquid fertilizers, soap, and chemical processing.',
            'features' => ['Pre-dissolved liquid KOH solution', 'High commercial purity'],
            'applications' => ['Liquid fertilizer formulation', 'Specialty chemical reaction medium'],
            'specifications' => ['KOH Content' => '48.0% - 50.0% Min', 'K2CO3' => '0.3% Max'],
            'storage_info' => 'Store in dedicated rubber-lined tanks.',
            'image_url' => 'assets/img/added/product/Caustic-Potash-Lye.jpg',
            'product_url' => 'caustic-potash-lye.php'
        ], $subSubPotassium->id);

        $createProduct([
            'name' => 'Potassium Carbonate',
            'slug' => 'potassium-carbonate',
            'brand' => 'GACL Granular',
            'chemical_name' => 'Potassium Carbonate (K2CO3)',
            'cas_number' => '584-08-7',
            'hsn_code' => '28364000',
            'purity' => '99.0% Min Pure White Powder',
            'packaging' => '25 Kg / 50 Kg Bags',
            'description' => 'White hygroscopic powder used in specialty glass, ceramics, soap, and pharmaceutical processing.',
            'features' => ['High purity white powder', 'Soluble in water yielding alkaline solution'],
            'applications' => ['Specialty glass & TV tube glass manufacturing', 'Ceramic glazes and pharma synthesis'],
            'specifications' => ['K2CO3' => '99.0% Min', 'KOH' => '0.2% Max', 'KCl' => '0.03% Max'],
            'storage_info' => 'Store in dry sealed bags.',
            'image_url' => 'assets/img/added/product/Potassium-Carbonate.jpg',
            'product_url' => 'potassium-carbonate.php'
        ], $subSubPotassium->id);

        // --- ALUMINIUM CHEMICALS ---
        $createProduct([
            'name' => 'Anhydrous Aluminium Chloride',
            'slug' => 'anhydrous-aluminium-chloride',
            'brand' => 'GACL Powder',
            'chemical_name' => 'Aluminium Chloride Anhydrous (AlCl3)',
            'cas_number' => '7446-70-0',
            'hsn_code' => '28273200',
            'purity' => '99.0% Technical Grade Granular',
            'packaging' => '25 Kg / 50 Kg Moisture-Proof Steel Drums / Bags',
            'description' => 'Lewis acid catalyst essential for Friedel-Crafts reactions, ethylbenzene synthesis, dyes, and pharmaceutical APIs.',
            'features' => ['High purity Lewis acid catalyst', 'Fuming in moist air'],
            'applications' => ['Friedel-Crafts alkylation & acylation reactions', 'Dyes and agrochemical intermediates'],
            'specifications' => ['AlCl3' => '99.0% Min', 'Fe' => '0.01% Max', 'Water Insolubles' => '0.05% Max'],
            'storage_info' => 'Store in airtight steel drums in dry moisture-free store.',
            'image_url' => 'assets/img/added/product/Anhydrous-Aluminium-Chloride.jpg',
            'product_url' => 'anhydrous-aluminium-chloride.php'
        ], $subSubAluminium->id);

        $createProduct([
            'name' => 'Poly Aluminium Chloride (PAC Powder)',
            'slug' => 'poly-aluminium-chloride',
            'brand' => 'GACL / Standard Grade',
            'chemical_name' => 'Polyaluminium Chloride [Aln(OH)mCl3n-m]',
            'cas_number' => '1327-41-9',
            'hsn_code' => '28273200',
            'purity' => '30% Al2O3 Yellow Powder',
            'packaging' => '25 Kg Laminated Bags',
            'description' => 'Inorganic polymer coagulant for municipal drinking water purification, paper sizing, and wastewater treatment.',
            'features' => ['Rapid floc formation and fast settling rate', 'Wide pH operating range (5.0-9.0)'],
            'applications' => ['Drinking water purification', 'Industrial effluent coagulation', 'Paper retention aid'],
            'specifications' => ['Al2O3' => '30.0% Min', 'Basicity' => '50% - 85%', 'pH (1% soln)' => '3.5 - 5.0'],
            'storage_info' => 'Store powder in dry cool warehouse.',
            'image_url' => 'assets/img/added/product/Poly-Aluminium-Chloride-PAC.jpg',
            'product_url' => 'poly-aluminium-chloride.php',
            'is_featured' => true
        ], $subSubAluminium->id);

        $createProduct([
            'name' => 'Poly Aluminium Liquid Chloride',
            'slug' => 'poly-aluminium-liquid-chloride',
            'brand' => 'GACL Liquid',
            'chemical_name' => 'Liquid Polyaluminium Chloride Solution',
            'cas_number' => '1327-41-9',
            'hsn_code' => '28273200',
            'purity' => '10% - 12% Al2O3 Liquid Solution',
            'packaging' => 'Road Tankers / HDPE Carboys',
            'description' => 'Ready-to-dose liquid coagulant solution for water plants and industrial wastewater coagulation.',
            'features' => ['Pre-dissolved liquid coagulant', 'Fast settling'],
            'applications' => ['Wastewater treatment plants', 'Industrial effluent clarification'],
            'specifications' => ['Al2O3 Content' => '10.0% - 12.0% Min', 'Basicity' => '50% - 80%'],
            'storage_info' => 'Store in FRP or HDPE storage tanks.',
            'image_url' => 'assets/img/added/product/Poly-Aluminium-Liquid-Chloride.jpg',
            'product_url' => 'poly-aluminium-liquid-chloride.php'
        ], $subSubAluminium->id);

        // --- WATER TREATMENT & PHOSPHATES ---
        $createProduct([
            'name' => 'Industrial Salt (Water Treatment)',
            'slug' => 'industrial-salt-water-treatment-chlor-alkali',
            'brand' => 'SRCIL Pure Salt',
            'chemical_name' => 'Sodium Chloride (NaCl)',
            'cas_number' => '7647-14-5',
            'hsn_code' => '25010010',
            'purity' => '99.0% Min Pure NaCl Grade',
            'packaging' => '50 Kg HDPE Bags / Bulk Loose',
            'description' => 'High purity refined industrial salt used for water softener resin regeneration and chlor-alkali brine preparation.',
            'features' => ['High purity low moisture salt', 'Minimal calcium & magnesium impurities'],
            'applications' => ['Water softener ion exchange resin regeneration', 'Chlor-alkali cell brine preparation'],
            'specifications' => ['NaCl' => '99.0% Min', 'Moisture' => '0.5% Max', 'Insolubles' => '0.05% Max'],
            'storage_info' => 'Store in covered dry storage shed.',
            'image_url' => 'assets/img/added/product/Industrial-Salt.jpg',
            'product_url' => 'industrial-salt-water-treatment-chlor-alkali.php'
        ], $subSubWaterTreatment->id);

        $createProduct([
            'name' => 'Food Grade Phosphoric Acid',
            'slug' => 'food-grade-phosphoric-acid',
            'brand' => 'Aditya Birla / Import Grade',
            'chemical_name' => 'Orthophosphoric Acid (H3PO4)',
            'cas_number' => '7664-38-2',
            'hsn_code' => '28092010',
            'purity' => '85% Food & Pharma Grade',
            'packaging' => '35 Kg HDPE Carboys / 300 Kg Drums',
            'description' => 'Ultra-pure food and pharma grade phosphoric acid for beverage acidulating, food processing, and phosphate salts.',
            'features' => ['85% food grade high purity mineral acid', 'Compliant with FCC & IP/BP standards'],
            'applications' => ['Beverage acidulant (cola drinks)', 'Pharma phosphate buffer synthesis', 'Food processing'],
            'specifications' => ['H3PO4' => '85.0% Min', 'Arsenic' => '1 ppm Max', 'Heavy Metals' => '5 ppm Max'],
            'storage_info' => 'Store in clean HDPE carboys away from bases.',
            'image_url' => 'assets/img/added/product/Food-Grade-Phosphoric-Acid.jpg',
            'product_url' => 'food-grade-phosphoric-acid.php'
        ], $subSubPhosphate->id);

        $createProduct([
            'name' => 'Pharma & Analytical Grade Phosphate Salts',
            'slug' => 'pharma-analytical-grade-phosphate-salts',
            'brand' => 'SRCIL Pure Grade',
            'chemical_name' => 'Sodium & Potassium Phosphate Salts',
            'cas_number' => '7558-79-4',
            'hsn_code' => '28352200',
            'purity' => '99.5% IP/BP/USP Grade',
            'packaging' => '25 Kg Bags / Fiber Drums',
            'description' => 'High purity phosphate salts including MSP, DSP, TSP, and MKP for pharmaceutical buffering and analytical chemistry.',
            'features' => ['High purity pharmaceutical grade', 'Strict trace metal control'],
            'applications' => ['Pharmaceutical formulation buffering', 'Laboratory analytical reagent'],
            'specifications' => ['Purity' => '99.5% Min', 'Heavy Metals' => '10 ppm Max'],
            'storage_info' => 'Store in sealed drums in dry place.',
            'image_url' => 'assets/img/added/product/Phosphate-Salts.jpg',
            'product_url' => 'pharma-analytical-grade-phosphate-salts.php'
        ], $subSubPhosphate->id);

        // --- OTHER SPECIALTY CHEMICALS ---
        $createProduct([
            'name' => 'Chlorinated Paraffin (CP 52%)',
            'slug' => 'chlorinated-paraffin',
            'brand' => 'SRCIL Standard',
            'chemical_name' => 'Chlorinated Paraffin Wax (C14-C17)',
            'cas_number' => '63449-39-8',
            'hsn_code' => '38249900',
            'purity' => '52% Chlorine Content Liquid',
            'packaging' => '250 Kg Drums / Tankers',
            'description' => 'Secondary plasticizer and flame retardant additive for PVC compounds, cables, lubricants, and sealants.',
            'features' => ['High thermal stability flame retardant plasticizer'],
            'applications' => ['PVC cable compounding', 'Metalworking fluid extreme pressure additive'],
            'specifications' => ['Chlorine Content' => '52.0% ± 1.0%', 'Viscosity at 25°C' => '15 - 25 Poise'],
            'storage_info' => 'Store in mild steel or HDPE drums.',
            'image_url' => 'assets/img/added/product/Chlorinated-Paraffin.jpg',
            'product_url' => 'chlorinated-paraffin.php'
        ], $subSubOtherSpecialty->id);

        $createProduct([
            'name' => 'Benzyl Chloride',
            'slug' => 'benzyl-chloride',
            'brand' => 'GACL / Standard',
            'chemical_name' => 'Alpha-Chlorotoluene (C7H7Cl)',
            'cas_number' => '100-44-7',
            'hsn_code' => '29039910',
            'purity' => '99.0% Technical Grade',
            'packaging' => '200 Kg Steel Drums',
            'description' => 'Organic intermediate for benzyl alcohol, benzyl esters, quaternary ammonium salts, and pharma synthesis.',
            'features' => ['Pungent clear lachrymatory liquid', 'High organic reactivity'],
            'applications' => ['Benzyl alcohol & benzyl acetate synthesis', 'Pharma API intermediates'],
            'specifications' => ['Assay' => '99.0% Min', 'Benzal Chloride' => '0.3% Max'],
            'storage_info' => 'Store in glass-lined or specialized lined steel drums.',
            'image_url' => 'assets/img/added/product/Benzyl-Chloride.jpg',
            'product_url' => 'benzyl-chloride.php'
        ], $subSubOtherSpecialty->id);

        $createProduct([
            'name' => 'Sodium Chlorate',
            'slug' => 'sodium-chlorate',
            'brand' => 'GACL / Standard',
            'chemical_name' => 'Sodium Chlorate (NaClO3)',
            'cas_number' => '7775-09-9',
            'hsn_code' => '28291100',
            'purity' => '99.0% Min White Crystals',
            'packaging' => '25 Kg / 50 Kg PP Bags with PE Liner',
            'description' => 'Powerful oxidizing agent utilized for chlorine dioxide generation in pulp bleaching and herbicide formulations.',
            'features' => ['Strong solid oxidant crystal', 'High chlorine dioxide yield'],
            'applications' => ['Pulp & paper ECF bleaching chlorine dioxide generation', 'Agrochemical formulations'],
            'specifications' => ['NaClO3' => '99.0% Min', 'NaCl' => '0.2% Max', 'Moisture' => '0.05% Max'],
            'storage_info' => 'Store away from organic materials and combustibles.',
            'image_url' => 'assets/img/added/product/Sodium-Chlorate.jpg',
            'product_url' => 'sodium-chlorate.php'
        ], $subSubOtherSpecialty->id);

        $createProduct([
            'name' => 'Monochloro Acetic Acid (MCA)',
            'slug' => 'monochloro-acetic-acid',
            'brand' => 'Standard Grade',
            'chemical_name' => 'Chloroacetic Acid (ClCH2COOH)',
            'cas_number' => '79-11-8',
            'hsn_code' => '29154010',
            'purity' => '99.0% Flakes Grade',
            'packaging' => '25 Kg Bags',
            'description' => 'Organochlorine compound used as building block for CMC (Carboxymethyl Cellulose), agrochemicals, and pharma.',
            'features' => ['99% pure white flakes', 'High reactivity chloro-acylating agent'],
            'applications' => ['Carboxymethyl Cellulose (CMC) manufacturing', '2,4-D herbicide synthesis'],
            'specifications' => ['MCA' => '99.0% Min', 'DCA' => '0.5% Max'],
            'storage_info' => 'Store in cool dry warehouse protected from moisture.',
            'image_url' => 'assets/img/added/product/Monochloro-Acetic-Acid.jpg',
            'product_url' => 'monochloro-acetic-acid.php'
        ], $subSubOtherSpecialty->id);

        // --- ORGANIC PRODUCTS: CHLOROBENZENES & NITRO ---
        $createProduct([
            'name' => 'Mono Chloro Benzene (MCB)',
            'slug' => 'mono-chloro-benzene',
            'brand' => 'Organic Fine Grade',
            'chemical_name' => 'Chlorobenzene (C6H5Cl)',
            'cas_number' => '108-90-7',
            'hsn_code' => '29039110',
            'purity' => '99.9% Pure Grade',
            'packaging' => '200 Kg Drums / Tankers',
            'description' => 'Colorless clear organic solvent used as intermediate in nitrochlorobenzene and phenol synthesis.',
            'features' => ['99.9% high purity clear solvent liquid'],
            'applications' => ['Intermediate for PNCB/ONCB synthesis', 'Process solvent in pharma'],
            'specifications' => ['Assay' => '99.9% Min', 'Benzene' => '0.05% Max'],
            'storage_info' => 'Store in steel drums away from fire sources.',
            'image_url' => 'assets/img/added/product/Mono-Chloro-Benzene.jpg',
            'product_url' => 'mono-chloro-benzene.php'
        ], $subSubChlorobenzenes->id);

        $createProduct([
            'name' => 'Para Di Chloro Benzene (PDCB)',
            'slug' => 'para-di-chloro-benzene',
            'brand' => 'Pure Flakes Grade',
            'chemical_name' => '1,4-Dichlorobenzene (C6H4Cl2)',
            'cas_number' => '106-46-7',
            'hsn_code' => '29039120',
            'purity' => '99.8% White Crystals',
            'packaging' => '25 Kg HDPE Bags / Drums',
            'description' => 'White crystalline solid compound used as disinfectant moth repellant and precursor for PPS polymer.',
            'features' => ['High purity white crystalline solid', 'Strong characteristic odor'],
            'applications' => ['PPS (Polyphenylene Sulfide) high temp resin manufacturing', 'Disinfectant & moth repellent'],
            'specifications' => ['PDCB' => '99.8% Min', 'Crystallization Point' => '52.8 °C Min'],
            'storage_info' => 'Store in sealed bags in cool place.',
            'image_url' => 'assets/img/added/product/Para-Di-Chloro-Benzene.jpg',
            'product_url' => 'para-di-chloro-benzene.php'
        ], $subSubChlorobenzenes->id);

        $createProduct([
            'name' => 'Ortho Di Chloro Benzene (ODCB)',
            'slug' => 'ortho-di-chloro-benzene',
            'brand' => 'Industrial Solvent Grade',
            'chemical_name' => '1,2-Dichlorobenzene (C6H4Cl2)',
            'cas_number' => '95-50-1',
            'hsn_code' => '29039130',
            'purity' => '99.5% Liquid Grade',
            'packaging' => '250 Kg Drums',
            'description' => 'High boiling heavy liquid chlorinated solvent used in paint removal, degreasing, and chemical reaction medium.',
            'features' => ['High boiling point (180.5°C) heavy solvent'],
            'applications' => ['Engine degreasing & carbon removal', 'TDI synthesis reaction medium'],
            'specifications' => ['ODCB' => '99.5% Min', 'Moisture' => '0.03% Max'],
            'storage_info' => 'Store in steel drums.',
            'image_url' => 'assets/img/added/product/Ortho-Di-Chloro-Benzene.jpg',
            'product_url' => 'ortho-di-chloro-benzene.php'
        ], $subSubChlorobenzenes->id);

        $createProduct([
            'name' => '1,2,4 Tri Chloro Benzene (TCB)',
            'slug' => '1-2-4-tri-chloro-benzene',
            'brand' => 'Specialty Solvent',
            'chemical_name' => '1,2,4-Trichlorobenzene (C6H3Cl3)',
            'cas_number' => '120-82-1',
            'hsn_code' => '29039990',
            'purity' => '99.0% Pure Liquid',
            'packaging' => '250 Kg Drums',
            'description' => 'High thermal stability organic solvent used as dye carrier and chemical intermediate.',
            'features' => ['High thermal resistance solvent'],
            'applications' => ['Textile dye carrier', 'Intermediate for herbicides'],
            'specifications' => ['Assay' => '99.0% Min'],
            'storage_info' => 'Store in steel drums.',
            'image_url' => 'assets/img/added/product/1-2-4-Tri-Chloro-Benzene.jpg',
            'product_url' => '1-2-4-tri-chloro-benzene.php'
        ], $subSubChlorobenzenes->id);

        $createProduct([
            'name' => '2,4 Di Nitro Chloro Benzene (2,4 DNCB)',
            'slug' => '2-4-di-nitro-chloro-benzene',
            'brand' => 'Intermediate Grade',
            'chemical_name' => '1-Chloro-2,4-dinitrobenzene (C6H3ClN2O4)',
            'cas_number' => '97-00-7',
            'hsn_code' => '29042090',
            'purity' => '99.0% Solid Crystalline',
            'packaging' => '50 Kg Drums',
            'description' => 'Aromatic nitro compound used in azo dye synthesis, sulfur dyes, and pharma.',
            'features' => ['Yellow crystalline solid', 'High chemical reactivity'],
            'applications' => ['Dyes and pigments intermediate'],
            'specifications' => ['Assay' => '99.0% Min', 'Crystallization Point' => '48.0 °C Min'],
            'storage_info' => 'Store in cool dry warehouse away from heat.',
            'image_url' => 'assets/img/added/product/2-4-Di-Nitro-Chloro-Benzene.jpg',
            'product_url' => '2-4-di-nitro-chloro-benzene.php'
        ], $subSubNitroChlorobenzene->id);

        $createProduct([
            'name' => '2,5 Di Chloro Nitro Benzene (2,5 DNCB)',
            'slug' => '2-5-di-chloro-nitro-benzene',
            'brand' => 'Dye Intermediate',
            'chemical_name' => '1,4-Dichloro-2-nitrobenzene (C6H3Cl2NO2)',
            'cas_number' => '89-61-2',
            'hsn_code' => '29042090',
            'purity' => '99.0% Min Solid',
            'packaging' => '50 Kg Bags / Drums',
            'description' => 'Key organic intermediate for 2,5-dichloroaniline and pigment synthesis.',
            'features' => ['Pure yellow crystalline solid'],
            'applications' => ['Pigments & dye intermediate'],
            'specifications' => ['Assay' => '99.0% Min'],
            'storage_info' => 'Store in sealed bags.',
            'image_url' => 'assets/img/added/product/2-5-Di-Chloro-Nitro-Benzene.jpg',
            'product_url' => '2-5-di-chloro-nitro-benzene.php'
        ], $subSubNitroChlorobenzene->id);

        $createProduct([
            'name' => '3,4 Di Chloro Nitro Benzene (3,4 DCNB)',
            'slug' => '3-4-di-chloro-nitro-benzene',
            'brand' => 'Fine Intermediate',
            'chemical_name' => '1,2-Dichloro-4-nitrobenzene (C6H3Cl2NO2)',
            'cas_number' => '99-54-7',
            'hsn_code' => '29042090',
            'purity' => '99.0% Pure Flakes',
            'packaging' => '50 Kg Bags',
            'description' => 'Intermediate for 3,4-dichloroaniline and agrochemical synthesis.',
            'features' => ['High purity nitro aromatic intermediate'],
            'applications' => ['Agrochemical & herbicide synthesis'],
            'specifications' => ['Assay' => '99.0% Min'],
            'storage_info' => 'Store in dry warehouse.',
            'image_url' => 'assets/img/added/product/3-4-Di-Chloro-Nitro-Benzene.jpg',
            'product_url' => '3-4-di-chloro-nitro-benzene.php'
        ], $subSubNitroChlorobenzene->id);

        $createProduct([
            'name' => 'Nitrobenzene (NB)',
            'slug' => 'nitrobenzene-nb',
            'brand' => 'Industrial Grade',
            'chemical_name' => 'Nitrobenzene (C6H5NO2)',
            'cas_number' => '98-95-3',
            'hsn_code' => '29042010',
            'purity' => '99.8% Pure Yellow Liquid',
            'packaging' => '250 Kg Drums / Tankers',
            'description' => 'Pale yellow oily liquid with almond odor, primary precursor for aniline and plant growth promoters.',
            'features' => ['99.8% high purity oily liquid'],
            'applications' => ['Aniline manufacturing precursor', 'Plant growth booster formulations'],
            'specifications' => ['Assay' => '99.8% Min', 'Moisture' => '0.1% Max'],
            'storage_info' => 'Store in tight steel drums away from light.',
            'image_url' => 'assets/img/added/product/Nitrobenzene.jpg',
            'product_url' => 'nitrobenzene-nb.php'
        ], $subSubNitroChlorobenzene->id);

        $createProduct([
            'name' => 'Para Nitro Chloro Benzene (PNCB)',
            'slug' => 'para-nitro-chloro-benzene',
            'brand' => 'Standard Grade',
            'chemical_name' => '1-Chloro-4-nitrobenzene (C6H4ClNO2)',
            'cas_number' => '100-00-5',
            'hsn_code' => '29042020',
            'purity' => '99.5% Yellow Crystals',
            'packaging' => '250 Kg Drums / Bulk Molten Tankers',
            'description' => 'Key building block for paracetamol (acetaminophen), dyes, and rubber chemicals.',
            'features' => ['99.5% high purity crystalline solid'],
            'applications' => ['Paracetamol API intermediate synthesis', 'Rubber antioxidant synthesis'],
            'specifications' => ['PNCB' => '99.5% Min', 'ONCB' => '0.3% Max'],
            'storage_info' => 'Store in dry cool space.',
            'image_url' => 'assets/img/added/product/Para-Nitro-Chloro-Benzene.jpg',
            'product_url' => 'para-nitro-chloro-benzene.php'
        ], $subSubNitroChlorobenzene->id);

        $createProduct([
            'name' => 'Ortho Nitro Chloro Benzene (ONCB)',
            'slug' => 'ortho-nitro-chloro-benzene',
            'brand' => 'Standard Grade',
            'chemical_name' => '1-Chloro-2-nitrobenzene (C6H4ClNO2)',
            'cas_number' => '88-73-3',
            'hsn_code' => '29042020',
            'purity' => '99.5% Crystalline Solid',
            'packaging' => '250 Kg Drums / Tankers',
            'description' => 'Precursor for ortho-anisidine, ortho-aniline, dyes, and pharmaceutical synthesis.',
            'features' => ['High purity aromatic nitro compound'],
            'applications' => ['Ortho-anisidine & dye intermediates synthesis'],
            'specifications' => ['ONCB' => '99.5% Min', 'PNCB' => '0.3% Max'],
            'storage_info' => 'Store in tightly closed drums.',
            'image_url' => 'assets/img/added/product/Ortho-Nitro-Chloro-Benzene.jpg',
            'product_url' => 'ortho-nitro-chloro-benzene.php'
        ], $subSubNitroChlorobenzene->id);

        $createProduct([
            'name' => 'Meta Nitro Chloro Benzene (MNCB)',
            'slug' => 'meta-nitro-chloro-benzene',
            'brand' => 'Fine Grade',
            'chemical_name' => '1-Chloro-3-nitrobenzene (C6H4ClNO2)',
            'cas_number' => '121-73-3',
            'hsn_code' => '29042020',
            'purity' => '99.0% Yellow Flakes',
            'packaging' => '50 Kg Bags / Drums',
            'description' => 'Intermediate for meta-chloroaniline and pharmaceutical synthesis.',
            'features' => ['99% pure yellow flakes'],
            'applications' => ['Meta-chloroaniline & pharma synthesis'],
            'specifications' => ['MNCB' => '99.0% Min'],
            'storage_info' => 'Store in dry cool space.',
            'image_url' => 'assets/img/added/product/Meta-Nitro-Chloro-Benzene.jpg',
            'product_url' => 'meta-nitro-chloro-benzene.php'
        ], $subSubNitroChlorobenzene->id);

        // --- CHLORO ANILINES & ANISIDINES ---
        $createProduct([
            'name' => 'Para Chloro Aniline (PCA)',
            'slug' => 'para-chloro-aniline',
            'brand' => 'Organic Grade',
            'chemical_name' => '4-Chloroaniline (C6H6ClN)',
            'cas_number' => '106-47-8',
            'hsn_code' => '29214220',
            'purity' => '99.0% Solid Grade',
            'packaging' => '200 Kg Drums',
            'description' => 'Aromatic amine intermediate used in pesticides, azo dyes, and pharmaceuticals.',
            'features' => ['High purity solid aromatic amine'],
            'applications' => ['Agrochemical pesticide & dye synthesis'],
            'specifications' => ['Assay' => '99.0% Min'],
            'storage_info' => 'Store in sealed dark drums.',
            'image_url' => 'assets/img/added/product/Para-Chloro-Aniline.jpg',
            'product_url' => 'para-chloro-aniline.php'
        ], $subSubChloroAnilines->id);

        $createProduct([
            'name' => 'Meta Chloro Aniline (MCA)',
            'slug' => 'meta-chloro-aniline',
            'brand' => 'Organic Grade',
            'chemical_name' => '3-Chloroaniline (C6H6ClN)',
            'cas_number' => '108-42-9',
            'hsn_code' => '29214220',
            'purity' => '99.0% Pure Liquid',
            'packaging' => '200 Kg Drums',
            'description' => 'Amber liquid organic amine used in agricultural fungicides and pharma.',
            'features' => ['Clear amber amine liquid'],
            'applications' => ['Fungicide & pharmaceutical intermediates'],
            'specifications' => ['Assay' => '99.0% Min'],
            'storage_info' => 'Store in dark steel drums.',
            'image_url' => 'assets/img/added/product/Meta-Chloro-Aniline.jpg',
            'product_url' => 'meta-chloro-aniline.php'
        ], $subSubChloroAnilines->id);

        $createProduct([
            'name' => 'Ortho Chloro Aniline (OCA)',
            'slug' => 'ortho-chloro-aniline',
            'brand' => 'Organic Grade',
            'chemical_name' => '2-Chloroaniline (C6H6ClN)',
            'cas_number' => '95-51-2',
            'hsn_code' => '29214220',
            'purity' => '99.0% Liquid Grade',
            'packaging' => '200 Kg Drums',
            'description' => 'Amber clear liquid used in petroleum additives, dyes, and agrochemicals.',
            'features' => ['High purity liquid amine'],
            'applications' => ['Petroleum additive & dye intermediate'],
            'specifications' => ['Assay' => '99.0% Min'],
            'storage_info' => 'Store in sealed steel drums.',
            'image_url' => 'assets/img/added/product/Ortho-Chloro-Aniline.jpg',
            'product_url' => 'ortho-chloro-aniline.php'
        ], $subSubChloroAnilines->id);

        $createProduct([
            'name' => '2,5 Di Chloro Aniline (2,5 DCA)',
            'slug' => '2-5-di-chloro-aniline',
            'brand' => 'Specialty Amine',
            'chemical_name' => '2,5-Dichloroaniline (C6H5Cl2N)',
            'cas_number' => '95-82-9',
            'hsn_code' => '29214290',
            'purity' => '99.0% Solid Crystalline',
            'packaging' => '50 Kg Bags / Drums',
            'description' => 'Intermediate for fast pigments and agrochemical herbicides.',
            'features' => ['Pure brown-white crystals'],
            'applications' => ['Pigments & herbicide manufacturing'],
            'specifications' => ['Assay' => '99.0% Min'],
            'storage_info' => 'Store in dry shed.',
            'image_url' => 'assets/img/added/product/2-5-Di-Chloro-Aniline.jpg',
            'product_url' => '2-5-di-chloro-aniline.php'
        ], $subSubChloroAnilines->id);

        $createProduct([
            'name' => '3,4 Di Chloro Aniline (3,4 DCA)',
            'slug' => '3-4-di-chloro-aniline',
            'brand' => 'Agro Intermediate',
            'chemical_name' => '3,4-Dichloroaniline (C6H5Cl2N)',
            'cas_number' => '95-76-1',
            'hsn_code' => '29214290',
            'purity' => '99.0% Pure Solid',
            'packaging' => '50 Kg Bags',
            'description' => 'Key raw material for Diuron and Propanil herbicides.',
            'features' => ['High purity agrochemical building block'],
            'applications' => ['Diuron & Propanil herbicide production'],
            'specifications' => ['Assay' => '99.0% Min'],
            'storage_info' => 'Store in dry place.',
            'image_url' => 'assets/img/added/product/3-4-Di-Chloro-Aniline.jpg',
            'product_url' => '3-4-di-chloro-aniline.php'
        ], $subSubChloroAnilines->id);

        $createProduct([
            'name' => '2,4,5 Tri Chloro Aniline (TCA)',
            'slug' => '2-4-5-tri-chloro-aniline',
            'brand' => 'Fine Chemical',
            'chemical_name' => '2,4,5-Trichloroaniline (C6H4Cl3N)',
            'cas_number' => '634-93-5',
            'hsn_code' => '29214290',
            'purity' => '99.0% Pure Crystals',
            'packaging' => '25 Kg Fiber Drums',
            'description' => 'Specialty organic intermediate for pigments and agrochemicals.',
            'features' => ['High purity solid crystals'],
            'applications' => ['Specialty pigment synthesis'],
            'specifications' => ['Assay' => '99.0% Min'],
            'storage_info' => 'Store in dark fiber drums.',
            'image_url' => 'assets/img/added/product/2-4-5-Tri-Chloro-Aniline.jpg',
            'product_url' => '2-4-5-tri-chloro-aniline.php'
        ], $subSubChloroAnilines->id);

        $createProduct([
            'name' => 'Ortho Anisidine (OA)',
            'slug' => 'ortho-anisidine',
            'brand' => 'Organic Grade',
            'chemical_name' => '2-Methoxyaniline (C7H9NO)',
            'cas_number' => '90-04-0',
            'hsn_code' => '29222910',
            'purity' => '99.0% Clear Liquid',
            'packaging' => '200 Kg Drums',
            'description' => 'Reddish-brown liquid amine precursor for azo dyes, pigments, and pharma.',
            'features' => ['Clear reddish-brown aromatic liquid'],
            'applications' => ['Azo dye & pigment manufacturing'],
            'specifications' => ['Assay' => '99.0% Min'],
            'storage_info' => 'Store in tight steel drums.',
            'image_url' => 'assets/img/added/product/Ortho-Anisidine.jpg',
            'product_url' => 'ortho-anisidine.php'
        ], $subSubAnisidinesToluidines->id);

        $createProduct([
            'name' => 'Para Anisidine (PA)',
            'slug' => 'para-anisidine',
            'brand' => 'Organic Grade',
            'chemical_name' => '4-Methoxyaniline (C7H9NO)',
            'cas_number' => '104-94-9',
            'hsn_code' => '29222910',
            'purity' => '99.0% Crystalline Flakes',
            'packaging' => '50 Kg Drums',
            'description' => 'Grey-white crystalline solid used as dye intermediate and pharmaceutical precursor.',
            'features' => ['High purity crystalline flakes'],
            'applications' => ['Dyes and pharma intermediate'],
            'specifications' => ['Assay' => '99.0% Min'],
            'storage_info' => 'Store in dry place.',
            'image_url' => 'assets/img/added/product/Para-Anisidine.jpg',
            'product_url' => 'para-anisidine.php'
        ], $subSubAnisidinesToluidines->id);

        $createProduct([
            'name' => 'Para Toluidine (PT)',
            'slug' => 'para-toluidine',
            'brand' => 'Industrial Grade',
            'chemical_name' => '4-Methylaniline (C7H9N)',
            'cas_number' => '106-49-0',
            'hsn_code' => '29214310',
            'purity' => '99.0% Solid Flakes',
            'packaging' => '50 Kg Drums',
            'description' => 'White crystalline solid amine used in dye manufacture and organic synthesis.',
            'features' => ['Pure white flakes solid'],
            'applications' => ['Dyes, pigments, and organic chemicals'],
            'specifications' => ['Assay' => '99.0% Min'],
            'storage_info' => 'Store in sealed containers.',
            'image_url' => 'assets/img/added/product/Para-Toluidine.jpg',
            'product_url' => 'para-toluidine.php'
        ], $subSubAnisidinesToluidines->id);

        $createProduct([
            'name' => 'Ortho Toluidine (OT)',
            'slug' => 'ortho-toluidine',
            'brand' => 'Industrial Grade',
            'chemical_name' => '2-Methylaniline (C7H9N)',
            'cas_number' => '95-53-4',
            'hsn_code' => '29214310',
            'purity' => '99.0% Liquid Grade',
            'packaging' => '200 Kg Drums',
            'description' => 'Clear liquid amine used in rubber vulcanization accelerators and dyes.',
            'features' => ['Clear aromatic liquid amine'],
            'applications' => ['Rubber accelerators & dye synthesis'],
            'specifications' => ['Assay' => '99.0% Min'],
            'storage_info' => 'Store in tight steel drums.',
            'image_url' => 'assets/img/added/product/Ortho-Toluidine.jpg',
            'product_url' => 'ortho-toluidine.php'
        ], $subSubAnisidinesToluidines->id);

        $createProduct([
            'name' => 'Meta Toluidine (MT)',
            'slug' => 'meta-toluidine',
            'brand' => 'Industrial Grade',
            'chemical_name' => '3-Methylaniline (C7H9N)',
            'cas_number' => '108-44-1',
            'hsn_code' => '29214310',
            'purity' => '99.0% Liquid Grade',
            'packaging' => '200 Kg Drums',
            'description' => 'Liquid organic amine used in pesticide intermediates and dyes.',
            'features' => ['High purity liquid intermediate'],
            'applications' => ['Pesticides & specialty dye synthesis'],
            'specifications' => ['Assay' => '99.0% Min'],
            'storage_info' => 'Store in steel drums.',
            'image_url' => 'assets/img/added/product/Meta-Toluidine.jpg',
            'product_url' => 'meta-toluidine.php'
        ], $subSubAnisidinesToluidines->id);

        // --- CALCIUM CHLORIDES & BENZENE & SPENT ACIDS ---
        $createProduct([
            'name' => 'Calcium Chloride Prills/Powder (94–97%)',
            'slug' => 'calcium-chloride-prills-powder',
            'brand' => 'SRCIL Pure Grade',
            'chemical_name' => 'Calcium Chloride Anhydrous (CaCl2)',
            'cas_number' => '10043-52-4',
            'hsn_code' => '28272000',
            'purity' => '94% - 97% Anhydrous Prills',
            'packaging' => '25 Kg / 1000 Kg Jumbo Bags',
            'description' => 'High purity anhydrous prills used for oil well drilling muds, dust suppression, and industrial desiccant.',
            'features' => ['94-97% high strength anhydrous prills', 'Exothermic dissolution in water'],
            'applications' => ['Oilfield drilling completion fluids', 'Industrial moisture desiccant & de-icing'],
            'specifications' => ['CaCl2' => '94.0% - 97.0% Min', 'Alkali Chlorides' => '2.0% Max'],
            'storage_info' => 'Store in moisture-proof sealed bags.',
            'image_url' => 'assets/img/added/product/Calcium-Chloride-Prills.jpg',
            'product_url' => 'calcium-chloride-prills-powder.php'
        ], $subSubCalciumBenzene->id);

        $createProduct([
            'name' => 'Calcium Chloride Brine (Solution)',
            'slug' => 'calcium-chloride-brine',
            'brand' => 'Industrial Liquid',
            'chemical_name' => 'Calcium Chloride Solution (CaCl2)',
            'cas_number' => '10043-52-4',
            'hsn_code' => '28272000',
            'purity' => '30% to 35% Liquid Brine',
            'packaging' => 'Road Tankers / HDPE Carboys',
            'description' => 'Heavy density liquid brine used as industrial refrigeration coolant and oilwell completion fluid.',
            'features' => ['Pre-mixed low freezing point brine'],
            'applications' => ['Industrial refrigeration chilling brine', 'Dust control on unpaved roads'],
            'specifications' => ['CaCl2 Content' => '30.0% - 35.0% Min'],
            'storage_info' => 'Store in rubber-lined or plastic tanks.',
            'image_url' => 'assets/img/added/product/Calcium-Chloride-Brine.jpg',
            'product_url' => 'calcium-chloride-brine.php'
        ], $subSubCalciumBenzene->id);

        $createProduct([
            'name' => 'Benzene (Pure, Refinery Grade)',
            'slug' => 'benzene-pure-refinery-grade',
            'brand' => 'IOCL / Reliance',
            'chemical_name' => 'Benzene (C6H6)',
            'cas_number' => '71-43-2',
            'hsn_code' => '27071000',
            'purity' => '99.9% Refinery Nitration Grade',
            'packaging' => 'Bulk Tankers / Steel Drums',
            'description' => 'Ultra-pure refinery aromatic hydrocarbon used in ethylbenzene, cumene, cyclohexane, and alkylbenzene.',
            'features' => ['99.9% high purity aromatic hydrocarbon'],
            'applications' => ['LAB, LABSA, styrene, cumene, & nylon synthesis'],
            'specifications' => ['Purity' => '99.9% Min', 'Non-Aromatic' => '0.05% Max'],
            'storage_info' => 'Store in dedicated flameproof storage tanks.',
            'image_url' => 'assets/img/added/product/Benzene.jpg',
            'product_url' => 'benzene-pure-refinery-grade.php'
        ], $subSubCalciumBenzene->id);

        $createProduct([
            'name' => 'Spent Sulphuric Acid 70% (DSA)',
            'slug' => 'spent-sulphuric-acid-70',
            'brand' => 'Industrial By-Product',
            'chemical_name' => 'Dilute Sulphuric Acid Solution (H2SO4)',
            'cas_number' => '7664-93-9',
            'hsn_code' => '28070010',
            'purity' => '68% - 70% Acid Solution',
            'packaging' => 'Rubber-Lined Tankers',
            'description' => 'Industrial grade spent sulfuric acid solution suitable for SSP fertilizer, alum manufacturing, and effluent treatment.',
            'features' => ['Cost-effective acid source for fertilizers & alum'],
            'applications' => ['Single Super Phosphate (SSP) manufacture', 'Ferrous alum manufacturing'],
            'specifications' => ['H2SO4' => '68.0% - 70.0% Min'],
            'storage_info' => 'Store in dedicated acid-resistant tankers or tanks.',
            'image_url' => 'assets/img/added/product/Spent-Sulphuric-Acid.jpg',
            'product_url' => 'spent-sulphuric-acid-70.php'
        ], $subSubCalciumBenzene->id);

        // --- DMCC: BORON & SULFUR ---
        $createProduct([
            'name' => 'Borax Decahydrate',
            'slug' => 'borax-decahydrate',
            'brand' => 'Eti Maden / DMCC',
            'chemical_name' => 'Disodium Tetraborate Decahydrate (Na2B4O7·10H2O)',
            'cas_number' => '1303-96-4',
            'hsn_code' => '28401100',
            'purity' => '99.5% Min Granular',
            'packaging' => '25 Kg / 50 Kg Bags',
            'description' => 'White crystalline refined borate compound used in glass, detergents, fluxes, and agriculture.',
            'features' => ['99.5% high purity white crystalline borate'],
            'applications' => ['Borosilicate glass manufacturing', 'Buffer and laundry detergent booster'],
            'specifications' => ['B2O3' => '36.5% Min', 'Na2B4O7·10H2O' => '99.5% Min'],
            'storage_info' => 'Store in cool dry warehouse.',
            'image_url' => 'assets/img/added/product/Borax-Decahydrate.jpg',
            'product_url' => 'Borax-Decahydrate.php'
        ], $subSubBorates->id);

        $createProduct([
            'name' => 'Borax Pentahydrate',
            'slug' => 'borax-pentahydrate',
            'brand' => 'Eti Maden / Rio Tinto',
            'chemical_name' => 'Disodium Tetraborate Pentahydrate (Na2B4O7·5H2O)',
            'cas_number' => '12179-04-3',
            'hsn_code' => '28401900',
            'purity' => '48% B2O3 Content Min',
            'packaging' => '25 Kg / 50 Kg Bags',
            'description' => 'Refined free-flowing crystalline powder delivering higher concentration of boron oxide per ton.',
            'features' => ['High B2O3 density reducing transport cost'],
            'applications' => ['Fiberglass & ceramic glazes', 'Agricultural fertilizers'],
            'specifications' => ['B2O3' => '48.0% Min', 'Purity' => '99.5% Min'],
            'storage_info' => 'Store in dry warehouse.',
            'image_url' => 'assets/img/added/product/Borax-Pentahydrate.jpg',
            'product_url' => 'Borax Pentahydrate.php'
        ], $subSubBorates->id);

        $createProduct([
            'name' => 'Boric Acid (Technical Grade)',
            'slug' => 'boric-acid',
            'brand' => 'DMCC / Eti Maden',
            'chemical_name' => 'Orthoboric Acid (H3BO3)',
            'cas_number' => '10043-35-3',
            'hsn_code' => '28100020',
            'purity' => '99.5% Min Technical Grade Powder',
            'packaging' => '25 Kg / 50 Kg PP Bags',
            'description' => 'White crystalline inorganic acid compound used in fiberglass, ceramic frits, fire retardants, and timber preservation.',
            'features' => ['99.5% pure white powder', 'Mild acidic borate source'],
            'applications' => ['Textile fiberglass manufacturing', 'Flame retardant & wood preservative'],
            'specifications' => ['H3BO3' => '99.5% Min', 'B2O3' => '56.0% Min'],
            'storage_info' => 'Store in dry covered space.',
            'image_url' => 'assets/img/added/product/Boric-Acid.jpg',
            'product_url' => 'Boric Acid.php'
        ], $subSubBorates->id);

        $createProduct([
            'name' => 'Boric Acid Special Quality Grade',
            'slug' => 'boric-acid-special-quality-grade',
            'brand' => 'DMCC Ultra Pure',
            'chemical_name' => 'High Purity Orthoboric Acid (H3BO3)',
            'cas_number' => '10043-35-3',
            'hsn_code' => '28100020',
            'purity' => '99.9% Ultra Pure Low Chloride',
            'packaging' => '25 Kg Sealed Bags',
            'description' => 'Ultra pure low-sulfate low-chloride boric acid for electronic capacitor, nuclear, and electroplating uses.',
            'features' => ['Ultra-low chloride and iron impurities'],
            'applications' => ['Electronic capacitor electrolyte', 'Electroplating bath flux'],
            'specifications' => ['H3BO3' => '99.9% Min', 'Cl' => '5 ppm Max'],
            'storage_info' => 'Store in clean sealed bags.',
            'image_url' => 'assets/img/added/product/Boric-Acid-Special.jpg',
            'product_url' => 'boric-acid-special-quality-grade.php'
        ], $subSubBorates->id);

        $createProduct([
            'name' => 'Oleum 23%',
            'slug' => 'oleum-23',
            'brand' => 'DMCC / GACL',
            'chemical_name' => 'Fuming Sulfuric Acid 23% Free SO3 (H2SO4·SO3)',
            'cas_number' => '8014-95-7',
            'hsn_code' => '28070020',
            'purity' => '23% Free SO3 Liquid',
            'packaging' => 'Dedicated Steel Tankers',
            'description' => 'Heavy fuming sulfuric acid solution containing 23% dissolved sulfur trioxide for nitration and sulfonation.',
            'features' => ['High strength dehydrating and sulfonating agent'],
            'applications' => ['Dyes & intermediates sulfonation', 'Explosives and nitration processes'],
            'specifications' => ['Free SO3' => '23.0% Min', 'Total H2SO4' => '105.17% Min'],
            'storage_info' => 'Store in specialized steel storage tanks with desiccant breathers.',
            'image_url' => 'assets/img/added/product/Oleum-23.jpg',
            'product_url' => 'oleum-23.php'
        ], $subSubOleumSulfur->id);

        $createProduct([
            'name' => 'Oleum 65%',
            'slug' => 'oleum-65',
            'brand' => 'DMCC / GACL',
            'chemical_name' => 'Fuming Sulfuric Acid 65% Free SO3 (H2SO4·SO3)',
            'cas_number' => '8014-95-7',
            'hsn_code' => '28070020',
            'purity' => '65% Free SO3 High Strength',
            'packaging' => 'Dedicated Steel Tankers',
            'description' => 'Ultra high strength fuming sulfuric acid with 65% free SO3 used in intensive chemical synthesis and caprolactam.',
            'features' => ['Extreme dehydrating capacity'],
            'applications' => ['Caprolactam & specialty sulfonation'],
            'specifications' => ['Free SO3' => '65.0% Min', 'Total H2SO4' => '114.6% Min'],
            'storage_info' => 'Store in dedicated temperature-controlled steel tanks.',
            'image_url' => 'assets/img/added/product/Oleum-65.jpg',
            'product_url' => 'oleum-65.php'
        ], $subSubOleumSulfur->id);

        $createProduct([
            'name' => 'Sulfuric Acid (Commercial Grade 98%)',
            'slug' => 'sulfuric-acid-commercial-grade',
            'brand' => 'DMCC / GACL',
            'chemical_name' => 'Sulfuric Acid (H2SO4)',
            'cas_number' => '7664-93-9',
            'hsn_code' => '28070010',
            'purity' => '98.0% Commercial Technical Grade',
            'packaging' => 'Steel Tankers / 300 Kg Drums',
            'description' => 'Dense corrosive mineral acid essential for fertilizer manufacturing, chemical synthesis, and pH control.',
            'features' => ['98% concentrated heavy mineral acid'],
            'applications' => ['Phosphate fertilizer production', 'Chemical synthesis & pickling'],
            'specifications' => ['H2SO4' => '98.0% Min', 'Iron' => '0.01% Max'],
            'storage_info' => 'Store in steel tanks.',
            'image_url' => 'assets/img/added/product/Sulphuric-Acid.jpg',
            'product_url' => 'sulfuric-acid-commercial-grade.php'
        ], $subSubOleumSulfur->id);

        $createProduct([
            'name' => 'Sulfuric Acid (Battery Grade)',
            'slug' => 'sulfuric-acid-battery-grade',
            'brand' => 'DMCC Pure',
            'chemical_name' => 'High Purity Sulfuric Acid (H2SO4)',
            'cas_number' => '7664-93-9',
            'hsn_code' => '28070010',
            'purity' => '98.0% Ultra Low Iron Battery Grade',
            'packaging' => 'HDPE Carboys / Lined Tankers',
            'description' => 'Ultra-pure sulfuric acid with stringent iron and heavy metal limits for lead-acid battery electrolyte manufacturing.',
            'features' => ['Ultra-low iron (<10 ppm) electrolyte grade'],
            'applications' => ['Lead-acid battery electrolyte preparation'],
            'specifications' => ['H2SO4' => '98.0% Min', 'Iron' => '10 ppm Max'],
            'storage_info' => 'Store in clean HDPE carboys.',
            'image_url' => 'assets/img/added/product/Sulfuric-Acid-Battery.jpg',
            'product_url' => 'sulfuric-acid-battery-grade.php'
        ], $subSubOleumSulfur->id);

        // --- GNFC PRODUCTS ---
        $createProduct([
            'name' => 'Formic Acid (85% / 99%)',
            'slug' => 'formic-acid',
            'brand' => 'GNFC / BASF',
            'chemical_name' => 'Methanoic Acid (HCOOH)',
            'cas_number' => '64-18-6',
            'hsn_code' => '29151100',
            'purity' => '85% & 99% Commercial Grade',
            'packaging' => '35 Kg HDPE Carboys / 250 Kg Drums',
            'description' => 'Clear pungent liquid organic acid utilized in leather tanning, textile dyeing, rubber coagulation, and silage.',
            'features' => ['Strong eco-friendly organic acid', 'High reducing efficiency'],
            'applications' => ['Leather de-liming & tanning', 'Textile dyeing pH control', 'Natural rubber latex coagulation'],
            'specifications' => ['Assay' => '85.0% Min / 99.0% Min', 'Acetic Acid' => '0.4% Max'],
            'storage_info' => 'Store in HDPE carboys below 30°C.',
            'image_url' => 'assets/img/added/product/Formic-Acid.jpg',
            'product_url' => 'formic-acid.php'
        ], $subSubGnfcOrganics->id);

        $createProduct([
            'name' => 'Technical Grade Urea',
            'slug' => 'technical-grade-urea',
            'brand' => 'GNFC Technical',
            'chemical_name' => 'Carbamide [CO(NH2)2]',
            'cas_number' => '57-13-6',
            'hsn_code' => '31021000',
            'purity' => '46% Nitrogen Uncoated Technical Grade',
            'packaging' => '50 Kg HDPE Bags',
            'description' => 'Uncoated non-agricultural white prills used in resin synthesis (UF/MF resins), AdBlue DEF solution, and yeast nutrient.',
            'features' => ['46.0% Nitrogen content uncoated prills', 'Rapid water solubility'],
            'applications' => ['Urea-Formaldehyde resin manufacturing', 'AdBlue (DEF) diesel exhaust fluid formulation'],
            'specifications' => ['Nitrogen' => '46.0% Min', 'Biuret' => '0.8% Max', 'Moisture' => '0.5% Max'],
            'storage_info' => 'Store in dry warehouse on pallets.',
            'image_url' => 'assets/img/added/product/Technical-Grade-Urea.jpg',
            'product_url' => 'technical-grade-urea.php'
        ], $subSubGnfcOrganics->id);

        $createProduct([
            'name' => 'Nitric Acid (68% / 60%)',
            'slug' => 'nitric-acid',
            'brand' => 'GNFC / Deepak Nitrite',
            'chemical_name' => 'Nitric Acid (HNO3)',
            'cas_number' => '7697-37-2',
            'hsn_code' => '28080010',
            'purity' => '68.0% Commercial Grade',
            'packaging' => 'Stainless Steel Tankers / 35 Kg Carboys',
            'description' => 'Clear to yellow corrosive mineral acid used for ammonium nitrate, nitro-aromatics, dyes, and pickling.',
            'features' => ['Strong oxidizing mineral acid'],
            'applications' => ['Nitration of aromatics (nitrobenzene, ONCB)', 'Stainless steel passivation & pickling'],
            'specifications' => ['HNO3' => '68.0% Min', 'Nitrous Acid' => '0.1% Max'],
            'storage_info' => 'Store in stainless steel tanks or carboys in shaded area.',
            'image_url' => 'assets/img/added/product/Nitric-Acid.jpg',
            'product_url' => 'nitric-acid.php'
        ], $subSubGnfcOrganics->id);

        $createProduct([
            'name' => 'Ethyl Acetate',
            'slug' => 'ethyl-acetate',
            'brand' => 'GNFC / Jubilant',
            'chemical_name' => 'Ethyl Ethanoate (CH3COOCH2CH3)',
            'cas_number' => '141-78-6',
            'hsn_code' => '29153100',
            'purity' => '99.8% Pure Ester Grade',
            'packaging' => '200 Kg Steel Drums / ISO Tanks',
            'description' => 'Fast evaporating ester solvent with pleasant fruity odor used in flexible packaging inks, lacquers, and pharma APIs.',
            'features' => ['99.8% high purity low moisture solvent', 'Pleasant ester odor'],
            'applications' => ['Flexographic & rotogravure printing inks', 'Pharma extraction solvent', 'Paints & lacquers'],
            'specifications' => ['Assay' => '99.8% Min', 'Water Content' => '0.05% Max', 'Acidity' => '0.005% Max'],
            'storage_info' => 'Store in flameproof storage store below 30°C.',
            'image_url' => 'assets/img/added/product/Ethyl-Acetate.jpg',
            'product_url' => 'ethyl-acetate.php'
        ], $subSubGnfcOrganics->id);

        $createProduct([
            'name' => 'Methanol (Methyl Alcohol)',
            'slug' => 'methanol',
            'brand' => 'GNFC / Import Pure',
            'chemical_name' => 'Methanol (CH3OH)',
            'cas_number' => '67-56-1',
            'hsn_code' => '29051100',
            'purity' => '99.85% Impure Water-Free Grade',
            'packaging' => 'Bulk Road Tankers / 200 L Steel Drums',
            'description' => 'High purity industrial alcohol solvent used for formaldehyde synthesis, biodiesel transesterification, and pharma.',
            'features' => ['99.85% high purity clear solvent alcohol'],
            'applications' => ['Formaldehyde manufacturing raw material', 'Biodiesel transesterification reactant', 'Pharma solvent'],
            'specifications' => ['Methanol' => '99.85% Min', 'Water' => '0.1% Max'],
            'storage_info' => 'Store in grounded steel tanks away from sparks.',
            'image_url' => 'assets/img/added/product/Methanol.jpg',
            'product_url' => 'methanol.php'
        ], $subSubGnfcOrganics->id);

        $createProduct([
            'name' => 'Aniline Oil',
            'slug' => 'aniline',
            'brand' => 'GNFC Pure',
            'chemical_name' => 'Aminobenzene / Aniline (C6H5NH2)',
            'cas_number' => '62-53-3',
            'hsn_code' => '29214110',
            'purity' => '99.9% Pure Liquid',
            'packaging' => '200 Kg Steel Drums / Tankers',
            'description' => 'Clear oily aromatic amine liquid precursor for MDI polyurethane, rubber chemicals, and azo dyes.',
            'features' => ['99.9% high purity clear liquid'],
            'applications' => ['MDI polyurethane precursor', 'Rubber processing chemicals'],
            'specifications' => ['Assay' => '99.9% Min', 'Nitrobenzene' => '0.001% Max'],
            'storage_info' => 'Store in sealed dark steel drums.',
            'image_url' => 'assets/img/added/product/Aniline.jpg',
            'product_url' => 'aniline.php'
        ], $subSubGnfcOrganics->id);

        // --- INDUSTRIAL SOLVENTS ---
        $createProduct([
            'name' => 'Isopropyl Alcohol (IPA 99.9%)',
            'slug' => 'isopropyl-alcohol',
            'brand' => 'Deepak Phenolics / Import',
            'chemical_name' => 'Isopropanol / 2-Propanol (C3H8O)',
            'cas_number' => '67-63-0',
            'hsn_code' => '29051220',
            'purity' => '99.9% Pharma & Electronic Grade',
            'packaging' => '160 Kg HDPE Barrels / Steel Drums / Tankers',
            'description' => 'Water-clear volatile alcohol solvent used in sanitizers, pharmaceutical crystallization, and electronics wipe.',
            'features' => ['99.9% pure water-free solvent', 'Fast evaporation rate'],
            'applications' => ['Hand sanitizer & surface disinfectant formulation', 'Pharma API crystallization solvent'],
            'specifications' => ['Assay' => '99.90% Min', 'Water Content' => '0.05% Max'],
            'storage_info' => 'Store in flameproof storage area.',
            'image_url' => 'assets/img/added/product/Isopropyl-Alcohol.jpg',
            'product_url' => 'isopropyl-alcohol.php',
            'is_featured' => true
        ], $subSubSolventsEsters->id);

        $createProduct([
            'name' => 'Butyl Acetate',
            'slug' => 'butyl-acetate',
            'brand' => 'Import Grade',
            'chemical_name' => 'n-Butyl Acetate (C6H12O2)',
            'cas_number' => '123-86-4',
            'hsn_code' => '29153300',
            'purity' => '99.5% Pure Ester',
            'packaging' => '180 Kg Steel Drums',
            'description' => 'Clear fruity ester solvent used in automotive OEM coatings, wood finishes, and nail polishes.',
            'features' => ['Excellent blush resistance in coatings'],
            'applications' => ['Automotive coatings & lacquer formulation', 'Wood polyurethane polishes'],
            'specifications' => ['Assay' => '99.5% Min', 'Water' => '0.05% Max'],
            'storage_info' => 'Store in cool ventilated drum store.',
            'image_url' => 'assets/img/added/product/Butyl-Acetate.jpg',
            'product_url' => 'butyl-acetate.php'
        ], $subSubSolventsEsters->id);

        $createProduct([
            'name' => 'NC Thinner (Nitrocellulose Thinner)',
            'slug' => 'nc-thinner',
            'brand' => 'SRCIL Formulation',
            'chemical_name' => 'Solvent Blend (Esters, Aromatics, Alcohols)',
            'cas_number' => 'N/A',
            'hsn_code' => '38140010',
            'purity' => 'Commercial Thinner Grade',
            'packaging' => '20 L Cans / 200 L Steel Drums',
            'description' => 'Balanced solvent blend engineered for thinning wood lacquers, automotive NC paints, and equipment cleanup.',
            'features' => ['Fast drying high solvency paint thinner'],
            'applications' => ['Nitrocellulose wood paint thinning', 'Spray painting viscosity adjustment'],
            'specifications' => ['Specific Gravity' => '0.82 - 0.85', 'Dry Time' => '5 - 10 mins'],
            'storage_info' => 'Store away from open flames.',
            'image_url' => 'assets/img/added/product/NC-Thinner.jpg',
            'product_url' => 'nc-thinner.php'
        ], $subSubSolventsEsters->id);

        // --- COAL & ENERGY ---
        $createProduct([
            'name' => 'Bio-Coal Briquettes',
            'slug' => 'bio-coal',
            'brand' => 'SRCIL Eco-Energy',
            'chemical_name' => 'Torrefied Biomass Pellets / Briquettes',
            'cas_number' => 'N/A',
            'hsn_code' => '44013100',
            'purity' => 'Calorific Value: 4000 - 4500 Kcal/Kg',
            'packaging' => 'Bulk Loose / 50 Kg Jumbo Bags',
            'description' => 'Eco-friendly renewable biomass bio-coal briquettes engineered for industrial boilers as carbon-neutral fuel.',
            'features' => ['High calorific output (4000-4500 Kcal/Kg)', 'Low ash (<6%) and zero sulfur'],
            'applications' => ['Industrial steam boilers in textile & chemical plants'],
            'specifications' => ['Calorific Value' => '4000 - 4500 Kcal/Kg', 'Moisture' => '6% - 8% Max'],
            'storage_info' => 'Store under dry covered shed.',
            'image_url' => 'assets/img/added/product/Bio-Coal.jpg',
            'product_url' => 'bio-coal.php',
            'is_featured' => true
        ], $subSubBioImportedCoal->id);

        $createProduct([
            'name' => 'Indonesian Coal',
            'slug' => 'indonesian-coal',
            'brand' => 'Imported Energy',
            'chemical_name' => 'Sub-Bituminous Thermal Coal',
            'cas_number' => 'N/A',
            'hsn_code' => '27011920',
            'purity' => 'GAR 3800 - 5000 Kcal/Kg',
            'packaging' => 'Bulk Shipments / Truckloads',
            'description' => 'High volatile sub-bituminous Indonesian coal ideal for industrial boiler fluid bed combustion.',
            'features' => ['High volatile matter fast ignition coal'],
            'applications' => ['Captive power plants & industrial boilers'],
            'specifications' => ['GAR' => '3800 - 5000 Kcal/Kg', 'Total Moisture' => '25% - 35%'],
            'storage_info' => 'Store in open coal yard with water sprinklers.',
            'image_url' => 'assets/img/added/product/Indonesian-Coal.jpg',
            'product_url' => 'indonesian-coal.php'
        ], $subSubBioImportedCoal->id);

        $createProduct([
            'name' => 'South African Coal',
            'slug' => 'south-african-coal',
            'brand' => 'Imported RB1/RB3',
            'chemical_name' => 'Bituminous Thermal Coal',
            'cas_number' => 'N/A',
            'hsn_code' => '27011920',
            'purity' => 'NAR 5500 - 6000 Kcal/Kg',
            'packaging' => 'Bulk Truckloads',
            'description' => 'High heat energy South African bituminous thermal coal with low moisture and stable combustion.',
            'features' => ['High NAR calorific density coal'],
            'applications' => ['Heavy industrial boilers and cement kilns'],
            'specifications' => ['NAR' => '5500 - 6000 Kcal/Kg', 'Ash' => '14% Max'],
            'storage_info' => 'Store in coal yard storage.',
            'image_url' => 'assets/img/added/product/South-African-Coal.jpg',
            'product_url' => 'south-african-coal.php'
        ], $subSubBioImportedCoal->id);

        // 6. Seed Blogs, FAQs, Certifications, Contact, Export Countries
        Blog::create([
            'title' => 'Demystifying Chlor-Alkali Products: Caustic Soda, Chlorine & Derivatives',
            'slug' => 'demystifying-chlor-alkali-products',
            'summary' => 'Technical overview of Chlor-Alkali electrolysis, grade variations, and industrial applications.',
            'content' => 'Chlor-Alkali electrolysis represents the backbone of modern chemical manufacturing yielding Caustic Soda, Chlorine, and Hydrogen...',
            'category' => 'Chlor-Alkali Guide',
            'read_time' => '6 min read',
            'published_at' => '2026-06-15',
            'image_url' => 'assets/img/added/product/Caustic-Soda-Flakes-NaOH.jpg',
            'url' => 'demystifying-chlor-alkali-products.php'
        ]);

        Faq::create([
            'question' => 'What is Caustic Soda Flakes and what are its key uses?',
            'answer' => 'Caustic Soda Flakes (Sodium Hydroxide, NaOH 99%) is a strong alkaline chemical used in textiles, paper, soap, and water treatment.',
            'category' => 'Products',
            'keywords' => json_encode(['caustic soda', 'flakes', 'naoh'])
        ]);

        Certification::create([
            'title' => 'ISO 9001:2015 Quality Management System',
            'issuer' => 'International Organization for Standardization',
            'description' => 'Certified for quality control, chemical batch testing, and standardized trade operations.',
            'document_url' => 'certificate.php',
            'icon' => 'fa-award'
        ]);

        ContactDetail::create([
            'office_name' => 'Corporate & Marketing Office',
            'address' => 'A-97 Sai Ashish, NH-8 Vadadla, Bharuch 392011',
            'city' => 'Bharuch',
            'state' => 'Gujarat',
            'country' => 'India',
            'postal_code' => '392011',
            'phone' => '+91 76001 81931 / +91 70415 53966',
            'email' => 'srchemicalindustries9@gmail.com / marketing@srchemicalindustries.com',
            'whatsapp' => '+917600181931',
            'working_hours' => 'Monday - Saturday: 9:00 AM - 7:00 PM IST',
            'google_map_url' => 'https://maps.google.com/?q=Bharuch,Gujarat'
        ]);

        ExportCountry::create([
            'name' => 'United Arab Emirates (UAE)',
            'code' => 'AE',
            'region' => 'Middle East',
            'flag_emoji' => '🇦🇪',
            'details' => 'Exporting Chlor-Alkali, Solvents, and Water Treatment chemicals to Dubai, Abu Dhabi, and Jebel Ali Port.'
        ]);

        echo "\n=== MIGRATION AND SEEDING COMPLETE == me=\n";
        echo "Root Categories: " . Category::where('level', 'root')->count() . "\n";
        echo "Main Categories (Level 1): " . Category::where('level', 'main_category')->count() . "\n";
        echo "Sub Categories (Level 2): " . Category::where('level', 'sub_category')->count() . "\n";
        echo "Sub Sub Categories (Level 3): " . Category::where('level', 'sub_sub_category')->count() . "\n";
        echo "Total Categories: " . Category::count() . "\n";
        echo "Total Products (Level 4): " . Product::count() . "\n";
    }
}

if (php_sapi_name() === 'cli' && isset($argv[0]) && (basename($argv[0]) === 'MySQLHierarchicalSeeder.php' || basename($argv[0]) === 'import_mysql.php')) {
    MySQLHierarchicalSeeder::run();
}
