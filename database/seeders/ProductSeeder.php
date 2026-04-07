<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        try {
            $products = Http::timeout(30)
                ->acceptJson()
                ->get('https://fakestoreapi.com/products')
                ->throw()
                ->json();

            foreach ($products as $item) {
                Product::updateOrCreate(
                    [
                        'name' => $item['title'],
                    ],
                    [
                        'price' => $item['price'] * 16500,
                        'stock' => random_int(20, 50),
                        'description' => $item['description'],
                    ]
                );
            }

            Log::info('Product seeder completed successfully.', [
                'total_products' => count($products),
            ]);
        } catch (Throwable $exception) {
            Log::error('Product seeder failed.', [
                'exception' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }
}
