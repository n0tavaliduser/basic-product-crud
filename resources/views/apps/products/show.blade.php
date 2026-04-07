@extends('layouts.app')

@section('title', 'Detail Produk')

@section('content')
    <div class="flex justify-between items-end mb-6">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-zinc-900 mb-2">Detail Produk</h1>
            <x-breadcrumb :items="['Dashboard' => url('/'), 'Produk' => route('products.index'), 'Detail' => null]" />
        </div>
        <div class="flex gap-3 items-center">
            <a class="text-zinc-500 hover:text-zinc-900 text-xs font-medium transition-colors" href="{{ route('products.index') }}">Kembali</a>
            <a class="bg-zinc-900 hover:bg-zinc-800 text-white px-4 py-2 rounded-md text-xs font-medium transition-colors shadow-sm" href="{{ route('products.edit', $product) }}">Edit Produk</a>
        </div>
    </div>

    <div class="bg-white border border-zinc-200 rounded-md shadow-sm overflow-hidden text-zinc-800">
        <dl class="divide-y divide-zinc-200">
            <div class="px-6 py-5 sm:grid sm:grid-cols-3 sm:gap-4 hover:bg-zinc-50 transition-colors">
                <dt class="text-sm font-medium text-zinc-500">Nama Produk</dt>
                <dd class="mt-1 text-sm text-zinc-900 sm:col-span-2 sm:mt-0 font-medium">{{ $product->name }}</dd>
            </div>
            <div class="px-6 py-5 sm:grid sm:grid-cols-3 sm:gap-4 hover:bg-zinc-50 transition-colors">
                <dt class="text-sm font-medium text-zinc-500">Harga</dt>
                <dd class="mt-1 text-sm text-zinc-900 sm:col-span-2 sm:mt-0 font-medium">Rp {{ number_format((float) $product->price, 2, ',', '.') }}</dd>
            </div>
            <div class="px-6 py-5 sm:grid sm:grid-cols-3 sm:gap-4 hover:bg-zinc-50 transition-colors">
                <dt class="text-sm font-medium text-zinc-500">Stok</dt>
                <dd class="mt-1 text-sm text-zinc-600 sm:col-span-2 sm:mt-0">{{ $product->stock }}</dd>
            </div>
            <div class="px-6 py-5 sm:grid sm:grid-cols-3 sm:gap-4 hover:bg-zinc-50 transition-colors">
                <dt class="text-sm font-medium text-zinc-500">Deskripsi</dt>
                <dd class="mt-1 text-sm text-zinc-600 sm:col-span-2 sm:mt-0 whitespace-pre-line leading-relaxed">{{ $product->description }}</dd>
            </div>
        </dl>
    </div>
@endsection
