<?php
session_start();
require 'db_connection.php';

// Obtener estadísticas generales
$query_stats = "
    SELECT 
        COUNT(*) AS total_signals,
        SUM(CASE WHEN vote = 'win' THEN 1 ELSE 0 END) AS total_wins,
        SUM(CASE WHEN vote = 'loss' THEN 1 ELSE 0 END) AS total_losses
    FROM votes";
$stmt_stats = $pdo->query($query_stats);
$stats = $stmt_stats->fetch();

// Obtener cantidad de votos por cada señal
$query_signal_votes = "
    SELECT 
        s.asset, 
        s.direction, 
        s.timestamp, 
        COUNT(v.vote) AS total_votes, 
        SUM(CASE WHEN v.vote = 'win' THEN 1 ELSE 0 END) AS wins,
        SUM(CASE WHEN v.vote = 'loss' THEN 1 ELSE 0 END) AS losses
    FROM signals s
    LEFT JOIN votes v ON s.id = v.signal_id
    GROUP BY s.id
    ORDER BY s.timestamp DESC
    LIMIT 10";
$stmt_signal_votes = $pdo->query($query_signal_votes);
$signal_votes = $stmt_signal_votes->fetchAll();

// Obtener rangos horarios con más señales ganadoras y perdedoras
$query_time_stats = "
    SELECT 
        HOUR(s.timestamp) AS signal_hour,
        SUM(CASE WHEN v.vote = 'win' THEN 1 ELSE 0 END) AS wins,
        SUM(CASE WHEN v.vote = 'loss' THEN 1 ELSE 0 END) AS losses
    FROM signals s
    LEFT JOIN votes v ON s.id = v.signal_id
    GROUP BY signal_hour
    ORDER BY wins DESC, losses DESC
    LIMIT 5";
$stmt_time_stats = $pdo->query($query_time_stats);
$time_stats = $stmt_time_stats->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="bootstrap.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="template.css">
    <title>Estadísticas del Bot</title>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="logo.png" alt="Logo">
                <span>FlixerTrade</span>
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="flixerbot.php">FlixerBot</a></li>
                    <li class="nav-item"><a class="nav-link btn btn-outline-light" href="estadisticas.php">Estadísticas</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item"><a class="nav-link" href="logout.php">Cerrar Sesión</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="login.php">Iniciar Sesión</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h1>Estadísticas del Bot</h1>
        <div class="card">
            <div class="card-body">
                <h3>Resumen General</h3>
                <p>Total de señales: <?php echo $stats['total_signals']; ?></p>
                <p>Señales Ganadoras: <?php echo $stats['total_wins']; ?></p>
                <p>Señales Perdedoras: <?php echo $stats['total_losses']; ?></p>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-body">
                <h3>Últimas Señales</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Activo</th>
                            <th>Dirección</th>
                            <th>Fecha y Hora</th>
                            <th>Votos Totales</th>
                            <th>Ganadoras</th>
                            <th>Perdedoras</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($signal_votes as $signal): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($signal['asset']); ?></td>
                            <td><?php echo htmlspecialchars($signal['direction']); ?></td>
                            <td><?php echo date('Y-m-d H:i:s', strtotime($signal['timestamp'])); ?></td>
                            <td><?php echo $signal['total_votes']; ?></td>
                            <td><?php echo $signal['wins']; ?></td>
                            <td><?php echo $signal['losses']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-body">
                <h3>Rangos Horarios con Más Ganancias</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Hora</th>
                            <th>Ganadoras</th>
                            <th>Perdedoras</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($time_stats as $time_stat): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($time_stat['signal_hour']); ?>:00</td>
                            <td><?php echo $time_stat['wins']; ?></td>
                            <td><?php echo $time_stat['losses']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <footer class="text-center mt-5">
        <p>© 2024 FlixerTrade. Todos los derechos reservados.</p>
    </footer>
</body>
</html>