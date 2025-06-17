<?php
session_start();
require_once 'includes/db.php'; // For potential future use (e.g., fetching user-specific info)
require_once 'includes/header.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = ['type' => 'danger', 'text' => 'Debes iniciar sesión para acceder a esta página.'];
    header('Location: login.php');
    exit;
}
?>

<div class="container mt-5 mb-5">
    <h2 class="mb-4">Panel de Usuario</h2>

    <?php
    // Display session messages (e.g., success/error messages from other actions)
    // Ensure this is compatible with how messages are set (e.g. $_SESSION['message']['type'] and $_SESSION['message']['text'])
    if (isset($_SESSION['message']) && is_array($_SESSION['message']) && isset($_SESSION['message']['text']) && isset($_SESSION['message']['type'])) {
        echo "<div class='alert alert-" . htmlspecialchars($_SESSION['message']['type']) . " alert-dismissible fade show' role='alert'>";
        echo htmlspecialchars($_SESSION['message']['text']);
        echo "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>";
        echo "</div>";
        unset($_SESSION['message']);
    }
    ?>

    <p class="lead">¡Bienvenido, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</p>
    <p>Desde aquí podrás gestionar tu cuenta y ver tus pedidos cuando estas funcionalidades estén disponibles.</p>

    <div class="row mt-4">
        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">Historial de Pedidos</h5>
                    <p class="card-text">Aquí podrás ver todos los pedidos que has realizado una vez que la funcionalidad esté implementada.</p>
                    <a href="order_history.php" class="btn btn-primary mt-auto disabled" aria-disabled="true">Ver Pedidos (Próximamente)</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">Gestionar Cuenta</h5>
                    <p class="card-text">Actualiza tu información personal o cambia tu contraseña. Esta sección estará disponible pronto.</p>
                    <a href="manage_account.php" class="btn btn-primary mt-auto disabled" aria-disabled="true">Gestionar Cuenta (Próximamente)</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
