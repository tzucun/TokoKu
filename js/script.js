$(document).ready(function() {
    // Theme Toggle Functionality
    function toggleTheme() {
        const html = document.documentElement;
        const currentTheme = html.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        
        // Update attribute and cookie
        html.setAttribute('data-theme', newTheme);
        document.cookie = `theme=${newTheme}; path=/; max-age=${30 * 24 * 60 * 60}`; // 30 days
        
        // Update button
        document.querySelector('.theme-toggle').textContent = 
            newTheme === 'dark' ? '🌞' : '🌙';
    }
    
    // Attach event listener to theme toggle button
    $('.theme-toggle').on('click', toggleTheme);
    
    // Product Search Functionality
    let searchTimeout;
    const searchInput = $('#searchInput');
    const productGrid = $('#productGrid');
    const searchStatus = $('#searchStatus');
    const noResults = $('#noResults');
    const searchQuery = $('#searchQuery');
    
    // Function to perform search
    function performSearch() {
        const query = searchInput.val().trim();
        
        // Show/hide search status
        if (query) {
            searchQuery.text(query);
            searchStatus.show();
        } else {
            searchStatus.hide();
        }
        
        // Make AJAX request
        $.ajax({
            url: 'search.php',
            type: 'GET',
            data: { query: query },
            dataType: 'json',
            success: function(products) {
                // Clear existing products
                productGrid.empty();
                
                if (products.length > 0) {
                    // Show products and hide no results message
                    productGrid.show();
                    noResults.hide();
                    
                    // Add each product to the grid
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
                    // Show no results message
                    productGrid.hide();
                    noResults.show();
                }
            },
            error: function(xhr, status, error) {
                console.error('Search error:', error);
            }
        });
    }
    
    // Search input event with debounce
    if(searchInput.length) {
        searchInput.on('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(performSearch, 300);
        });
        
        // Clear search button
        $('#clearSearch').click(function() {
            searchInput.val('');
            searchStatus.hide();
            performSearch();
        });
    }
    
    // Admin Search Functionality
    let adminSearchTimeout;
    const adminSearchInput = $('#adminSearchInput');
    const productListContainer = $('#productListContainer');
    const adminSearchStatus = $('#adminSearchStatus');
    const adminNoResults = $('#adminNoResults');
    const adminSearchQuery = $('#adminSearchQuery');
    
    // Function to perform admin search
    function performAdminSearch() {
        const query = adminSearchInput.val().trim();
        
        // Show/hide search status
        if (query) {
            adminSearchQuery.text(query);
            adminSearchStatus.show();
        } else {
            adminSearchStatus.hide();
        }
        
        // Make AJAX request
        $.ajax({
            url: 'search.php',
            type: 'GET',
            data: { query: query },
            dataType: 'json',
            success: function(products) {
                // Clear existing products
                productListContainer.empty();
                
                if (products.length > 0) {
                    productListContainer.show();
                    adminNoResults.hide();
                    
                    // Add each product to the list
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
    
    // Admin search input event with debounce
    if(adminSearchInput.length) {
        adminSearchInput.on('input', function() {
            clearTimeout(adminSearchTimeout);
            adminSearchTimeout = setTimeout(performAdminSearch, 300);
        });
        
        // Clear admin search button
        $('#adminClearSearch').click(function() {
            adminSearchInput.val('');
            adminSearchStatus.hide();
            performAdminSearch();
        });
    }
    
    // Modal Functionality
    const modal = document.getElementById('editModal');
    if(modal) {
        const span = document.getElementsByClassName('close')[0];
        
        // Close modal when clicking X
        span.onclick = () => modal.style.display = 'none';
        
        // Close modal when clicking outside
        window.onclick = (event) => {
            if (event.target === modal) modal.style.display = 'none';
        };
    }
});

// Function to open edit modal
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