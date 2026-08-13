<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    protected $fillable = [
        'name',
        'slug',
        'brand',
        'chemical_name',
        'cas_number',
        'hsn_code',
        'purity',
        'packaging',
        'description',
        'short_description',
        'features',
        'applications',
        'specifications',
        'storage_info',
        'category_id',
        'image_url',
        'msds_url',
        'specification_url',
        'specification_image',
        'product_url',
        'is_featured',
        'sort_order',
        'status',
        'meta_title',
        'meta_description',
        'meta_keywords'
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'status' => 'boolean',
        'sort_order' => 'integer'
    ];

    protected static function booted()
    {
        static::creating(function ($product) {
            if (empty($product->product_url)) {
                $product->product_url = '/products/' . ($product->slug ?? \Illuminate\Support\Str::slug($product->name));
            }
            if (is_null($product->description)) {
                $product->description = '';
            }
            if (is_null($product->image_url)) {
                $product->image_url = 'assets/img/added/Chemical Supply Solutions.jpg';
            }
        });
    }

    public static function normalizeValue($value)
    {
        if (is_null($value)) {
            return null;
        }

        if (is_array($value)) {
            $result = [];
            foreach ($value as $k => $v) {
                $cleaned = self::normalizeValue($v);
                if ($cleaned !== null && $cleaned !== '') {
                    if (is_array($cleaned)) {
                        foreach ($cleaned as $subK => $subV) {
                            if (is_numeric($subK)) {
                                $result[] = $subV;
                            } else {
                                $result[$subK] = $subV;
                            }
                        }
                    } else {
                        if (is_numeric($k)) {
                            $result[] = $cleaned;
                        } else {
                            $result[$k] = $cleaned;
                        }
                    }
                }
            }
            return $result;
        }

        if (is_string($value)) {
            $str = trim($value);
            if ($str === '') {
                return null;
            }

            for ($i = 0; $i < 5; $i++) {
                if ((str_starts_with($str, '"') && str_ends_with($str, '"')) || (str_starts_with($str, "'") && str_ends_with($str, "'"))) {
                    $unquoted = substr($str, 1, -1);
                    if ($unquoted !== false && $unquoted !== '') {
                        $str = trim($unquoted);
                    }
                }

                if (str_contains($str, '\\')) {
                    $stripped = stripslashes($str);
                    if ($stripped !== false) {
                        $str = trim($stripped);
                    }
                }

                $decoded = json_decode($str, true);
                if (json_last_error() === JSON_ERROR_NONE && $decoded !== null) {
                    if (is_array($decoded)) {
                        return self::normalizeValue($decoded);
                    }
                    if (is_string($decoded) && $decoded !== $str) {
                        $str = trim($decoded);
                        continue;
                    }
                }

                if (str_starts_with($str, '[') && str_ends_with($str, ']')) {
                    $inner = trim(substr($str, 1, -1));
                    $items = array_map(function ($item) {
                        return stripslashes(trim($item, " \t\n\r\0\v\"'"));
                    }, explode(',', $inner));
                    $items = array_values(array_filter($items, fn($item) => $item !== ''));
                    if (!empty($items)) {
                        return $items;
                    }
                }

                break;
            }

            $str = trim($str, " \t\n\r\0\v\"'");
            $str = stripslashes($str);
            return $str !== '' ? $str : null;
        }

        return $value;
    }

    public function getDescriptionAttribute($value): string
    {
        $norm = self::normalizeValue($value);
        if (is_array($norm)) {
            return implode(' ', $norm);
        }
        return $norm ?? '';
    }

    public function getShortDescriptionAttribute($value): string
    {
        $norm = self::normalizeValue($value);
        if (!empty($norm) && is_string($norm)) {
            return $norm;
        }
        $desc = $this->description;
        if (!empty($desc)) {
            return \Illuminate\Support\Str::limit(trim(strip_tags($desc)), 160);
        }
        return '';
    }

    public function getFeaturesAttribute($value): array
    {
        $norm = self::normalizeValue($value);
        if (is_array($norm)) {
            return array_values($norm);
        }
        if (is_string($norm) && $norm !== '') {
            return [$norm];
        }
        return [];
    }

    public function getApplicationsAttribute($value)
    {
        $norm = self::normalizeValue($value);
        if (is_array($norm)) {
            return array_values($norm);
        }
        if (is_string($norm) && $norm !== '') {
            return [$norm];
        }
        return [];
    }

    public function getSpecificationsAttribute($value)
    {
        $norm = self::normalizeValue($value);
        if (is_array($norm)) {
            return $norm;
        }
        if (is_string($norm) && $norm !== '') {
            return ['Specification' => $norm];
        }
        return [];
    }

    public function getStorageInfoAttribute($value): string
    {
        $norm = self::normalizeValue($value);
        if (is_array($norm)) {
            return implode(', ', $norm);
        }
        return $norm ?? '';
    }

    public function setDescriptionAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['description'] = implode(' ', array_filter(array_map('trim', $value)));
        } else {
            $this->attributes['description'] = is_null($value) ? null : (string) $value;
        }
    }

    public function setFeaturesAttribute($value)
    {
        $this->attributes['features'] = $this->encodeJsonAttribute($value);
    }

    public function setApplicationsAttribute($value)
    {
        $this->attributes['applications'] = $this->encodeJsonAttribute($value);
    }

    public function setSpecificationsAttribute($value)
    {
        $this->attributes['specifications'] = $this->encodeJsonAttribute($value);
    }

    public function setStorageInfoAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['storage_info'] = implode(', ', array_filter(array_map('trim', $value)));
        } else {
            $this->attributes['storage_info'] = is_null($value) ? null : (string) $value;
        }
    }

    private function encodeJsonAttribute($value)
    {
        if (is_null($value)) {
            return null;
        }

        if (is_array($value)) {
            $cleaned = self::normalizeValue($value);
            if (empty($cleaned)) {
                return null;
            }
            return json_encode($cleaned, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        if (is_string($value)) {
            $trimmed = trim($value);
            if ($trimmed === '' || strtolower($trimmed) === 'null') {
                return null;
            }
            $decoded = json_decode($trimmed, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $cleaned = self::normalizeValue($decoded);
                return !empty($cleaned) ? json_encode($cleaned, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : null;
            }
            return $trimmed;
        }

        return (string) $value;
    }

    public function getImageUrlAttribute($value): string
    {
        if (empty($value) || $value === '#') {
            return 'assets/img/added/Chemical Supply Solutions.jpg';
        }

        $clean = trim($value);
        if (str_starts_with($clean, 'http://') || str_starts_with($clean, 'https://')) {
            return $clean;
        }

        // Clean leading slashes and duplicated storage prefixes
        $clean = ltrim($clean, '/');
        $clean = str_replace(['public/storage/', 'storage/storage/'], 'storage/', $clean);

        if (str_starts_with($clean, 'uploads/')) {
            $clean = 'storage/' . $clean;
        }

        return $clean;
    }

    public function getSpecPdfUrlAttribute(): ?string
    {
        if (!empty($this->specification_image)) {
            return trim($this->specification_image);
        }

        $spec = (!empty($this->specification_url) && $this->specification_url !== '#') ? trim($this->specification_url) : null;
        $msds = (!empty($this->msds_url) && $this->msds_url !== '#') ? trim($this->msds_url) : null;

        $url = $spec ?: $msds;
        if (!empty($url) && is_string($url)) {
            $clean = trim($url);
            $lower = strtolower($clean);
            if (
                str_ends_with($lower, '.pdf') ||
                str_ends_with($lower, '.png') ||
                str_ends_with($lower, '.jpg') ||
                str_ends_with($lower, '.jpeg') ||
                str_ends_with($lower, '.webp') ||
                str_contains($lower, '/pdf/') ||
                str_contains($lower, '/assets/') ||
                str_contains($lower, 'storage/')
            ) {
                return $clean;
            }
        }
        return null;
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_product');
    }

    /**
     * Get full product path string e.g. "Products > GACL Products > Acid Products > Nitric Acid"
     */
    public function getHierarchyPathAttribute(): string
    {
        if ($this->category) {
            return $this->category->path . ' > ' . $this->name;
        }
        return 'Products > General > ' . $this->name;
    }

    /**
     * Get full clean SEO URL for product
     */
    public function getFullUrlAttribute(): string
    {
        if ($this->category) {
            $slugs = array_map(fn($cat) => $cat->slug, $this->category->ancestors);
            $slugs[] = $this->slug;
            return url('/c/' . implode('/', $slugs));
        }
        return url('/products/' . $this->slug);
    }

    public function relatedProducts()
    {
        return Product::where('category_id', $this->category_id)
            ->where('id', '!=', $this->id)
            ->where('status', true)
            ->take(4)
            ->get();
    }
}
