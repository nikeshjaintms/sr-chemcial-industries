<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Category;
use App\Models\Company;
use App\Models\Blog;
use App\Models\Faq;
use App\Models\Certification;
use App\Models\ContactDetail;
use App\Models\ExportCountry;
use App\Models\ChatHistory;

class ChatbotEngineService
{
    /**
     * Process incoming chat query and return a structured JSON response.
     */
    public function processQuery(string $query, string $sessionId): array
    {
        $queryClean = trim($query);
        $queryLower = mb_strtolower($queryClean);

        if (empty($queryClean)) {
            return [
                'status' => 'success',
                'message' => 'Please type a question or choose from the suggested questions below.',
                'card_type' => 'none',
                'session_id' => $sessionId
            ];
        }

        // 1. Retrieve session context (last discussed product ID, if any)
        $lastContext = ChatHistory::where('session_id', $sessionId)
            ->whereNotNull('context_product_id')
            ->latest('id')
            ->first();

        $contextProductId = $lastContext ? $lastContext->context_product_id : null;

        // 2. Check for Greetings / Hello
        if (preg_match('/\b(hi|hello|hey|greetings|good morning|good afternoon)\b/i', $queryClean)) {
            $company = Company::first();
            $greetingMsg = "Hello! Welcome to **" . ($company ? $company->name : 'SR Chemical Industries Limited') . "**.\n\n" .
                "I am your official AI Knowledge Assistant. I can help you with details on our **chemicals, purity specifications, export destinations, SDS/MSDS sheets, water treatment solutions, and contact details**.\n\n" .
                "How may I assist you today?";

            $this->saveHistory($sessionId, $queryClean, $greetingMsg, 'greeting', $contextProductId);

            return [
                'status' => 'success',
                'message' => $greetingMsg,
                'card_type' => 'suggested',
                'suggestions' => [
                    "What is Caustic Soda Flakes?",
                    "What is its purity?",
                    "Do you export to UAE?",
                    "Show me water treatment chemicals",
                    "Which chemical is used for textile industries?",
                    "How can I contact SR Chemicals?"
                ],
                'session_id' => $sessionId
            ];
        }

        // 3. Follow-up detection for purity, specifications, packaging, MSDS, applications
        if ($contextProductId && $this->isFollowUpQuestion($queryLower)) {
            $product = Product::with('category')->find($contextProductId);
            if ($product) {
                return $this->handleProductFollowUp($queryLower, $product, $sessionId);
            }
        }

        // 4. Highly Accurate Multi-Field Product Search Intent (Runs first so exact products are matched)
        $productSearchResult = $this->searchProducts($queryClean, $sessionId);
        if ($productSearchResult) {
            return $productSearchResult;
        }

        // 4.5 Check for "All Products" / Catalog intent
        $allProductsResponse = $this->handleAllProductsQuery($queryLower, $sessionId, $queryClean);
        if ($allProductsResponse) {
            return $allProductsResponse;
        }

        // 5. Industry / Application Query Intent (e.g. "chemicals for textile", "water treatment chemicals")
        $industryResponse = $this->handleIndustryOrCategoryQuery($queryLower, $sessionId, $queryClean);
        if ($industryResponse) {
            return $industryResponse;
        }

        // 6. Export Countries Query Intent (e.g. "do you export to UAE?", "export countries")
        $exportResponse = $this->handleExportQuery($queryLower, $sessionId, $queryClean);
        if ($exportResponse) {
            return $exportResponse;
        }

        // 7. Contact / Email / Inquiry Intent
        if (preg_match('/\b(contact|mail|email|e-mail|gmail|phone|call|mobile|number|address|location|reach|office|whatsapp|map|inquiry|enquiry|quote|pricing|price)\b/i', $queryLower) || str_contains($queryLower, 'mail') || str_contains($queryLower, 'email')) {
            return $this->handleContactQuery($sessionId, $queryClean);
        }

        // 8. Bulk Order / Minimum Order Intent
        if (preg_match('/\b(bulk|order|tanker|drums|minimum order|moq|quantity|packaging)\b/i', $queryLower)) {
            return $this->handleBulkOrderQuery($sessionId, $queryClean);
        }

        // 9. FAQ Matching
        $faqResponse = $this->handleFaqSearch($queryLower, $sessionId, $queryClean);
        if ($faqResponse) {
            return $faqResponse;
        }

        // 10. Blog Search Intent
        $blogResponse = $this->handleBlogSearch($queryLower, $sessionId, $queryClean);
        if ($blogResponse) {
            return $blogResponse;
        }

        // 11. Company Overview Intent
        if (preg_match('/\b(about|company|srcil|pure grade|who are you|history|mission|certificate|iso)\b/i', $queryLower)) {
            return $this->handleCompanyQuery($sessionId, $queryClean);
        }

        // 12. Strict Fallback - No hallucination
        $fallbackMsg = "No matching product found.";

        $this->saveHistory($sessionId, $queryClean, $fallbackMsg, 'fallback', $contextProductId);

        return [
            'status' => 'success',
            'message' => $fallbackMsg,
            'card_type' => 'contact_prompt',
            'contact_button' => [
                'label' => 'Contact Support Team',
                'url' => route('contact'),
                'phone' => '+91 76001 81931',
                'email' => 'marketing@srchemicalindustries.com'
            ],
            'session_id' => $sessionId
        ];
    }

    /**
     * Check if user question is a follow-up ("its purity", "packaging", "msds", "applications of it")
     */
    private function isFollowUpQuestion(string $queryLower): bool
    {
        return (bool) preg_match('/\b(its|it|this|that|same|the product|msds|sds|purity|specification|specifications|hsn|cas|packaging|package|application|applications|uses|storage|brand|price)\b/i', $queryLower);
    }

    /**
     * Handle follow-up query on previously discussed product
     */
    private function handleProductFollowUp(string $queryLower, Product $product, string $sessionId): array
    {
        $reply = "";

        if (str_contains($queryLower, 'purity') || str_contains($queryLower, 'grade') || str_contains($queryLower, 'percentage') || str_contains($queryLower, 'assay')) {
            $reply = "**Purity of " . $product->name . "**: `" . ($product->purity ?? 'Technical Grade High Purity') . "`\n\n" .
                     "• **Brand/Maker**: " . ($product->brand ?? 'SRCIL Standard') . "\n" .
                     "• **Chemical Formula**: " . ($product->chemical_name ?? 'N/A') . "\n" .
                     "• **CAS Number**: " . ($product->cas_number ?? 'N/A') . "\n" .
                     "• **HSN Code**: " . ($product->hsn_code ?? 'N/A');
        } elseif (str_contains($queryLower, 'cas')) {
            $reply = "**CAS Number for " . $product->name . "**: `" . ($product->cas_number ?? 'N/A') . "`";
        } elseif (str_contains($queryLower, 'hsn') || str_contains($queryLower, 'hs code')) {
            $reply = "**HSN Code for " . $product->name . "**: `" . ($product->hsn_code ?? 'N/A') . "`";
        } elseif (str_contains($queryLower, 'pack') || str_contains($queryLower, 'bag') || str_contains($queryLower, 'drum') || str_contains($queryLower, 'tanker')) {
            $reply = "**Packaging Details for " . $product->name . "**:\n" . ($product->packaging ?? '50 Kg HDPE Bags / Bulk Tankers');
        } elseif (str_contains($queryLower, 'msds') || str_contains($queryLower, 'sds') || str_contains($queryLower, 'safety')) {
            $reply = "**MSDS / Safety Data Sheet for " . $product->name . "**:\n" .
                     "You can download the official document [here](" . ($product->msds_url ?? route('contact')) . ").";
        } elseif (str_contains($queryLower, 'storage') || str_contains($queryLower, 'store') || str_contains($queryLower, 'handling')) {
            $reply = "**Storage Information for " . $product->name . "**:\n" . ($product->storage_info ?? 'Store in dry, well-ventilated area in sealed packaging.');
        } elseif (str_contains($queryLower, 'application') || str_contains($queryLower, 'use') || str_contains($queryLower, 'used for')) {
            $apps = is_array($product->applications) ? $product->applications : json_decode($product->applications, true);
            $reply = "**Applications of " . $product->name . "**:\n\n";
            if (!empty($apps)) {
                foreach ($apps as $app) {
                    $reply .= "• " . $app . "\n";
                }
            } else {
                $reply .= $product->description;
            }
        } elseif (str_contains($queryLower, 'spec') || str_contains($queryLower, 'specification')) {
            $specs = is_array($product->specifications) ? $product->specifications : json_decode($product->specifications, true);
            $reply = "**Technical Specifications for " . $product->name . "**:\n\n";
            if (!empty($specs)) {
                foreach ($specs as $key => $val) {
                    $reply .= "• **" . $key . "**: " . $val . "\n";
                }
            } else {
                $reply .= "Purity: " . $product->purity;
            }
        } else {
            return $this->buildProductResponse($product, $sessionId, $queryLower);
        }

        $this->saveHistory($sessionId, $queryLower, $reply, 'product_followup', $product->id);

        return [
            'status' => 'success',
            'message' => $reply,
            'card_type' => 'product_quick',
            'product' => $this->formatProductPayload($product),
            'session_id' => $sessionId
        ];
    }

    /**
     * Handle query asking for all products / product catalog listing
     */
    private function handleAllProductsQuery(string $queryLower, string $sessionId, string $queryClean): ?array
    {
        $isAllProductsIntent = (
            preg_match('/\b(all products|list products|product list|catalog|our products|show products|what products|chemical list|products list|list of products|products|product)\b/i', $queryClean) ||
            (str_contains($queryLower, 'product') && (
                str_contains($queryLower, 'all') || 
                str_contains($queryLower, 'list') || 
                str_contains($queryLower, 'show') || 
                str_contains($queryLower, 'badhi') || 
                str_contains($queryLower, 'ketli') || 
                str_contains($queryLower, 'ahda') || 
                str_contains($queryLower, 'jetl') ||
                str_contains($queryLower, 'available')
            )) ||
            in_array($queryLower, ['products', 'all products', 'product list', 'catalog'])
        );

        if (!$isAllProductsIntent) {
            return null;
        }

        $products = Product::where('status', true)->with('category')->get();
        if ($products->isEmpty()) {
            return null;
        }

        $msg = "### 🧪 Complete Product Range — SR Chemical Industries Limited\n\n";
        $msg .= "We supply high-grade industrial chemicals, chlor-alkali compounds, acids, solvents, boron compounds, and energy products:\n\n";

        $grouped = $products->groupBy(fn($p) => $p->category ? $p->category->name : 'General Industrial Chemicals');

        foreach ($grouped as $catName => $items) {
            $msg .= "#### 🔹 " . $catName . ":\n";
            foreach ($items as $p) {
                $msg .= "• **[" . $p->name . "](" . route('products.show', $p->slug) . ")**: Purity `" . ($p->purity ?? 'Standard Grade') . "` (" . ($p->brand ?? 'SRCIL') . ")\n";
            }
            $msg .= "\n";
        }

        $msg .= "💡 *Tip: Click on any product link above or type a specific product name (e.g. \"Caustic Soda Flakes\" or \"MDC\") for full technical specs, MSDS, and packaging info.*";

        $this->saveHistory($sessionId, $queryClean, $msg, 'all_products_list');

        return [
            'status' => 'success',
            'message' => $msg,
            'card_type' => 'product_list',
            'products' => $products->map(fn($p) => $this->formatProductPayload($p))->toArray(),
            'session_id' => $sessionId
        ];
    }

    /**
     * Highly accurate multi-field product search algorithm
     */
    public function searchProducts(string $queryRaw, string $sessionId): ?array
    {
        $result = SearchService::search($queryRaw);

        if ($result['count'] === 0) {
            return null;
        }

        // Deduplicate matched products strictly by Product Name & Product ID
        $products = collect($result['products'])
            ->unique(fn($p) => mb_strtolower(trim($p->name), 'UTF-8'))
            ->values()
            ->all();

        if (empty($products)) {
            return null;
        }

        // Single best product match found -> Return ONLY that product
        if (count($products) === 1) {
            return $this->buildProductResponse($products[0], $sessionId, $queryRaw);
        }

        // Multiple matched products
        $topProducts = array_slice($products, 0, 10);
        $msg = "### 🧪 Found " . count($products) . " Matching Products\n\n";
        $msg .= "Here are the best matches for **\"" . e($queryRaw) . "\"**:\n\n";

        foreach ($topProducts as $p) {
            $brandStr = !empty($p->brand) ? " | Brand: **" . e($p->brand) . "**" : "";
            $catStr = $p->category ? " | Category: *" . e($p->category->name) . "*" : "";
            $msg .= "• **[" . $p->name . "](" . route('products.show', $p->slug) . ")**" . $brandStr . $catStr . "\n";
        }

        $msg .= "\n💡 *Click on any product above or type the exact product name for complete details.*";

        $this->saveHistory($sessionId, $queryRaw, $msg, 'product_search_list');

        return [
            'status' => 'success',
            'message' => $msg,
            'card_type' => 'product_list',
            'products' => array_map(fn($p) => $this->formatProductPayload($p), $topProducts),
            'session_id' => $sessionId
        ];
    }

    /**
     * Build rich Product Response with full metadata and Card payload
     */
    private function buildProductResponse(Product $product, string $sessionId, string $queryClean): array
    {
        $features = is_array($product->features) ? $product->features : json_decode($product->features, true);
        $apps = is_array($product->applications) ? $product->applications : json_decode($product->applications, true);
        $specs = is_array($product->specifications) ? $product->specifications : json_decode($product->specifications, true);

        $msg = "### 🧪 " . $product->name . "\n\n";
        $msg .= "**Brand**: " . ($product->brand ?? 'SRCIL Premier') . "\n";
        $msg .= "**Purity Grade**: `" . ($product->purity ?? 'Standard High Grade') . "`\n";
        if (!empty($product->chemical_name) && $product->chemical_name !== $product->name) {
            $msg .= "**Chemical Name**: " . $product->chemical_name . "\n";
        }
        $msg .= "**HSN Code**: `" . ($product->hsn_code ?? 'N/A') . "`";
        if (!empty($product->cas_number) && $product->cas_number !== 'N/A') {
            $msg .= " | **CAS No**: `" . $product->cas_number . "`";
        }
        $msg .= "\n**Packaging**: " . ($product->packaging ?? 'Standard Packaging') . "\n\n";

        $msg .= "#### 📋 Description:\n" . $product->description . "\n\n";

        if (!empty($features)) {
            $msg .= "#### ✨ Key Features:\n";
            foreach ($features as $f) {
                $msg .= "• " . $f . "\n";
            }
            $msg .= "\n";
        }

        if (!empty($apps)) {
            $msg .= "#### 🏭 Major Applications:\n";
            foreach ($apps as $a) {
                $msg .= "• " . $a . "\n";
            }
            $msg .= "\n";
        }

        if (!empty($specs)) {
            $msg .= "#### 📊 Technical Specifications:\n";
            foreach ($specs as $k => $v) {
                $msg .= "• **" . $k . "**: " . $v . "\n";
            }
            $msg .= "\n";
        }

        if ($product->storage_info) {
            $msg .= "#### 🛡️ Storage & Safety:\n" . $product->storage_info . "\n\n";
        }

        $this->saveHistory($sessionId, $queryClean, $msg, 'product_detail', $product->id);

        return [
            'status' => 'success',
            'message' => $msg,
            'card_type' => 'product',
            'product' => $this->formatProductPayload($product),
            'session_id' => $sessionId
        ];
    }

    /**
     * Handle Industry / Application & Category queries
     */
    private function handleIndustryOrCategoryQuery(string $queryLower, string $sessionId, string $queryClean): ?array
    {
        if (str_contains($queryLower, 'water treatment') || str_contains($queryLower, 'effluent') || str_contains($queryLower, 'purification')) {
            $products = Product::where('status', true)->where(function($q) {
                $q->whereHas('category', function($catQ) {
                    $catQ->where('slug', 'water-treatment-chemicals');
                })->orWhere('applications', 'LIKE', '%water treatment%');
            })->get();

            $msg = "### 💧 Water Treatment Chemicals at SR Chemicals\n\n";
            $msg .= "We supply high-efficiency water treatment, coagulation, and effluent neutralization chemicals:\n\n";

            foreach ($products as $p) {
                $msg .= "• **[" . $p->name . "](" . route('products.show', $p->slug) . ")**: " . ($p->purity ?? 'High Grade') . " — " . substr($p->description, 0, 100) . "...\n";
            }

            $this->saveHistory($sessionId, $queryClean, $msg, 'category_query');

            return [
                'status' => 'success',
                'message' => $msg,
                'card_type' => 'product_list',
                'products' => $products->map(fn($p) => $this->formatProductPayload($p))->toArray(),
                'session_id' => $sessionId
            ];
        }

        if (str_contains($queryLower, 'textile') || str_contains($queryLower, 'dyeing') || str_contains($queryLower, 'fabric')) {
            $products = Product::where('status', true)->where('applications', 'LIKE', '%textile%')->get();

            $msg = "### 🧶 Chemicals Used for Textile & Dyeing Industries\n\n";
            $msg .= "SR Chemical Industries Limited provides high-purity chemicals for textile processing, mercerization, eco-bleaching, and color fixing:\n\n";

            foreach ($products as $p) {
                $msg .= "• **[" . $p->name . "](" . route('products.show', $p->slug) . ")**: Purity `" . ($p->purity ?? 'Standard Grade') . "` (" . ($p->brand ?? 'SRCIL') . ")\n";
            }

            $this->saveHistory($sessionId, $queryClean, $msg, 'industry_query');

            return [
                'status' => 'success',
                'message' => $msg,
                'card_type' => 'product_list',
                'products' => $products->map(fn($p) => $this->formatProductPayload($p))->toArray(),
                'session_id' => $sessionId
            ];
        }

        if (str_contains($queryLower, 'solvent') || str_contains($queryLower, 'solvents') || str_contains($queryLower, 'degreas')) {
            $products = Product::where('status', true)->where(function($q) {
                $q->whereHas('category', function($catQ) {
                    $catQ->where('slug', 'pharmaceutical-chemical-solvents');
                })->orWhere('slug', 'methylene-chloride');
            })->get();

            $msg = "### 🧪 Industrial & Pharmaceutical Solvents\n\n";
            $msg .= "We supply high-purity organic solvents for pharmaceutical synthesis, paint stripping, and industrial degreasing:\n\n";

            foreach ($products as $p) {
                $msg .= "• **[" . $p->name . "](" . route('products.show', $p->slug) . ")**: Purity `" . ($p->purity ?? 'Standard Grade') . "` (" . ($p->brand ?? 'SRCIL') . ")\n";
            }

            $this->saveHistory($sessionId, $queryClean, $msg, 'category_query');

            return [
                'status' => 'success',
                'message' => $msg,
                'card_type' => 'product_list',
                'products' => $products->map(fn($p) => $this->formatProductPayload($p))->toArray(),
                'session_id' => $sessionId
            ];
        }

        if (preg_match('/\b(all acids|acid category|list of acids|acid products)\b/i', $queryClean)) {
            $products = Product::where('status', true)->whereHas('category', function($q) {
                $q->where('slug', 'acid-products');
            })->get();

            $msg = "### ⚗️ Industrial Grade Acid Products\n\n";
            foreach ($products as $p) {
                $msg .= "• **[" . $p->name . "](" . route('products.show', $p->slug) . ")**: Purity `" . ($p->purity ?? 'Standard Grade') . "`\n";
            }

            $this->saveHistory($sessionId, $queryClean, $msg, 'category_query');

            return [
                'status' => 'success',
                'message' => $msg,
                'card_type' => 'product_list',
                'products' => $products->map(fn($p) => $this->formatProductPayload($p))->toArray(),
                'session_id' => $sessionId
            ];
        }

        return null;
    }

    /**
     * Handle Export Destination queries
     */
    private function handleExportQuery(string $queryLower, string $sessionId, string $queryClean): ?array
    {
        if (str_contains($queryLower, 'export') || str_contains($queryLower, 'uae') || str_contains($queryLower, 'dubai') || str_contains($queryLower, 'saudi') || str_contains($queryLower, 'countries')) {
            $countries = ExportCountry::all();

            $msg = "### 🌍 Global Export Network - SR Chemical Industries Limited\n\n";
            $msg .= "Yes! We export high-purity industrial chemicals, solvents, and energy commodities across the globe with full DGFT and international shipping compliance.\n\n";
            $msg .= "#### Key Export Markets:\n";

            foreach ($countries as $c) {
                $msg .= "• " . ($c->flag_emoji ?? '🌐') . " **" . $c->name . "** (" . $c->region . "): " . $c->details . "\n";
            }

            $msg .= "\nFor international trade queries or FOB/CIF price quotes, contact **marketing@srchemicalindustries.com**.";

            $this->saveHistory($sessionId, $queryClean, $msg, 'export_query');

            return [
                'status' => 'success',
                'message' => $msg,
                'card_type' => 'export_list',
                'countries' => $countries->toArray(),
                'session_id' => $sessionId
            ];
        }

        return null;
    }

    /**
     * Handle Contact & Inquiry queries
     */
    private function handleContactQuery(string $sessionId, string $queryClean): array
    {
        $contact = ContactDetail::first();

        $msg = "### 📞 Contact SR Chemical Industries Limited\n\n";
        $msg .= "📍 **Location**: " . ($contact ? $contact->address : 'A-97 Sai Ashish, NH-8 Vadadla, Bharuch 392011') . "\n";
        $msg .= "📞 **Contact Number**: " . ($contact ? $contact->phone : '+91 76001 81931 / +91 70415 53966') . "\n";
        $msg .= "✉️ **Email**: " . ($contact ? $contact->email : 'srchemicalindustries9@gmail.com / marketing@srchemicalindustries.com') . "\n";
        $msg .= "🕒 **Working Hours**: " . ($contact ? $contact->working_hours : 'Mon - Sat: 9:00 AM - 7:00 PM IST') . "\n\n";
        $msg .= "You can also submit an inquiry online via our [Contact Page](" . route('contact') . ").";

        $this->saveHistory($sessionId, $queryClean, $msg, 'contact_query');

        return [
            'status' => 'success',
            'message' => $msg,
            'card_type' => 'contact',
            'contact' => $contact ? $contact->toArray() : [],
            'session_id' => $sessionId
        ];
    }

    /**
     * Handle Bulk Order & Packaging queries
     */
    private function handleBulkOrderQuery(string $sessionId, string $queryClean): array
    {
        $msg = "### 📦 Bulk Order & Logistics Capabilities\n\n";
        $msg .= "Yes! SR Chemical Industries Limited specializes in direct bulk order supply for domestic and international clients.\n\n";
        $msg .= "#### Packaging Options Available:\n";
        $msg .= "• **Solid Chemicals**: 25 Kg / 50 Kg Moisture-proof HDPE bags with inner liner, 1000 Kg Jumbo bags.\n";
        $msg .= "• **Liquid Chemicals & Solvents**: 30 Kg HDPE carboys, 210 L steel/plastic drums, ISO tank containers, and dedicated road tankers (15 to 30 tons).\n\n";
        $msg .= "To request a bulk order quotation or custom logistics arrangement, please reach out to **srchemicalindustries9@gmail.com** / **marketing@srchemicalindustries.com** or call **+91 76001 81931 / +91 70415 53966**.";

        $this->saveHistory($sessionId, $queryClean, $msg, 'bulk_order_query');

        return [
            'status' => 'success',
            'message' => $msg,
            'card_type' => 'general',
            'session_id' => $sessionId
        ];
    }

    /**
     * Handle FAQ Database Search
     */
    private function handleFaqSearch(string $queryLower, string $sessionId, string $queryClean): ?array
    {
        $faqs = Faq::all();
        $bestMatch = null;
        $highestScore = 0;

        foreach ($faqs as $faq) {
            $qLower = mb_strtolower($faq->question);
            $keywords = is_array($faq->keywords) ? $faq->keywords : json_decode($faq->keywords, true);

            $score = 0;
            if (!empty($keywords)) {
                foreach ($keywords as $kw) {
                    if (str_contains($queryLower, mb_strtolower($kw))) {
                        $score += 2;
                    }
                }
            }

            if (similar_text($queryLower, $qLower, $percent) && $percent > 40) {
                $score += ($percent / 10);
            }

            if ($score > $highestScore) {
                $highestScore = $score;
                $bestMatch = $faq;
            }
        }

        if ($bestMatch && $highestScore >= 2) {
            $msg = "### ❓ " . $bestMatch->question . "\n\n" . $bestMatch->answer;
            $this->saveHistory($sessionId, $queryClean, $msg, 'faq_match');

            return [
                'status' => 'success',
                'message' => $msg,
                'card_type' => 'faq',
                'faq' => $bestMatch->toArray(),
                'session_id' => $sessionId
            ];
        }

        return null;
    }

    /**
     * Handle Blog Database Search
     */
    private function handleBlogSearch(string $queryLower, string $sessionId, string $queryClean): ?array
    {
        if (str_contains($queryLower, 'blog') || str_contains($queryLower, 'article') || str_contains($queryLower, 'guide') || str_contains($queryLower, 'news')) {
            $blogs = Blog::all();

            $msg = "### 📰 SR Chemicals Technical Blogs & Guides\n\n";
            foreach ($blogs as $b) {
                $msg .= "• **[" . $b->title . "](" . route('blog.show', $b->slug) . ")**: " . $b->summary . "\n\n";
            }

            $this->saveHistory($sessionId, $queryClean, $msg, 'blog_search');

            return [
                'status' => 'success',
                'message' => $msg,
                'card_type' => 'blog_list',
                'blogs' => $blogs->toArray(),
                'session_id' => $sessionId
            ];
        }

        return null;
    }

    /**
     * Handle Company Overview
     */
    private function handleCompanyQuery(string $sessionId, string $queryClean): array
    {
        $company = Company::first();

        $msg = "### 🏢 About " . ($company ? $company->name : 'SR Chemical Industries Limited') . "\n\n";
        $msg .= ($company ? $company->about : 'Premier chemical supplier and global exporter based in Bharuch, Gujarat, India.') . "\n\n";
        $msg .= "#### 🌟 Why Choose Us:\n";
        $msg .= "• **Integrity in Trade**: Built on transparency and long-term partnerships.\n";
        $msg .= "• **Global Footprint**: Serving domestic India & 25+ international destinations.\n";
        $msg .= "• **Quality Certified**: 100% verified assay purity & ISO standard compliance.\n\n";
        $msg .= "Learn more on our [About Us](" . route('about') . ") page.";

        $this->saveHistory($sessionId, $queryClean, $msg, 'company_overview');

        return [
            'status' => 'success',
            'message' => $msg,
            'card_type' => 'general',
            'session_id' => $sessionId
        ];
    }

    /**
     * Save query and response to ChatHistory table for context persistence
     */
    private function saveHistory(string $sessionId, string $query, string $response, string $intent, ?int $productId = null): void
    {
        ChatHistory::create([
            'session_id' => $sessionId,
            'user_query' => $query,
            'bot_response' => $response,
            'matched_intent' => $intent,
            'context_product_id' => $productId
        ]);
    }

    /**
     * Format Product Eloquent model into clean array payload
     */
    private function formatProductPayload(Product $p): array
    {
        $catName = $p->category ? $p->category->name : 'General Industrial Chemicals';
        $subCatName = null;
        if ($p->category && $p->category->parent) {
            $subCatName = $p->category->name;
            $catName = $p->category->parent->name;
        }

        $imageUrl = $p->image_url ? asset($p->image_url) : asset('assets/img/added/Chemical Supply Solutions.jpg');
        $msdsUrl = ($p->msds_url && $p->msds_url !== '#') ? asset($p->msds_url) : null;
        $specPdfUrl = $p->spec_pdf_url ? asset($p->spec_pdf_url) : $msdsUrl;

        return [
            'id' => $p->id,
            'name' => $p->name,
            'slug' => $p->slug,
            'category' => $catName,
            'subcategory' => $subCatName,
            'category_path' => $p->hierarchy_path,
            'brand' => $p->brand,
            'chemical_name' => $p->chemical_name,
            'cas_number' => $p->cas_number,
            'hsn_code' => $p->hsn_code,
            'purity' => $p->purity,
            'packaging' => $p->packaging,
            'short_description' => $p->short_description,
            'description' => $p->description,
            'specifications' => $p->specifications,
            'image_url' => $imageUrl,
            'msds_url' => $msdsUrl,
            'spec_pdf_url' => $specPdfUrl,
            'product_url' => $p->full_url
        ];
    }
}
