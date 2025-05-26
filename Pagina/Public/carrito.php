<?php
session_start();
require '../Config/db.php';

// Solo permitir acceso si está logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// Función para vaciar el carrito
if (isset($_GET['vaciar']) && $_GET['vaciar'] == 1) {
    $usuario_id = $_SESSION['usuario_id'];
    $stmt = $conn->prepare("SELECT id FROM carrito WHERE id_usuario = ?");
    $stmt->execute([$usuario_id]);
    $carrito_id = $stmt->fetchColumn();

    if ($carrito_id) {
        $stmt = $conn->prepare("DELETE FROM carrito_producto WHERE id_carrito = ?");
        $stmt->execute([$carrito_id]);
    }
    header("Location: carrito.php");
    exit;
}

// Botón "Comprar Productos": vacía el carrito y redirige a index.php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comprar'])) {
    $usuario_id = $_SESSION['usuario_id'];
    $stmt = $conn->prepare("SELECT id FROM carrito WHERE id_usuario = ?");
    $stmt->execute([$usuario_id]);
    $carrito_id = $stmt->fetchColumn();

    if ($carrito_id) {
        $stmt = $conn->prepare("DELETE FROM carrito_producto WHERE id_carrito = ?");
        $stmt->execute([$carrito_id]);
    }
    header("Location: index.php");
    exit;
}

// Función para agregar o quitar cantidad
if (isset($_GET['accion'], $_GET['producto_id'])) {
    $producto_id = $_GET['producto_id'];
    $accion = $_GET['accion'];

    $usuario_id = $_SESSION['usuario_id'];
    $stmt = $conn->prepare("SELECT id FROM carrito WHERE id_usuario = ?");
    $stmt->execute([$usuario_id]);
    $carrito_id = $stmt->fetchColumn();

    if ($carrito_id) {
        if ($accion == 'sumar') {
            // Si ya existe, sumar cantidad
            $stmt = $conn->prepare("SELECT cantidad FROM carrito_producto WHERE id_carrito = ? AND id_producto = ?");
            $stmt->execute([$carrito_id, $producto_id]);
            $cantidadActual = $stmt->fetchColumn();

            if ($cantidadActual !== false) {
                $stmt = $conn->prepare("UPDATE carrito_producto SET cantidad = cantidad + 1 WHERE id_carrito = ? AND id_producto = ?");
                $stmt->execute([$carrito_id, $producto_id]);
            }
        } elseif ($accion == 'restar') {
            // Restar y eliminar si llega a 0
            $stmt = $conn->prepare("SELECT cantidad FROM carrito_producto WHERE id_carrito = ? AND id_producto = ?");
            $stmt->execute([$carrito_id, $producto_id]);
            $cantidadActual = $stmt->fetchColumn();

            if ($cantidadActual !== false) {
                if ($cantidadActual <= 1) {
                    $stmt = $conn->prepare("DELETE FROM carrito_producto WHERE id_carrito = ? AND id_producto = ?");
                    $stmt->execute([$carrito_id, $producto_id]);
                } else {
                    $stmt = $conn->prepare("UPDATE carrito_producto SET cantidad = cantidad - 1 WHERE id_carrito = ? AND id_producto = ?");
                    $stmt->execute([$carrito_id, $producto_id]);
                }
            }
        }
    }

    header("Location: carrito.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras – FitStore Pro</title>
    <link rel="stylesheet" href="../Css/carrito.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="particles"></div>
    
    <div class="cart-container">
        <!-- Header del carrito -->
        <div class="cart-header">
            <h1 class="cart-title">Mi Carrito</h1>
            <p class="cart-subtitle">Revisa y gestiona tus productos seleccionados</p>
        </div>

        <!-- Navegación superior -->
        <nav class="cart-nav">
            <a href="index.php" class="nav-link">
                <i class="fas fa-home"></i>
                Volver a Home
            </a>
            <a href="perfil.php" class="nav-link">
                <i class="fas fa-user"></i>
                Mi Perfil
            </a>
            <a href="carrito.php?vaciar=1" class="nav-link danger" 
               onclick="return confirm('¿Estás seguro de vaciar el carrito?');">
                <i class="fas fa-trash"></i>
                Vaciar Carrito
            </a>
        </nav>

        <?php
        $hayProductos = false;
        $total = 0;

        $usuario_id = $_SESSION['usuario_id'];

        $stmt = $conn->prepare("SELECT id FROM carrito WHERE id_usuario = ?");
        $stmt->execute([$usuario_id]);
        $carrito_id = $stmt->fetchColumn();

        if ($carrito_id) {
            $sql = "SELECT p.id, p.nombre, p.precio, cp.cantidad
                    FROM carrito_producto cp
                    JOIN productos p ON cp.id_producto = p.id
                    WHERE cp.id_carrito = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$carrito_id]);
            $productos = $stmt->fetchAll();

            if ($productos) {
                $hayProductos = true;
                echo '<div class="cart-items">';
                echo '<ul class="cart-list">';
                
                $delay = 0;
                foreach ($productos as $item) {
                    $subtotal = $item['cantidad'] * $item['precio'];
                    $total += $subtotal;

                    echo '<li class="cart-item" style="--delay: ' . $delay . ';">
                            <div class="item-info">
                                <h3 class="item-name">' . htmlspecialchars($item['nombre']) . '</h3>
                                <p class="item-price">Precio unitario: $' . number_format($item['precio'], 2) . '</p>
                                <p class="item-subtotal">Subtotal: $' . number_format($subtotal, 2) . '</p>
                            </div>
                            <div class="quantity-controls">
                                <a href="?accion=restar&producto_id=' . $item['id'] . '" class="quantity-btn">
                                    <i class="fas fa-minus"></i>
                                </a>
                                <span class="quantity-display">' . $item['cantidad'] . '</span>
                                <a href="?accion=sumar&producto_id=' . $item['id'] . '" class="quantity-btn">
                                    <i class="fas fa-plus"></i>
                                </a>
                            </div>
                          </li>';
                    $delay++;
                }
                
                echo '</ul>';
                echo '</div>';
            }
        }

        if ($hayProductos) {
            echo '<div class="cart-summary">
                    <h3 class="summary-title">Resumen del Pedido</h3>
                    <div class="total-amount">Total: $' . number_format($total, 2) . '</div>
                    <form method="post" class="checkout-form">
                        <button type="submit" name="comprar" class="checkout-btn">
                            <i class="fas fa-credit-card"></i>
                            Proceder al Pago
                        </button>
                    </form>
                  </div>';
        } else {
            echo '<div class="empty-cart">
                    <div class="empty-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <p class="empty-message">Tu carrito está vacío</p>
                    <a href="index.php" class="empty-cta">
                        <i class="fas fa-shopping-bag"></i>
                        Comenzar a Comprar
                    </a>
                  </div>';
        }
        ?>
    </div>
</body>
</html>