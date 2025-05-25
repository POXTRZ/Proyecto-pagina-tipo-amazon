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

    if (!isset($_SESSION['usuario_id'])) {
        // Mostrar carrito temporal
        if (!empty($_SESSION['carrito_temporal'])) {
            echo "<ul>";
            foreach ($_SESSION['carrito_temporal'] as $id => $item) {
                $hayProductos = true;
                echo "<li>{$item['nombre']} - {$item['cantidad']} x \${$item['precio']}</li>";
            }
            echo "</ul>";
        }
    } else {
        // Mostrar carrito de usuario logueado
        if (!empty($carrito)) {
            echo "<ul>";
            foreach ($carrito as $item) {
                $hayProductos = true;
                echo "<li>{$item['nombre']} - {$item['cantidad']} x \${$item['precio']}</li>";
            }
            echo "</ul>";
        }
    }

    if (!$hayProductos) {
        echo "<p>Tu carrito está vacío.</p>";
    }
    ?>
</body>
</html>
