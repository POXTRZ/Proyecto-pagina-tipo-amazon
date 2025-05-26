<?php
session_start();
require '../Config/db.php';

// Checar login
$logged = !empty($_SESSION['usuario_id']);
$user = $_SESSION['nombre_usuario'] ?? '';

// Contar carrito
$cart_count = 0;
if ($logged) {
    $sql = "SELECT SUM(cp.cantidad) AS total
            FROM carrito c
            JOIN carrito_producto cp ON c.id = cp.id_carrito
            WHERE c.id_usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$_SESSION['usuario_id']]);
    $cart_count = $stmt->fetchColumn() ?: 0;
}

// Obtener el ID del producto
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header("Location: index.php");
    exit();
}

// Buscar el producto
$sql = "SELECT * FROM productos WHERE id = ? AND activo = 1 LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->execute([$id]);
$producto = $stmt->fetch(PDO::FETCH_ASSOC);

// Si no encuentra el producto, redirigir
if (!$producto) {
    header("Location: index.php");
    exit();
}

// Obtener categorías del producto
$sql = "SELECT c.nombre FROM categorias c 
        JOIN producto_categoria pc ON c.id = pc.id_categoria 
        WHERE pc.id_producto = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$id]);
$categorias = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Obtener productos relacionados (misma categoría)
$productos_relacionados = [];
if (!empty($categorias)) {
    $sql = "SELECT DISTINCT p.* FROM productos p
            JOIN producto_categoria pc ON p.id = pc.id_producto
            JOIN categorias c ON pc.id_categoria = c.id
            WHERE c.nombre IN (" . str_repeat('?,', count($categorias) - 1) . "?)
            AND p.id != ? AND p.activo = 1
            ORDER BY RAND()
            LIMIT 4";
    $params = array_merge($categorias, [$id]);
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $productos_relacionados = $stmt->fetchAll();
}

// Todas las categorías para el nav
$cats = $conn->query("SELECT * FROM categorias ORDER BY nombre")->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($producto['nombre']) ?> – FitStore Pro</title>
    <link rel="stylesheet" href="../Css/index.css">
    <link rel="stylesheet" href="../Css/productos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
    <header class="header">
        <?php include '../Includes/nav.php'; ?>
    </header>

    <div class="particles"></div>

    <!-- Contenedor principal del producto -->
    <div class="product-detail-container">
        <div class="product-detail-card">
            <!-- Navegación breadcrumb -->
            <nav class="breadcrumb">
                <a href="index.php"><i class="fas fa-home"></i> Inicio</a>
                <span class="separator">></span>
                <?php if (!empty($categorias)): ?>
                    <span><?= htmlspecialchars($categorias[0]) ?></span>
                    <span class="separator">></span>
                <?php endif; ?>
                <span class="current"><?= htmlspecialchars($producto['nombre']) ?></span>
            </nav>

            <div class="product-detail-content">
                <!-- Imagen del producto -->
                <div class="product-image-section">
                    <div class="main-image">
                        <img src="<?= isset($producto['imagen']) && !empty($producto['imagen']) ? '../Images/' . htmlspecialchars($producto['imagen']) : '../Images/default-product.png' ?>" 
                             alt="<?= htmlspecialchars($producto['nombre']) ?>"
                             id="main-product-image">
                    </div>
                    
                    <!-- Badges del producto -->
                    <div class="product-badges">
                        <?php if ($producto['stock'] == 0): ?>
                            <div class="badge stock-out">Agotado</div>
                        <?php elseif ($producto['stock'] <= 5): ?>
                            <div class="badge stock-warning">¡Últimas unidades!</div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Información del producto -->
                <div class="product-info-section">
                    <h1 class="product-title"><?= htmlspecialchars($producto['nombre']) ?></h1>
                    
                    <!-- Categorías -->
                    <?php if (!empty($categorias)): ?>
                        <div class="product-categories">
                            <?php foreach ($categorias as $categoria): ?>
                                <span class="category-tag"><?= htmlspecialchars($categoria) ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Precio -->
                    <div class="price-section">
                        <span class="current-price">$<?= number_format($producto['precio'], 2) ?></span>
                    </div>

                    <!-- Stock -->
                    <div class="stock-info">
                        <i class="fas fa-box"></i>
                        <span>Stock disponible: <strong><?= $producto['stock'] ?> unidades</strong></span>
                    </div>

                    <!-- Descripción -->
                    <div class="product-description">
                        <h3>Descripción</h3>
                        <p><?= nl2br(htmlspecialchars($producto['descripcion'] ?? 'Descripción no disponible.')) ?></p>
                    </div>

                    <!-- Acciones -->
                    <div class="product-actions">
                        <?php if ($producto['stock'] > 0): ?>
                            <?php if ($logged): ?>
                                <form action="agregar_carrito.php" method="post" class="add-to-cart-form">
                                    <input type="hidden" name="producto_id" value="<?= $producto['id'] ?>">
                                    <button type="submit" class="btn-add-cart">
                                        <i class="fas fa-cart-plus"></i>
                                        Agregar al Carrito
                                    </button>
                                </form>
                            <?php else: ?>
                                <a href="login.php" class="btn-add-cart">
                                    <i class="fas fa-sign-in-alt"></i>
                                    Iniciar Sesión para Comprar
                                </a>
                            <?php endif; ?>
                        <?php else: ?>
                            <button class="btn-add-cart btn-disabled" disabled>
                                <i class="fas fa-times"></i>
                                Producto Agotado
                            </button>
                        <?php endif; ?>

                        <a href="index.php" class="btn-back">
                            <i class="fas fa-arrow-left"></i>
                            Volver a la Tienda
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Productos relacionados -->
        <?php if (!empty($productos_relacionados)): ?>
            <div class="related-products">
                <h2 class="section-title">Productos Relacionados</h2>
                <div class="products-grid">
                    <?php foreach ($productos_relacionados as $relacionado): ?>
                        <div class="product-card">
                            <a href="producto.php?id=<?= $relacionado['id'] ?>">
                                <div class="product-image">
                                    <img src="<?= isset($relacionado['imagen']) && !empty($relacionado['imagen']) ? '../Images/' . htmlspecialchars($relacionado['imagen']) : '../Images/default-product.png' ?>" 
                                         alt="<?= htmlspecialchars($relacionado['nombre']) ?>">
                                </div>
                                <div class="product-info">
                                    <h3 class="product-title"><?= htmlspecialchars($relacionado['nombre']) ?></h3>
                                    <div class="product-price">$<?= number_format($relacionado['precio'], 2) ?></div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-logo"><i class="fas fa-dumbbell"></i> FitStore Pro</div>
            <p class="footer-text">Tu tienda de confianza para suplementos deportivos de alta calidad.</p>
            <div class="social-links">
                <a href="https://www.facebook.com/search/top?q=facultad%20de%20inform%C3%A1tica%20-%20uaq" class="social-link" target="_blank"><i class="fab fa-facebook"></i></a>
                <a href="https://www.instagram.com/informaticauaq/" class="social-link" target="_blank"><i class="fab fa-instagram"></i></a>
                <a href="https://x.com/InformaticaUAQ" class="social-link" target="_blank"><i class="fab fa-twitter"></i></a>
                <a href="https://www.youtube.com/@fifuaq" class="social-link" target="_blank"><i class="fab fa-youtube"></i></a>
            </div>
            <div class="footer-divider"></div>
            <p class="copyright">&copy; <?= date("Y") ?> FitStore Pro. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script src="../Js/main.js"></script>
</body>
</html>