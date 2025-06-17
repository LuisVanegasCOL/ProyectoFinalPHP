<?php
session_start();
include 'includes/db.php';

// Handle AJAX request for product details
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_ids'])) {
    header('Content-Type: application/json');
    $productIdsJson = $_POST['product_ids'];
    $productIds = json_decode($productIdsJson, true);

    if (empty($productIds) || !is_array($productIds)) {
        echo json_encode(['error' => 'No product IDs provided or invalid format.']);
        exit;
    }

    // Sanitize IDs to ensure they are integers
    $sanitizedProductIds = array_map('intval', $productIds);
    // Filter out any IDs that became 0 or less after intval (if they weren't numeric strings)
    $sanitizedProductIds = array_filter($sanitizedProductIds, function($id) {
        return $id > 0;
    });

    if (empty($sanitizedProductIds)) {
        echo json_encode(['error' => 'No valid product IDs provided.']);
        exit;
    }

    // Build placeholders for IN clause
    $placeholders = implode(',', array_fill(0, count($sanitizedProductIds), '?'));
    $sql = "SELECT id, name, price, image FROM products WHERE id IN ($placeholders)";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($sanitizedProductIds);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Re-key by product ID for easier lookup on the client-side
        $productsById = [];
        foreach ($products as $product) {
            $productsById[$product['id']] = $product;
        }

        echo json_encode($productsById);
    } catch (PDOException $e) {
        error_log("Error fetching cart product details: " . $e->getMessage());
        echo json_encode(['error' => 'Failed to fetch product details.']);
    }
    exit; // Important to prevent HTML rendering for AJAX
}

// If not an AJAX request, proceed to render the HTML page
include 'includes/header.php';
?>

<div class="container mt-5 mb-5">
    <h2>Tu Carrito de Compras</h2>
    <hr>
    <div id="cart-items-container">
        <!-- Cart items will be rendered here by JavaScript -->
        <p>Cargando carrito...</p>
    </div>
    <div id="cart-total" class="mt-4" style="font-size: 1.5em; text-align: right;">
        <strong>Total: €0.00</strong>
    </div>
    <div class="mt-4 text-end">
        <a href="index.php" class="btn btn-secondary">Seguir Comprando</a>
        <a href="checkout.php" id="checkout-button" class="btn btn-primary">Proceder al Pago</a>
    </div>
</div>

<?php
// The JavaScript for cart handling will be in main.js, which is included by footer.php
include 'includes/footer.php';
?> 