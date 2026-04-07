@extends('layouts.app')

@section('title', 'Tambah Produk')

@section('content')
    <div class="flex justify-between items-end mb-6">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-zinc-900 mb-2">Tambah Produk</h1>
            <x-breadcrumb :items="['Dashboard' => url('/'), 'Produk' => route('products.index'), 'Tambah' => null]" />
        </div>
        <a class="text-zinc-500 hover:text-zinc-900 text-sm font-medium transition-colors" href="{{ route('products.index') }}">Kembali</a>
    </div>

    @if ($errors->any())
        <div class="bg-red-50 text-red-800 px-3 py-2 rounded-md text-sm mb-4 border border-red-200 shadow-sm">
            <ul class="list-disc pl-5 m-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white border border-zinc-200 rounded-md shadow-sm">
        <form action="{{ route('products.store') }}" method="POST" class="p-4 sm:p-5">
            @csrf

            <div class="mb-4">
                <label for="name" class="block font-medium text-zinc-900 text-sm mb-1.5">Nama Produk</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" required
                       class="w-full bg-zinc-50 border border-zinc-200 rounded-md px-3 py-1.5 text-sm focus:bg-white focus:ring-1 focus:ring-zinc-900 focus:border-zinc-900 outline-none transition-colors">
            </div>

            <div class="mb-4">
                <label for="price" class="block font-medium text-zinc-900 text-sm mb-1.5">Harga</label>
                <input id="price" name="price" type="number" step="0.01" min="0" value="{{ old('price') }}" required
                       class="w-full bg-zinc-50 border border-zinc-200 rounded-md px-3 py-1.5 text-sm focus:bg-white focus:ring-1 focus:ring-zinc-900 focus:border-zinc-900 outline-none transition-colors">
            </div>

            <div class="mb-4">
                <label for="stock" class="block font-medium text-zinc-900 text-sm mb-1.5">Stok</label>
                <input id="stock" name="stock" type="number" min="0" value="{{ old('stock') }}" required
                       class="w-full bg-zinc-50 border border-zinc-200 rounded-md px-3 py-1.5 text-sm focus:bg-white focus:ring-1 focus:ring-zinc-900 focus:border-zinc-900 outline-none transition-colors">
            </div>

            <div class="mb-5">
                <label for="description" class="block font-medium text-zinc-900 text-sm mb-1.5">Deskripsi</label>
                <textarea id="description" name="description" rows="3" required
                          class="w-full bg-zinc-50 border border-zinc-200 rounded-md px-3 py-1.5 text-sm focus:bg-white focus:ring-1 focus:ring-zinc-900 focus:border-zinc-900 outline-none transition-colors resize-none">{{ old('description') }}</textarea>
            </div>

            <div class="flex gap-3 items-center border-t border-zinc-100 pt-3">
                <button type="submit" class="bg-zinc-900 hover:bg-zinc-800 text-white px-4 py-1.5 rounded-md text-xs font-medium transition-colors shadow-sm">Simpan Data</button>
            </div>
        </form>
    </div>
@endsection
