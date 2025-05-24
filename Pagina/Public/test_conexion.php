<?php
require_once '../Config/db.php';
// Verificar la conexión PDO
if ($pdo) {
    echo "✅ Conexión exitosa a la base de datos (PDO)";
} else {
    echo "❌ Error de conexión a la base de datos (PDO)";
}
// Verificar la conexión mysqli
if ($mysqli) {
    echo "✅ Conexión exitosa a la base de datos (mysqli)";
} else {
    echo "❌ Error de conexión a la base de datos (mysqli)";
}
// Cerrar la conexión al final (opcional, ya que PHP lo hace automáticamente al finalizar el script)
$pdo = null;
$mysqli->close();
?>