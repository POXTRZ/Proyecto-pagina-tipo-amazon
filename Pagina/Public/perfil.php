<?php
session_start();
include __DIR__ . '/../Config/db.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

try {
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario – FitStore Pro</title>
    <link rel="stylesheet" href="../Css/perfi.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="particles"></div>
    
    <div class="profile-container">
        <!-- Header del perfil -->
        <div class="profile-header">
            <h1 class="profile-title">Mi Perfil</h1>
            <p class="profile-subtitle">Gestiona tu información personal y configuración de cuenta</p>
        </div>

        <!-- Avatar y estadísticas -->
        <div class="profile-avatar">
            <div class="avatar-circle">
                <i class="fas fa-user"></i>
            </div>
            <h2 class="username"><?= htmlspecialchars($usuario['nombre_usuario']) ?></h2>
            <p class="user-role">Cliente FitStore Pro</p>
            
            <div class="profile-stats">
                <div class="stat">
                    <span class="stat-number">0</span>
                    <span class="stat-label">Pedidos</span>
                </div>
                <div class="stat">
                    <span class="stat-number">0</span>
                    <span class="stat-label">Favoritos</span>
                </div>
                <div class="stat">
                    <span class="stat-number">0</span>
                    <span class="stat-label">Reseñas</span>
                </div>
            </div>
        </div>

        <!-- Información del usuario -->
        <div class="profile-info">
            <div class="info-section">
                <h3 class="section-title">
                    <i class="fas fa-info-circle"></i>
                    Información Personal
                </h3>

                <div class="info-item">
                    <div class="info-label">
                        <i class="fas fa-user"></i>
                        Nombre de usuario
                    </div>
                    <div class="info-value"><?= htmlspecialchars($usuario['nombre_usuario']) ?></div>
                </div>

                <div class="info-item">
                    <div class="info-label">
                        <i class="fas fa-envelope"></i>
                        Correo electrónico
                    </div>
                    <div class="info-value"><?= htmlspecialchars($usuario['correo']) ?></div>
                </div>

                <div class="info-item">
                    <div class="info-label">
                        <i class="fas fa-phone"></i>
                        Teléfono
                    </div>
                    <div class="info-value <?= empty($usuario['telefono']) ? 'empty' : '' ?>">
                        <?= !empty($usuario['telefono']) ? htmlspecialchars($usuario['telefono']) : 'No especificado' ?>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-label">
                        <i class="fas fa-map-marker-alt"></i>
                        Dirección
                    </div>
                    <div class="info-value <?= empty($usuario['direccion']) ? 'empty' : '' ?>">
                        <?= !empty($usuario['direccion']) ? htmlspecialchars($usuario['direccion']) : 'No especificada' ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Acciones del perfil -->
        <div class="profile-actions">
            <a href="index.php" class="btn btn-primary">
                <i class="fas fa-home"></i>
                Volver al Inicio
            </a>
            <a href="carrito.php" class="btn btn-secondary">
                <i class="fas fa-shopping-cart"></i>
                Ver Carrito
            </a>
            <a href="logout.php" class="btn btn-danger">
                <i class="fas fa-sign-out-alt"></i>
                Cerrar Sesión
            </a>
        </div>
    </div>
</body>
</html>
