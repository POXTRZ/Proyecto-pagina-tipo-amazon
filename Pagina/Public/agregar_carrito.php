<?php
session_start();
require_once '../Config/db.php';

header('Content-Type: application/json');

// Validar que el usuario esté logueado
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'Debes iniciar sesión.']);
    exit;
}

$id_usuario = $_SESSION['usuario_id'];
$id_producto = $_POST['producto_id'] ?? null;
$cantidad = $_POST['cantidad'] ?? 1;

// Validaciones básicas
if (!$id_producto || $cantidad <= 0) {
    echo json_encode(['success' => false, 'message' => 'Datos inválidos.']);
    exit;
}

// Verificar si ya existe un carrito para este usuario
$stmt = $pdo->prepare("SELECT id FROM carrito WHERE id_usuario = ?");
$stmt->execute([$id_usuario]);
$carrito = $stmt->fetch();

if ($carrito) {
    $id_carrito = $carrito['id'];
} else {
    // Crear carrito si no existe
    $stmt = $pdo->prepare("INSERT INTO carrito (id_usuario) VALUES (?)");
    $stmt->execute([$id_usuario]);
    $id_carrito = $pdo->lastInsertId();
}

// Verificar si el producto ya está en el carrito
$stmt = $pdo->prepare("SELECT * FROM carrito_producto WHERE id_carrito = ? AND id_producto = ?");
$stmt->execute([$id_carrito, $id_producto]);
$existe = $stmt->fetch();

if ($existe) {
    // Si ya está, actualizar cantidad
    $stmt = $pdo->prepare("
        UPDATE carrito_producto 
        SET cantidad = cantidad + ? 
        WHERE id_carrito = ? AND id_producto = ?
    ");
    $stmt->execute([$cantidad, $id_carrito, $id_producto]);
} else {
    // Si no está, insertar
    $stmt = $pdo->prepare("
        INSERT INTO carrito_producto (id_carrito, id_producto, cantidad) 
        VALUES (?, ?, ?)
    ");
    $stmt->execute([$id_carrito, $id_producto, $cantidad]);
}

// Obtener total actualizado
$stmt = $pdo->prepare("
    SELECT SUM(cantidad) as total 
    FROM carrito_producto 
    WHERE id_carrito = ?
");
$stmt->execute([$id_carrito]);
$total = $stmt->fetchColumn();

echo json_encode([
    'success' => true,
    'message' => 'Producto agregado correctamente.',
    'cart_count' => $total
]);
?>
pitito