<?php
include 'db.php';

$success_msg = [];
$error_msg = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];
    $confirmar = $_POST["confirmar"];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_msg[] = "Correo electrónico no válido.";
    } elseif (strlen($password) < 8) {
        $error_msg[] = "La contraseña debe tener al menos 8 caracteres.";
    } elseif ($password !== $confirmar) {
        $error_msg[] = "Las contraseñas no coinciden.";
    } else {
        try {
            // Verificar si ya existe el correo
            $stmt = $conn->prepare("SELECT * FROM usuarios WHERE correo = ?");
            $stmt->execute([$email]);

            if ($stmt->rowCount() > 0) {
                $error_msg[] = "Ese correo ya está registrado.";
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $nombre_usuario = explode('@', $email)[0] . rand(1000, 9999);

                $insertar = $conn->prepare("INSERT INTO usuarios (nombre_usuario, correo, contrasena) VALUES (?, ?, ?)");
                $insertar->execute([$nombre_usuario, $email, $hash]);

                $success_msg[] = "Registro exitoso. ¡Bienvenido!";
            }
        } catch (PDOException $e) {
            $error_msg[] = "Error al registrar: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Crear cuenta - CHOCHOS.COM</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link href="https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css" rel="stylesheet">
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
</head>
<body>

<div class="principal">
    <section>
        <div class="Formulario" id="login">
            <form action="" method="post">
                <h1>Crear Cuenta</h1>

                <div class="input">
                    <label>Email<sup>*</sup></label><br>
                    <input type="email" name="email" required placeholder="Correo electrónico">
                </div><br>

                <div class="input">
                    <label>Contraseña<sup>*</sup></label><br>
                    <input type="password" name="password" required placeholder="Contraseña">
                </div><br>

                <div class="input">
                    <label>Confirmar Contraseña<sup>*</sup></label><br>
                    <input type="password" name="confirmar" required placeholder="Confirmar contraseña">
                </div><br>

                <button type="submit">Crear cuenta</button>
                <p>¿Ya tienes una cuenta? <a href="login.php">Iniciar sesión</a></p>
            </form>
        </div>
    </section>
</div>

<?php
if (!empty($success_msg)) {
    foreach ($success_msg as $msg) {
        echo "<script>swal('¡Éxito!', '".htmlspecialchars($msg)."', 'success');</script>";
    }
}

if (!empty($error_msg)) {
    foreach ($error_msg as $msg) {
        echo "<script>swal('Error', '".htmlspecialchars($msg)."', 'error');</script>";
    }
}
?>

</body>
</html>
