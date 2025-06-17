<?php include 'includes/header.php'; ?>
<form method="POST" action="mailto:tucorreo@ejemplo.com" enctype="text/plain">
    Nombre: <input type="text" name="name"><br>
    Mensaje: <textarea name="message"></textarea><br>
    <input type="submit" value="Enviar">
</form>
<?php include 'includes/footer.php'; ?> 