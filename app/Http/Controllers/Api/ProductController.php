<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProductController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $products = Product::query()
                ->latest()
                ->paginate(10);

            return ProductResource::collection($products)
                ->additional([
                    'message' => 'Daftar produk berhasil diambil.',
                ])
                ->response();
        } catch (Throwable $exception) {
            Log::error('Failed to fetch products from API.', [
                'exception' => $exception->getMessage(),
            ]);

            return response()->json([
                'message' => 'Terjadi kesalahan saat mengambil daftar produk.',
            ], 500);
        }
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $product = Product::create($request->validated());

            DB::commit();

            Log::info('Product created successfully from API.', [
                'product_id' => $product->id,
                'product_name' => $product->name,
            ]);

            return (new ProductResource($product))
                ->additional([
                    'message' => 'Produk berhasil dibuat.',
                ])
                ->response()
                ->setStatusCode(201);
        } catch (Throwable $exception) {
            DB::rollBack();

            Log::error('Failed to create product from API.', [
                'payload' => $request->validated(),
                'exception' => $exception->getMessage(),
            ]);

            return response()->json([
                'message' => 'Terjadi kesalahan saat membuat produk.',
            ], 500);
        }
    }

    public function show(Product $product): JsonResponse
    {
        try {
            return (new ProductResource($product))
                ->additional([
                    'message' => 'Detail produk berhasil diambil.',
                ])
                ->response();
        } catch (Throwable $exception) {
            Log::error('Failed to fetch product detail from API.', [
                'product_id' => $product->id,
                'exception' => $exception->getMessage(),
            ]);

            return response()->json([
                'message' => 'Terjadi kesalahan saat mengambil detail produk.',
            ], 500);
        }
    }

    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        try {
            DB::beginTransaction();

            $product->update($request->validated());

            DB::commit();

            Log::info('Product updated successfully from API.', [
                'product_id' => $product->id,
                'product_name' => $product->name,
            ]);

            return (new ProductResource($product->fresh()))
                ->additional([
                    'message' => 'Produk berhasil diperbarui.',
                ])
                ->response();
        } catch (Throwable $exception) {
            DB::rollBack();

            Log::error('Failed to update product from API.', [
                'product_id' => $product->id,
                'payload' => $request->validated(),
                'exception' => $exception->getMessage(),
            ]);

            return response()->json([
                'message' => 'Terjadi kesalahan saat memperbarui produk.',
            ], 500);
        }
    }

    public function destroy(Product $product): JsonResponse
    {
        try {
            DB::beginTransaction();

            $productId = $product->id;
            $productName = $product->name;

            $product->delete();

            DB::commit();

            Log::info('Product deleted successfully from API.', [
                'product_id' => $productId,
                'product_name' => $productName,
            ]);

            return response()->json([
                'message' => 'Produk berhasil dihapus.',
            ]);
        } catch (Throwable $exception) {
            DB::rollBack();

            Log::error('Failed to delete product from API.', [
                'product_id' => $product->id,
                'exception' => $exception->getMessage(),
            ]);

            return response()->json([
                'message' => 'Terjadi kesalahan saat menghapus produk.',
            ], 500);
        }
    }
}
