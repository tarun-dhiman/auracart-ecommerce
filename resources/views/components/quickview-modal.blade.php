<!-- Quick View Modal Container -->
<div id="quickview-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeQuickView()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal Dialog -->
        <div class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-slate-100">
            <div class="relative p-6 sm:p-8">
                <!-- Close Button -->
                <button type="button" onclick="closeQuickView()" class="absolute top-5 right-5 text-slate-600 hover:text-slate-600 p-2 rounded-xl hover:bg-slate-100 transition">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>

                <div id="quickview-content" class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
                    <!-- Dynamic AJAX injection here -->
                    <div class="flex items-center justify-center py-12 col-span-2 text-slate-600">
                        <i data-lucide="loader" class="w-8 h-8 animate-spin text-indigo-600"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function openQuickView(productId) {
        const modal = document.getElementById('quickview-modal');
        const content = document.getElementById('quickview-content');
        modal.classList.remove('hidden');
        content.innerHTML = `<div class="flex items-center justify-center py-16 col-span-2"><i data-lucide="loader" class="w-8 h-8 animate-spin text-indigo-600"></i></div>`;
        lucide.createIcons();

        fetch('/quick-view/' + productId)
            .then(res => res.json())
            .then(data => {
                let variantsHtml = '';
                if (data.variants && data.variants.length > 0) {
                    variantsHtml = `
                        <div class="mb-4">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Options / Variants</label>
                            <select id="qv-variant-select" class="w-full text-xs font-semibold px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:outline-none focus:border-indigo-500">
                                ${data.variants.map(v => `<option value="${v.id}">${v.variant_name} - ₹${v.price ? Number(v.price).toFixed(2) : data.effective_price.toFixed(2)}</option>`).join('')}
                            </select>
                        </div>
                    `;
                }

                content.innerHTML = `
                    <div class="aspect-square bg-slate-50 rounded-2xl overflow-hidden border border-slate-100 flex items-center justify-center">
                        <img src="${data.primary_image}" alt="${data.name}" class="w-full h-full object-cover">
                    </div>
                    <div class="flex flex-col justify-between h-full">
                        <div>
                            <span class="text-[11px] font-bold text-indigo-600 uppercase tracking-wider">${data.category}</span>
                            <h2 class="text-lg sm:text-xl font-black text-slate-900 mt-1 mb-2">${data.name}</h2>
                            <div class="flex items-baseline gap-2 mb-3">
                                <span class="text-xl font-black text-slate-900">₹${Number(data.effective_price).toFixed(2)}</span>
                                ${data.sale_price ? `<span class="text-xs text-slate-600 line-through">₹${Number(data.price).toFixed(2)}</span>` : ''}
                                ${data.discount_percentage ? `<span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-500 text-white">-${data.discount_percentage}%</span>` : ''}
                            </div>
                            <p class="text-xs text-slate-600 line-clamp-3 mb-4 leading-relaxed">${data.short_description || ''}</p>
                            ${variantsHtml}
                            <div class="flex items-center gap-3 mb-5">
                                <label class="text-xs font-bold text-slate-700">Quantity:</label>
                                <div class="flex items-center border border-slate-200 rounded-xl bg-slate-50 overflow-hidden">
                                    <button type="button" onclick="const q = document.getElementById('qv-qty'); if(q.value > 1) q.value--;" class="px-3 py-1.5 hover:bg-slate-200 text-slate-600 font-bold">-</button>
                                    <input type="number" id="qv-qty" value="1" min="1" max="${data.stock_quantity}" class="w-12 text-center text-xs font-bold bg-transparent border-0 focus:ring-0">
                                    <button type="button" onclick="const q = document.getElementById('qv-qty'); if(q.value < ${data.stock_quantity}) q.value++;" class="px-3 py-1.5 hover:bg-slate-200 text-slate-600 font-bold">+</button>
                                </div>
                                <span class="text-xs font-medium text-emerald-600 flex items-center gap-1"><i data-lucide="check" class="w-3.5 h-3.5"></i> In Stock</span>
                            </div>
                        </div>
                        <div class="space-y-2 pt-2 border-t border-slate-100">
                            <button type="button" onclick="handleQuickViewAddToCart(${data.id})" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition shadow-md shadow-indigo-100 flex items-center justify-center gap-2">
                                <i data-lucide="shopping-bag" class="w-4 h-4"></i>
                                <span>Add to Cart</span>
                            </button>
                            <a href="${data.url}" class="block text-center text-xs font-bold text-slate-600 hover:text-indigo-600 py-1.5">
                                View Full Product Specifications &rarr;
                            </a>
                        </div>
                    </div>
                `;
                lucide.createIcons();
            });
    }

    function closeQuickView() {
        document.getElementById('quickview-modal').classList.add('hidden');
    }

    function handleQuickViewAddToCart(productId) {
        const qty = parseInt(document.getElementById('qv-qty')?.value || '1');
        const variantSelect = document.getElementById('qv-variant-select');
        const variantId = variantSelect ? variantSelect.value : null;

        addToCart(productId, qty, variantId);
        closeQuickView();
    }
</script>
