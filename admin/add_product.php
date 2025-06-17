<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
?>
<h2>Agregar Producto</h2>
<form method="POST" enctype="multipart/form-data">
    Nombre: <input type="text" name="name"><br>
    Descripción: <textarea name="description"></textarea><br>
    Precio: <input type="number" step="0.01" name="price"><br>
    Imagen: <input type="file" name="image"><br>
    Categoría: <input type="text" name="category"><br>
    <input type="submit" value="Agregar">
</form> 