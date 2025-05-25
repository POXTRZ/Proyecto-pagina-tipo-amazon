<?php
session_start();
include __DIR__ . '/../Config/db.php'; // Conexión a la base de datos

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

try {
    // CORREGIDO: Cambiamos 'nombre' por 'nombre_usuario'
    $stmt = $conn->prepare("SELECT nombre_usuario, correo, telefono, direccion FROM usuarios WHERE id = ?");
    $stmt->execute([$usuario_id]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        echo "Usuario no encontrado.";
        exit;
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Perfil de Usuario</title>
</head>
<body>
    <h1>Perfil de Usuario</h1>

    <p><strong>Nombre:</strong> <?= htmlspecialchars($usuario['nombre_usuario']) ?></p>
    <p><strong>Correo:</strong> <?= htmlspecialchars($usuario['correo']) ?></p>
    <p><strong>Teléfono:</strong> <?= htmlspecialchars($usuario['telefono']) ?></p>
    <p><strong>Dirección:</strong> <?= htmlspecialchars($usuario['direccion']) ?></p>

    <br>

    <a href="logout.php"><button>Cerrar sesión</button></a>
    <a href="index.php"><button>Volver a Home</button></a>
</body>
</html>
