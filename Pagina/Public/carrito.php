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
    <title>Carrito</title>
</head>
<body>
    <h2>Tu carrito</h2>

    <div style="margin-bottom: 20px;">
        <a href="index.php">🏠 Volver a Home</a> |
        <a href="perfil.php">👤 Perfil</a> |
        <a href="carrito.php?vaciar=1" onclick="return confirm('¿Estás seguro de vaciar el carrito?');">🗑️ Vaciar Carrito</a>
    </div>

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
            echo "<ul>";
            foreach ($productos as $item) {
                $subtotal = $item['cantidad'] * $item['precio'];
                $total += $subtotal;
                $hayProductos = true;

                echo "<li>
                    {$item['nombre']} - {$item['cantidad']} x \$" . number_format($item['precio'], 2) . "
                    <a href='?accion=sumar&producto_id={$item['id']}'>➕</a>
                    <a href='?accion=restar&producto_id={$item['id']}'>➖</a>
                </li>";
            }
            echo "</ul>";
        }
    }

    if ($hayProductos) {
        echo "<h3>Total: \$" . number_format($total, 2) . "</h3>";
        // Botón Comprar Productos
        echo '<form method="post" style="margin-top:20px;">
                <button type="submit" name="comprar" style="padding:10px 20px; font-size:1rem; background:#28a745; color:#fff; border:none; border-radius:5px; cursor:pointer;">
                    Comprar Productos
                </button>
              </form>';
    } else {
        echo "<p>Tu carrito está vacío.</p>";
    }
    ?>
</body>
</html>