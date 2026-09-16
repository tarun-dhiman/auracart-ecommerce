<script>
    function auraStore() {
        return {
            mobileMenuOpen: false,
            cartDrawerOpen: false,
            cartCount: {{ app(\App\Services\CartService::class)->getSummary()['items_count'] }},
            drawerItems: [],
            drawerSubtotal: 0,
            wishlistCount: 0,

            init() {
                lucide.createIcons();
            },

            openCartDrawer() {
                this.cartDrawerOpen = true;
                this.fetchCartDrawer();
            },

            fetchCartDrawer() {
                fetch('{{ route('cart.drawer') }}')
                    .then(res => res.json())
                    .then(data => {
                        this.drawerItems = data.items;
                        this.drawerSubtotal = data.subtotal;
                        this.cartCount = data.items_count;
                        this.$nextTick(() => lucide.createIcons());
                    });
            },

            removeCartItem(id) {
                fetch('/cart/remove/' + id, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    }
                })
                .then(res => res.json())
                .then(data => {
                    this.fetchCartDrawer();
                });
            }
        }
    }

    function searchAutocomplete() {
        return {
            query: '',
            open: false,
            results: { products: [], categories: [] },

            fetchSuggestions() {
                if (this.query.trim().length < 2) {
                    this.results = { products: [], categories: [] };
                    this.open = false;
                    return;
                }
                fetch('{{ route('search.suggestions') }}?q=' + encodeURIComponent(this.query))
                    .then(res => res.json())
                    .then(data => {
                        this.results = data;
                        this.open = (data.products && data.products.length > 0) || (data.categories && data.categories.length > 0);
                        this.$nextTick(() => lucide.createIcons());
                    });
            }
        }
    }

    function addToCart(productId, quantity = 1, variantId = null) {
        const formData = new FormData();
        formData.append('product_id', productId);
        formData.append('quantity', quantity);
        if (variantId) formData.append('variant_id', variantId);

        fetch('{{ route('cart.add') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const root = document.querySelector('[x-data]');
                if (root && root._x_dataStack) {
                    root._x_dataStack[0].cartCount = data.cart_count;
                    root._x_dataStack[0].openCartDrawer();
                }
            } else {
                alert(data.message || 'Error adding product.');
            }
        })
        .catch(err => {
            console.error(err);
            alert('Could not add to cart.');
        });
    }

    function toggleWishlist(productId) {
        fetch('/wishlist/toggle/' + productId, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            }
        })
        .then(res => {
            if (res.status === 401) {
                window.location.href = '{{ route('login') }}';
                return;
            }
            return res.json();
        })
        .then(data => {
            if (data && data.success) {
                const root = document.querySelector('[x-data]');
                if (root && root._x_dataStack) {
                    root._x_dataStack[0].wishlistCount = data.wishlist_count;
                }
                alert(data.message);
            }
        });
    }
</script>
