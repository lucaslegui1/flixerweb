<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require 'db_connection.php'; // Conexión a la base de datos

// Verificar si el usuario es VIP
$stmt = $pdo->prepare("SELECT is_vip FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user || !$user['is_vip']) {
    echo "<h2>No tienes permiso para acceder a esta sección.</h2>";
    exit();
}

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM signals WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $signal = $stmt->fetch();

    if (!$signal) {
        echo "<h2>No se encontró la señal.</h2>";
        exit();
    }
} else {
    // Manejo de error si no se pasa un ID válido
    echo "<h2>No se encontró la señal.</h2>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="bootstrap.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="template.css"> <!-- Asegúrate de tener el CSS del template -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap">
    <title>Señal Generada - FlixerTrade</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
        }

        header {
            text-align: center;
            padding: 20px;
            background-color: #343a40; /* Color del header */
            color: white;
        }

        main {
            padding: 40px;
            text-align: center;
        }

        main h2 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }

        main p {
            font-size: 18px;
            color: #555;
        }

        .signal-box {
            border-radius: 10px;
            padding: 20px;
            margin: 20px auto;
            width: 80%;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .call {
            background-color: #d4edda; /* Verde suave */
        }

        .put {
            background-color: #f8d7da; /* Rojo claro */
        }

        footer {
            text-align: center;
            padding: 20px;
            background-color: #343a40;
            color: white;
            position: fixed;
            bottom: 0;
            width: 100%;
        }

        a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <header>
        <h1>Señal Generada</h1>
    </header>
    <main>
        <div class="signal-box <?php echo htmlspecialchars($signal['direction'] === 'CALL' ? 'call' : 'put'); ?>">
            <!-- Mostrar Activo -->
            <h2><?php echo htmlspecialchars($signal['asset']); ?></h2>
            
            <!-- Mostrar Dirección -->
            <p><?php echo htmlspecialchars($signal['direction'] === 'CALL' ? '⬆️ CALL ⬆️' : '📉 PUT 📉'); ?></p>

            <!-- Mostrar Temporalidad (puedes ajustar este valor si es fijo o agregarlo a la base de datos) -->
            <p>Temporalidad: 5M</p> 

            <!-- Mostrar Horario de Entrada -->
            <p>Horario: <?php echo htmlspecialchars(date('H:i', strtotime($signal['timestamp']))); ?></p> 
        </div>
        
        <div>
            <a href="flixerbot.php">Volver a FlixerBOT</a>
            <a href="historial.php">Historial</a>
        </div>
    </main>
    <footer>
        <p>© 2024 FlixerTrade. Todos los derechos reservados.</p>
    </footer>
</body>
</html>