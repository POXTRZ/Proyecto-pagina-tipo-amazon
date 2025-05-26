<?php
// /Pagina/Public/categorias.php
require_once __DIR__ . '/../Includes/categoria.php';
// ya tienes: $logged, $user, $cart_count, $categoria, $productos, $cats
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($categoria['nombre']) ?> – FitStore Pro</title>
  <!-- 1) Importar estilos generales antes de los específicos -->
  <link rel="stylesheet" href="../Css/index.css">
  <link rel="stylesheet" href="../Css/categorias.css">
  <!-- QUITAR esta línea porque nav.php ya incluye nav.css -->
  <!-- <link rel="stylesheet" href="../Css/nav.css"> -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
  <!-- AGREGAR la clase "header" aquí -->
  <header class="header">
    <?php include __DIR__ . '/../Includes/nav.php'; ?>
  </header>

  <!-- Hero de la categoría -->
  <section class="category-hero">
    <h1><?= htmlspecialchars($categoria['nombre']) ?></h1>
    <p>Explora nuestra selección de <?= strtolower(htmlspecialchars($categoria['nombre'])) ?></p>
  </section>

  <!-- Resto del contenido igual... -->
  <section class="category-products-section">
    <?php if (count($productos)): ?>
      <!-- 2) Controles de filtro y orden -->
      <div class="controls-container">
        <div class="filter-sort-controls">
          <div class="filter-group">
            <label for="price-filter">Precio hasta:</label>
            <input type="range" id="price-filter"
                   min="0"
                   max="<?= max(array_column($productos,'precio')) ?>"
                   value="<?= max(array_column($productos,'precio')) ?>">
            <span id="price-filter-value">
              $<?= number_format(max(array_column($productos,'precio')),2) ?>
            </span>
          </div>
          <div class="filter-group">
            <label><input type="checkbox" id="stock-filter" checked> Solo disponibles</label>
          </div>
          <div class="sort-group">
            <label for="sort-by">Ordenar por:</label>
            <select id="sort-by">
              <option value="name_asc">Nombre A-Z</option>
              <option value="name_desc">Nombre Z-A</option>
              <option value="price_asc">Precio ↑</option>
              <option value="price_desc">Precio ↓</option>
            </select>
          </div>
        </div>
      </div>

      <div class="products-grid">
        <?php foreach ($productos as $p): ?>
          <div class="product-card"
               data-price="<?= $p['precio'] ?>"
               data-stock="<?= $p['stock'] ?>">
            <!-- enlace a detalle -->
           <a href="producto.php?id=<?= $p['id'] ?>" class="product-link">
              <div class="product-image">
                <img src="<?= isset($p['imagen']) && !empty($p['imagen']) ? '../Images/' . htmlspecialchars($p['imagen']) : '../Images/default-product.png' ?>"
                     alt="<?= htmlspecialchars($p['nombre']) ?>">
              </div>
              <?php if ($p['stock'] == 0): ?>
                <div class="product-badge stock-out">Agotado</div>
              <?php elseif ($p['stock'] < 5): ?>
                <div class="product-badge stock-warning">Pocas unidades</div>
              <?php endif; ?>
              <div class="product-info">
                <h3 class="product-title"><?= htmlspecialchars($p['nombre']) ?></h3>
                <p class="product-price">$<?= number_format($p['precio'],2) ?></p>
              </div>
            </a>
            <!-- formulario agregar al carrito -->
            <?php if ($p['stock'] > 0): ?>
              <?php if ($logged): ?>
                <form method="post" action="agregar_carrito.php">
                  <input type="hidden" name="producto_id" value="<?= $p['id'] ?>">
                  <button type="submit" class="add-to-cart">
                    <i class="fas fa-cart-plus"></i>
                    Agregar al Carrito
                  </button>
                </form>
              <?php else: ?>
                <a href="login.php" class="add-to-cart">
                  <i class="fas fa-cart-plus"></i>
                  Agregar al Carrito
                </a>
              <?php endif; ?>
            <?php else: ?>
              <button class="add-to-cart btn-disabled" disabled>
                <i class="fas fa-times"></i>
                Producto Agotado
              </button>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="no-products-message">
        <i class="fas fa-box-open"></i>
        <p>No hay productos en esta categoría.</p>
      </div>
    <?php endif; ?>
  </section>
  
  <footer class="footer">
        <div class="footer-content">
            <div class="footer-logo"><i class="fas fa-dumbbell"></i> FitStore Pro</div>
            <p class="footer-text">Tu tienda de confianza para suplementos deportivos de alta calidad. Enfócate en tu entrenamiento, nosotros nos encargamos del resto.</p>
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

  <script src="../Js/categorias.js"></script>
  <script src="../Js/main.js"></script>
</body>
</html>
