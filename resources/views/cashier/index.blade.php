@extends('layouts.main')

@section('title','Kasir')
@section('header', 'Sistem Kasir')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Cashier Area -->
    <div class="lg:col-span-2">
        <div class="card-elevated">
            <h3 class="text-xl font-bold mb-6">🛒 Input Barang</h3>
            
            <!-- Search Input -->
            <div class="flex gap-3 mb-6">
                <div class="relative flex-1">
                    <input 
                        id="q" 
                        placeholder="🔍 Scan barcode atau masukkan kode barang..." 
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-accent-green focus:outline-none transition-colors"
                        autocomplete="off"
                        autofocus
                    />
                    <div id="suggestions" class="absolute left-0 right-0 mt-1 bg-white border border-gray-200 rounded shadow z-50 hidden max-h-60 overflow-auto"></div>
                </div>
                <button id="add" class="btn btn-primary px-6 no-underline">Tambah</button>
            </div>

            <!-- Items Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-sm table-striped">
                    <thead class="bg-gray-50 border-b-2 border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left font-bold text-gray-700">Nama Barang</th>
                            <th class="px-4 py-3 text-center font-bold text-gray-700">Qty</th>
                            <th class="px-4 py-3 text-right font-bold text-gray-700">Harga</th>
                            <th class="px-4 py-3 text-right font-bold text-gray-700">Total</th>
                            <th class="px-4 py-3 text-center font-bold text-gray-700">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="items"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Sidebar - Summary -->
    <div class="lg:col-span-1">
        <div class="card-elevated sticky top-24">
            <h3 class="text-lg font-bold mb-4">💳 Ringkasan Pembayaran</h3>
            
            <div class="space-y-4 mb-6 pb-6 border-b-2 border-gray-200">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Total Item</span>
                    <span id="item-count" class="text-2xl font-bold text-gray-900">0</span>
                </div>
                <div class="bg-accent-green/10 p-4 rounded-lg">
                    <p class="text-sm text-gray-600 mb-1">Total Pembayaran</p>
                    <p class="text-3xl font-bold text-accent-green" id="total">Rp 0</p>
                </div>
            </div>

            <!-- Payment Form -->
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Uang Pembayaran</label>
                    <input 
                        id="paid" 
                        type="number" 
                        placeholder="Masukkan jumlah uang..." 
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-accent-green focus:outline-none transition-colors"
                    />
                </div>
                
                <div id="change-info" class="hidden p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <p class="text-sm text-gray-600 mb-1">Kembalian</p>
                    <p class="text-2xl font-bold text-blue-600" id="change">Rp 0</p>
                </div>

                <button id="checkout" class="w-full btn btn-primary text-lg font-bold py-3 no-underline transition-transform hover:scale-105" disabled>
                    ✓ Selesai Transaksi
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
    // Format currency IDR
    function formatRp(num) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(num);
    }

    let items = [];
    
    // fetch products (returns array)
    async function find(q) {
        try {
            const res = await fetch(`{{ route('cashier.find') }}?q=${encodeURIComponent(q)}`);
            if (!res.ok) return [];
            return await res.json();
        } catch (err) {
            console.error(err);
            return [];
        }
    }

    // debounce helper
    function debounce(fn, wait = 250) {
        let t;
        return (...args) => {
            clearTimeout(t);
            t = setTimeout(() => fn(...args), wait);
        };
    }

    let selectedSuggestion = null;

    function renderSuggestions(list) {
        const box = document.getElementById('suggestions');
        if (!list || list.length === 0) {
            box.classList.add('hidden');
            box.innerHTML = '';
            return;
        }

        box.innerHTML = list.map(p => `
            <div data-id="${p.id}" data-name="${p.name}" data-price="${p.sell_price}" class="px-4 py-3 hover:bg-gray-100 cursor-pointer flex items-center justify-between">
                <div class="truncate"><strong>${p.code || p.barcode}</strong> — ${p.name}</div>
                <div class="text-sm text-gray-600">${new Intl.NumberFormat('id-ID', {style:'currency',currency:'IDR',minimumFractionDigits:0}).format(p.sell_price)}</div>
            </div>
        `).join('');

        box.classList.remove('hidden');

        // attach click listeners
        box.querySelectorAll('div[data-id]').forEach(el => {
            el.addEventListener('click', () => {
                selectedSuggestion = {
                    product_id: el.getAttribute('data-id'),
                    name: el.getAttribute('data-name'),
                    price: parseFloat(el.getAttribute('data-price'))
                };
                // add to cart immediately
                addSelectedSuggestion();
                box.classList.add('hidden');
            });
        });
    }

    async function handleSearchInput(q) {
        if (!q || q.trim() === '') {
            renderSuggestions([]);
            return;
        }

        const results = await find(q.trim());
        renderSuggestions(results || []);
    }

    const debouncedSearch = debounce(handleSearchInput, 250);

    document.getElementById('q').addEventListener('input', (e) => {
        selectedSuggestion = null;
        debouncedSearch(e.target.value);
    });

    document.getElementById('add').addEventListener('click', async () => {
        // if a suggestion was selected via click, it will be added
        if (selectedSuggestion) {
            addSelectedSuggestion();
            selectedSuggestion = null;
            return;
        }

        const q = document.getElementById('q').value.trim();
        if (!q) {
            alert('⚠️ Masukkan kode atau nama barang');
            return;
        }

        const results = await find(q);
        if (!results || results.length === 0) {
            alert('❌ Produk tidak ditemukan. Coba kode lain.');
            return;
        }

        // choose first result if multiple
        const p = results[0];
        const existing = items.find(i => i.product_id == p.id);
        if (existing) {
            existing.quantity++;
        } else {
            items.push({
                product_id: p.id,
                name: p.name,
                quantity: 1,
                price: parseFloat(p.sell_price)
            });
        }

        document.getElementById('q').value = '';
        document.getElementById('q').focus();
        renderItems();
        renderSuggestions([]);
    });

    function addSelectedSuggestion() {
        if (!selectedSuggestion) return;
        const p = selectedSuggestion;
        const existing = items.find(i => i.product_id == p.product_id);
        if (existing) {
            existing.quantity++;
        } else {
            items.push({
                product_id: p.product_id,
                name: p.name,
                quantity: 1,
                price: p.price
            });
        }
        document.getElementById('q').value = '';
        document.getElementById('q').focus();
        renderItems();
    }

    // Enter key to add item
    document.getElementById('q').addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('add').click();
        }
        if (e.key === 'Escape') {
            renderSuggestions([]);
        }
    });

    function renderItems() {
        const tbody = document.getElementById('items');
        tbody.innerHTML = '';
        let total = 0;
        let itemCount = items.length;
        
        items.forEach((it, idx) => {
            const itemTotal = it.quantity * it.price;
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="px-4 py-3">${it.name}</td>
                <td class="px-4 py-3 text-center">
                    <input type="number" min="1" value="${it.quantity}" 
                        onchange="updateQty(${idx}, this.value)" 
                        class="w-16 px-2 py-1 border border-gray-300 rounded text-center"
                    />
                </td>
                <td class="px-4 py-3 text-right">${formatRp(it.price)}</td>
                <td class="px-4 py-3 text-right font-semibold">${formatRp(itemTotal)}</td>
                <td class="px-4 py-3 text-center">
                    <button onclick="removeItem(${idx})" class="text-red-600 font-medium hover:underline">
                        🗑️ Hapus
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
            total += itemTotal;
        });
        
        document.getElementById('total').innerText = formatRp(total);
        document.getElementById('item-count').innerText = itemCount;
        document.getElementById('checkout').disabled = itemCount === 0;
        
        // Handle payment calculation
        const paidInput = document.getElementById('paid');
        if (paidInput.value) {
            updatePayment();
        }
    }

    window.updateQty = function(idx, val) {
        const qty = parseInt(val) || 1;
        if (qty <= 0) {
            removeItem(idx);
        } else {
            items[idx].quantity = qty;
            renderItems();
        }
    };

    window.removeItem = function(idx) {
        items.splice(idx, 1);
        renderItems();
    };

    function updatePayment() {
        const total = items.reduce((sum, it) => sum + (it.quantity * it.price), 0);
        const paid = parseFloat(document.getElementById('paid').value) || 0;
        const changeInfo = document.getElementById('change-info');
        
        if (paid >= total && total > 0) {
            const change = paid - total;
            document.getElementById('change').innerText = formatRp(change);
            changeInfo.classList.remove('hidden');
            document.getElementById('checkout').disabled = false;
        } else {
            changeInfo.classList.add('hidden');
            document.getElementById('checkout').disabled = true;
        }
    }

    document.getElementById('paid').addEventListener('input', updatePayment);

    document.getElementById('checkout').addEventListener('click', async () => {
        const total = items.reduce((sum, it) => sum + (it.quantity * it.price), 0);
        const paid = parseFloat(document.getElementById('paid').value) || 0;
        
        if (items.length === 0) {
            alert('❌ Keranjang kosong');
            return;
        }
        
        if (paid < total) {
            alert('❌ Uang tidak cukup');
            return;
        }

        try {
            const res = await fetch(`{{ route('cashier.store') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    items: items,
                    paid_amount: paid
                })
            });

            if (!res.ok) {
                alert('❌ Gagal menyimpan transaksi');
                return;
            }

            const data = await res.json();
            const change = (paid - total).toFixed(0);
            
            alert(`✅ Transaksi berhasil!\n\nTotal: ${formatRp(total)}\nUang Pembayaran: ${formatRp(paid)}\nKembalian: ${formatRp(change)}`);
            
            // Reset form
            items = [];
            renderItems();
            document.getElementById('paid').value = '';
            document.getElementById('q').focus();
            
        } catch (err) {
            console.error(err);
            alert('❌ Terjadi kesalahan. Silakan coba lagi.');
        }
    });
    </script>
@endpush
@endsection