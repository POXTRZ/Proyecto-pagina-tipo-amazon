document.addEventListener('DOMContentLoaded', function() {
    const priceFilter = document.getElementById('price-filter');
    const priceFilterValue = document.getElementById('price-filter-value');
    const stockFilter = document.getElementById('stock-filter');
    const sortBySelect = document.getElementById('sort-by');
    const productsGrid = document.querySelector('.category-products-section .products-grid');
    const initialNoProductsMessage = document.querySelector('.category-products-section .no-products-message');
    let allProductCards = [];

    if (productsGrid) { // Solo ejecutar si hay una parrilla de productos
        allProductCards = Array.from(productsGrid.querySelectorAll('.product-card'));
    } else if (!initialNoProductsMessage) {
        // Si no hay parrilla ni mensaje inicial, no hay nada que filtrar/ordenar
        return;
    }


    function updateProductsDisplay() {
        if (allProductCards.length === 0 && initialNoProductsMessage) {
            // Si no había productos desde el servidor, el mensaje ya está visible
            return;
        }
        if (!productsGrid) return; // No hay dónde mostrar los productos

        let filteredProducts = [...allProductCards];

        // Price filter
        if (priceFilter) {
            const maxPrice = parseFloat(priceFilter.value);
            filteredProducts = filteredProducts.filter(card => {
                const price = parseFloat(card.dataset.price);
                return price <= maxPrice;
            });
        }

        // Stock filter
        if (stockFilter && stockFilter.checked) {
            filteredProducts = filteredProducts.filter(card => {
                const stock = parseInt(card.dataset.stock);
                return stock > 0;
            });
        }

        // Sorting
        if (sortBySelect) {
            const sortValue = sortBySelect.value;
            filteredProducts.sort((a, b) => {
                const titleA = a.querySelector('.product-title').textContent.toLowerCase();
                const titleB = b.querySelector('.product-title').textContent.toLowerCase();
                const priceA = parseFloat(a.dataset.price);
                const priceB = parseFloat(b.dataset.price);

                switch (sortValue) {
                    case 'name_asc': return titleA.localeCompare(titleB);
                    case 'name_desc': return titleB.localeCompare(titleA);
                    case 'price_asc': return priceA - priceB;
                    case 'price_desc': return priceB - priceA;
                    default: return 0;
                }
            });
        }

        // Update DOM
        productsGrid.innerHTML = ''; // Clear current products
        let dynamicNoProductsMessage = document.querySelector('.dynamic-no-products');

        if (filteredProducts.length > 0) {
            filteredProducts.forEach(card => productsGrid.appendChild(card));
            if (initialNoProductsMessage) initialNoProductsMessage.style.display = 'none';
            if (dynamicNoProductsMessage) dynamicNoProductsMessage.remove();
        } else {
            if (initialNoProductsMessage && allProductCards.length === 0) {
                // Si el mensaje inicial era porque no había productos del servidor, mantenerlo
                initialNoProductsMessage.style.display = 'block';
            } else if (!dynamicNoProductsMessage) {
                // Crear mensaje dinámico si no hay productos por los filtros
                dynamicNoProductsMessage = document.createElement('div');
                dynamicNoProductsMessage.className = 'no-products-message dynamic-no-products'; // Reutilizar clase
                dynamicNoProductsMessage.innerHTML = `
                    <i class="fas fa-search"></i>
                    <p>No se encontraron productos que coincidan con tus filtros.</p>
                    <button id="reset-filters-btn" class="cta-button">Restablecer Filtros</button>
                `;
                productsGrid.parentNode.insertBefore(dynamicNoProductsMessage, productsGrid.nextSibling);
                document.getElementById('reset-filters-btn')?.addEventListener('click', resetFilters);
            }
            if (initialNoProductsMessage && allProductCards.length > 0) initialNoProductsMessage.style.display = 'none'; // Ocultar el original si había productos inicialmente
        }
    }

    function resetFilters() {
        if (priceFilter) {
            priceFilter.value = priceFilter.max;
            if(priceFilterValue) priceFilterValue.textContent = `$${priceFilter.max}`;
        }
        if (stockFilter) stockFilter.checked = true;
        if (sortBySelect) sortBySelect.value = 'name_asc';
        
        updateProductsDisplay();
        // El mensaje dinámico se elimina dentro de updateProductsDisplay si hay resultados
    }

    if (priceFilter && priceFilterValue) {
        priceFilterValue.textContent = `$${priceFilter.value}`; // Set initial value
        priceFilter.addEventListener('input', () => {
            priceFilterValue.textContent = `$${priceFilter.value}`;
            updateProductsDisplay();
        });
    }
    if (stockFilter) stockFilter.addEventListener('change', updateProductsDisplay);
    if (sortBySelect) sortBySelect.addEventListener('change', updateProductsDisplay);

    // Initial display update
    if (productsGrid || initialNoProductsMessage) {
         updateProductsDisplay();
    }
});