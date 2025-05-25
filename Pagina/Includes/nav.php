<?php
// Si no se han definido las categorías, las obtenemos
if (!isset($cats)) {
    require_once '../Config/db.php';
    $cats = $conn->query("SELECT * FROM categorias ORDER BY nombre")->fetchAll();
}
?>
<nav class="nav">
    <link rel="stylesheet" href="../Css/nav.css">
    
    <div class="logo">
        <i class="fas fa-dumbbell"></i>
        <span>FitStore Pro</span>
    </div>
    
    <ul class="nav-links">
        <li><a href="index.php">Inicio</a></li>
        <li class="dropdown">
            <a href="#">Categorías <i class="fas fa-chevron-down"></i></a>
            <div class="dropdown-menu">
                <?php foreach ($cats as $c): ?>
                    <a href="categoria.php?id=<?=$c['id']?>"><?=htmlspecialchars($c['nombre'])?></a>
                <?php endforeach; ?>
            </div>
        </li>
        <li><a href="ofertas.php">Ofertas</a></li>
        <li><a href="nuevos.php">Nuevos</a></li>
        <li><a href="contacto.php">Contacto</a></li>
    </ul>
    
    <div class="user-controls">
        <?php if ($logged): ?>
            <span>¡Hola, <?=htmlspecialchars($user)?>!</span>
            <a href="perfil.php" title="Perfil"><i class="fas fa-user-circle"></i></a>
            <a href="logout.php" title="Salir"><i class="fas fa-sign-out-alt"></i></a>
        <?php else: ?>
            <a href="login.php">Ingresar</a>
            <a href="register.php">Registrarse</a>
        <?php endif; ?>
        <a href="carrito.php" class="cart-icon">
            <i class="fas fa-shopping-cart"></i>
            <span class="cart-count"><?=$cart_count?></span>
        </a>
    </div>
</nav>