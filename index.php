<?php
include 'includes/db.php';
include 'includes/header.php'; // session_start() is expected to be in header.php

// Display session messages (e.g., from login, logout, registration)
if (isset($_SESSION['message']) && !empty($_SESSION['message']['text'])) {
    $message_type = isset($_SESSION['message']['type']) && $_SESSION['message']['type'] === 'success' ? 'alert-success' : 'alert-danger';
    // Default to 'alert-info' if type is not set or not 'success'/'error'
    if (!in_array($message_type, ['alert-success', 'alert-danger'])) {
        $message_type = 'alert-info';
    }

    echo "<div class='container mt-3'>"; // Added container for better layout
    echo "  <div class='alert " . $message_type . " alert-dismissible fade show' role='alert'>";
    echo htmlspecialchars($_SESSION['message']['text']);
    echo "    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>";
    echo "  </div>";
    echo "</div>";
    unset($_SESSION['message']);
}

$stmt = $pdo->query("SELECT * FROM products");
echo "<div class='products'>";
while ($row = $stmt->fetch()) {
    $img = (strpos($row['image'], 'http') === 0) ? $row['image'] : 'uploads/' . $row['image'];
    // Ensure image path is correctly handled if it already contains 'uploads/'
    if (strpos($row['image'], 'http') !== 0 && strpos($row['image'], 'uploads/') === 0) {
        $img = $row['image'];
    } elseif (strpos($row['image'], 'http') !== 0) {
        $img = 'uploads/' . $row['image'];
    }

    // Check if local file exists, otherwise show a placeholder
    if (strpos($img, 'http') !== 0 && !file_exists($img)) {
        $img = 'img/placeholder.png'; // Adjust if your placeholder is elsewhere
    }

    echo "
    <div class='product'>
        <a href='product.php?id={$row['id']}' style='text-decoration: none; color: inherit;'>
            <img src='" . htmlspecialchars($img) . "' alt='" . htmlspecialchars($row['name']) . "'>
            <h3 class='product-name'>" . htmlspecialchars($row['name']) . "</h3>
        </a>
        <p class='product-price'>" . htmlspecialchars(number_format($row['price'], 2)) . " €</p>
        <button class='product-button add-to-cart' data-id='{$row['id']}'>Añadir al carrito</button>
    </div>";
}
echo "</div>";

include 'includes/footer.php';
// JavaScript files should ideally be included in footer.php for consistency
// If not already there, ensure footer.php includes them or uncomment these lines
// and remove them from footer.php if they are duplicated.
// For now, assuming footer.php handles JS includes as per best practice.
?>