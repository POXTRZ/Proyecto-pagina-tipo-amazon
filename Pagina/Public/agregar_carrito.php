<?php
session_start();
include __DIR__ . '/../Config/db.php';

$id_producto = isset($_POST['id_producto']) ? intval($_POST['id_producto']) : 0;
$cantidad = isset($_POST['cantidad']) ? max(1, intval($_POST['cantidad'])) : 1; // No permitir negativos

if ($id_producto <= 0) {
    die("Producto inválido.");
}

// Obtener precio del producto
$stmt = $conn->prepare("SELECT nombre, precio FROM productos WHERE id = ?");
$stmt->execute([$id_producto]);
$producto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$producto || $producto['precio'] < 0) {
    die("Producto no encontrado o precio inválido.");
}

if (!isset($_SESSION['usuario_id'])) {
    // Carrito temporal
    if (!isset($_SESSION['carrito_temporal'])) {
        $_SESSION['carrito_temporal'] = [];
    }

    if (isset($_SESSION['carrito_temporal'][$id_producto])) {
        $_SESSION['carrito_temporal'][$id_producto]['cantidad'] += $cantidad;
    } else {
        $_SESSION['carrito_temporal'][$id_producto] = [
            'nombre' => $producto['nombre'],
            'precio' => $producto['precio'],
            'cantidad' => $cantidad
        ];
    }

    header("Location: carrito.php");
    exit();
}

// Si está logeado, guardar en la base de datos
$id_usuario = $_SESSION['usuario_id'];

$stmt = $conn->prepare("SELECT cantidad FROM carrito WHERE id_usuario = ? AND id_producto = ?");
$stmt->execute([$id_usuario, $id_producto]);

if ($stmt->rowCount() > 0) {
    $update = $conn->prepare("UPDATE carrito SET cantidad = cantidad + ? WHERE id_usuario = ? AND id_producto = ?");
    $update->execute([$cantidad, $id_usuario, $id_producto]);
} else {
    $insert = $conn->prepare("INSERT INTO carrito (id_usuario, id_producto, cantidad) VALUES (?, ?, ?)");
    $insert->execute([$id_usuario, $id_producto, $cantidad]);
}

header("Location: carrito.php");
exit();
