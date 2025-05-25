<?php
session_start();
require '../Config/db.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Carrito</title>
</head>
<body>
    <h2>Tu carrito</h2>

    <?php
    $hayProductos = false;

    // Si el usuario NO ha iniciado sesión, usamos el carrito temporal
    if (!isset($_SESSION['usuario_id'])) {
        if (!empty($_SESSION['carrito_temporal'])) {
            echo "<ul>";
            foreach ($_SESSION['carrito_temporal'] as $id => $item) {
                $hayProductos = true;
                echo "<li>{$item['nombre']} - {$item['cantidad']} x \${$item['precio']}</li>";
            }
            echo "</ul>";
        }
    } else {
        // Usuario logueado: consultamos carrito desde la base de datos
        $usuario_id = $_SESSION['usuario_id'];

        // Conseguimos el ID del carrito del usuario
        $stmt = $conn->prepare("SELECT id FROM carrito WHERE id_usuario = ?");
        $stmt->execute([$usuario_id]);
        $carrito_id = $stmt->fetchColumn();

        if ($carrito_id) {
            // Conseguimos los productos del carrito
            $sql = "SELECT p.nombre, p.precio, cp.cantidad
                    FROM carrito_producto cp
                    JOIN productos p ON cp.id_producto = p.id
                    WHERE cp.id_carrito = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$carrito_id]);
            $productos = $stmt->fetchAll();

            if ($productos) {
                echo "<ul>";
                foreach ($productos as $item) {
                    $hayProductos = true;
                    echo "<li>{$item['nombre']} - {$item['cantidad']} x \$" . number_format($item['precio'], 2) . "</li>";
                }
                echo "</ul>";
            }
        }
    }

    if (!$hayProductos) {
        echo "<p>Tu carrito está vacío.</p>";
    }
    ?>
</body>
</html>
