<?php
// /Pagina/Includes/categoria.php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../Config/db.php';

// 1) Usuario y carrito
$logged     = !empty($_SESSION['usuario_id']);
$user       = $_SESSION['nombre_usuario'] ?? '';
$cart_count = 0;
if ($logged) {
    $sql = "SELECT SUM(cp.cantidad) FROM carrito c
            JOIN carrito_producto cp ON c.id = cp.id_carrito
            WHERE c.id_usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$_SESSION['usuario_id']]);
    $cart_count = $stmt->fetchColumn() ?: 0;
}

// 2) Validar id de categoría
$categoria_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$categoria_id) {
    header('Location: index.php');
    exit;
}

// 3) Traer categoría
$stmt = $conn->prepare("SELECT * FROM categorias WHERE id = ?");
$stmt->execute([$categoria_id]);
$categoria = $stmt->fetch();
if (!$categoria) {
    header('Location: index.php');
    exit;
}

// 4) Traer productos de esa categoría
$stmt = $conn->prepare("
  SELECT p.* 
  FROM productos p
  JOIN producto_categoria pc ON p.id = pc.id_producto
  WHERE pc.id_categoria = ? AND p.activo = 1
  ORDER BY p.nombre
");
$stmt->execute([$categoria_id]);
$productos = $stmt->fetchAll();

// 5) Todas las categorías para el nav y “Otras categorías”
$cats = $conn->query("SELECT * FROM categorias ORDER BY nombre")->fetchAll();
