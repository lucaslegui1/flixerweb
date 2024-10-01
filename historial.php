<?php
session_start();
require 'db_connection.php';

// Establecer cuántas señales mostrar por página
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Filtrado por fecha
$date_filter = $_GET['date_filter'] ?? null;
$current_time = date('Y-m-d H:i:s'); // Obtener la hora actual

$query = "SELECT * FROM signals WHERE timestamp <= :current_time"; // Filtrar por timestamp
if ($date_filter) {
    $query .= " AND DATE(timestamp) = :date_filter"; // Filtrar por fecha si se proporciona
}
$query .= " ORDER BY timestamp DESC LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($query);
$stmt->bindValue(':current_time', $current_time); // Vincular la hora actual
if ($date_filter) {
    $stmt->execute(['date_filter' => $date_filter, 'limit' => $limit, 'offset' => $offset]);
} else {
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
}
$signals = $stmt->fetchAll();

// Obtener el total de señales para calcular el número de páginas
$total_query = "SELECT COUNT(*) FROM signals WHERE timestamp <= :current_time"; // Contar señales filtradas por timestamp
if ($date_filter) {
    $total_query .= " AND DATE(timestamp) = :date_filter"; // Filtrar por fecha si se proporciona
}
$total_stmt = $pdo->prepare($total_query);
$total_stmt->bindValue(':current_time', $current_time); // Vincular la hora actual
if ($date_filter) {
    $total_stmt->execute(['date_filter' => $date_filter]);
} else {
    $total_stmt->execute();
}
$total_signals = $total_stmt->fetchColumn();
$total_pages = ceil($total_signals / $limit);

// Obtener votos del usuario
$user_id = $_SESSION['user_id'] ?? null;
$voted_signals = [];
if ($user_id) {
    $vote_stmt = $pdo->prepare("SELECT signal_id, vote FROM votes WHERE user_id = ?");
    $vote_stmt->execute([$user_id]);
    while ($vote = $vote_stmt->fetch()) {
        $voted_signals[$vote['signal_id']] = $vote['vote'];
    }
}
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
    <title>Historial de Señales</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            background-color: #f4f4f4;
        }
        .container {
            margin: 20px auto;
            max-width: 800px;
        }
        .signal-card {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .signal-card.green {
            background-color: #d4edda; /* Color verde */
        }
        .signal-card.red {
            background-color: #f8d7da; /* Color rojo */
        }
        .signal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .signal-time {
            font-size: 1.2em; /* Aumentado para mayor visibilidad */
            color: gray;
        }
        .signal-actions {
            margin-top: 15px;
        }
        .filter-form {
            margin-bottom: 20px;
        }
        .pagination {
            margin-top: 20px;
        }
		        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            background-color: #f4f4f4;
        }
        .navbar {
            background-color: rgba(52, 58, 64, 0.9);
            transition: background-color 0.3s;
        }
        .navbar.scrolled {
            background-color: rgba(52, 58, 64, 1);
        }
        .navbar-brand {
            display: flex;
            align-items: center;
        }
        .navbar-brand img {
            height: 60px; /* Aumentar tamaño del logo */
            margin-right: 10px; /* Espacio entre el logo y el texto */
            transition: transform 0.3s;
        }
        .navbar-brand img:hover {
            transform: scale(1.1); /* Aumento al pasar el mouse */
        }
        .navbar-brand span {
            font-size: 24px; /* Tamaño del texto */
            color: white; /* Color del texto */
            font-weight: bold; /* Peso del texto */
        }
        .nav-link {
            font-size: 16px; /* Aumentar tamaño de fuente del menú */
            padding: 10px 15px; /* Espaciado para mejorar clics */
            color: #fff; /* Color del texto */
            transition: color 0.3s;
        }
        .nav-link:hover {
            color: #ffc107; /* Cambiar color al pasar el mouse */
        }
        .btn-outline-light {
            border-color: #ffc107; /* Color del borde */
            color: #ffc107; /* Color del texto */
        }
        .btn-outline-light:hover {
            background-color: #ffc107; /* Color de fondo al pasar el mouse */
            color: #fff; /* Color del texto al pasar el mouse */
        }
        .hero {
            background-image: url('image.jpg');
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            text-align: center;
            position: relative;
            z-index: 1;
        }
        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: -1;
        }
        .blog {
            padding: 60px 20px;
            background: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Sombra para el contenido */
            border-radius: 8px; /* Esquinas redondeadas */
        }
        .card {
            margin: 20px 0;
            transition: transform 0.2s;
            border-radius: 8px;
            overflow: hidden;
        }
        .card:hover {
            transform: scale(1.05);
        }
        .card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        footer {
            text-align: center;
            padding: 20px;
            background-color: #343a40;
            color: white;
            margin-top: 40px; /* Espacio antes del pie de página */
        }
        .pagination {
            justify-content: center;
        }
.hero-logo {
    width: 100%; /* Asegura que la imagen sea responsiva */
    max-width: 200px; /* Ajusta el tamaño máximo según lo que desees */
    border-radius: 50%; /* Hacer que el logo tenga esquinas redondeadas */
    transition: transform 0.3s; /* Efecto de transición */
    margin-bottom: 20px; /* Espacio entre el logo y el texto */
}

.hero-logo:hover {
    transform: scale(1.1); /* Aumenta el tamaño al pasar el mouse */
}
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="logo.png" alt="Logo">
                <span>FlixerTrade</span> <!-- Nombre del sitio -->
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item"><a class="nav-link" href="index.php">Inicio</a></li>
						<li class="nav-item"><a class="nav-link btn btn-outline-light" href="flixerbot.php">FlixerBot</a></li>
                        <li class="nav-item"><a class="nav-link" href="logout.php">Cerrar Sesión</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="login.php">Iniciar Sesión</a></li>
                        <li class="nav-item"><a class="nav-link" href="register.php">Registrarse</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container">
        <h1>Historial de Señales</h1>
        
        <form class="filter-form" method="GET">
            <label for="date_filter">Filtrar por fecha:</label>
            <input type="date" name="date_filter" id="date_filter">
            <button type="submit" class="btn btn-primary">Filtrar</button>
            <a href="flixerbot.php" class="btn btn-success" style="margin-left: 10px;">Ir a FlixerBOT</a>
        </form>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($_SESSION['error']); ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <?php foreach ($signals as $signal): ?>
        <div class="signal-card <?php 
            // Determinar color de la tarjeta
            $win_count = $pdo->prepare("SELECT COUNT(*) FROM votes WHERE signal_id = ? AND vote = 'win'");
            $win_count->execute([$signal['id']]);
            $loss_count = $pdo->prepare("SELECT COUNT(*) FROM votes WHERE signal_id = ? AND vote = 'loss'");
            $loss_count->execute([$signal['id']]);
            $wins = $win_count->fetchColumn();
            $losses = $loss_count->fetchColumn();
            if ($wins > $losses) {
                echo 'green';
            } elseif ($losses > $wins) {
                echo 'red';
            }
        ?>">
            <div class="signal-header">
                <h4><?php echo htmlspecialchars($signal['asset']); ?> (<?php echo htmlspecialchars($signal['direction']); ?>)</h4> <!-- Mostrar tipo de señal -->
                <span class="signal-time"><?php echo date('Y-m-d H:i:s', strtotime($signal['timestamp'])); ?></span>
            </div>
            <div class="signal-actions">
                <p>Ganadoras: <?php echo $wins; ?> - Perdidas: <?php echo $losses; ?></p>
                <?php if ($user_id): ?>
                    <?php if (isset($voted_signals[$signal['id']])): ?>
                        <form action="votar.php" method="POST" style="display:inline;">
                            <input type="hidden" name="signal_id" value="<?php echo $signal['id']; ?>">
                            <input type="hidden" name="vote" value="undo">
                            <button type="submit" class="btn btn-warning">Deshacer Voto</button>
                        </form>
                    <?php else: ?>
                        <form action="votar.php" method="POST" style="display:inline;">
                            <input type="hidden" name="signal_id" value="<?php echo $signal['id']; ?>">
                            <input type="hidden" name="vote" value="win">
                            <button type="submit" class="btn btn-success">Votar como Ganadora</button>
                        </form>
                        <form action="votar.php" method="POST" style="display:inline;">
                            <input type="hidden" name="signal_id" value="<?php echo $signal['id']; ?>">
                            <input type="hidden" name="vote" value="loss">
                            <button type="submit" class="btn btn-danger">Votar como Perdida</button>
                        </form>
                    <?php endif; ?>
                <?php else: ?>
                    <p><a href="login.php" class="btn btn-warning">Inicia sesión para votar</a></p>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>

        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>" class="btn btn-secondary">Anterior</a>
            <?php endif; ?>
            <?php if ($page < $total_pages): ?>
                <a href="?page=<?php echo $page + 1; ?>" class="btn btn-secondary">Siguiente</a>
            <?php endif; ?>
        </div>

    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>