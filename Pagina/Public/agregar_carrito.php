<?php
session_start();
require '../Config/db.php';

// Solo permitir agregar si está logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $producto_id = isset($_POST['producto_id']) ? (int) $_POST['producto_id'] : 0;
    if ($producto_id <= 0) exit;

    $usuario_id = $_SESSION['usuario_id'];

    // Buscar carrito del usuario
    $stmt = $conn->prepare("SELECT id FROM carrito WHERE id_usuario = ?");
    $stmt->execute([$usuario_id]);
    $carrito = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($carrito) {
        $carrito_id = $carrito['id'];
    } else {
        $stmt = $conn->prepare("INSERT INTO carrito (id_usuario) VALUES (?)");
        $stmt->execute([$usuario_id]);
        $carrito_id = $conn->lastInsertId();
    }

    // Agregar o actualizar producto en carrito
    $stmt = $conn->prepare("SELECT cantidad FROM carrito_producto WHERE id_carrito = ? AND id_producto = ?");
    $stmt->execute([$carrito_id, $producto_id]);
    $existente = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existente) {
        $stmt = $conn->prepare("UPDATE carrito_producto SET cantidad = cantidad + 1 WHERE id_carrito = ? AND id_producto = ?");
        $stmt->execute([$carrito_id, $producto_id]);
    } else {
        $stmt = $conn->prepare("INSERT INTO carrito_producto (id_carrito, id_producto, cantidad) VALUES (?, ?, 1)");
        $stmt->execute([$carrito_id, $producto_id]);
    }
}

// ⚠️ Redirigir a la misma página de donde vino
header('Location: ' . $_SERVER['HTTP_REFERER']);
exit;