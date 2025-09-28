<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\SubCategory;
use App\Models\Offer;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductsImport implements ToModel, WithHeadingRow
{
    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function model(array $row)
    {
        // Skip row if required fields are missing
        if (empty($row['name']) || empty($row['price']) || empty($row['sub_category_id'])) {
            return null;
        }

        // Validate subcategory exists
        $subCategory = SubCategory::find($row['sub_category_id']);
        if (!$subCategory) {
            return null; // skip row if subcategory doesn't exist
        }

        // Create product
        $product = Product::create([
            'name' => $row['name'],
            'price' => $row['price'],
            'model_number' => $row['model_number'] ?? null,
            'sub_category_id' => $row['sub_category_id'],
            'description' => $row['description'] ?? null,
            'min_order_quantity' => $row['min_order_quantity'] ?? 1,
            'preparation_days' => $row['preparation_days'] ?? null,
            'shipping_days' => $row['shipping_days'] ?? null,
            'production_capacity' => $row['production_capacity'] ?? null,
            'product_weight' => $row['product_weight'] ?? null,
            'package_dimensions' => $row['package_dimensions'] ?? null,
            'material_type' => $row['material_type'] ?? null,
            'available_quantity' => $row['available_quantity'] ?? null,
            'sizes' => isset($row['sizes']) ? explode(',', $row['sizes']) : [],
            'colors' => isset($row['colors']) ? json_decode($row['colors'], true) : [],
            'price_tiers' => $this->parseWholesale($row),
            'product_status' => $row['product_status'] ?? 'ready_for_delivery',
            'business_data_id' => $this->user->business->id,
            'slug' => Str::slug($row['name']) . '-' . uniqid(),
            'is_featured' => true,
        ]);

        // Create offer if offer data exists
        if (!empty($row['discount_percent']) && !empty($row['offer_start']) && !empty($row['offer_end'])) {
            Offer::create([
                'product_id' => $product->id,
                'discount_percent' => $row['discount_percent'],
                'offer_start' => $row['offer_start'],
                'offer_end' => $row['offer_end'],
            ]);
        }

        return $product;
    }

    /**
     * Parse wholesale tiers from Excel row
     */
    private function parseWholesale($row)
    {
        $tiers = [];
        for ($i = 1; $i <= 2; $i++) { // support 2 wholesale tiers
            if (!empty($row["wholesale_from_$i"]) || !empty($row["wholesale_to_$i"]) || !empty($row["wholesale_price_$i"])) {
                $tiers[] = [
                    'from' => $row["wholesale_from_$i"],
                    'to' => $row["wholesale_to_$i"],
                    'price' => $row["wholesale_price_$i"],
                ];
            }
        }
        return $tiers;
    }
}
