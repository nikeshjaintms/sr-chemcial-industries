<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\Category;
use App\Models\Product;
use App\Models\Blog;
use App\Models\Faq;
use App\Models\Certification;
use App\Models\ContactDetail;
use App\Models\ExportCountry;
use Illuminate\Support\Str;

class SRChemicalsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Company Information
        Company::updateOrCreate(
            ['id' => 1],
            [
                'name' => 'SR Chemical Industries Limited (SRCIL)',
                'short_name' => 'SRCIL / Pure Grade Exim',
                'tagline' => 'Global Trade. Trusted Quality. Connecting domestic and international industries with high-purity chemicals.',
                'about' => 'SR Chemical Industries Limited (SRCIL) is a premier chemical supplier, importer, exporter, and distributor based in Bharuch, Gujarat, India. Operating over 25+ global trade partnerships, SRCIL delivers industrial-grade acids, chlor-alkali, solvents, water treatment chemicals, and mineral energy commodities with uncompromising purity, full trade compliance, and end-to-end supply chain logistics.',
                'mission' => 'To empower worldwide manufacturing through reliable chemical supply, absolute transparency, stringent quality verification, and sustainable industrial trade practices.',
                'vision' => 'To be recognized globally as the most trusted partner for high-purity industrial chemicals, solvents, and raw material logistics.',
                'address' => 'GF-10, Bhavani Shopping Complex, Nr. Hotel NyayMandir, Zadeshwar, Bharuch - 392015, Gujarat, India',
                'phone_primary' => '+91 99047 88479',
                'phone_secondary' => '+91 76988 81819',
                'email_primary' => 'marketing@puregrade.in',
                'email_secondary' => 'sales@srchemical.com',
                'website_url' => 'https://srchemical.com',
                'logo_url' => 'assets/img/added/blue-logo.png',
                'services' => [
                    'Global Import & Export of Hazardous & Non-Hazardous Chemicals',
                    'Domestic Indian Market Distribution with Fast Dispatch',
                    'Bulk Order Procurement & Custom Packaging Solutions',
                    'End-to-End Hazardous Material Logistics & Tanker Transport',
                    'Technical Documentation (MSDS, SDS, TDS, COA) Provision'
                ],
                'highlights' => [
                    '25+ Global Trade Partners & International Exporters',
                    '100% Verified Pure Grade Quality Standard',
                    'Dedicated Hazardous & Bulk Cargo Logistics Network',
                    'Comprehensive ISO & Regulatory Compliance'
                ],
                'logistics_info' => 'Full movement support for bulk liquid chemicals via dedicated tankers, ISO tanks, and heavy HDPE drums across domestic and international shipping lines.',
                'compliance_info' => 'Strict adherence to international safety standards, ISO certifications, REACH guidelines, and comprehensive SDS/TDS document verifications.'
            ]
        );

        // 2. Seed Categories
        $catAcids = Category::updateOrCreate(
            ['slug' => 'acid-products'],
            [
                'name' => 'Industrial Acids',
                'type' => 'Industrial Chemicals',
                'description' => 'High-purity industrial acids for textile, pharma, fertilizer, and chemical synthesis applications.',
                'image_url' => 'assets/img/added/product/Nitric-Acid.jpg'
            ]
        );

        $catChlorAlkali = Category::updateOrCreate(
            ['slug' => 'chlor-alkali-chemicals'],
            [
                'name' => 'Chlor-Alkali Chemicals',
                'type' => 'Industrial Chemicals',
                'description' => 'Essential Chlor-Alkali chemicals including Caustic Soda, Caustic Potash, Liquid Chlorine, and Sodium Hypochlorite.',
                'image_url' => 'assets/img/added/product/Caustic-Soda-Flakes-NaOH.jpg'
            ]
        );

        $catWaterTreatment = Category::updateOrCreate(
            ['slug' => 'water-treatment-chemicals'],
            [
                'name' => 'Water Treatment Chemicals',
                'type' => 'Industrial Chemicals',
                'description' => 'Specialized chemical agents for pH regulation, coagulation, flocculation, disinfection, and effluent treatment.',
                'image_url' => 'assets/img/added/product/Poly-Aluminium-Chloride-PAC.jpg'
            ]
        );

        $catSolvents = Category::updateOrCreate(
            ['slug' => 'pharmaceutical-chemical-solvents'],
            [
                'name' => 'Industrial Solvents',
                'type' => 'Industrial Solvents',
                'description' => 'High-purity organic solvents for paints, coatings, pharmaceutical synthesis, and industrial degreasing.',
                'image_url' => 'assets/img/added/product/Methylene-Chloride-MDC.jpg'
            ]
        );

        $catBoron = Category::updateOrCreate(
            ['slug' => 'boron-chemicals'],
            [
                'name' => 'Boron Chemicals',
                'type' => 'Industrial Chemicals',
                'description' => 'Refined borate compounds including Borax Decahydrate, Borax Pentahydrate, and Technical Grade Boric Acid.',
                'image_url' => 'assets/img/added/product/Boric-Acid.jpg'
            ]
        );

        $catCoal = Category::updateOrCreate(
            ['slug' => 'coal-products'],
            [
                'name' => 'Coal & Energy Products',
                'type' => 'Energy & Commodities',
                'description' => 'High calorific bio-coal, Indonesian coal, and South African coal for industrial furnaces and power generation.',
                'image_url' => 'assets/img/added/product/Bio-Coal.jpg'
            ]
        );

        // 3. Seed Featured Core Products
        Product::updateOrCreate(
            ['slug' => 'caustic-soda-flakes'],
            [
                'name' => 'Caustic Soda Flakes',
                'brand' => 'GRASIM INDUSTRIES LIMITED',
                'chemical_name' => 'Sodium Hydroxide (NaOH)',
                'cas_number' => '1310-73-2',
                'hsn_code' => '28151110',
                'purity' => '99.0% Min Purity',
                'packaging' => '50 Kg HDPE Bags with inner liner',
                'description' => 'Caustic Soda Flakes is a strongly alkaline chemical compound in white solid flake form. It is highly soluble in water and heat-generating upon dissolution, serving as a fundamental raw material in paper manufacturing, aluminum processing, textile processing, and chemical synthesis.',
                'features' => [
                    'High purity white crystalline solid flakes',
                    'Exothermic reaction in water forming strong alkaline lye',
                    'Excellent saponification properties for soap & detergent industries',
                    'Delivered in moisture-proof 50 Kg HDPE bags'
                ],
                'applications' => [
                    'Textile processing, mercerizing, and fiber treatment',
                    'Pulp & paper manufacturing and wood pulping',
                    'Soap, detergent, and surfactant manufacturing',
                    'Water treatment and pH neutralizer in industrial effluents',
                    'Alumina refining and metal surface preparation',
                    'Petroleum refining and organic chemical synthesis'
                ],
                'specifications' => [
                    'Sodium Hydroxide (NaOH)' => '99.0% Min',
                    'Sodium Carbonate (Na2CO3)' => '0.4% Max',
                    'Sodium Chloride (NaCl)' => '0.03% Max',
                    'Iron (Fe)' => '15 ppm Max',
                    'Appearance' => 'White Flakes free from foreign matter'
                ],
                'storage_info' => 'Store in tightly sealed HDPE bags in a cool, dry, well-ventilated area away from acids, moisture, and flammable substances. Hygroscopic material.',
                'category_id' => $catChlorAlkali->id,
                'image_url' => 'assets/img/added/product/Caustic-Soda-Flakes-NaOH.jpg',
                'msds_url' => 'assets/pdf/MSDC/CAUSTIC-SODA-FLAKES.pdf',
                'specification_url' => 'caustic-soda-flakes.php',
                'product_url' => 'caustic-soda-flakes.php',
                'is_featured' => true
            ]
        );

        Product::updateOrCreate(
            ['slug' => 'caustic-soda-lye'],
            [
                'name' => 'Caustic Soda Lye',
                'brand' => 'EPIGRAL / GACL',
                'chemical_name' => 'Sodium Hydroxide Solution (NaOH)',
                'cas_number' => '1310-73-2',
                'hsn_code' => '28151200',
                'purity' => '48% to 50% Liquid Lye Solution',
                'packaging' => 'Bulk Road Tankers / 250 Kg HDPE Barrels',
                'description' => 'Ready-to-use liquid Sodium Hydroxide Lye (48%-50% concentration) supplied in bulk tankers for direct metering into automated industrial processes including water neutralization, textile dyeing, and chemical plants.',
                'features' => [
                    'Pre-dissolved liquid ready for automated dosing',
                    'High commercial purity liquid with minimal impurities',
                    'Supplied in specialized corrosion-resistant tankers'
                ],
                'applications' => [
                    'Bulk industrial water treatment and pH balancing',
                    'Textile dyeing house processes',
                    'Detergent and liquid cleaner manufacturing',
                    'Effluent neutralization in chemical manufacturing plants'
                ],
                'specifications' => [
                    'NaOH Content' => '48.0% - 50.0% Min',
                    'Na2CO3' => '0.2% Max',
                    'NaCl' => '0.1% Max',
                    'Appearance' => 'Clear colorless viscous liquid'
                ],
                'storage_info' => 'Store in dedicated rubber-lined or stainless steel storage tanks. Avoid temperature drops below 15°C to prevent crystallization.',
                'category_id' => $catChlorAlkali->id,
                'image_url' => 'assets/img/added/product/Caustic-Soda-Lye-NaOH.jpg',
                'msds_url' => 'assets/pdf/MSDC/CAUSTIC-SODA-LYE gacl.pdf',
                'specification_url' => 'caustic-soda-lye.php',
                'product_url' => 'caustic-soda-lye.php',
                'is_featured' => true
            ]
        );

        Product::updateOrCreate(
            ['slug' => 'caustic-soda-prills'],
            [
                'name' => 'Caustic Soda Prills',
                'brand' => 'GACL / Reliance',
                'chemical_name' => 'Sodium Hydroxide Micro-spheres (NaOH)',
                'cas_number' => '1310-73-2',
                'hsn_code' => '28151110',
                'purity' => '99.0% Min Purity',
                'packaging' => '25 Kg / 50 Kg Moisture-Proof HDPE Bags',
                'description' => 'Free-flowing spherical micro-prills of Sodium Hydroxide engineered for non-dusty handling, easy pneumatic transfer, fast dissolution rates, and precise batch metering in dry chemical blending.',
                'features' => [
                    'Dust-free spherical prills for safer worker handling',
                    'Rapid uniform dissolution rate',
                    'Free-flowing non-caking physical structure'
                ],
                'applications' => [
                    'Pharmaceutical formulation & synthesis',
                    'Water treatment and automated feeder systems',
                    'Specialty soap formulations and dry chemical mixing'
                ],
                'specifications' => [
                    'NaOH Content' => '99.0% Min',
                    'Particle Size' => '0.5mm - 1.0mm micro-spheres',
                    'Appearance' => 'Uniform white shiny prills'
                ],
                'storage_info' => 'Keep sealed in original packaging in a cool, dry warehouse.',
                'category_id' => $catChlorAlkali->id,
                'image_url' => 'assets/img/added/product/Caustic-Soda-Prills-NaOH.jpg',
                'msds_url' => 'assets/pdf/MSDC/CAUSTIC-SODA-PRILLS.pdf',
                'specification_url' => 'caustic-soda-prills.php',
                'product_url' => 'caustic-soda-prills.php',
                'is_featured' => false
            ]
        );

        Product::updateOrCreate(
            ['slug' => 'poly-aluminium-chloride'],
            [
                'name' => 'Poly Aluminium Chloride (PAC)',
                'brand' => 'GACL / Standard Grade',
                'chemical_name' => 'Polyaluminium Chloride [Aln(OH)mCl3n-m]',
                'cas_number' => '1327-41-9',
                'hsn_code' => '28273200',
                'purity' => '30% Al2O3 Yellow Powder / 10-12% Liquid',
                'packaging' => '25 Kg Laminated Bags / Liquid Tankers',
                'description' => 'Poly Aluminium Chloride (PAC) is a highly efficient inorganic polymer coagulant widely utilized for municipal drinking water purification, industrial effluent clarification, paper sizing, and wastewater solids removal.',
                'features' => [
                    'Rapid floc formation and fast settling rate',
                    'Wide pH operating range (5.0 to 9.0)',
                    'Lower residual aluminum concentration in treated water',
                    'Effective even at low temperatures'
                ],
                'applications' => [
                    'Drinking water purification and municipal water treatment',
                    'Industrial effluent coagulation in textile, dye, and paper plants',
                    'Sludge dewatering and solids-liquid separation',
                    'Paper making retention aid and sizing agent'
                ],
                'specifications' => [
                    'Al2O3 Content' => '30.0% Min (Powder) / 10.0% Min (Liquid)',
                    'Basicity' => '50% - 85%',
                    'pH (1% aqueous solution)' => '3.5 - 5.0',
                    'Insolubles' => '0.5% Max'
                ],
                'storage_info' => 'Store powder in dry cool area. Liquid PAC should be stored in FRP or HDPE containers.',
                'category_id' => $catWaterTreatment->id,
                'image_url' => 'assets/img/added/product/Poly-Aluminium-Chloride-PAC.jpg',
                'msds_url' => 'assets/pdf/MSDC/POLY-ALUMINIUM-CHLORIDE.pdf',
                'specification_url' => 'poly-aluminium-chloride.php',
                'product_url' => 'poly-aluminium-chloride.php',
                'is_featured' => true
            ]
        );

        Product::updateOrCreate(
            ['slug' => 'acetic-acid'],
            [
                'name' => 'Acetic Acid (Glacial)',
                'brand' => 'GNFC / Laxmi Organic',
                'chemical_name' => 'Ethanoic Acid (CH3COOH)',
                'cas_number' => '64-19-7',
                'hsn_code' => '29152100',
                'purity' => '99.8% Glacial Grade',
                'packaging' => '30 Kg HDPE Carboys / 210 Kg Drums / Tankers',
                'description' => 'Glacial Acetic Acid is a water-clear organic acid essential for textile dyeing, vinyl acetate monomer (VAM) synthesis, purified terephthalic acid (PTA) production, food preservation, and pharma synthesis.',
                'features' => [
                    '99.8% ultra-pure water-free glacial quality',
                    'Strong acrid odor and characteristic acidity',
                    'Versatile solvent and acidifying agent'
                ],
                'applications' => [
                    'Textile dyeing and printing color fixing agent',
                    'Manufacture of acetic anhydride and acetate esters',
                    'Pharmaceutical APIs and intermediate formulations',
                    'Food acidulant and rubber coagulation'
                ],
                'specifications' => [
                    'Purity' => '99.8% Min',
                    'Freezing Point' => '16.6 °C Min',
                    'Water Content' => '0.15% Max',
                    'Formic Acid' => '0.05% Max'
                ],
                'storage_info' => 'Store above 17°C to prevent freezing/freezing solid in winter. Keep away from heat and open flames.',
                'category_id' => $catAcids->id,
                'image_url' => 'assets/img/added/product/Acetic-Acid.jpg',
                'msds_url' => 'assets/pdf/MSDC/ACETIC-ACID.pdf',
                'specification_url' => 'acetic-acid.php',
                'product_url' => 'acetic-acid.php',
                'is_featured' => true
            ]
        );

        Product::updateOrCreate(
            ['slug' => 'hydrochloric-acid'],
            [
                'name' => 'Hydrochloric Acid (HCl)',
                'brand' => 'SRCIL Industrial Grade',
                'chemical_name' => 'Hydrogen Chloride Solution (HCl)',
                'cas_number' => '7647-01-0',
                'hsn_code' => '28061000',
                'purity' => '30% - 33% Technical Grade',
                'packaging' => 'Rubber-Lined Tankers / 220 L Drums',
                'description' => 'Hydrochloric Acid is a pungent, highly corrosive strong mineral acid widely used in steel pickling, oil well acidizing, pH control, boiler descaling, and chemical synthesis.',
                'features' => [
                    'High concentration 30-33% commercial acid',
                    'Superior descaling and metal oxide removal',
                    'Fast reaction rate for pH reduction'
                ],
                'applications' => [
                    'Steel pickling and rust removal',
                    'pH adjustment in industrial wastewater treatment',
                    'Regeneration of ion exchange resins in water demineralization',
                    'Ore processing and chemical manufacturing'
                ],
                'specifications' => [
                    'Assay (as HCl)' => '30.0% - 33.0% Min',
                    'Iron (Fe)' => '0.005% Max',
                    'Free Chlorine' => '0.002% Max'
                ],
                'storage_info' => 'Store in rubber-lined steel, PVC, or HDPE tanks with fumes scrubbing system.',
                'category_id' => $catAcids->id,
                'image_url' => 'assets/img/added/product/Hydrochloric-Acid-HCl.jpg',
                'msds_url' => 'assets/pdf/MSDC/HYDROCHLORIC-ACID.pdf',
                'specification_url' => 'hydrochloric-acid.php',
                'product_url' => 'hydrochloric-acid.php',
                'is_featured' => false
            ]
        );

        Product::updateOrCreate(
            ['slug' => 'hydrogen-peroxide'],
            [
                'name' => 'Hydrogen Peroxide (H2O2)',
                'brand' => 'National Peroxide / Asian Peroxide',
                'chemical_name' => 'Hydrogen Peroxide (H2O2)',
                'cas_number' => '7722-84-1',
                'hsn_code' => '28470000',
                'purity' => '50% w/w Industrial Grade',
                'packaging' => '30 Kg / 50 Kg Vent-Cap HDPE Drums / Tankers',
                'description' => 'Hydrogen Peroxide 50% is a powerful, eco-friendly oxidizing and bleaching agent that decomposes into harmless water and oxygen, making it ideal for eco-textile bleaching, paper pulp bleaching, and effluent treatment.',
                'features' => [
                    'Environmentally clean oxidant yielding only H2O and O2',
                    'High bleaching efficiency for cotton fibers and paper pulp',
                    'Strong disinfectant for water and surface sanitation'
                ],
                'applications' => [
                    'Textile fiber bleaching and color stripping',
                    'Pulp and paper eco-bleaching',
                    'Chemical synthesis of organic peroxides',
                    'Wastewater odor control and COD reduction'
                ],
                'specifications' => [
                    'H2O2 Concentration' => '50.0% Min',
                    'Stability' => '98.5% Min',
                    'Free Acid (as H2SO4)' => '0.02% Max'
                ],
                'storage_info' => 'Store in cool ventilated space away from sunlight and combustibles. Use vented caps on drums.',
                'category_id' => $catChlorAlkali->id,
                'image_url' => 'assets/img/added/product/Hydrogen-Peroxide-H2O2.jpg',
                'msds_url' => 'assets/pdf/MSDC/HYDROGEN-PEROXIDE.pdf',
                'specification_url' => 'hydrogen-peroxide.php',
                'product_url' => 'hydrogen-peroxide.php',
                'is_featured' => true
            ]
        );

        Product::updateOrCreate(
            ['slug' => 'methylene-chloride'],
            [
                'name' => 'Methylene Chloride (MDC)',
                'brand' => 'EPIGRAL / SRF',
                'chemical_name' => 'Methylene Dichloride / Dichloromethane (CH2Cl2)',
                'cas_number' => '75-09-2',
                'hsn_code' => '29031200',
                'purity' => '99.9% Pure Solvent Grade',
                'packaging' => '250 Kg Steel Drums / ISO Tank Containers',
                'description' => 'Methylene Dichloride (MDC) is a clear, volatile liquid chlorinated solvent with high solvency power, used extensively in pharmaceutical extraction, paint stripping, foam blowing, and polycarbonate resin production.',
                'features' => [
                    '99.9% high purity low moisture solvent',
                    'High volatility with low boiling point (39.8°C)',
                    'Non-flammable under ambient operating conditions'
                ],
                'applications' => [
                    'Pharmaceutical API reaction medium and crystallization solvent',
                    'Paint, coating, and varnish remover formulas',
                    'Flexible polyurethane foam blowing agent',
                    'Metal degreasing and precision cleaning'
                ],
                'specifications' => [
                    'Assay' => '99.90% Min',
                    'Water Content' => '0.03% Max',
                    'Acidity (as HCl)' => '0.001% Max',
                    'Non-Volatile Matter' => '0.001% Max'
                ],
                'storage_info' => 'Store in sealed tight steel drums below 30°C in shaded, well-ventilated dry areas.',
                'category_id' => $catSolvents->id,
                'image_url' => 'assets/img/added/product/Methylene-Chloride-MDC.jpg',
                'msds_url' => 'assets/pdf/MSDC/METHYLENE-CHLORIDE.pdf',
                'specification_url' => 'methylene-chloride.php',
                'product_url' => 'methylene-chloride.php',
                'is_featured' => true
            ]
        );

        Product::updateOrCreate(
            ['slug' => 'borax-pentahydrate'],
            [
                'name' => 'Borax Pentahydrate',
                'brand' => 'Eti Maden / Rio Tinto',
                'chemical_name' => 'Disodium Tetraborate Pentahydrate (Na2B4O7·5H2O)',
                'cas_number' => '12179-04-3',
                'hsn_code' => '28401900',
                'purity' => '48% B2O3 Content Min',
                'packaging' => '25 Kg / 50 Kg PP/PE Bags',
                'description' => 'Borax Pentahydrate is a refined, free-flowing crystalline powder delivering higher concentration of boron oxide (B2O3) per ton than decahydrate, optimized for insulation fiberglass, ceramic glazes, and agriculture.',
                'features' => [
                    'High B2O3 density reducing transport cost per unit boron',
                    'Excellent solubility in hot water',
                    'Refractory and fluxing agent in glass formulation'
                ],
                'applications' => [
                    'Fiberglass & textile glass insulation manufacture',
                    'Ceramic frit, enamel, and tile glaze formulations',
                    'Agricultural micronutrient fertilizer formulations',
                    'Metallurgical fluxes and flame retardant additives'
                ],
                'specifications' => [
                    'B2O3 Content' => '48.0% Min',
                    'Na2B4O7 Content' => '69.0% Min',
                    'Purity' => '99.5% Min'
                ],
                'storage_info' => 'Store in cool dry warehouse protected from direct moisture.',
                'category_id' => $catBoron->id,
                'image_url' => 'assets/img/added/product/Borax-Pentahydrate.jpg',
                'msds_url' => 'assets/pdf/MSDC/BORAX-PENTAHYDRATE.pdf',
                'specification_url' => 'Borax Pentahydrate.php',
                'product_url' => 'Borax Pentahydrate.php',
                'is_featured' => false
            ]
        );

        Product::updateOrCreate(
            ['slug' => 'bio-coal'],
            [
                'name' => 'Bio-Coal',
                'brand' => 'SRCIL Eco-Energy',
                'chemical_name' => 'Torrefied Biomass Pellets / Briquettes',
                'cas_number' => 'N/A',
                'hsn_code' => '44013100',
                'purity' => 'Calorific Value: 4000 - 4500 Kcal/Kg',
                'packaging' => 'Bulk Loose / 50 Kg Jumbo Bags',
                'description' => 'Eco-friendly Bio-Coal briquettes engineered from agricultural residue biomass. Acts as a renewable, carbon-neutral direct drop-in alternative for fossil coal in industrial boilers and power plants.',
                'features' => [
                    'High calorific output (4000-4500 Kcal/Kg)',
                    'Low moisture (<8%) and low ash content (<6%)',
                    'Zero sulfur emissions contributing to green energy carbon offset'
                ],
                'applications' => [
                    'Industrial steam boilers in textile, chemical, and pharma plants',
                    'Thermal power generation co-firing',
                    'Brick kilns and furnace combustion'
                ],
                'specifications' => [
                    'Calorific Value' => '4000 - 4500 Kcal/Kg',
                    'Moisture' => '6% - 8% Max',
                    'Ash Content' => '5% - 8% Max',
                    'Sulfur' => '< 0.05%'
                ],
                'storage_info' => 'Store under dry covered shed protected from rain exposure.',
                'category_id' => $catCoal->id,
                'image_url' => 'assets/img/added/product/Bio-Coal.jpg',
                'msds_url' => '#',
                'specification_url' => 'bio-coal.php',
                'product_url' => 'bio-coal.php',
                'is_featured' => true
            ]
        );

        // 4. Scan source directory C:\xampp\htdocs\SR to auto-seed all other product files!
        $sourceDir = 'C:\xampp\htdocs\SR';
        if (file_exists($sourceDir)) {
            $productFiles = glob($sourceDir . '/*.php');
            $ignoreList = [
                'about.php', 'acid-products.php', 'aluminium-based-chemicals.php', 'blog-details.php', 
                'blog.php', 'blue-print-global-trade.php', 'boron-chemicals.php', 'certificate.php',
                'chlor-alkali-chemicals.php', 'chloromethane-chemicals.php', 'cleaning-degreasing-solvents.php',
                'clients.php', 'contact.php', 'demystifying-chlor-alkali-products.php', 'details.php',
                'flakes-lye-prills-caustic-soda.php', 'footer.php', 'header.php', 'index.php',
                'industrial-salt-water-treatment-chlor-alkali.php', 'integrated-logistics-chemical-supply-chains.php',
                'other-specialty-chemicals.php', 'paint-coating-industry-solvents.php',
                'pharma-analytical-grade-phosphate-salts.php', 'pharmaceutical-chemical-solvents.php',
                'phosphate-chemicals.php', 'potassium-chemicals.php', 'products.php', 'products_old.php',
                'sendMail.php', 'sourcing-paint-coating-solvents.php', 'sulfur-products.php',
                'technical-specifications-acids.php', 'thank-you.php', 'understanding-chemical-grades.php',
                'water-treatment-chemicals.php', 'why-high-purity-inorganic-salts-matter.php'
            ];

            foreach ($productFiles as $pf) {
                $bName = basename($pf);
                if (in_array($bName, $ignoreList) || str_ends_with($bName, '.orig')) continue;

                $fileContent = file_get_contents($pf);
                if (!str_contains($fileContent, '$product')) continue;

                $title = ''; $brand = ''; $hsn = ''; $pack = ''; $desc = ''; $app = ''; $image = '';
                if (preg_match('/["\']title["\']\s*=>\s*["\'](.*?)["\']/s', $fileContent, $m)) $title = trim($m[1]);
                if (preg_match('/["\']brand["\']\s*=>\s*["\'](.*?)["\']/s', $fileContent, $m)) $brand = trim($m[1]);
                if (preg_match('/["\']hsn["\']\s*=>\s*["\'](.*?)["\']/s', $fileContent, $m)) $hsn = trim($m[1]);
                if (preg_match('/["\']pack["\']\s*=>\s*["\'](.*?)["\']/s', $fileContent, $m)) $pack = trim($m[1]);
                if (preg_match('/["\']desc["\']\s*=>\s*["\'](.*?)["\']/s', $fileContent, $m)) $desc = trim($m[1]);
                if (preg_match('/["\']app["\']\s*=>\s*["\'](.*?)["\']/s', $fileContent, $m)) $app = trim($m[1]);
                if (preg_match('/["\']image["\']\s*=>\s*["\'](.*?)["\']/s', $fileContent, $m)) $image = trim($m[1]);

                if (empty($title)) continue;
                $slugStr = Str::slug(pathinfo($bName, PATHINFO_FILENAME));

                $catId = $catAcids->id;
                $lTitle = strtolower($title);
                if (str_contains($lTitle, 'acid')) $catId = $catAcids->id;
                elseif (str_contains($lTitle, 'caustic') || str_contains($lTitle, 'chlorine') || str_contains($lTitle, 'bleaching')) $catId = $catChlorAlkali->id;
                elseif (str_contains($lTitle, 'solvent') || str_contains($lTitle, 'alcohol') || str_contains($lTitle, 'acetate') || str_contains($lTitle, 'chloroform') || str_contains($lTitle, 'methanol')) $catId = $catSolvents->id;
                elseif (str_contains($lTitle, 'bor')) $catId = $catBoron->id;
                elseif (str_contains($lTitle, 'coal')) $catId = $catCoal->id;

                Product::updateOrCreate(
                    ['slug' => $slugStr],
                    [
                        'name' => $title,
                        'brand' => !empty($brand) ? $brand : 'SRCIL Standard',
                        'chemical_name' => $title,
                        'cas_number' => 'N/A',
                        'hsn_code' => !empty($hsn) ? $hsn : '2915',
                        'purity' => 'Technical Grade High Purity',
                        'packaging' => !empty($pack) ? $pack : 'Standard Packaging',
                        'description' => !empty($desc) ? $desc : $title . ' supplied by SR Chemical Industries Limited.',
                        'features' => ['High Purity Grade', 'Industrial Standard Quality', 'Reliable Supply'],
                        'applications' => !empty($app) ? [$app] : ['Industrial Chemical Synthesis'],
                        'specifications' => ['Purity' => 'Technical Grade', 'Form' => 'Standard'],
                        'storage_info' => 'Store in cool dry well ventilated warehouse.',
                        'category_id' => $catId,
                        'image_url' => !empty($image) ? $image : 'assets/img/added/product/' . $title . '.jpg',
                        'msds_url' => 'contact',
                        'specification_url' => $bName,
                        'product_url' => $bName,
                        'is_featured' => false
                    ]
                );
            }
        }

        // 5. Seed Blogs
        Blog::updateOrCreate(
            ['slug' => 'demystifying-chlor-alkali-products'],
            [
                'title' => 'Demystifying Chlor-Alkali Products: Caustic Soda, Chlorine & Derivatives',
                'summary' => 'A comprehensive technical overview of the Chlor-Alkali manufacturing process, key grade variations (Flakes, Lye, Prills), and industrial application benchmarks.',
                'content' => 'Chlor-Alkali electrolysis represents the backbone of modern chemical manufacturing. By electrolyzing brine (sodium chloride solution), three high-demand chemicals are yielded: Caustic Soda (NaOH), Chlorine (Cl2), and Hydrogen gas (H2)...',
                'category' => 'Chlor-Alkali Technical Guide',
                'read_time' => '6 min read',
                'published_at' => '2026-06-15',
                'image_url' => 'assets/img/added/product/Caustic-Soda-Flakes-NaOH.jpg',
                'url' => 'demystifying-chlor-alkali-products.php'
            ]
        );

        Blog::updateOrCreate(
            ['slug' => 'industrial-salt-water-treatment-chlor-alkali'],
            [
                'title' => 'Selecting the Right Coagulant for Industrial Water Treatment: PAC vs Alum',
                'summary' => 'Compare Poly Aluminium Chloride (PAC) and traditional Aluminium Sulphate (Alum) in water clarification, pH stability, and operational efficiency.',
                'content' => 'In industrial effluent treatment and municipal water plants, choosing between PAC and Alum impacts turbidity reduction speed, chemical dosage cost, and residual aluminum concentration...',
                'category' => 'Water Treatment',
                'read_time' => '5 min read',
                'published_at' => '2026-07-02',
                'image_url' => 'assets/img/added/product/Poly-Aluminium-Chloride-PAC.jpg',
                'url' => 'industrial-salt-water-treatment-chlor-alkali.php'
            ]
        );

        Blog::updateOrCreate(
            ['slug' => 'integrated-logistics-chemical-supply-chains'],
            [
                'title' => 'Sustainable Energy Transition: Utilizing Bio-Coal in Industrial Boilers',
                'summary' => 'How biomass torrefied bio-coal pellets reduce carbon footprint while maintaining high thermal efficiency in commercial boilers.',
                'content' => 'As carbon emissions mandates tighten globally, industrial boiler operators are migrating towards bio-coal briquettes. Offering 4200 Kcal/Kg heat energy with low ash and zero sulfur, bio-coal provides a seamless green replacement...',
                'category' => 'Energy Commodities',
                'read_time' => '4 min read',
                'published_at' => '2026-07-20',
                'image_url' => 'assets/img/added/product/Bio-Coal.jpg',
                'url' => 'integrated-logistics-chemical-supply-chains.php'
            ]
        );

        // 6. Seed FAQs
        Faq::updateOrCreate(
            ['question' => 'What is Caustic Soda Flakes and what are its key uses?'],
            [
                'answer' => 'Caustic Soda Flakes (Sodium Hydroxide, NaOH 99%) is a strong alkaline chemical in solid white flake form. It is widely used in textile mercerizing, pulp & paper pulping, soap & detergent manufacturing, water treatment pH adjustment, and chemical processing.',
                'category' => 'Products',
                'keywords' => ['caustic soda', 'flakes', 'naoh', 'sodium hydroxide', 'uses', 'purity']
            ]
        );

        Faq::updateOrCreate(
            ['question' => 'Do you export chemicals to UAE and other international countries?'],
            [
                'answer' => 'Yes! SR Chemical Industries Limited (SRCIL) actively exports high-grade industrial chemicals, solvents, and energy commodities to the UAE, Saudi Arabia, USA, Germany, Vietnam, Oman, Kuwait, South Africa, and over 25+ global destinations with full hazardous shipping compliance and customs documentation.',
                'category' => 'Export & Logistics',
                'keywords' => ['export', 'uae', 'dubai', 'saudi arabia', 'shipping', 'international', 'countries']
            ]
        );

        Faq::updateOrCreate(
            ['question' => 'Which chemicals are used for textile industries?'],
            [
                'answer' => 'SRCIL supplies key textile processing chemicals including Caustic Soda Flakes (mercerizing agent), Glacial Acetic Acid (dye fixing & pH control), Hydrogen Peroxide 50% (eco-bleaching agent), Sodium Hypochlorite, and Formic Acid.',
                'category' => 'Industries',
                'keywords' => ['textile', 'fabric', 'dyeing', 'bleaching', 'mercerizing', 'acetic acid', 'caustic soda']
            ]
        );

        Faq::updateOrCreate(
            ['question' => 'Show me all water treatment chemicals available at SR Chemicals.'],
            [
                'answer' => 'Our comprehensive water treatment product range includes: Poly Aluminium Chloride (PAC 30% powder & liquid), Caustic Soda Lye/Flakes (pH correction), Hydrochloric Acid (resin regeneration & descaling), Sodium Hypochlorite (disinfection & chlorination), and Hydrogen Peroxide 50% (COD reduction & odor control).',
                'category' => 'Water Treatment',
                'keywords' => ['water treatment', 'pac', 'poly aluminium chloride', 'effluent', 'coagulant', 'disinfection']
            ]
        );

        Faq::updateOrCreate(
            ['question' => 'What is the purity of Caustic Soda supplied by SRCIL?'],
            [
                'answer' => 'Caustic Soda Flakes and Prills feature a minimum purity of 99.0% NaOH. Caustic Soda Lye is supplied in liquid form at 48% to 50% concentration. All shipments include a Certificate of Analysis (COA) verifying purity specs.',
                'category' => 'Specifications',
                'keywords' => ['purity', 'caustic soda', 'grade', 'percentage', 'concentration', 'coa', 'naoh']
            ]
        );

        Faq::updateOrCreate(
            ['question' => 'How can I contact SR Chemicals for bulk pricing or inquiries?'],
            [
                'answer' => 'You can reach SR Chemical Industries Limited by calling +91 76001 81931 or +91 70415 53966, emailing srchemicalindustries9@gmail.com or marketing@srchemicalindustries.com, or visiting our location at A-97 Sai Ashish, NH-8 Vadadla, Bharuch 392011, Gujarat, India.',
                'category' => 'Contact',
                'keywords' => ['contact', 'phone', 'email', 'address', 'location', 'inquiry', 'quote', 'bulk']
            ]
        );

        Faq::updateOrCreate(
            ['question' => 'Do you provide bulk orders and custom packaging options?'],
            [
                'answer' => 'Yes, SRCIL specializes in bulk orders for domestic and export clients. We offer custom packaging options ranging from 25 Kg bags, 50 Kg HDPE bags, 250 Kg steel/plastic drums, ISO tank containers, to dedicated road liquid tankers.',
                'category' => 'Orders',
                'keywords' => ['bulk order', 'minimum quantity', 'packaging', 'tankers', 'iso tank', 'custom packaging']
            ]
        );

        Faq::updateOrCreate(
            ['question' => 'Are Technical Data Sheets (TDS) and Material Safety Data Sheets (MSDS) available?'],
            [
                'answer' => 'Yes! MSDS and TDS documents are available for download directly on each product detail page on our website, or you can request official stamped copies by contacting marketing@puregrade.in.',
                'category' => 'Compliance',
                'keywords' => ['msds', 'sds', 'tds', 'safety sheet', 'technical data', 'download', 'pdf']
            ]
        );

        // 7. Seed Certifications
        Certification::updateOrCreate(
            ['title' => 'ISO 9001:2015 Quality Management System'],
            [
                'issuer' => 'International Organization for Standardization',
                'description' => 'Certified for stringent quality control, chemical batch testing, and standardized trade operations.',
                'document_url' => 'certificate',
                'icon' => 'fa-award'
            ]
        );

        Certification::updateOrCreate(
            ['title' => 'Pure Grade Standard Quality Guarantee'],
            [
                'issuer' => 'SRCIL Quality Board',
                'description' => '100% verified assay purity, chemical batch tracing, and zero-impurity guarantee on all chemical consignments.',
                'document_url' => 'certificate',
                'icon' => 'fa-certificate'
            ]
        );

        Certification::updateOrCreate(
            ['title' => 'REACH Compliance & Import/Export License'],
            [
                'issuer' => 'Directorate General of Foreign Trade (DGFT)',
                'description' => 'Authorized global trade licensing for hazardous and non-hazardous chemical export and import transactions.',
                'document_url' => 'certificate',
                'icon' => 'fa-shield-halved'
            ]
        );

        // 8. Seed Contact Details
        ContactDetail::updateOrCreate(
            ['office_name' => 'Corporate & Marketing Office'],
            [
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
            ]
        );

        // 9. Seed Export Countries
        $countries = [
            ['name' => 'United Arab Emirates (UAE)', 'code' => 'AE', 'region' => 'Middle East', 'flag' => '🇦🇪', 'details' => 'Exporting Chlor-Alkali, Solvents, and Water Treatment chemicals to Dubai, Abu Dhabi, and Jebel Ali Port.'],
            ['name' => 'Saudi Arabia', 'code' => 'SA', 'region' => 'Middle East', 'flag' => '🇸🇦', 'details' => 'Supplying industrial acids, caustic soda, and specialty solvents for oilfield and petrochemical manufacturing.'],
            ['name' => 'United States of America (USA)', 'code' => 'US', 'region' => 'North America', 'flag' => '🇺🇸', 'details' => 'Supplying high purity inorganic borate salts, specialty chemicals, and pharmaceutical solvents.'],
            ['name' => 'Germany', 'code' => 'DE', 'region' => 'Europe', 'flag' => '🇩🇪', 'details' => 'REACH-compliant exports of fine chemical intermediates and high-grade acids.'],
            ['name' => 'Vietnam', 'code' => 'VN', 'region' => 'Southeast Asia', 'flag' => '🇻🇳', 'details' => 'Supplying textile auxiliary chemicals, PAC coagulants, and caustic soda for Vietnam manufacturing sector.'],
            ['name' => 'Indonesia', 'code' => 'ID', 'region' => 'Southeast Asia', 'flag' => '🇮🇩', 'details' => 'Chemical trade partnerships & coal commodity import/export operations.'],
            ['name' => 'South Africa', 'code' => 'ZA', 'region' => 'Africa', 'flag' => '🇿🇦', 'details' => 'Water purification chemicals, PAC, and mining grade reagents.'],
            ['name' => 'Oman & Kuwait', 'code' => 'OM/KW', 'region' => 'Middle East', 'flag' => '🇴🇲', 'details' => 'Oilfield chemicals, caustic soda lye, and industrial degreasers.']
        ];

        foreach ($countries as $c) {
            ExportCountry::updateOrCreate(
                ['name' => $c['name']],
                [
                    'code' => $c['code'],
                    'region' => $c['region'],
                    'flag_emoji' => $c['flag'],
                    'details' => $c['details']
                ]
            );
        }
    }
}
