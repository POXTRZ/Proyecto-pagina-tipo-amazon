<?php
// /Pagina/Includes/nav.php

// Si no tenemos $cats, lo cargamos desde la base de datos
if (!isset($cats)) {
    require_once __DIR__ . '/../Config/db.php';
    $cats = $conn
        ->query("SELECT * FROM categorias ORDER BY nombre")
        ->fetchAll();
}

// Asegúrate de que el CSS apunte bien desde /Pagina/Public
$cssPath = '../Css/nav.css';
?>
<link rel="stylesheet" href="<?= htmlspecialchars($cssPath) ?>">

<nav class="nav">
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
                    <!-- Apunta a Public/categorias.php -->
                    <a href="categorias.php?id=<?= $c['id'] ?>">
                        <?= htmlspecialchars($c['nombre']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </li>
        
        <li><a href="https://wa.me/524642057146" target="_blank">Contacto</a></li>
    </ul>
    
    <div class="user-controls">
        <?php if (!empty($logged)): ?>
            <span>¡Hola, <?= htmlspecialchars($user) ?>!</span>
            <a href="perfil.php" title="Perfil">
                <i class="fas fa-user-circle"></i>
            </a>
            <!-- Asegúrate de que el nombre del archivo coincida: Logout.php vs logout.php -->
            <a href="Logout.php" title="Salir">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        <?php else: ?>
            <a href="login.php">Ingresar</a>
            <a href="register.php">Registrarse</a>
        <?php endif; ?>

        <a href="carrito.php" class="cart-icon">
            <i class="fas fa-shopping-cart"></i>
            <span class="cart-count"><?= $cart_count ?></span>
        </a>
    </div>
</nav>
