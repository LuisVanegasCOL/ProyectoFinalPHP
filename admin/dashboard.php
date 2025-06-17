<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
echo "<h2>Bienvenido, " . $_SESSION['admin'] . "</h2>";
echo "<a href='add_product.php'>Agregar producto</a> | <a href='logout.php'>Cerrar sesión</a>"; 