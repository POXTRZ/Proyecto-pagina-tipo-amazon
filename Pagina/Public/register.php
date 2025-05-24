<?php
include __DIR__ . '/../Config/db.php'; // Ruta al archivo de conexión

ini_set('display_errors', 1);
error_reporting(E_ALL);

$success_msg = [];
$error_msg = [];
$formData = [
    'nombre_usuario' => '',
    'correo' => '',
    'telefono' => '',
    'direccion' => ''
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $formData["nombre_usuario"] = trim($_POST["nombre_usuario"] ?? '');
    $formData["correo"] = trim($_POST["correo"] ?? '');
    $formData["telefono"] = trim($_POST["telefono"] ?? '');
    $formData["direccion"] = trim($_POST["direccion"] ?? '');

    $username  = $formData["nombre_usuario"];
    $email     = $formData["correo"];
    $password  = $_POST["contrasena"] ?? '';
    $confirm   = $_POST["confirmar"] ?? '';
    $telefono  = $formData["telefono"];
    $direccion = $formData["direccion"];

    // Validaciones básicas
    if (empty($username) || strlen($username) < 3) {
        $error_msg[] = "El nombre de usuario debe tener al menos 3 caracteres.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_msg[] = "Correo electrónico no válido.";
    }

    if (strlen($password) < 8) {
        $error_msg[] = "La contraseña debe tener al menos 8 caracteres.";
    }

    if ($password !== $confirm) {
        $error_msg[] = "Las contraseñas no coinciden.";
    }

    if (!empty($telefono) && !preg_match('/^[0-9\-\+\s]+$/', $telefono)) {
        $error_msg[] = "El teléfono solo puede contener números, espacios o guiones.";
    }

    // Si no hay errores, insertar en la base de datos
    if (empty($error_msg)) {
        try {
            $stmt = $conn->prepare("SELECT id FROM usuarios WHERE nombre_usuario = ? OR correo = ?");
            $stmt->execute([$username, $email]);

            if ($stmt->rowCount() > 0) {
                $error_msg[] = "El nombre de usuario o correo ya están registrados.";
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $insert = $conn->prepare("INSERT INTO usuarios (nombre_usuario, correo, contrasena, telefono, direccion)
                                          VALUES (?, ?, ?, ?, ?)");
                $insert->execute([$username, $email, $hash, $telefono, $direccion]);

                $success_msg[] = "¡Registro exitoso, $username!";
                $formData = [
                    'nombre_usuario' => '',
                    'correo' => '',
                    'telefono' => '',
                    'direccion' => ''
                ];
            }
        } catch (PDOException $e) {
            $error_msg[] = "Error al registrar: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro</title>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
</head>
<body>

<form action="" method="post">
  <h1>Registro</h1>

  <div class="input">
    <label>Nombre de usuario<sup>*</sup></label>
    <input type="text" name="nombre_usuario" required placeholder="Nombre de usuario"
           value="<?= htmlspecialchars($formData["nombre_usuario"]) ?>">
  </div>

  <div class="input">
    <label>Email<sup>*</sup></label>
    <input type="email" name="correo" required placeholder="Correo electrónico"
           value="<?= htmlspecialchars($formData["correo"]) ?>">
  </div>

  <div class="input">
    <label>Contraseña<sup>*</sup></label>
    <input type="password" name="contrasena" required placeholder="Contraseña">
  </div>

  <div class="input">
    <label>Confirmar Contraseña<sup>*</sup></label>
    <input type="password" name="confirmar" required placeholder="Repite la contraseña">
  </div>

  <div class="input">
    <label>Teléfono</label>
    <input type="text" name="telefono" placeholder="Opcional"
           value="<?= htmlspecialchars($formData["telefono"]) ?>">
  </div>

  <div class="input">
    <label>Dirección</label>
    <input type="text" name="direccion" placeholder="Opcional"
           value="<?= htmlspecialchars($formData["direccion"]) ?>">
  </div>

  <button type="submit">Registrar</button>
</form>

<?php
foreach ($success_msg as $msg) {
    echo "<script>swal('¡Éxito!', '".addslashes($msg)."', 'success');</script>";
}
foreach ($error_msg as $msg) {
    echo "<script>swal('Error', '".addslashes($msg)."', 'error');</script>";
}
?>

</body>
</html>
