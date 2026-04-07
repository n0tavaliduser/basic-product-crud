@extends('layouts.app')

@section('title', 'Products')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-semibold tracking-tight text-zinc-900 mb-2">Daftar Produk</h1>
        <x-breadcrumb :items="['Dashboard' => url('/'), 'Produk' => null]" />
    </div>

    @if (session('success'))
        <div class="bg-emerald-50 text-emerald-800 px-4 py-3 rounded-md text-sm mb-6 border border-emerald-200 shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-50 text-red-800 px-4 py-3 rounded-md text-sm mb-6 border border-red-200 shadow-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
        <form action="{{ route('products.index') }}" method="GET" class="w-full sm:max-w-xs relative">
            <div class="absolute inset-y-0 left-0 flex items-center pl-2.5 pointer-events-none text-zinc-400">
                <i class="ri-search-line text-[15px]"></i>
            </div>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama produk..." 
                   class="w-full bg-white border border-zinc-200 rounded-md py-1.5 pl-8 pr-3 text-sm focus:ring-1 focus:ring-zinc-900 focus:border-zinc-900 outline-none shadow-sm transition-colors">
        </form>
        
        <a class="inline-flex items-center bg-zinc-900 hover:bg-zinc-800 text-white px-3 py-1.5 rounded-md text-xs font-medium transition-colors shadow-sm"
           href="{{ route('products.create') }}">
           <i class="ri-add-line mr-1 text-sm"></i> Tambah Produk
        </a>
    </div>

    <div class="bg-white border border-zinc-200 rounded-md shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white border-b border-zinc-200">
                        <th class="py-4 pr-4 pl-6 font-semibold text-zinc-900 text-xs uppercase tracking-wider">Nama</th>
                        <th class="py-4 px-4 font-semibold text-zinc-900 text-xs uppercase tracking-wider">Harga</th>
                        <th class="py-4 px-4 font-semibold text-zinc-900 text-xs uppercase tracking-wider">Stok</th>
                        <th
                            class="py-4 px-4 font-semibold text-zinc-900 text-xs uppercase tracking-wider hidden md:table-cell">
                            Deskripsi</th>
                        <th class="py-4 pl-4 pr-6 font-semibold text-zinc-900 text-xs uppercase tracking-wider text-right">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100">
                    @forelse ($products as $product)
                        <tr class="hover:bg-zinc-50 transition-colors">
                            <td class="py-4 pr-4 pl-6 align-top text-zinc-900">{{ $product->name }}</td>
                            <td class="py-4 px-4 align-top whitespace-nowrap text-zinc-600">Rp
                                {{ number_format((float) $product->price, 2, ',', '.') }}</td>
                            <td class="py-4 px-4 align-top text-zinc-600">{{ $product->stock }}</td>
                            <td class="py-4 px-4 align-top text-zinc-500 hidden md:table-cell max-w-xs truncate">
                                {{ $product->description }}</td>
                            <td class="py-2 pl-4 pr-6 align-middle whitespace-nowrap text-right">
                                <div class="flex gap-1.5 justify-end items-center">
                                    <a class="w-7 h-7 flex items-center justify-center text-blue-600 bg-blue-50 hover:bg-blue-100 rounded transition-colors"
                                        href="{{ route('products.show', $product) }}" title="Detail Produk">
                                        <i class="ri-eye-line text-[15px]"></i>
                                    </a>
                                    <a class="w-7 h-7 flex items-center justify-center text-emerald-600 bg-emerald-50 hover:bg-emerald-100 rounded transition-colors"
                                        href="{{ route('products.edit', $product) }}" title="Edit Produk">
                                        <i class="ri-pencil-line text-[15px]"></i>
                                    </a>
                                    <button type="button"
                                        class="w-7 h-7 flex items-center justify-center text-red-600 bg-red-50 hover:bg-red-100 rounded transition-colors"
                                        onclick="openDeleteModal('{{ route('products.destroy', $product) }}')"
                                        title="Hapus Produk">
                                        <i class="ri-delete-bin-line text-[15px]"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-zinc-400 italic">Data produk belum tersedia.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if (method_exists($products, 'hasPages') && $products->hasPages())
            <div class="p-4 border-t border-zinc-100 bg-white">
                {{ $products->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Konfirmasi Hapus Custom -->
    <div id="deleteModal"
        class="hidden fixed inset-0 z-50 items-center justify-center bg-zinc-900/50 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-md shadow-lg max-w-sm w-full mx-4 overflow-hidden transform scale-95 transition-transform"
            id="deleteModalContent">
            <div class="p-6">
                <div class="flex items-center justify-center w-12 h-12 rounded-full bg-red-100 mb-4 mx-auto">
                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-zinc-900 text-center mb-2">Konfirmasi Hapus</h3>
                <p class="text-sm text-zinc-500 text-center">Apakah Anda yakin ingin menghapus produk ini? Data yang dihapus
                    tidak dapat dipulihkan kembali.</p>
            </div>
            <div class="bg-zinc-50 px-6 py-4 flex gap-3 justify-center border-t border-zinc-200">
                <button type="button" onclick="closeDeleteModal()"
                    class="bg-white border border-zinc-300 text-zinc-700 hover:bg-zinc-50 px-4 py-2 rounded-md text-sm font-medium transition-colors">Batal</button>
                <form id="deleteForm" method="POST" class="m-0 p-0">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors shadow-sm">Ya,
                        Hapus Data</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openDeleteModal(url) {
            document.getElementById('deleteForm').action = url;
            const modal = document.getElementById('deleteModal');
            const modalContent = document.getElementById('deleteModalContent');
            modal.classList.remove('hidden');
            modal.classList.add('flex');

            // Trigger animation effect
            setTimeout(() => {
                modalContent.classList.remove('scale-95');
                modalContent.classList.add('scale-100');
            }, 10);
        }

        function closeDeleteModal() {
            const modal = document.getElementById('deleteModal');
            const modalContent = document.getElementById('deleteModalContent');

            modalContent.classList.remove('scale-100');
            modalContent.classList.add('scale-95');

            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }, 150); // wait for scale animation
        }
    </script>
@endsection
