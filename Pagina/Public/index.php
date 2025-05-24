<?php
session_start();
require_once '../Config/db.php';

// Verificar si el usuario está logueado
$usuario_logueado = isset($_SESSION['usuario_id']);
$nombre_usuario = $usuario_logueado ? $_SESSION['nombre_usuario'] : '';

// Obtener productos destacados
$stmt = $pdo->query("SELECT * FROM productos WHERE activo = 1 ORDER BY id LIMIT 8");
$productosDestacados = $stmt->fetchAll();

// Obtener ofertas especiales (productos más baratos)
$stmt = $pdo->query("SELECT * FROM productos WHERE activo = 1 ORDER BY precio ASC LIMIT 6");
$ofertasEspeciales = $stmt->fetchAll();

// Obtener categorías para el menú
$stmt = $pdo->query("SELECT * FROM categorias ORDER BY nombre");
$categorias = $stmt->fetchAll();

// Obtener productos nuevos
$stmt = $pdo->query("SELECT * FROM productos WHERE activo = 1 ORDER BY fecha_agregado DESC LIMIT 6");
$productosNuevos = $stmt->fetchAll();

// Función para obtener cantidad de productos en carrito
function obtenerCantidadCarrito() {
    global $pdo;
    if (!isset($_SESSION['usuario_id'])) return 0;
    
    $stmt = $pdo->prepare("
        SELECT SUM(cp.cantidad) as total 
        FROM carrito c 
        JOIN carrito_producto cp ON c.id = cp.id_carrito 
        WHERE c.id_usuario = ?
    ");
    $stmt->execute([$_SESSION['usuario_id']]);
    $result = $stmt->fetch();
    return $result['total'] ?? 0;
}

$cantidadCarrito = obtenerCantidadCarrito();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitStore - Tu Tienda de Suplementos Deportivos</title>
    <link rel="stylesheet" href="/Pagina/Css/index.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- HEADER -->
    <header class="main-header">
        <div class="container">
            <!-- Top Bar -->
            <div class="top-bar">
                <div class="logo">
                    <h2><i class="fas fa-dumbbell"></i> FitStore</h2>
                </div>
                
                <!-- Buscador -->
                <div class="search-bar">
                    <form action="buscar.php" method="GET">
                        <input type="text" name="q" placeholder="Buscar productos..." required>
                        <button type="submit"><i class="fas fa-search"></i></button>
                    </form>
                </div>

                <!-- Usuario y Carrito -->
                <div class="user-actions">
                    <?php if ($usuario_logueado): ?>
                        <div class="user-menu">
                            <span>¡Hola, <?php echo htmlspecialchars($nombre_usuario); ?>!</span>
                            <div class="dropdown">
                                <a href="perfil.php"><i class="fas fa-user"></i> Mi Perfil</a>
                                <a href="mis_ordenes.php"><i class="fas fa-box"></i> Mis Órdenes</a>
                                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="auth-links">
                            <a href="login.php"><i class="fas fa-sign-in-alt"></i> Ingresar</a>
                            <a href="register.php"><i class="fas fa-user-plus"></i> Registrarse</a>
                        </div>
                    <?php endif; ?>
                    
                    <a href="carrito.php" class="cart-link">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="cart-count"><?php echo $cantidadCarrito; ?></span>
                    </a>
                </div>
            </div>

            <!-- Navigation Menu -->
            <nav class="main-nav">
                <ul>
                    <li><a href="index.php"><i class="fas fa-home"></i> Inicio</a></li>
                    <li class="dropdown">
                        <a href="#"><i class="fas fa-list"></i> Categorías <i class="fas fa-chevron-down"></i></a>
                        <ul class="dropdown-menu">
                            <?php foreach ($categorias as $categoria): ?>
                                <li><a href="categoria.php?id=<?php echo $categoria['id']; ?>"><?php echo htmlspecialchars($categoria['nombre']); ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                    <li><a href="ofertas.php"><i class="fas fa-fire"></i> Ofertas</a></li>
                    <li><a href="nuevos.php"><i class="fas fa-star"></i> Nuevos</a></li>
                    <li><a href="contacto.php"><i class="fas fa-envelope"></i> Contacto</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- HERO SECTION -->
    <section class="hero">
        <div class="hero-content">
            <h1 class="glitch-effect" data-text="Potencia Tu Entrenamiento">Potencia Tu Entrenamiento</h1>
            <p>Los mejores suplementos deportivos para alcanzar tus metas fitness</p>
            <a href="#productos-destacados" class="cta-button">Ver Productos</a>
        </div>
    </section>

    <!-- PRODUCTOS DESTACADOS -->
    <section id="productos-destacados" class="featured-products">
        <div class="container">
            <h2 class="section-title">
                <i class="fas fa-star"></i> Productos Destacados
            </h2>
            
            <div class="products-grid">
                <?php foreach ($productosDestacados as $producto): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <img src="../Images/<?php echo htmlspecialchars($producto['imagen']); ?>" 
                                 alt="<?php echo htmlspecialchars($producto['nombre']); ?>"
                                 onerror="this.src='../Images/placeholder.jpg'">
                            <?php if ($producto['stock'] <= 5): ?>
                                <span class="stock-badge low-stock">¡Últimas unidades!</span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="product-info">
                            <h3><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                            <p class="product-description">
                                <?php echo htmlspecialchars(substr($producto['descripcion'], 0, 80)) . '...'; ?>
                            </p>
                            <div class="product-price">
                                <span class="price">$<?php echo number_format($producto['precio'], 2); ?></span>
                            </div>
                            <div class="product-stock">
                                <span>Stock: <?php echo $producto['stock']; ?> unidades</span>
                            </div>
                            
                            <div class="product-actions">
                                <?php if ($usuario_logueado): ?>
                                    <form action="agregar_carrito.php" method="POST" class="add-to-cart-form">
                                        <input type="hidden" name="producto_id" value="<?php echo $producto['id']; ?>">
                                        <input type="number" name="cantidad" value="1" min="1" max="<?php echo $producto['stock']; ?>" class="quantity-input">
                                        <button type="submit" class="btn-add-cart">
                                            <i class="fas fa-cart-plus"></i> Agregar
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <a href="login.php" class="btn-login-required">
                                        <i class="fas fa-sign-in-alt"></i> Inicia sesión para comprar
                                    </a>
                                <?php endif; ?>
                                <a href="producto.php?id=<?php echo $producto['id']; ?>" class="btn-view-details">
                                    <i class="fas fa-eye"></i> Ver detalles
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- OFERTAS ESPECIALES -->
    <section class="special-offers">
        <div class="container">
            <h2 class="section-title">
                <i class="fas fa-fire"></i> Ofertas Especiales
            </h2>
            
            <div class="offers-grid">
                <?php foreach ($ofertasEspeciales as $oferta): ?>
                    <div class="offer-card">
                        <div class="offer-badge">¡OFERTA!</div>
                        <div class="product-image">
                            <img src="../Images/<?php echo htmlspecialchars($oferta['imagen']); ?>" 
                                 alt="<?php echo htmlspecialchars($oferta['nombre']); ?>"
                                 onerror="this.src='../Images/placeholder.jpg'">
                        </div>
                        
                        <div class="offer-info">
                            <h3><?php echo htmlspecialchars($oferta['nombre']); ?></h3>
                            <div class="offer-price">
                                <span class="price">$<?php echo number_format($oferta['precio'], 2); ?></span>
                            </div>
                            
                            <div class="offer-actions">
                                <?php if ($usuario_logueado): ?>
                                    <form action="agregar_carrito.php" method="POST">
                                        <input type="hidden" name="producto_id" value="<?php echo $oferta['id']; ?>">
                                        <button type="submit" class="btn-offer">
                                            <i class="fas fa-bolt"></i> ¡Aprovechar Oferta!
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <a href="login.php" class="btn-offer">
                                        <i class="fas fa-sign-in-alt"></i> Inicia sesión
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- PRODUCTOS NUEVOS -->
    <section class="new-products">
        <div class="container">
            <h2 class="section-title">
                <i class="fas fa-sparkles"></i> Productos Nuevos
            </h2>
            
            <div class="products-slider">
                <?php foreach ($productosNuevos as $nuevo): ?>
                    <div class="new-product-card">
                        <span class="new-badge">¡NUEVO!</span>
                        <img src="../Images/<?php echo htmlspecialchars($nuevo['imagen']); ?>" 
                             alt="<?php echo htmlspecialchars($nuevo['nombre']); ?>"
                             onerror="this.src='../Images/placeholder.jpg'">
                        <h4><?php echo htmlspecialchars($nuevo['nombre']); ?></h4>
                        <span class="price">$<?php echo number_format($nuevo['precio'], 2); ?></span>
                        <a href="producto.php?id=<?php echo $nuevo['id']; ?>" class="btn-new">Ver Producto</a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- CATEGORÍAS DESTACADAS -->
    <section class="featured-categories">
        <div class="container">
            <h2 class="section-title">
                <i class="fas fa-th-large"></i> Categorías Principales
            </h2>
            
            <div class="categories-grid">
                <div class="category-card">
                    <a href="categorias/proteinas.php">
                        <i class="fas fa-drumstick-bite"></i>
                        <h3>Proteínas</h3>
                        <p>Whey, Casein, Veganas</p>
                    </a>
                </div>
                
                <div class="category-card">
                    <a href="categorias/creatinas.php">
                        <i class="fas fa-fire-flame-curved"></i>
                        <h3>Creatinas</h3>
                        <p>Monohidrato, HCL, Kre-Alkalyn</p>
                    </a>
                </div>
                
                <div class="category-card">
                    <a href="categorias/preentrenos.php">
                        <i class="fas fa-bolt"></i>
                        <h3>Pre-Entrenos</h3>
                        <p>Energía y Focus</p>
                    </a>
                </div>
                
                <div class="category-card">
                    <a href="categorias/aminoacidos.php">
                        <i class="fas fa-pills"></i>
                        <h3>Aminoácidos</h3>
                        <p>BCAA, EAA, Glutamina</p>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="main-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3><i class="fas fa-dumbbell"></i> FitStore</h3>
                    <p>Tu tienda de confianza para suplementos deportivos de la más alta calidad.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                
                <div class="footer-section">
                    <h4>Categorías</h4>
                    <ul>
                        <li><a href="categorias/proteinas.php">Proteínas</a></li>
                        <li><a href="categorias/creatinas.php">Creatinas</a></li>
                        <li><a href="categorias/preentrenos.php">Pre-Entrenos</a></li>
                        <li><a href="categorias/aminoacidos.php">Aminoácidos</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h4>Cuenta</h4>
                    <ul>
                        <?php if ($usuario_logueado): ?>
                            <li><a href="perfil.php">Mi Perfil</a></li>
                            <li><a href="mis_ordenes.php">Mis Órdenes</a></li>
                            <li><a href="carrito.php">Mi Carrito</a></li>
                            <li><a href="logout.php">Cerrar Sesión</a></li>
                        <?php else: ?>
                            <li><a href="login.php">Iniciar Sesión</a></li>
                            <li><a href="register.php">Registrarse</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h4>Información</h4>
                    <ul>
                        <li><a href="sobre_nosotros.php">Sobre Nosotros</a></li>
                        <li><a href="envios.php">Envíos</a></li>
                        <li><a href="devoluciones.php">Devoluciones</a></li>
                        <li><a href="contacto.php">Contacto</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2025 FitStore. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="../Js/main.js"></script>
    <script>
        // Función para agregar al carrito con AJAX
        document.querySelectorAll('.add-to-cart-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                
                fetch('agregar_carrito.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Actualizar contador del carrito
                        document.querySelector('.cart-count').textContent = data.cart_count;
                        
                        // Mostrar mensaje de éxito
                        showNotification('Producto agregado al carrito', 'success');
                    } else {
                        showNotification(data.message || 'Error al agregar producto', 'error');
                    }
                })
                .catch(error => {
                    showNotification('Error de conexión', 'error');
                });
            });
        });

        // Función para mostrar notificaciones
        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        // Efecto de glitch en el título
        const glitchText = document.querySelector('.glitch-effect');
        
    </script>
</body>
</html>


