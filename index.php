<?php
include 'includes/db.php';
include 'includes/header.php';

$stmt = $pdo->query("SELECT * FROM products");
echo "<div class='products'>";
while ($row = $stmt->fetch()) {
    $img = (strpos($row['image'], 'http') === 0) ? $row['image'] : 'uploads/' . $row['image'];
    echo "
    <div class='product'>
        <img src='$img' width='150'>
        <h3>{$row['name']}</h3>
        <p>{$row['price']} €</p>
        <button class='add-to-cart' data-id='{$row['id']}'>Añadir al carrito</button>
    </div>";
}
echo "</div>";

include 'includes/footer.php';
?>
<script src="/mini-ecommerce/js/jquery.min.js"></script>
<script src="/mini-ecommerce/js/main.js"></script> 