// Function to update mini cart and header cart count
function updateMiniCart() {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    let miniCartItemsContainer = $('#mini-cart-items');
    let cartCountBadge = $('#cart-count');

    // Update header cart count badge
    cartCountBadge.text(cart.length);
    if (cart.length > 0) {
        cartCountBadge.removeClass('bg-secondary').addClass('bg-danger');
    } else {
        cartCountBadge.removeClass('bg-danger').addClass('bg-secondary');
    }

    if (!miniCartItemsContainer.length) { // Mini cart dropdown not present on this page (should always be, but good check)
        return;
    }

    if (cart.length === 0) {
        miniCartItemsContainer.html('<li><p class="dropdown-item text-center mb-0">Tu carrito está vacío.</p></li>');
        // Ensure checkout button in mini-cart (if it exists) is disabled
        $('#mini-cart-checkout-btn').addClass('disabled');
        return;
    }

    let productQuantities = {};
    cart.forEach(id => {
        let productIdStr = id.toString();
        productQuantities[productIdStr] = (productQuantities[productIdStr] || 0) + 1;
    });
    let uniqueProductIds = Object.keys(productQuantities);

    $.ajax({
        url: 'cart.php', // Using existing cart.php AJAX handler
        type: 'POST',
        data: { product_ids: JSON.stringify(uniqueProductIds) },
        success: function(response) {
            let productsData = response;
            miniCartItemsContainer.empty(); // Clear previous items

            if (productsData.error) {
                miniCartItemsContainer.html('<li><p class="dropdown-item text-danger text-center mb-0">Error al cargar mini carrito.</p></li>');
                $('#mini-cart-checkout-btn').addClass('disabled');
                return;
            }
            if (Object.keys(productsData).length === 0 && uniqueProductIds.length > 0) {
                 miniCartItemsContainer.html('<li><p class="dropdown-item text-warning text-center mb-0">Algunos productos no se encontraron.</p></li>');
                 // Still might have a total for found items, or disable checkout
                 // For simplicity, let's show an error and disable checkout for now
                 $('#mini-cart-checkout-btn').addClass('disabled');
                 // return; // Or proceed to show what was found
            }
             if (Object.keys(productsData).length === 0 && uniqueProductIds.length === 0) {
                 miniCartItemsContainer.html('<li><p class="dropdown-item text-center mb-0">Tu carrito está vacío.</p></li>');
                 $('#mini-cart-checkout-btn').addClass('disabled');
                 return;
            }

            let miniCartTotal = 0;
            uniqueProductIds.forEach(id => {
                let product = productsData[id];
                if (product) {
                    let quantity = productQuantities[id];
                    let price = parseFloat(product.price);
                    if(isNaN(price)) price = 0;
                    miniCartTotal += price * quantity;
                    let imageUrl = product.image ? (product.image.startsWith('http') || product.image.startsWith('uploads/') ? product.image : 'uploads/' + product.image) : 'img/placeholder.png';

                    let itemHtml = `
                        <li class="dropdown-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="me-2">
                                    <img src="${imageUrl}" alt="${product.name || ''}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                                </div>
                                <div class="flex-grow-1" style="font-size: 0.9em;">
                                    ${product.name || 'Producto'} (x ${quantity})
                                </div>
                                <span class="fw-bold ms-2" style="font-size: 0.9em;">€${(price * quantity).toFixed(2)}</span>
                            </div>
                        </li>`;
                    miniCartItemsContainer.append(itemHtml);
                }
            });

            if (miniCartTotal > 0) {
                miniCartItemsContainer.append('<li><hr class="dropdown-divider"></li>');
                miniCartItemsContainer.append(`<li><p class="dropdown-item text-end mb-2"><strong>Subtotal: €${miniCartTotal.toFixed(2)}</strong></p></li>`);
                miniCartItemsContainer.append(`
                    <li>
                        <div class="d-grid gap-2 px-2">
                            <a class="btn btn-primary btn-sm mb-1" href="/mini-ecommerce/cart.php">Ver Carrito</a>
                            <a class="btn btn-success btn-sm" id="mini-cart-checkout-btn" href="/mini-ecommerce/checkout.php">Pagar</a>
                        </div>
                    </li>`);
            } else if (uniqueProductIds.length > 0 && Object.keys(productsData).length === 0) {
                // Cart had IDs but no valid products returned
                 miniCartItemsContainer.html('<li><p class="dropdown-item text-center mb-0">No se pudieron cargar los productos del carrito.</p></li>');
                 $('#mini-cart-checkout-btn').addClass('disabled');
            }
            else { // Should be caught by earlier checks, but as a fallback
                miniCartItemsContainer.html('<li><p class="dropdown-item text-center mb-0">Tu carrito está vacío.</p></li>');
                $('#mini-cart-checkout-btn').addClass('disabled');
            }
        },
        error: function() {
            miniCartItemsContainer.html('<li><p class="dropdown-item text-danger text-center mb-0">Error al conectar con el carrito.</p></li>');
            $('#mini-cart-checkout-btn').addClass('disabled');
        }
    });
}


// Function to load and display cart items on the main cart page
function loadAndDisplayCart() {
    if (!$('#cart-items-container').length) {
        return; // Not on cart page
    }
    updateMiniCart(); // Keep mini cart sync'd when on main cart page as well

    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    // updateCartCount(); // Now handled by updateMiniCart()

    if (cart.length === 0) {
        $('#cart-items-container').html('<p>Tu carrito está vacío.</p>');
        // This part is identical to the start of updateMiniCart's logic for getting productQuantities and uniqueProductIds
        // So, no need to repeat the AJAX call if updateMiniCart has already fetched the data.
        // However, loadAndDisplayCart is specifically for the main cart page table,
        // while updateMiniCart is for the header dropdown. They fetch the same data but render differently.
        // For now, keeping them separate but ensuring updateMiniCart is called from relevant places.
        // The AJAX call in loadAndDisplayCart will proceed as it's for the main cart content.
        $('#cart-total').html('<strong>Total: €0.00</strong>'); // Reset total before populating
        $('#checkout-button').addClass('disabled btn-secondary').removeClass('btn-primary'); // Reset button state
        // ... (rest of the original loadAndDisplayCart AJAX call and rendering logic remains here) ...
        // (The original AJAX call from loadAndDisplayCart is still needed here to populate the main cart page)
        // Count product quantities
        let productQuantities = {};
        cart.forEach(id => {
            let productIdStr = id.toString();
            productQuantities[productIdStr] = (productQuantities[productIdStr] || 0) + 1;
        });
        let uniqueProductIds = Object.keys(productQuantities);

        $.ajax({
            url: 'cart.php',
            type: 'POST',
            data: { product_ids: JSON.stringify(uniqueProductIds) },
            success: function(response) {
                let productsData = response;

                if (productsData.error) {
                    $('#cart-items-container').html('<p>' + productsData.error + '</p>');
                    return;
                }
                // ... (rest of the success callback from original loadAndDisplayCart)
                 if (Object.keys(productsData).length === 0 && uniqueProductIds.length > 0) {
                    $('#cart-items-container').html('<p>No se pudieron cargar los detalles de los productos del carrito. Es posible que los productos ya no existan o hayan sido eliminados.</p>');
                    return;
                }
                 if (Object.keys(productsData).length === 0 && uniqueProductIds.length === 0) {
                     $('#cart-items-container').html('<p>Tu carrito está vacío.</p>');
                     return;
                }

                let total = 0;
                let itemsHtml = '<table class="table align-middle"><thead><tr><th>Imagen</th><th>Producto</th><th>Precio Unit.</th><th class="text-center" style="width:120px;">Cantidad</th><th class="text-end">Subtotal</th><th class="text-center">Acción</th></tr></thead><tbody>';

                uniqueProductIds.forEach(id => {
                    let product = productsData[id];
                    if (product) {
                        let quantity = productQuantities[id];
                        let price = parseFloat(product.price);
                         if (isNaN(price)) price = 0;
                        let subtotal = price * quantity;
                        total += subtotal;

                        let imageUrl = product.image ? (product.image.startsWith('http') || product.image.startsWith('uploads/') ? product.image : 'uploads/' + product.image) : 'img/placeholder.png';

                        itemsHtml += `
                            <tr>
                                <td><img src="${imageUrl}" alt="${product.name || 'Producto'}" style="width: 60px; height: auto; border-radius: 4px;"></td>
                                <td>${product.name || 'Nombre no disponible'}</td>
                                <td>€${price.toFixed(2)}</td>
                                <td class="text-center">
                                    <input type="number" class="form-control form-control-sm item-quantity text-center" data-id="${product.id}" value="${quantity}" min="1" style="width: 70px; display: inline-block;">
                                </td>
                                <td class="text-end">€${subtotal.toFixed(2)}</td>
                                <td class="text-center">
                                    <button class="remove-item btn btn-danger btn-sm" data-id="${product.id}" title="Eliminar producto">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    } else {
                         itemsHtml += `
                            <tr>
                                <td colspan="6">Un producto en tu carrito (ID: ${id}) ya no está disponible.
                                    <button class="remove-item-direct btn btn-warning btn-sm" data-id="${id}">Eliminar de la lista</button>
                                </td>
                            </tr>
                        `;
                    }
                });
                itemsHtml += '</tbody></table>';
                $('#cart-items-container').html(itemsHtml);
                $('#cart-total').html(`<strong>Total: €${total.toFixed(2)}</strong>`);

                if (total > 0) {
                    $('#checkout-button').removeClass('disabled btn-secondary').addClass('btn-primary');
                } else {
                    $('#cart-items-container').html('<p>Tu carrito está vacío.</p>');
                    $('#checkout-button').addClass('disabled btn-secondary').removeClass('btn-primary');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("AJAX Error:", textStatus, errorThrown, jqXHR.responseText);
                $('#cart-items-container').html('<p>Error al cargar el carrito. Por favor, revisa la consola e inténtalo de nuevo.</p>');
            }
        });
    }
}


$(document).ready(function() {
    updateMiniCart(); // Call on every page load

    $('.add-to-cart').click(function(e) {
        e.preventDefault();
        let id = $(this).data('id').toString();
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        cart.push(id);
        localStorage.setItem('cart', JSON.stringify(cart));
        updateMiniCart(); // Update mini cart after adding

        let originalText = $(this).html();
        $(this).html('Añadido <i class="fas fa-check"></i>').addClass('btn-success').removeClass('btn-primary');
        setTimeout(() => {
            $(this).html(originalText).removeClass('btn-success').addClass('btn-primary');
        }, 1500);
    });

    if ($('#cart-items-container').length) { // Only if on main cart page
        loadAndDisplayCart();
    }

    $('#cart-items-container').on('change', '.item-quantity', function() {
        let productId = $(this).data('id').toString();
        let newQuantity = parseInt($(this).val());
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        if (newQuantity < 1) newQuantity = 1; $(this).val(1);

        let updatedCart = cart.filter(id => id.toString() !== productId);
        for (let i = 0; i < newQuantity; i++) updatedCart.push(productId);

        localStorage.setItem('cart', JSON.stringify(updatedCart));
        loadAndDisplayCart(); // Reload main cart
        updateMiniCart();     // Also update mini cart
    });

    $('#cart-items-container').on('click', '.remove-item', function() {
        let productId = $(this).data('id').toString();
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        let updatedCart = cart.filter(id => id.toString() !== productId);
        localStorage.setItem('cart', JSON.stringify(updatedCart));
        loadAndDisplayCart(); // Reload main cart
        updateMiniCart();     // Also update mini cart
    });

     $('#cart-items-container').on('click', '.remove-item-direct', function() {
        let productId = $(this).data('id').toString();
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        let updatedCart = cart.filter(id => id.toString() !== productId);
        localStorage.setItem('cart', JSON.stringify(updatedCart));
        loadAndDisplayCart(); // Reload main cart
        updateMiniCart();     // Also update mini cart
    });
}); 