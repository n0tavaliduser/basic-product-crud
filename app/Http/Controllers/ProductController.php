<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProductController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        try {
            $products = Product::query()
                ->when(request('search'), function ($query) {
                    $query->where('name', 'like', '%' . request('search') . '%');
                })
                ->latest()
                ->paginate(10);

            return view('apps.products.index', compact('products'));
        } catch (Throwable $exception) {
            Log::error('Failed to fetch products.', [
                'exception' => $exception->getMessage(),
            ]);

            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan saat mengambil daftar produk.');
        }
    }

    public function create(): View
    {
        return view('apps.products.create');
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $product = Product::create($request->validated());

            DB::commit();

            Log::info('Product created successfully.', [
                'product_id' => $product->id,
                'product_name' => $product->name,
            ]);

            return redirect()
                ->route('products.index')
                ->with('success', 'Produk berhasil dibuat.');
        } catch (Throwable $exception) {
            DB::rollBack();

            Log::error('Failed to create product.', [
                'payload' => $request->validated(),
                'exception' => $exception->getMessage(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat membuat produk.');
        }
    }

    public function show(Product $product): View|RedirectResponse
    {
        try {
            return view('apps.products.show', compact('product'));
        } catch (Throwable $exception) {
            Log::error('Failed to fetch product detail.', [
                'product_id' => $product->id,
                'exception' => $exception->getMessage(),
            ]);

            return redirect()
                ->route('products.index')
                ->with('error', 'Terjadi kesalahan saat mengambil detail produk.');
        }
    }

    public function edit(Product $product): View
    {
        return view('apps.products.edit', compact('product'));
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $product->update($request->validated());

            DB::commit();

            Log::info('Product updated successfully.', [
                'product_id' => $product->id,
                'product_name' => $product->name,
            ]);

            return redirect()
                ->route('products.index')
                ->with('success', 'Produk berhasil diperbarui.');
        } catch (Throwable $exception) {
            DB::rollBack();

            Log::error('Failed to update product.', [
                'product_id' => $product->id,
                'payload' => $request->validated(),
                'exception' => $exception->getMessage(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui produk.');
        }
    }

    public function destroy(Product $product): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $productId = $product->id;
            $productName = $product->name;

            $product->delete();

            DB::commit();

            Log::info('Product deleted successfully.', [
                'product_id' => $productId,
                'product_name' => $productName,
            ]);

            return redirect()
                ->route('products.index')
                ->with('success', 'Produk berhasil dihapus.');
        } catch (Throwable $exception) {
            DB::rollBack();

            Log::error('Failed to delete product.', [
                'product_id' => $product->id,
                'exception' => $exception->getMessage(),
            ]);

            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan saat menghapus produk.');
        }
    }
}
