<?php include 'includes/header.php'; ?>
<form action="process_order.php" method="POST">
    Nombre: <input type="text" name="name" required><br>
    Email: <input type="email" name="email" required><br>
    Dirección: <textarea name="address" required></textarea><br>
    <input type="submit" value="Realizar Pedido">
</form>
<?php include 'includes/footer.php'; ?> 