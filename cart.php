<?php
include 'includes/header.php';
include 'includes/db.php';

$cart = json_decode($_COOKIE['cart'] ?? '[]');
$total = 0;

if (count($cart) > 0) {
    $ids = implode(',', array_map('intval', $cart));
    $stmt = $pdo->query("SELECT * FROM products WHERE id IN ($ids)");

    while ($row = $stmt->fetch()) {
        $img = (strpos($row['image'], 'http') === 0) ? $row['image'] : 'uploads/' . $row['image'];
        echo "<div>
            <img src='$img' width='100'>
            <h3>{$row['name']}</h3>
            <p>{$row['price']} €</p>
        </div>";
        $total += $row['price'];
    }
}
echo "<p>Total: $total €</p>";
echo "<a href='checkout.php'>Finalizar compra</a>";

include 'includes/footer.php';
?> 