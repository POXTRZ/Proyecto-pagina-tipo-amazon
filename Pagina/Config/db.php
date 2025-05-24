<?php
// Configuración de la base de datos
$host = 'localhost';        // Servidor (en XAMPP siempre es localhost)
$dbname = 'mi_amazon';      // Nombre de tu base de datos
$username = 'root';         // Usuario por defecto en XAMPP
$password = '';             // Contraseña vacía por defecto en XAMPP

try {
    // Crear conexión PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    
    // Configurar PDO para mostrar errores
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // echo "Conexión exitosa a la base de datos mi_amazon";
    
} catch(PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// También crear una conexión mysqli (por si la necesitas)
$mysqli = new mysqli($host, $username, $password, $dbname);

if ($mysqli->connect_error) {
    die("Error de conexión mysqli: " . $mysqli->connect_error);
}

$mysqli->set_charset("utf8");

?>
