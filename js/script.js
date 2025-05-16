$(document).ready(function() {
    function toggleTheme() {
        const html = document.documentElement;
        const currentTheme = html.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        
        // Update attribute and cookie
        html.setAttribute('data-theme', newTheme);
        document.cookie = `theme=${newTheme}; path=/; max-age=${30 * 24 * 60 * 60}`; // 30 days

        document.querySelector('.theme-toggle').textContent = 
            newTheme === 'dark' ? '🌞' : '🌙';
    }

    $('.theme-toggle').on('click', toggleTheme);

    let searchTimeout;
    const searchInput = $('#searchInput');
    const productGrid = $('#productGrid');
    const searchStatus = $('#searchStatus');
    const noResults = $('#noResults');
    const searchQuery = $('#searchQuery');

    function performSearch() {
        const query = searchInput.val().trim();

        if (query) {
            searchQuery.text(query);
            searchStatus.show();
        } else {
            searchStatus.hide();
        }

        $.ajax({
            url: 'search.php',
            type: 'GET',
            data: { query: query },
            dataType: 'json',
            success: function(products) {
                productGrid.empty();
                
                if (products.length > 0) {
                    productGrid.show();
                    noResults.hide();

                    $.each(products, function(index, product) {
                        const stockClass = product.stock > 0 ? 'in-stock' : 'out-stock';
                        const stockText = product.stock > 0 ? `Stok: ${product.stock}` : 'Stok Habis';
                        
                        const productCard = `
                            <article class="product-card" data-id="${product.id}">
                                <div class="card-image">
                                    <img src="uploads/${product.image || 'placeholder.jpg'}" 
                                         alt="${product.name}"
                                         loading="lazy">
                                </div>
                                <div class="card-body">
                                    <h3 class="product-title">${product.name}</h3>
                                    <p class="product-desc">${product.description}</p>
                                    <div class="product-meta">
                                        <div class="price-tag">
                                            ${product.formatted_price || 'Rp ' + new Intl.NumberFormat('id-ID').format(product.price)}
                                        </div>
                                        <div class="stock-status ${stockClass}">
                                            <i class="fas fa-cubes"></i>
                                            ${stockText}
                                        </div>
                                    </div>
                                </div>
                            </article>
                        `;
                        
                        productGrid.append(productCard);
                    });
                } else {
                    productGrid.hide();
                    noResults.show();
                }
            },
            error: function(xhr, status, error) {
                console.error('Search error:', error);
            }
        });
    }

    if(searchInput.length) {
        searchInput.on('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(performSearch, 300);
        });

        $('#clearSearch').click(function() {
            searchInput.val('');
            searchStatus.hide();
            performSearch();
        });
    }

    let adminSearchTimeout;
    const adminSearchInput = $('#adminSearchInput');
    const productListContainer = $('#productListContainer');
    const adminSearchStatus = $('#adminSearchStatus');
    const adminNoResults = $('#adminNoResults');
    const adminSearchQuery = $('#adminSearchQuery');

    function performAdminSearch() {
        const query = adminSearchInput.val().trim();

        if (query) {
            adminSearchQuery.text(query);
            adminSearchStatus.show();
        } else {
            adminSearchStatus.hide();
        }

        $.ajax({
            url: 'search.php',
            type: 'GET',
            data: { query: query },
            dataType: 'json',
            success: function(products) {
                productListContainer.empty();
                
                if (products.length > 0) {
                    productListContainer.show();
                    adminNoResults.hide();

                    $.each(products, function(index, product) {
                        const formattedPrice = 'Rp ' + new Intl.NumberFormat('id-ID').format(product.price);
                        
                        const productCard = `
                            <div class="manage-product-card" data-id="${product.id}">
                                <img src="uploads/${product.image || 'placeholder.jpg'}" class="product-thumb" alt="${product.name}">
                                <div class="product-info">
                                    <h3>${product.name}</h3>
                                    <p>${product.formatted_price || formattedPrice}</p>
                                    <small>Stok: ${product.stock}</small>
                                </div>
                                <div class="product-actions">
                                    <button class="btn-edit" onclick="openEditModal(${product.id})">✏️ Edit</button>
                                    <form method="POST">
                                        <input type="hidden" name="id" value="${product.id}">
                                        <button type="submit" name="delete" class="btn-delete" onclick="return confirm('Hapus produk ini?')">🗑️ Hapus</button>
                                    </form>
                                </div>
                            </div>
                        `;
                        
                        productListContainer.append(productCard);
                    });
                } else {
                    productListContainer.hide();
                    adminNoResults.show();
                }
            },
            error: function(xhr, status, error) {
                console.error('Search error:', error);
            }
        });
    }
    
    if(adminSearchInput.length) {
        adminSearchInput.on('input', function() {
            clearTimeout(adminSearchTimeout);
            adminSearchTimeout = setTimeout(performAdminSearch, 300);
        });

        $('#adminClearSearch').click(function() {
            adminSearchInput.val('');
            adminSearchStatus.hide();
            performAdminSearch();
        });
    }
    
    const modal = document.getElementById('editModal');
    if(modal) {
        const span = document.getElementsByClassName('close')[0];
        
        span.onclick = () => modal.style.display = 'none';
        
        window.onclick = (event) => {
            if (event.target === modal) modal.style.display = 'none';
        };
    }
});

function openEditModal(id) {
    fetch(`getProduct.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('editId').value = data.id;
            document.getElementById('editName').value = data.name;
            document.getElementById('editDescription').value = data.description;
            document.getElementById('editPrice').value = data.price;
            document.getElementById('editStock').value = data.stock;
            document.getElementById('editModal').style.display = 'block';
        })
        .catch(error => {
            console.error('Error fetching product data:', error);
            alert('Failed to load product data. Please try again.');
        });
}