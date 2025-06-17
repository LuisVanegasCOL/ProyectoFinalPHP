<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
?>
<h2>Eliminar Producto</h2>
<form method="POST">
    ID del producto: <input type="number" name="id"><br>
    <input type="submit" value="Eliminar">
</form> 