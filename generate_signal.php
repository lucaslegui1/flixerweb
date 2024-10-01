<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$assets = [
    "EUR/USD OTC", "AUD/CAD OTC", "AUD/CHF OTC", "AUD/JPY OTC",
        "AUD/NZD OTC", "AUD/USD OTC", "CAD/CHF OTC", "CAD/JPY OTC",
        "CHF/JPY OTC", "EUR/CHF OTC", "EUR/GBP OTC", "EUR/JPY OTC",
        "EUR/NZD OTC", "GBP/AUD OTC", "GBP/JPY OTC",
        "AED/CNY OTC", "BHD/CNY OTC", "EUR/HUF OTC", "GBP/USD OTC",
        "NZD/JPY OTC", "NZD/USD OTC", "QAR/CNY OTC", "USD/ARS OTC",
        "USD/BRL OTC", "USD/COP OTC", "USD/PKR OTC", "USD/SGD OTC",
        "USD/THB OTC", "EUR/RUB OTC", "JOD/CNY OTC", "USD/EGP OTC",
        "USD/RUB OTC", "USD/PHP OTC", "USD/DZD OTC", "CHF/NOK OTC",
        "YER/USD OTC", "USD/IDR OTC", "USD/JPY OTC", "MAD/USD OTC",
        "USD/CAD OTC", "USD/CLP OTC", "USD/MYR OTC", "OMR/CNY OTC",
        "USD/MXN OTC", "EUR/TRY OTC", "USD/BDT OTC", "LBP/USD OTC",
        "USD/CHF OTC", "TND/USD OTC", "USD/VND OTC", "USD/CNH OTC",
        "Bitcoin ETF OTC", "BNB OTC", "Bitcoin OTC", "Ethereum OTC",
        "Chainlink OTC", "Litecoin OTC", "Polygon OTC", "Ripple OTC",
        "Boeing Company OTC", "FACEBOOK INC OTC", "Intel OTC",
        "Johnson & Johnson OTC", "McDonald's OTC", "Microsoft OTC",
        "Citigroup Inc OTC", "Alibaba OTC", "American Express OTC",
        "Apple OTC", "VISA OTC", "ExxonMobil OTC", "FedEx OTC",
        "TWITTER OTC", "Tesla OTC", "Amazon OTC", "Pfizer Inc OTC",
        "Cisco OTC", "Netflix OTC",
    // Agrega más activos según sea necesario
];

// Calcular el próximo múltiplo de 5 minutos
$current_time = new DateTime();
$minutes = (int) $current_time->format('i');
$next_multiple_of_5 = ceil($minutes / 5) * 5; // Redondear hacia arriba al próximo múltiplo de 5
$current_time->setTime((int)$current_time->format('H'), $next_multiple_of_5); // Establecer el nuevo tiempo
$signal_time = $current_time->format('Y-m-d H:i'); // Formato de la señal

// Conectar a la base de datos
require 'db_connection.php'; // Asegúrate de tener un archivo para la conexión a la base de datos

// Comprobar si ya existe una señal para el rango horario
$stmt = $pdo->prepare("SELECT * FROM signals WHERE timestamp = ?");
$stmt->execute([$signal_time]);
$existing_signal = $stmt->fetch();

if ($existing_signal) {
    // Redirigir a la página de carga con la señal existente
    header("Location: loading.php?signal_id=" . $existing_signal['id'] . "&signal_time=" . $signal_time);
    exit();
} else {
    // Generar señal
    $randomAsset = $assets[array_rand($assets)];
    $direction = rand(0, 1) ? 'CALL' : 'PUT';

    // Guardar la señal en la base de datos
    $stmt = $pdo->prepare("INSERT INTO signals (asset, direction, timestamp, user_id) VALUES (?, ?, ?, ?)");
    $stmt->execute([$randomAsset, $direction, $signal_time, $_SESSION['user_id']]);

    // Redirigir a la página de carga con la señal generada
    header("Location: loading.php?signal_id=" . $pdo->lastInsertId() . "&signal_time=" . $signal_time);
    exit();
}
?>