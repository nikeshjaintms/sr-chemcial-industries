<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;

class HierarchySeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ensure Root Categories exist
        $gacl = Category::firstOrCreate(['slug' => 'gacl-products'], [
            'name' => 'GACL Products',
            'type' => 'Industrial Chemicals',
            'description' => 'Gujarat Alkalies and Chemicals Limited (GACL) products supplied by SR Chemical Industries.',
            'sort_order' => 1,
            'status' => true
        ]);
        $gacl->update(['parent_id' => null, 'sort_order' => 1]);

        $organic = Category::firstOrCreate(['slug' => 'organic-products'], [
            'name' => 'Organic Products',
            'type' => 'Organic Chemicals',
            'description' => 'High purity organic chemical intermediates and aromatic compounds.',
            'sort_order' => 2,
            'status' => true
        ]);
        $organic->update(['parent_id' => null, 'sort_order' => 2]);

        $dmcc = Category::firstOrCreate(['slug' => 'dmcc-products'], [
            'name' => 'DMCC Products',
            'type' => 'Industrial Chemicals',
            'description' => 'Dharamsi Morarji Chemical Company (DMCC) boron and sulfur products.',
            'sort_order' => 3,
            'status' => true
        ]);
        $dmcc->update(['parent_id' => null, 'sort_order' => 3]);

        $gnfc = Category::firstOrCreate(['slug' => 'gnfc-products'], [
            'name' => 'GNFC',
            'type' => 'Industrial Chemicals',
            'description' => 'Gujarat Narmada Valley Fertilizers & Chemicals (GNFC) industrial chemicals.',
            'sort_order' => 4,
            'status' => true
        ]);
        $gnfc->update(['name' => 'GNFC', 'parent_id' => null, 'sort_order' => 4]);

        $solvents = Category::firstOrCreate(['slug' => 'industrial-solvents-commodities'], [
            'name' => 'Industrial Solvents & Commodities',
            'type' => 'Industrial Solvents',
            'description' => 'Paints, coatings, pharmaceutical, and degreasing industrial solvents.',
            'sort_order' => 5,
            'status' => true
        ]);
        $solvents->update(['parent_id' => null, 'sort_order' => 5]);

        // 2. Map GACL Subcategories
        $gaclChildren = [
            'acid-products' => ['name' => 'Acid Products', 'order' => 1],
            'chlor-alkali-chemicals' => ['name' => 'Chlor-Alkali Chemicals', 'order' => 2],
            'hydrogen-peroxide-chemicals' => ['name' => 'Hydrogen & Peroxide Chemicals', 'order' => 3],
            'chloromethane-chemicals' => ['name' => 'Chloromethane Chemicals', 'order' => 4],
            'potassium-chemicals' => ['name' => 'Potassium Chemicals', 'order' => 5],
            'aluminium-based-chemicals' => ['name' => 'Aluminium Based Chemicals', 'order' => 6],
            'water-treatment-chemicals' => ['name' => 'Water Treatment Chemicals', 'order' => 7],
            'phosphate-chemicals' => ['name' => 'Phosphate Chemicals', 'order' => 8],
            'other-specialty-chemicals' => ['name' => 'Other Specialty Chemicals', 'order' => 9],
        ];
        foreach ($gaclChildren as $slug => $info) {
            Category::updateOrCreate(['slug' => $slug], [
                'name' => $info['name'],
                'type' => 'Industrial Chemicals',
                'parent_id' => $gacl->id,
                'sort_order' => $info['order'],
                'status' => true
            ]);
        }

        // 3. Map Organic Subcategories
        $organicChildren = [
            'chlorobenzenes' => ['name' => 'Chlorobenzenes', 'order' => 1],
            'nitrobenzenes' => ['name' => 'Nitrobenzenes', 'order' => 2],
            'anilines-toluidines' => ['name' => 'Anilines & Toluidines', 'order' => 3],
            'calcium-benzene-products' => ['name' => 'Calcium & Benzene Products', 'order' => 4],
        ];
        foreach ($organicChildren as $slug => $info) {
            Category::updateOrCreate(['slug' => $slug], [
                'name' => $info['name'],
                'type' => 'Organic Chemicals',
                'parent_id' => $organic->id,
                'sort_order' => $info['order'],
                'status' => true
            ]);
        }

        // 4. Map DMCC Subcategories
        $dmccChildren = [
            'boron-chemicals' => ['name' => 'Boron Chemicals', 'order' => 1],
            'sulfur-products' => ['name' => 'Sulfur Products', 'order' => 2],
        ];
        foreach ($dmccChildren as $slug => $info) {
            Category::updateOrCreate(['slug' => $slug], [
                'name' => $info['name'],
                'type' => 'Industrial Chemicals',
                'parent_id' => $dmcc->id,
                'sort_order' => $info['order'],
                'status' => true
            ]);
        }

        // 5. Map GNFC Subcategories
        $gnfcChildren = [
            'organic-acids-esters' => ['name' => 'Organic Acids & Esters', 'order' => 1],
            'industrial-intermediates' => ['name' => 'Industrial Intermediates', 'order' => 2],
            'specialty-chemicals-urea' => ['name' => 'Specialty Chemicals & Urea', 'order' => 3],
        ];
        foreach ($gnfcChildren as $slug => $info) {
            Category::updateOrCreate(['slug' => $slug], [
                'name' => $info['name'],
                'type' => 'Industrial Chemicals',
                'parent_id' => $gnfc->id,
                'sort_order' => $info['order'],
                'status' => true
            ]);
        }

        // 6. Map Industrial Solvents Subcategories
        $solventChildren = [
            'paint-coating-industry-solvents' => ['name' => 'Paint & Coating Industry Solvents', 'order' => 1],
            'pharmaceutical-chemical-solvents' => ['name' => 'Pharmaceutical & Chemical Solvents', 'order' => 2],
            'cleaning-degreasing-solvents' => ['name' => 'Cleaning & Degreasing Solvents', 'order' => 3],
            'coal-products' => ['name' => 'Coal & Energy Products', 'order' => 4],
        ];
        foreach ($solventChildren as $slug => $info) {
            Category::updateOrCreate(['slug' => $slug], [
                'name' => $info['name'],
                'type' => 'Industrial Solvents',
                'parent_id' => $solvents->id,
                'sort_order' => $info['order'],
                'status' => true
            ]);
        }

        // 7. Ensure all products have category_product pivot sync
        foreach (Product::all() as $product) {
            if ($product->category_id) {
                $product->categories()->sync([$product->category_id]);
            }
        }
    }
}
