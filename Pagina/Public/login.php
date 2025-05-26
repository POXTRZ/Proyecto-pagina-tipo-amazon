<?php 
session_start();
include __DIR__ . '/../Config/db.php';

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
            $stmt = $conn->prepare("SELECT * FROM usuarios WHERE nombre_usuario = ? OR correo = ?");
            $stmt->execute([$usuario, $usuario]);

            if ($stmt->rowCount() == 1) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if (password_verify($password, $user['contrasena'])) {
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión – FitStore Pro</title>
    <link rel="stylesheet" href="../Css/login.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
</head>
<body>
    <div class="particles"></div>
    
    <div class="form-container">
        <form method="POST" action="">
            <h1>Iniciar Sesión</h1>

            <div class="input">
                <label>Usuario o correo<sup>*</sup></label>
                <input type="text" name="usuario" required placeholder="Nombre de usuario o correo">
            </div>

            <div class="input">
                <label>Contraseña<sup>*</sup></label>
                <input type="password" name="contrasena" required placeholder="Contraseña">
            </div>

            <button type="submit">Ingresar</button>
        </form>

        <div style="margin-top: 20px;">
            <a href="index.php">
                <button type="button">Volver al inicio</button>
            </a>
        </div>

        <div style="margin-top: 10px;">
            <p>¿Aún no tienes cuenta? <a href="register.php">Regístrate aquí</a></p>
        </div>
    </div>

    <?php
    foreach ($error_msg as $msg) {
        echo "<script>swal('Error', '".addslashes($msg)."', 'error');</script>";
    }
    ?>
</body>
</html>
