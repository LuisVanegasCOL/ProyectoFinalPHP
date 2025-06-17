<?php if (session_status() === PHP_SESSION_NONE) { session_start(); } ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mini E-commerce</title>
    <link rel="stylesheet" href="/mini-ecommerce/css/style.css">
    <!-- Assuming Font Awesome is loaded for icons (e.g., via CDN in <head> or locally) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <header>
        <h1>Mini E-commerce</h1>
        <nav>
            <ul style="list-style: none; padding-left: 0; margin: 0; display: flex; align-items: center; flex-wrap: wrap;">
                <li style="margin-right: 15px;"><a href="/mini-ecommerce/index.php">Inicio</a></li>
                <li style="margin-right: 15px;"><a href="/mini-ecommerce/contact.php">Contacto</a></li>
                <li style="margin-right: 15px;"><a href="/mini-ecommerce/about.php">Sobre Nosotros</a></li>

                <!-- Mini Cart Dropdown -->
                <li class="nav-item dropdown" style="margin-right: 15px;">
                    <a class="nav-link dropdown-toggle" href="#" id="miniCartDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="text-decoration: none; color: inherit;">
                        <i class="fas fa-shopping-cart"></i> Carrito <span id="cart-count" class="badge bg-danger">0</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="miniCartDropdown" id="mini-cart-items" style="min-width: 280px; padding: 10px;">
                        <li><p class="dropdown-item text-center mb-0">Tu carrito está vacío.</p></li>
                    </ul>
                </li>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <li style="margin-left: auto; margin-right: 5px;">
                        <span style="padding: 8px 10px; display: inline-block;">¡Hola, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</span>
                    </li>
                    <li style="margin-right: 15px;"><a href="/mini-ecommerce/user_dashboard.php">Mi Cuenta</a></li>
                    <?php
                        // Check if current user is admin (e.g. by checking a specific user_id or a role stored in session)
                        // This is a placeholder for admin check. In a real app, you'd have a better way to identify admins.
                        // For example, after login, if $user['is_admin'] == 1, set $_SESSION['is_admin'] = true;
                        if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true || (isset($_SESSION['user_name']) && $_SESSION['user_name'] === 'admin')): // Simplified admin check
                    ?>
                        <li style="margin-right: 15px;"><a href="/mini-ecommerce/admin/dashboard.php">Admin</a></li>
                    <?php endif; ?>
                    <li style="margin-right: 15px;"><a href="/mini-ecommerce/logout.php">Cerrar Sesión</a></li>
                <?php else: ?>
                    <li style="margin-left: auto; margin-right: 15px;"><a href="/mini-ecommerce/login.php">Login</a></li>
                    <li style="margin-right: 15px;"><a href="/mini-ecommerce/register.php">Registrarse</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <hr>
    <!-- Ensure Bootstrap 5 JS bundle is loaded, typically in footer.php or before closing </body> -->
    <!-- Example: <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script> -->
    <!-- Assuming Font Awesome is also loaded for the cart icon -->