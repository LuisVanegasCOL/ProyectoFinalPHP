<?php
session_start();
include 'includes/db.php'; // For $pdo

$errors = [];
$name = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validation
    if (empty($name)) {
        $errors[] = "El nombre es obligatorio.";
    }
    if (empty($email)) {
        $errors[] = "El correo electrónico es obligatorio.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "El formato del correo electrónico no es válido.";
    }
    if (empty($password)) {
        $errors[] = "La contraseña es obligatoria.";
    } elseif (strlen($password) < 6) {
        $errors[] = "La contraseña debe tener al menos 6 caracteres.";
    }
    if ($password !== $confirm_password) {
        $errors[] = "Las contraseñas no coinciden.";
    }

    // Check if email already exists (if no other validation errors)
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $errors[] = "Este correo electrónico ya está registrado. Por favor, intenta con otro.";
            }
        } catch (PDOException $e) {
            error_log("Error checking existing email: " . $e->getMessage());
            $errors[] = "Error al verificar el correo. Inténtalo más tarde.";
        }
    }

    // If no errors, proceed to insert user
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        try {
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            if ($stmt->execute([$name, $email, $hashed_password])) {
                $_SESSION['user_id'] = $pdo->lastInsertId();
                $_SESSION['user_name'] = $name;
                $_SESSION['message'] = [
                    'type' => 'success',
                    'text' => '¡Registro exitoso! Bienvenido, ' . htmlspecialchars($name) . '.'
                ];
                header("Location: index.php"); // Or admin/dashboard.php
                exit;
            } else {
                $errors[] = "Hubo un error durante el registro. Por favor, inténtalo de nuevo.";
            }
        } catch (PDOException $e) {
            error_log("Error inserting user: " . $e->getMessage());
            // Check for specific integrity constraint violation (duplicate email, if missed by earlier check or race condition)
            if ($e->getCode() == '23000') {
                 $errors[] = "Este correo electrónico ya está registrado. (Error código: DB23000)";
            } else {
                $errors[] = "Error de base de datos durante el registro. (Error código: DB" . $e->getCode() . ")";
            }
        }
    }

    // If there were errors, store them in session to display after redirect (or display directly if not redirecting)
    // For this setup, we are re-rendering the page, so $errors will be available directly.
    // If we were redirecting on error: $_SESSION['register_errors'] = $errors; header('Location: register.php'); exit;
}

include 'includes/header.php';
?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h2 class="text-center mb-4">Crear Cuenta</h2>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger" role="alert">
                    <strong>Errores:</strong>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php
            // Display success message from other pages if redirected here with one
            if (isset($_SESSION['message']) && $_SESSION['message']['type'] === 'success') {
                echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['message']['text']) . '</div>';
                unset($_SESSION['message']);
            }
            // Display error message from other pages (e.g. login attempt failed)
             if (isset($_SESSION['message']) && $_SESSION['message']['type'] === 'error') {
                echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['message']['text']) . '</div>';
                unset($_SESSION['message']);
            }
            ?>

            <form action="register.php" method="POST" class="border p-4 rounded shadow-sm">
                <div class="mb-3">
                    <label for="name" class="form-label">Nombre Completo</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Correo Electrónico</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                    <small class="form-text text-muted">Mínimo 6 caracteres.</small>
                </div>
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirmar Contraseña</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Registrarse</button>
                </div>
                <p class="mt-3 text-center">
                    ¿Ya tienes una cuenta? <a href="login.php">Inicia Sesión aquí</a>
                </p>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
