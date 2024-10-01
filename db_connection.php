<?php
$host = 'localhost';
$db = 'c2212322_ft'; // Reemplaza con el nombre de tu base de datos
$user = 'c2212322_ft'; // Reemplaza con tu usuario de base de datos
$pass = '15bunoMOgu'; // Reemplaza con tu contraseña de base de datos

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
}
?>
