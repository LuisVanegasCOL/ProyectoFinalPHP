$(document).ready(function() {
    $('.add-to-cart').click(function() {
        let id = $(this).data('id');
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        cart.push(id);
        localStorage.setItem('cart', JSON.stringify(cart));
        alert('Producto añadido al carrito.');
    });
}); 