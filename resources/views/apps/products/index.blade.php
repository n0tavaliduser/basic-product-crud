@extends('layouts.app')

@section('title', 'Products')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-semibold tracking-tight text-zinc-900 mb-2">Daftar Produk</h1>
        <x-breadcrumb :items="['Dashboard' => url('/'), 'Produk' => null]" />
    </div>

    <!-- Toolbar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
        <div class="w-full sm:max-w-xs relative">
            <div class="absolute inset-y-0 left-0 flex items-center pl-2.5 pointer-events-none text-zinc-400">
                <i class="ri-search-line text-[15px]"></i>
            </div>
            <input type="text" id="searchInput" placeholder="Cari nama produk..." autocomplete="off"
                   class="w-full bg-white border border-zinc-200 rounded-md py-1.5 pl-8 pr-3 text-sm focus:ring-1 focus:ring-zinc-900 focus:border-zinc-900 outline-none shadow-sm transition-colors">
        </div>
        
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <button type="button" onclick="window.confirmResetDatabase()"
               class="flex-1 sm:flex-none inline-flex items-center justify-center bg-white border border-rose-200 hover:bg-rose-50 text-rose-600 px-3 py-1.5 rounded-md text-xs font-medium transition-colors shadow-sm"
               title="Jalankan worker reset database">
               <i class="ri-loop-right-line mr-1 text-sm"></i> Reset Database
            </button>
            <a class="flex-1 sm:flex-none inline-flex items-center justify-center bg-zinc-900 hover:bg-zinc-800 text-white px-3 py-1.5 rounded-md text-xs font-medium transition-colors shadow-sm"
               href="{{ route('products.create') }}">
               <i class="ri-add-line mr-1 text-sm"></i> Tambah Produk
            </a>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white border border-zinc-200 rounded-md shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white border-b border-zinc-200">
                        <th class="py-4 pr-4 pl-6 font-semibold text-zinc-900 text-xs uppercase tracking-wider">Nama</th>
                        <th class="py-4 px-4 font-semibold text-zinc-900 text-xs uppercase tracking-wider">Harga</th>
                        <th class="py-4 px-4 font-semibold text-zinc-900 text-xs uppercase tracking-wider">Stok</th>
                        <th class="py-4 px-4 font-semibold text-zinc-900 text-xs uppercase tracking-wider hidden md:table-cell">Deskripsi</th>
                        <th class="py-4 pl-4 pr-6 font-semibold text-zinc-900 text-xs uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableBody" class="divide-y divide-zinc-100">
                    <tr><td colspan="5" class="py-10 text-center text-zinc-500 text-sm"><i class="ri-loader-4-line animate-spin text-xl align-middle mr-2"></i>Memuat data...</td></tr>
                </tbody>
            </table>
        </div>
        <div id="paginationContainer" class="p-4 border-t border-zinc-100 bg-white hidden">
             <!-- Pagination rendered via JS -->
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus Custom -->
    <div id="deleteModal" class="hidden fixed inset-0 z-50 items-center justify-center bg-zinc-900/50 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-md shadow-lg max-w-sm w-full mx-4 overflow-hidden transform scale-95 transition-transform" id="deleteModalContent">
            <div class="p-6">
                <div class="flex items-center justify-center w-12 h-12 rounded-full bg-red-100 mb-4 mx-auto">
                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-zinc-900 text-center mb-2">Konfirmasi Hapus</h3>
                <p class="text-sm text-zinc-500 text-center">Apakah Anda yakin ingin menghapus produk ini? Data yang dihapus tidak dapat dipulihkan kembali.</p>
            </div>
            <div class="bg-zinc-50 px-6 py-4 flex gap-3 justify-center border-t border-zinc-200">
                <button type="button" onclick="window.closeDeleteModal()" class="bg-white border border-zinc-300 text-zinc-700 hover:bg-zinc-50 px-4 py-2 rounded-md text-sm font-medium transition-colors">Batal</button>
                <form id="deleteForm" method="POST" class="m-0 p-0" onsubmit="window.handleDelete(event)">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors shadow-sm">Ya, Hapus Data</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Notification Toast -->
    <div id="toastNotification" class="fixed bottom-4 right-4 z-50 transform transition-all duration-300 translate-y-full opacity-0 pointer-events-none">
        <div class="bg-zinc-900 text-white px-4 py-3 rounded-md text-sm shadow-lg flex items-center gap-3">
            <i id="toastIcon" class="ri-checkbox-circle-fill text-emerald-400 text-lg"></i>
            <span id="toastMessage"></span>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const tableBody = document.getElementById('tableBody');
            const paginationContainer = document.getElementById('paginationContainer');
            
            let debounceTimer;
            const routeShow = "{{ route('products.show', ':id') }}";
            const routeEdit = "{{ route('products.edit', ':id') }}";
            const routeApiIndex = "/api/products";
            let currentFetchUrl = routeApiIndex;

            // Format Currency IDR
            const formatCurrency = (amount) => {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 2
                }).format(amount);
            };

            // Custom Toast
            const showToast = (message, isError = false) => {
                const toast = document.getElementById('toastNotification');
                const toastMsg = document.getElementById('toastMessage');
                const toastIcon = document.getElementById('toastIcon');
                
                toastMsg.innerText = message;
                
                if (isError) {
                    toastIcon.className = "ri-error-warning-fill text-red-500 text-lg";
                } else {
                    toastIcon.className = "ri-checkbox-circle-fill text-emerald-400 text-lg";
                }

                toast.classList.remove('translate-y-full', 'opacity-0');
                
                setTimeout(() => {
                    toast.classList.add('translate-y-full', 'opacity-0');
                }, 3000);
            };

            // Fetch Products via API Hook
            const fetchProducts = async (url = routeApiIndex) => {
                const search = searchInput.value;
                const finalUrl = new URL(url, window.location.origin);
                
                // Keep search parameter
                if (search) finalUrl.searchParams.set('search', search);

                currentFetchUrl = finalUrl.toString();

                try {
                    tableBody.innerHTML = '<tr><td colspan="5" class="py-10 text-center text-zinc-500 text-sm"><i class="ri-loader-4-line animate-spin text-xl align-middle mr-2"></i>Memuat data...</td></tr>';
                    
                    const response = await fetch(finalUrl, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    
                    const data = await response.json();
                    
                    renderTable(data.data);
                    renderPagination(data.meta || {});
                } catch (error) {
                    console.error(error);
                    tableBody.innerHTML = '<tr><td colspan="5" class="py-10 text-center text-red-500 text-sm">Terjadi kesalahan saat memuat data.</td></tr>';
                }
            };

            // Render Table Rows
            const renderTable = (products) => {
                if (!products || !products.length) {
                    tableBody.innerHTML = '<tr><td colspan="5" class="py-10 text-center text-zinc-400 italic">Data produk belum tersedia.</td></tr>';
                    return;
                }

                tableBody.innerHTML = products.map(product => {
                    const urlShow = routeShow.replace(':id', product.id);
                    const urlEdit = routeEdit.replace(':id', product.id);

                    const desc = product.description || '';
                    const stock = product.stock || 0;
                    const price = product.price || 0;
                    
                    return `
                        <tr class="hover:bg-zinc-50 transition-colors">
                            <td class="py-4 pr-4 pl-6 align-middle text-zinc-900">
                                <div class="group relative max-w-[150px] sm:max-w-[200px] md:max-w-[250px]">
                                    <span class="block truncate cursor-default">${product.name}</span>
                                    <div class="pointer-events-none absolute bottom-full left-0 mb-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200 z-50 w-max max-w-xs bg-zinc-900 text-white text-xs rounded px-2.5 py-1.5 shadow-lg whitespace-normal leading-relaxed">
                                        ${product.name}
                                        <div class="absolute -bottom-1 left-3 w-2.5 h-2.5 bg-zinc-900 rotate-45 transform"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-4 align-middle whitespace-nowrap text-zinc-600">${formatCurrency(price)}</td>
                            <td class="py-4 px-4 align-middle text-zinc-600">${stock}</td>
                            <td class="py-4 px-4 align-middle text-zinc-500 hidden md:table-cell max-w-xs truncate">${desc}</td>
                            <td class="py-4 pl-4 pr-6 align-middle whitespace-nowrap text-right">
                                <div class="flex gap-1.5 justify-end items-center">
                                    <a class="w-7 h-7 flex items-center justify-center text-blue-600 bg-blue-50 hover:bg-blue-100 rounded transition-colors" href="${urlShow}" title="Detail Produk"><i class="ri-eye-line text-[15px]"></i></a>
                                    <a class="w-7 h-7 flex items-center justify-center text-emerald-600 bg-emerald-50 hover:bg-emerald-100 rounded transition-colors" href="${urlEdit}" title="Edit Produk"><i class="ri-pencil-line text-[15px]"></i></a>
                                    <button type="button" class="w-7 h-7 flex items-center justify-center text-red-600 bg-red-50 hover:bg-red-100 rounded transition-colors" onclick="window.openDeleteModal(${product.id})" title="Hapus Produk"><i class="ri-delete-bin-line text-[15px]"></i></button>
                                </div>
                            </td>
                        </tr>
                    `;
                }).join('');
            };

            // Render Pagination (matching completely with Laravel Tailwind setup)
            const renderPagination = (meta) => {
                if (!meta || meta.last_page <= 1) {
                    paginationContainer.classList.add('hidden');
                    return;
                }
                
                paginationContainer.classList.remove('hidden');
                
                let linksHtml = '';
                
                if (meta.links && Array.isArray(meta.links)) {
                    meta.links.forEach(link => {
                        let activeClass = link.active 
                            ? 'text-white bg-zinc-900 border-zinc-900 cursor-default' 
                            : 'text-zinc-600 bg-white hover:text-zinc-900 hover:bg-zinc-50 active:bg-zinc-100 cursor-pointer';
                        
                        if (!link.url) {
                            activeClass = 'text-zinc-400 bg-white cursor-not-allowed';
                        }

                        let label = link.label;
                        if (label.includes('Previous')) label = '&laquo;';
                        if (label.includes('Next')) label = '&raquo;';

                        linksHtml += `
                            <button type="button" ${link.url ? `onclick="window.fetchPage('${link.url}')"` : 'disabled'}
                               class="inline-flex items-center px-4 py-2 -ml-px text-sm font-medium border border-zinc-200 leading-5 transition ease-in-out duration-150 ${activeClass}">
                               ${label}
                            </button>
                        `;
                    });
                }

                paginationContainer.innerHTML = `
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="text-sm text-zinc-600">
                            Menampilkan <span class="font-medium text-zinc-900">${meta.from || 0}</span> sampai <span class="font-medium text-zinc-900">${meta.to || 0}</span> dari <span class="font-medium text-zinc-900">${meta.total}</span> hasil
                        </div>
                        <div class="inline-flex shadow-sm rounded-md overflow-hidden">
                            ${linksHtml}
                        </div>
                    </div>
                `;
            };

            // Global Methods attached to Window
            window.fetchPage = (url) => {
                if(url) fetchProducts(url);
            };

            // Global Delete Handlers
            window.productIdToDelete = null;

            window.openDeleteModal = (id) => {
                window.productIdToDelete = id;
                const modal = document.getElementById('deleteModal');
                const modalContent = document.getElementById('deleteModalContent');
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                
                setTimeout(() => {
                    modalContent.classList.remove('scale-95');
                    modalContent.classList.add('scale-100');
                }, 10);
            };

            window.closeDeleteModal = () => {
                window.productIdToDelete = null;
                const modal = document.getElementById('deleteModal');
                const modalContent = document.getElementById('deleteModalContent');
                modalContent.classList.remove('scale-100');
                modalContent.classList.add('scale-95');
                
                setTimeout(() => {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }, 150);
            };

            window.handleDelete = async (e) => {
                e.preventDefault();
                if(!window.productIdToDelete) return;
                
                const csrfToken = document.querySelector('input[name="_token"]').value;
                const deleteBtn = e.target.querySelector('button[type="submit"]');
                const oldContent = deleteBtn.innerHTML;
                
                deleteBtn.disabled = true;
                deleteBtn.innerHTML = '<i class="ri-loader-4-line animate-spin"></i> Menghapus...';

                try {
                    const response = await fetch(`/api/products/${window.productIdToDelete}`, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    });

                    const data = await response.json();

                    if(response.ok) {
                        showToast(data.message || 'Produk berhasil dihapus');
                        window.closeDeleteModal();
                        fetchProducts(currentFetchUrl); // Refresh data table only
                    } else {
                        showToast(data.message || 'Gagal menghapus produk', true);
                        window.closeDeleteModal();
                    }
                } catch(error) {
                    showToast('Terjadi kesalahan koneksi', true);
                    window.closeDeleteModal();
                } finally {
                    deleteBtn.disabled = false;
                    deleteBtn.innerHTML = oldContent;
                }
            };

            // Search with Debounce
            searchInput.addEventListener('input', () => {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    fetchProducts(routeApiIndex);
                }, 400); 
            });

            // Database Reset Handler
            window.confirmResetDatabase = () => {
                const token = prompt("Peringatan: Aksi ini akan me-reset seluruh database di background.\nMasukkan token rahasia keamanan untuk melanjutkan:");
                if (!token) return;

                showToast("Mengirim perintah reset...");

                fetch('/api/database/reset', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Reset-Token': token
                    }
                })
                .then(async response => {
                    const data = await response.json().catch(() => ({}));
                    if(response.ok) {
                        showToast(data.message || "Perintah reset berhasil dijalankan di background.");
                        // Optional: table will refresh after X seconds if needed manually
                    } else {
                        showToast(data.message || "Gagal: Token salah atau proses ditolak.", true);
                    }
                })
                .catch(error => {
                    showToast("Kesalahan sistem saat menghubungi server.", true);
                });
            };

            // Init call
            fetchProducts();
        });
    </script>
@endsection
