<?php 
session_start();
include __DIR__ . '/../Config/db.php'; // Ruta al archivo de conexión

ini_set('display_errors', 1);
error_reporting(E_ALL);

$error_msg = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = trim($_POST['usuario'] ?? '');
    $password = $_POST['contrasena'] ?? '';

    if (empty($usuario) || empty($password)) {
        $error_msg[] = "Por favor, completa todos los campos.";
    } else {
        try {
            // Buscar por nombre de usuario o correo
            $stmt = $conn->prepare("SELECT * FROM usuarios WHERE nombre_usuario = ? OR correo = ?");
            $stmt->execute([$usuario, $usuario]);

            if ($stmt->rowCount() == 1) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if (password_verify($password, $user['contrasena'])) {
                    // Iniciar sesión
                    $_SESSION['usuario_id'] = $user['id'];
                    $_SESSION['nombre_usuario'] = $user['nombre_usuario'];

                    header("Location: index.php");
                    exit;
                } else {
                    $error_msg[] = "Contraseña incorrecta.";
                }
            } else {
                $error_msg[] = "Usuario no encontrado.";
            }
        } catch (PDOException $e) {
            $error_msg[] = "Error al iniciar sesión: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
</head>
<body>

<form method="POST" action="">
    <h1>Iniciar Sesión</h1>

    <div class="input">
        <label>Usuario o correo:</label>
        <input type="text" name="usuario" required placeholder="Nombre de usuario o correo">
    </div>

    <div class="input">
        <label>Contraseña:</label>
        <input type="password" name="contrasena" required placeholder="Contraseña">
    </div>

    <button type="submit">Ingresar</button>
</form>

<!-- Botón para volver al inicio -->
<div style="margin-top: 20px;">
    <a href="index.php">
        <button type="button">Volver al inicio</button>
    </a>
</div>

<!-- Enlace a registro -->
<div style="margin-top: 10px;">
    <p>¿Aún no tienes cuenta? <a href="register.php">Regístrate aquí</a></p>
</div>

<?php
foreach ($error_msg as $msg) {
    echo "<script>swal('Error', '".addslashes($msg)."', 'error');</script>";
}
?>

</body>
</html>
