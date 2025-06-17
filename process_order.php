<?php
require_once 'includes/db.php';

$cart = json_decode($_COOKIE['cart'] ?? '[]');
$ids = implode(',', array_map('intval', $cart));
$stmt = $pdo->query("SELECT * FROM products WHERE id IN ($ids)");

$items = [];
$total = 0;
while ($row = $stmt->fetch()) {
    $items[] = $row['name'];
    $total += $row['price'];
}

$insert = $pdo->prepare("INSERT INTO orders (name, email, address, cart, total) VALUES (?, ?, ?, ?, ?)");
$insert->execute([
    $_POST['name'],
    $_POST['email'],
    $_POST['address'],
    json_encode($items),
    $total
]);

echo "Pedido realizado con éxito.";
setcookie('cart', '', time() - 3600);
?> 