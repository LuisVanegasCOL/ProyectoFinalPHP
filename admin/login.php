<?php
session_start();
require_once '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->execute([$user]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($pass, $admin['password'])) {
        $_SESSION['admin'] = $admin['username'];
        header("Location: dashboard.php");
    } else {
        echo "Credenciales incorrectas.";
    }
}
?>

<form method="POST">
    Usuario: <input type="text" name="username">
    Contraseña: <input type="password" name="password">
    <input type="submit" value="Ingresar">
</form> 