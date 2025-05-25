<?php
session_start();
require '../Config/db.php';

// Función para vaciar el carrito
if (isset($_GET['vaciar']) && $_GET['vaciar'] == 1) {
    if (!isset($_SESSION['usuario_id'])) {
        unset($_SESSION['carrito_temporal']);
    } else {
        $usuario_id = $_SESSION['usuario_id'];
        $stmt = $conn->prepare("SELECT id FROM carrito WHERE id_usuario = ?");
        $stmt->execute([$usuario_id]);
        $carrito_id = $stmt->fetchColumn();

        if ($carrito_id) {
            $stmt = $conn->prepare("DELETE FROM carrito_producto WHERE id_carrito = ?");
            $stmt->execute([$carrito_id]);
        }
    }
    header("Location: carrito.php");
    exit;
}

// Función para agregar o quitar cantidad
if (isset($_GET['accion'], $_GET['producto_id'])) {
    $producto_id = $_GET['producto_id'];
    $accion = $_GET['accion'];

    if (!isset($_SESSION['usuario_id'])) {
        if (!isset($_SESSION['carrito_temporal'][$producto_id])) {
            $_SESSION['carrito_temporal'][$producto_id] = ['cantidad' => 0];
        }

        if ($accion == 'sumar') {
            $_SESSION['carrito_temporal'][$producto_id]['cantidad']++;
        } elseif ($accion == 'restar') {
            $_SESSION['carrito_temporal'][$producto_id]['cantidad']--;
            if ($_SESSION['carrito_temporal'][$producto_id]['cantidad'] <= 0) {
                unset($_SESSION['carrito_temporal'][$producto_id]);
            }
        }
    } else {
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

    if (!isset($_SESSION['usuario_id'])) {
        if (!empty($_SESSION['carrito_temporal'])) {
            echo "<ul>";
            foreach ($_SESSION['carrito_temporal'] as $id => $item) {
                $stmt = $conn->prepare("SELECT nombre, precio FROM productos WHERE id = ?");
                $stmt->execute([$id]);
                $producto = $stmt->fetch();

                if ($producto) {
                    $nombre = $producto['nombre'];
                    $precio = $producto['precio'];
                    $cantidad = $item['cantidad'];
                    $subtotal = $cantidad * $precio;
                    $total += $subtotal;
                    $hayProductos = true;

                    echo "<li>
                        {$nombre} - {$cantidad} x \$" . number_format($precio, 2) . "
                        <a href='?accion=sumar&producto_id=$id'>➕</a>
                        <a href='?accion=restar&producto_id=$id'>➖</a>
                    </li>";
                }
            }
            echo "</ul>";
        }
    } else {
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
    }

    if ($hayProductos) {
        echo "<h3>Total: \$" . number_format($total, 2) . "</h3>";
    } else {
        echo "<p>Tu carrito está vacío.</p>";
    }
    ?>
</body>
</html>
