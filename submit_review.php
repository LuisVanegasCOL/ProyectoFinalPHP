<?php
session_start();
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basic validation
    if (empty($_POST['product_id']) || !is_numeric($_POST['product_id']) ||
        empty($_POST['user_name']) ||
        empty($_POST['rating']) || !is_numeric($_POST['rating']) || $_POST['rating'] < 1 || $_POST['rating'] > 5 ||
        empty($_POST['review_text'])) {

        $_SESSION['review_message'] = "Por favor, completa todos los campos correctamente.";
        // Redirect back to product page if product_id is available, otherwise to index
        $redirect_url = isset($_POST['product_id']) && is_numeric($_POST['product_id']) ? "product.php?id=" . intval($_POST['product_id']) : "index.php";
        header("Location: " . $redirect_url);
        exit;
    }

    $productId = intval($_POST['product_id']);
    $userName = trim(htmlspecialchars($_POST['user_name'])); // Trim and sanitize
    $rating = intval($_POST['rating']);
    $reviewText = trim(htmlspecialchars($_POST['review_text'])); // Trim and sanitize

    // Further validation for string length if desired, e.g.
    if (mb_strlen($userName) > 100) {
        $_SESSION['review_message'] = "El nombre de usuario es demasiado largo.";
        header("Location: product.php?id=" . $productId);
        exit;
    }
    if (mb_strlen($reviewText) > 2000) { // Example limit for review text
        $_SESSION['review_message'] = "La opinión es demasiado larga. Máximo 2000 caracteres.";
        header("Location: product.php?id=" . $productId);
        exit;
    }

    // Check if product exists before adding review
    $stmt_check_product = $pdo->prepare("SELECT id FROM products WHERE id = ?");
    $stmt_check_product->execute([$productId]);
    if ($stmt_check_product->fetch() === false) {
        $_SESSION['review_message'] = "El producto sobre el que intentas opinar no existe.";
        header("Location: index.php"); // Or some other appropriate error page
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO product_reviews (product_id, user_name, rating, review_text) VALUES (?, ?, ?, ?)");
        $stmt->execute([$productId, $userName, $rating, $reviewText]);

        $_SESSION['review_message'] = "¡Gracias por tu opinión! Será visible en breve."; // Updated message
    } catch (PDOException $e) {
        error_log("Error submitting review: " . $e->getMessage());
        // Check for specific error codes, e.g., foreign key constraint
        if ($e->getCode() == '23000') { // Integrity constraint violation
             $_SESSION['review_message'] = "Hubo un problema al verificar los datos de tu opinión. Asegúrate de que el producto exista.";
        } else {
            $_SESSION['review_message'] = "Hubo un error al enviar tu opinión. Por favor, inténtalo de nuevo.";
        }
    }

    header("Location: product.php?id=" . $productId . "#reviews-section"); // Redirect back to product page, anchor to reviews
    exit;

} else {
    // Not a POST request, redirect to homepage
    $_SESSION['review_message'] = "Acceso inválido.";
    header("Location: index.php");
    exit;
}
?>
