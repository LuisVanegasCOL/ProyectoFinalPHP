<?php
session_start();
require_once 'includes/db.php';

// If already logged in, redirect to homepage or dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$errors = [];
$email = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email)) {
        $errors[] = "El correo electrónico es obligatorio.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Formato de correo electrónico inválido.";
    }
    if (empty($password)) {
        $errors[] = "La contraseña es obligatoria.";
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT id, name, email, password FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = htmlspecialchars($user['name']); // Sanitize name before storing in session
                $_SESSION['message'] = ['type' => 'success', 'text' => '¡Bienvenido de nuevo, ' . htmlspecialchars($user['name']) . '!'];
                header("Location: index.php"); // Or a user dashboard page
                exit;
            } else {
                $errors[] = "Correo electrónico o contraseña incorrectos.";
            }
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            $errors[] = "Ocurrió un error en el servidor. Por favor, inténtalo de nuevo más tarde.";
        }
    }
}

require_once 'includes/header.php'; // For consistent layout
?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h2 class="text-center mb-4">Iniciar Sesión</h2>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger" role="alert">
                    <?php foreach ($errors as $error): ?>
                        <p class="mb-0"><?php echo htmlspecialchars($error); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php
            // Display general session messages (e.g., from registration success if redirected here)
            if (isset($_SESSION['message']) && !empty($_SESSION['message']['text'])) {
                $message_type = $_SESSION['message']['type'] === 'success' ? 'alert-success' : 'alert-danger';
                echo "<div class='alert " . $message_type . "'>" . htmlspecialchars($_SESSION['message']['text']) . "</div>";
                unset($_SESSION['message']);
            }
            ?>

            <form method="POST" action="login.php" class="border p-4 rounded shadow-sm">
                <div class="mb-3">
                    <label for="email" class="form-label">Correo Electrónico</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Ingresar</button>
                </div>
            </form>
            <p class="mt-3 text-center">
                ¿No tienes una cuenta? <a href="register.php">Regístrate aquí</a>.
            </p>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
