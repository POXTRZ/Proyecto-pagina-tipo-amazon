<?php
session_start();
include __DIR__ . '/../Config/db.php';

// Solo permitir eliminar si está logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$id_producto = isset($_POST['id_producto']) ? intval($_POST['id_producto']) : 0;

if ($id_producto <= 0) {
    die("Producto inválido.");
}

// Carrito persistente en la base de datos
$id_usuario = $_SESSION['usuario_id'];

$stmt = $conn->prepare("SELECT id FROM carrito WHERE id_usuario = ?");
$stmt->execute([$id_usuario]);
$carrito_id = $stmt->fetchColumn();

if ($carrito_id) {
    $delete = $conn->prepare("DELETE FROM carrito_producto WHERE id_carrito = ? AND id_producto = ?");
    $delete->execute([$carrito_id, $id_producto]);
}

header("Location: carrito.php");
exit;