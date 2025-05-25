<?php
session_start();
require '../Config/db.php';

// Checar login
$logged = !empty($_SESSION['usuario_id']);
$user   = $_SESSION['nombre_usuario'] ?? '';

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

// Consultas directas
$destacados = $conn->query("SELECT * FROM productos WHERE activo=1 ORDER BY id LIMIT 6")->fetchAll();
$ofertas    = $conn->query("SELECT * FROM productos WHERE activo=1 ORDER BY precio ASC LIMIT 6")->fetchAll();
$nuevos     = $conn->query("SELECT * FROM productos WHERE activo=1 ORDER BY fecha_agregado DESC LIMIT 6")->fetchAll();
$cats       = $conn->query("SELECT * FROM categorias ORDER BY nombre")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitStore Pro - Suplementos Deportivos</title>
    <link rel="stylesheet" href="../Css/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600;700;800;900&family=Cormorant+Garamond:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <header class="header">
        <?php include '../Includes/nav.php'; ?>
    </header>

    <section class="hero" id="inicio">
        <div class="particles"></div>
        <div class="hero-content">
            <h1 class="hero-title">SUPERA TUS LÍMITES</h1>
            <p class="hero-subtitle">Los mejores suplementos deportivos para alcanzar tus objetivos</p>
            <a href="#productos" class="cta-button">EXPLORAR PRODUCTOS</a>
        </div>
    </section>

    <section class="categories" id="categorias">
        <div class="fade-in">
            <h2 class="section-title">Categorías Principales</h2>
            <div class="categories-grid">
                <?php
                $icons = [
                    '<i class="fas fa-dumbbell"></i>', 
                    '<i class="fas fa-bolt"></i>', 
                    '<i class="fas fa-pills"></i>',
                    '<i class="fas fa-heartbeat"></i>', 
                    '<i class="fas fa-running"></i>', 
                    '<i class="fas fa-leaf"></i>'
                ];
                $i = 0;
                foreach (array_slice($cats, 0, 6) as $c): 
                ?>
                <div class="category-card">
                    <div class="category-icon"><?= $icons[$i % count($icons)] ?></div>
                    <h3 class="category-title"><?=htmlspecialchars($c['nombre'])?></h3>
                    <p>Productos de la más alta calidad para tu rendimiento</p>
                    <a href="categoria.php?id=<?=$c['id']?>" class="cta-button" style="margin-top: 1rem; padding: 0.5rem 1rem; font-size: 0.9rem;">Ver Productos</a>
                </div>
                <?php $i++; endforeach; ?>
            </div>
        </div>
    </section>

    <section class="featured-products" id="productos">
        <div class="fade-in">
            <h2 class="section-title">Productos Destacados</h2>
            <div class="products-grid">
                <?php foreach ($destacados as $p): ?>
                <div class="product-card">
                    <div class="product-image">
                        <img src="../Images/<?=htmlspecialchars($p['imagen'])?>" alt="<?=htmlspecialchars($p['nombre'])?>">
                        <?php if ($p['stock'] <= 5 && $p['stock'] > 0): ?>
                            <div class="product-badge stock-warning">¡Últimas unidades!</div>
                        <?php elseif ($p['stock'] == 0): ?>
                            <div class="product-badge stock-out">Agotado</div>
                        <?php endif; ?>
                    </div>
                    <div class="product-info">
                        <h3 class="product-title"><?=htmlspecialchars($p['nombre'])?></h3>
                        <div class="product-price">\$<?=number_format($p['precio'],2)?></div>
                        <div class="product-actions">
                            <?php if ($p['stock'] > 0): ?>
                                <form action="agregar_carrito.php" method="post">
                                    <input type="hidden" name="producto_id" value="<?= $p['id'] ?>">
                                    <button type="submit" class="add-to-cart"><i class="fas fa-cart-plus"></i> Agregar al Carrito</button>
                                </form>
                            <?php else: ?>
                                <button type="button" class="add-to-cart btn-disabled" disabled>Agotado</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="featured-products" style="background-color: rgba(10, 10, 10, 0.2);">
        <div class="fade-in">
            <h2 class="section-title">Ofertas Especiales</h2>
            <div class="products-grid">
                <?php foreach ($ofertas as $o): ?>
                <div class="product-card">
                    <div class="product-image">
                        <img src="../Images/<?=htmlspecialchars($o['imagen'])?>" alt="<?=htmlspecialchars($o['nombre'])?>">
                        <div class="product-badge" style="background-color: var(--text-light);">¡Oferta!</div>
                        <?php if ($o['stock'] <= 5 && $o['stock'] > 0): ?>
                            <div class="product-badge stock-warning" style="left: 10px; right: auto;">¡Últimas unidades!</div>
                        <?php elseif ($o['stock'] == 0): ?>
                            <div class="product-badge stock-out" style="left: 10px; right: auto;">Agotado</div>
                        <?php endif; ?>
                    </div>
                    <div class="product-info">
                        <h3 class="product-title"><?=htmlspecialchars($o['nombre'])?></h3>
                        <div class="product-price">\$<?=number_format($o['precio'],2)?></div>
                        <div class="product-actions">
                            <?php if ($o['stock'] > 0): ?>
                                <form action="agregar_carrito.php" method="post">
                                    <input type="hidden" name="producto_id" value="<?= $o['id'] ?>">
                                    <button type="submit" class="add-to-cart"><i class="fas fa-cart-plus"></i> ¡Aprovechar!</button>
                                </form>
                            <?php else: ?>
                                <button type="button" class="add-to-cart btn-disabled" disabled>Agotado</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="footer-content">
            <div class="footer-logo"><i class="fas fa-dumbbell"></i> FitStore Pro</div>
            <p class="footer-text">Tu tienda de confianza para suplementos deportivos de alta calidad. Enfócate en tu entrenamiento, nosotros nos encargamos del resto.</p>
            <div class="social-links">
                <a href="#" class="social-link"><i class="fab fa-facebook"></i></a>
                <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                <a href="#" class="social-link"><i class="fab fa-youtube"></i></a>
            </div>
            <div class="footer-divider"></div>
            <p class="copyright">&copy; <?= date("Y") ?> FitStore Pro. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script src="../Js/index.js"></script>
</body>
</html>
