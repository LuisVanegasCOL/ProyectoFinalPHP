<?php
session_start(); // Start the session to access cart, etc.
include 'includes/db.php';
include 'includes/header.php';

// Display review submission messages
if (isset($_SESSION['review_message'])) {
    // Determine alert type based on message content for better UX
    $message_class = 'alert-info'; // Default
    if (strpos(strtolower($_SESSION['review_message']), 'error') !== false || strpos(strtolower($_SESSION['review_message']), 'completa todos los campos') !== false) {
        $message_class = 'alert-danger';
    } elseif (strpos(strtolower($_SESSION['review_message']), 'gracias') !== false) {
        $message_class = 'alert-success';
    }
    echo "<div class='container mt-3'><div class='alert " . $message_class . " alert-dismissible fade show' role='alert'>";
    echo htmlspecialchars($_SESSION['review_message']);
    echo "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>";
    echo "</div></div>";
    unset($_SESSION['review_message']);
}
?>

<div class="container" style="padding-top: 20px; padding-bottom: 20px;">
    <?php
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $productId = intval($_GET['id']); // Sanitize to integer

        try {
            $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
            $stmt->execute([$productId]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($product) {
    ?>
                <div class="row">
                    <div class="col-md-6">
                        <?php
                        $imagePath = $product['image'];
                        // Check if the image path is an external URL or a local file
                        if (filter_var($imagePath, FILTER_VALIDATE_URL)) {
                            $imageUrl = $imagePath;
                        } else {
                            // Prepend 'uploads/' if it's a local file and not already there
                            if (strpos($imagePath, 'uploads/') !== 0) {
                                $imageUrl = 'uploads/' . $imagePath;
                            } else {
                                $imageUrl = $imagePath;
                            }
                        }
                        // Basic check if local file exists, otherwise show a placeholder or default image
                        if (!filter_var($imageUrl, FILTER_VALIDATE_URL) && !file_exists($imageUrl)) {
                            // Attempt to locate placeholder relative to the script's directory
                            $placeholderPath = __DIR__ . '/img/placeholder.png';
                            if (file_exists($placeholderPath)) {
                                $imageUrl = 'img/placeholder.png';
                            } else {
                                $imageUrl = ''; // Or some text like "Image not available"
                            }
                        }
                        ?>
                        <?php if ($imageUrl): ?>
                        <img src="<?php echo htmlspecialchars($imageUrl); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="img-fluid" style="max-height: 500px; border-radius: 8px; margin-bottom: 20px;">
                        <?php else: ?>
                        <p>Imagen no disponible.</p>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <h1><?php echo htmlspecialchars($product['name']); ?></h1>
                        <p class="text-muted">Categoría: <?php echo htmlspecialchars($product['category'] ?? 'General'); ?></p>
                        <hr>
                        <h3>Precio: <?php echo htmlspecialchars(number_format($product['price'], 2)); ?> €</h3>

                        <?php if (!empty($product['description'])): ?>
                            <h4>Descripción:</h4>
                            <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                        <?php endif; ?>

                        <button class='btn btn-primary add-to-cart mt-3' data-id='<?php echo htmlspecialchars($product['id']); ?>'>
                            <i class="fas fa-shopping-cart"></i> Añadir al carrito
                        </button>
                        <a href="index.php" class="btn btn-outline-secondary mt-3">Volver a la tienda</a>
                    </div>
                </div>
    <?php
                // Reviews Section
                if ($product) { // Only show reviews if product was found
                    echo "<hr style='margin-top: 40px; margin-bottom: 40px;'>";
                    echo "<div class='reviews-section mt-5'>";
                    echo "<h3>Opiniones de Clientes</h3>";

                    try {
                        $stmt_reviews = $pdo->prepare("
                            SELECT user_name, rating, review_text, DATE_FORMAT(created_at, '%e %M %Y a las %H:%i') AS formatted_date
                            FROM product_reviews
                            WHERE product_id = ?
                            ORDER BY created_at DESC
                        ");
                        $stmt_reviews->execute([$productId]);
                        $reviews = $stmt_reviews->fetchAll(PDO::FETCH_ASSOC);

                        if ($reviews) {
                            echo "<div class='list-group mt-3'>";
                            foreach ($reviews as $review) {
                                echo "<div class='list-group-item list-group-item-action flex-column align-items-start mb-3' style='border-radius: 8px;'>";
                                echo "<div class='d-flex w-100 justify-content-between'>";
                                echo "<h5 class='mb-1'>" . htmlspecialchars($review['user_name']) . "</h5>";
                                echo "<small class='text-muted'>" . htmlspecialchars($review['formatted_date']) . "</small>";
                                echo "</div>";
                                echo "<p class='mb-1'><strong>Rating: " . str_repeat('★', intval($review['rating'])) . str_repeat('☆', 5 - intval($review['rating'])) . "</strong> (" . htmlspecialchars($review['rating']) . "/5)</p>";
                                echo "<p class='mb-1'>" . nl2br(htmlspecialchars($review['review_text'])) . "</p>";
                                echo "</div>";
                            }
                            echo "</div>";
                        } else {
                            echo "<p class='mt-3'>Aún no hay opiniones para este producto. ¡Sé el primero en opinar!</p>";
                        }
                    } catch (PDOException $e) {
                        error_log("Database error fetching reviews: " . $e->getMessage());
                        echo "<p class='text-danger mt-3'>No se pudieron cargar las opiniones en este momento. Intente más tarde.</p>";
                    }
                    echo "</div>"; // end .reviews-section

                    // Review Submission Form Section
                    echo "<div class='review-form-section mt-5'>";
                    echo "<h4>Deja tu opinión</h4>";
                    echo "<form action='submit_review.php' method='POST' class='mt-3 p-3 border rounded bg-light'>";
                    echo "<input type='hidden' name='product_id' value='" . htmlspecialchars($productId) . "'>";

                    echo "<div class='form-group mb-3'>";
                    echo "<label for='user_name' class='form-label'>Nombre:</label>";
                    echo "<input type='text' name='user_name' id='user_name' class='form-control' required>";
                    echo "</div>";

                    echo "<div class='form-group mb-3'>";
                    echo "<label for='rating' class='form-label'>Puntuación:</label>";
                    echo "<select name='rating' id='rating' class='form-select' required>";
                    echo "<option value='' disabled selected>Elige una puntuación</option>";
                    echo "<option value='5'>5 Estrellas ★★★★★</option>";
                    echo "<option value='4'>4 Estrellas ★★★★☆</option>";
                    echo "<option value='3'>3 Estrellas ★★★☆☆</option>";
                    echo "<option value='2'>2 Estrellas ★★☆☆☆</option>";
                    echo "<option value='1'>1 Estrella ★☆☆☆☆</option>";
                    echo "</select>";
                    echo "</div>";

                    echo "<div class='form-group mb-3'>";
                    echo "<label for='review_text' class='form-label'>Tu opinión:</label>";
                    echo "<textarea name='review_text' id='review_text' class='form-control' rows='4' required></textarea>";
                    echo "</div>";

                    echo "<button type='submit' class='btn btn-primary'>Enviar opinión</button>";
                    echo "</form>";
                    echo "</div>"; // end .review-form-section
                }

            } else { // This 'else' corresponds to 'if ($product)'
                echo "<div class='alert alert-warning' role='alert'>Producto no encontrado.</div>";
            }
        } catch (PDOException $e) { // This catch corresponds to the try block for fetching the product
            // Log error to a file or monitoring system
            error_log("Database error fetching product: " . $e->getMessage());
            echo "<div class='alert alert-danger' role='alert'>Error al cargar el producto. Por favor, intente más tarde.</div>";
        }
    } else { // This 'else' corresponds to 'if (isset($_GET['id']) ...)'
        echo "<div class='alert alert-danger' role='alert'>ID de producto inválido o no proporcionado.</div>";
    }
    ?>
</div>

<?php include 'includes/footer.php'; ?> 