<?php
session_start();
include __DIR__ . '/../Config/db.php';

$id_producto = isset($_POST['id_producto']) ? intval($_POST['id_producto']) : 0;

if ($id_producto <= 0) {
    die("Producto inválido.");
}

if (!isset($_SESSION['usuario_id'])) {
    // Carrito temporal
    if (isset($_SESSION['carrito_temporal'][$id_producto])) {
        unset($_SESSION['carrito_temporal'][$id_producto]);
    }

    header("Location: carrito.php");
    exit();
}

// Carrito persistente en la base de datos
$id_usuario = $_SESSION['usuario_id'];

$delete = $conn->prepare("DELETE FROM carrito WHERE id_usuario = ? AND id_producto = ?");
$delete->execute([$id_usuario, $id_producto]);

header("Location: carrito.php");
exit();
