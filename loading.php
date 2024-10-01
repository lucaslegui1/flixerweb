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
    echo "No tienes permiso para acceder a esta sección.";
    exit();
}

$signal_id = $_GET['signal_id'];
$signal_time = $_GET['signal_time'];

// Verificar si es hora de mostrar la señal
$current_time = new DateTime();
if ($current_time >= new DateTime($signal_time)) {
    // Redirigir a la página de resultados si ya es tiempo
    header("Location: result.php?id=" . $signal_id);
    exit();
}

// Obtener la última señal generada
$stmt = $pdo->prepare("SELECT * FROM signals WHERE id < ? ORDER BY id DESC LIMIT 1");
$stmt->execute([$signal_id]);
$last_signal = $stmt->fetch();

// Mostrar la página de carga
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="bootstrap.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="template.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap">
    <title>Cargando Señal - FlixerTrade</title>
    <meta http-equiv="refresh" content="5;url=loading.php?signal_id=<?php echo $signal_id; ?>&signal_time=<?php echo $signal_time; ?>">
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

        .loading {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .loading img {
            width: 100px; /* Ajusta el tamaño de la imagen de carga */
        }

        .loading p {
            margin-top: 20px;
            font-size: 18px;
            color: #333;
        }

        .last-signal {
            margin-top: 30px;
            padding: 10px;
            border: 1px solid #007bff;
            border-radius: 5px;
            background-color: #e7f1ff; /* Color de fondo suave */
        }

        .last-signal h3 {
            margin: 0;
            font-size: 20px;
            color: #007bff;
        }

        .last-signal p {
            margin: 5px 0;
            color: #333;
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
    </style>
</head>
<body>
    <header>
        <h1>FlixerBot - Cargando Señal...</h1>
    </header>
    <main>
        <div class="loading">
            <img src="loading.gif" alt="Cargando...">
            <p>Estamos generando tu señal, por favor espera...</p>
        </div>

        <?php if ($last_signal): ?>
        <div class="last-signal">
            <h3>Ultima Señal Generada:</h3>
            <p>Activo: <?php echo htmlspecialchars($last_signal['asset']); ?></p>
            <p>Dirección: <?php echo htmlspecialchars($last_signal['direction'] === 'CALL' ? '⬆️ CALL ⬆️' : '📉 PUT 📉'); ?></p>
            <p>Horario: <?php echo htmlspecialchars(date('H:i', strtotime($last_signal['timestamp']))); ?></p>
        </div>
        <?php endif; ?>

        <div>
            <a href="flixerbot.php">Volver a la Página Principal</a>
        </div>
    </main>
    <footer>
        <p>© 2024 FlixerTrade. Todos los derechos reservados.</p>
    </footer>
</body>
</html>