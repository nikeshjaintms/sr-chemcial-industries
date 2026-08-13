<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;

class BulkPdfMatchingService
{
    protected ProductFilenameMatcher $matcher;

    public function __construct(?ProductFilenameMatcher $matcher = null)
    {
        $this->matcher = $matcher ?? new ProductFilenameMatcher();
    }

    public function normalizeFilename(string $filename): string
    {
        return $this->matcher->normalizeFilename($filename);
    }

    public function normalizeProductString(?string $input): string
    {
        return $this->matcher->normalizeProductString($input);
    }

    public function getBaseProductString(?string $input): string
    {
        return $this->matcher->getBaseProductString($input);
    }

    /**
     * Match a given filename against the existing products collection.
     */
    public function matchFilenameToProduct(
        string $filename,
        Collection $allProducts,
        string $pdfType = 'msds',
        string $mode = 'skip'
    ): array {
        $res = $this->matcher->matchFilenameToProduct($filename, $allProducts, $pdfType, $mode);

        $productId = $res['matched_product_id'];
        $productObj = $productId ? $allProducts->firstWhere('id', $productId) ?? Product::find($productId) : null;

        return [
            'filename' => $filename,
            'norm_filename' => $this->normalizeFilename($filename),
            'product' => $productObj,
            'matched_product_id' => $productId,
            'matched_product_name' => $res['matched_product_name'],
            'status' => $res['status'],
            'message' => $res['message'],
            'badge_class' => match ($res['status']) {
                'SUCCESS', 'MATCHED' => 'bg-success',
                'ALREADY EXISTS' => 'bg-info text-dark',
                'AMBIGUOUS' => 'bg-warning text-dark',
                default => 'bg-secondary',
            },
        ];
    }
}
