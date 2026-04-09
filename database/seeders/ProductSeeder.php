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
        $products = $this->fetchProducts();

        foreach ($products as $item) {
            Product::updateOrCreate(
                [
                    'name' => $item['title'],
                ],
                [
                    'price' => $item['price'] * 16500,
                    'stock' => (int) ($item['stock'] ?? random_int(20, 50)),
                    'description' => $item['description'],
                ]
            );
        }

        Log::info('Product seeder completed successfully.', [
            'total_products' => count($products),
        ]);
    }

    private function fetchProducts(): array
    {
        try {
            return $this->fetchFakeStoreProducts();
        } catch (Throwable $exception) {
            Log::warning('Fake Store product fetch failed. Falling back to DummyJSON.', [
                'exception' => $exception->getMessage(),
            ]);

            return $this->fetchDummyJsonProducts();
        }
    }

    private function fetchFakeStoreProducts(): array
    {
        $products = Http::timeout(30)
            ->acceptJson()
            ->get('https://fakestoreapi.com/products')
            ->throw()
            ->json();

        if (! is_array($products) || $products === []) {
            throw new \RuntimeException('Fake Store payload is empty or invalid.');
        }

        return $products;
    }

    private function fetchDummyJsonProducts(): array
    {
        try {
            $payload = Http::timeout(30)
                ->acceptJson()
                ->get('https://dummyjson.com/products')
                ->throw()
                ->json();

            $products = $payload['products'] ?? null;

            if (! is_array($products) || $products === []) {
                throw new \RuntimeException('DummyJSON payload is empty or invalid.');
            }

            return array_map(function (array $item): array {
                return [
                    'title' => $item['title'] ?? 'Produk Tanpa Nama',
                    'price' => (float) ($item['price'] ?? 0),
                    'stock' => (int) ($item['stock'] ?? random_int(20, 50)),
                    'description' => $item['description'] ?? '',
                ];
            }, $products);
        } catch (Throwable $exception) {
            Log::error('DummyJSON product fallback failed.', [
                'exception' => $exception->getMessage(),
            ]);

            throw new \RuntimeException('Semua sumber data produk gagal diambil.', previous: $exception);
        }
    }
}
