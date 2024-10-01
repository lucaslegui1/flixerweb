<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require 'db_connection.php'; // Conexión a la base de datos

// Verificación de VIP
$stmt = $pdo->prepare("SELECT is_vip FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
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
    <title>FlixerBOT</title>
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

        header img {
            max-width: 150px; /* Ajusta el tamaño del logo */
            margin-bottom: 10px;
        }

        nav {
            margin-top: 10px;
        }

        nav ul {
            list-style: none;
            padding: 0;
        }

        nav ul.menu {
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        nav ul.menu li a {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 5px;
            transition: background 0.3s;
        }

        nav ul.menu li a:hover {
            background-color: #495057;
        }

        main {
            padding: 40px;
            text-align: center;
        }

        .alert-box {
            background-color: #dc3545; /* Color de alerta */
            color: white;
            padding: 20px;
            border-radius: 5px;
            margin: 20px auto;
            max-width: 500px;
        }

        .request-button {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border-radius: 5px;
            text-decoration: none;
        }

        .signal-form {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            margin: 20px auto;
            text-align: left;
        }

        .signal-form label {
            display: block;
            margin: 10px 0 5px;
        }

        .signal-form select,
        .signal-form input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        .signal-form button {
            background-color: #28a745; /* Color del botón */
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .signal-form button:hover {
            background-color: #218838; /* Color al pasar el mouse */
        }

        footer {
            text-align: center;
            padding: 20px;
            background-color: #343a40;
            color: white;
            position: relative; /* Cambia a static si es necesario */
            margin-top: 20px; /* Asegúrate de que haya espacio arriba */
        }
 .history-button {
        display: inline-block;
        margin: 20px 0; /* Margen para separar el botón del texto y el formulario */
        padding: 10px 20px;
        background-color: #007bff; /* Color azul del botón */
        color: white;
        border-radius: 5px;
        text-decoration: none;
        transition: background 0.3s;
    }

    .history-button:hover {
        background-color: #0056b3; /* Color al pasar el mouse */
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
                        <li class="nav-item"><a class="nav-link btn btn-outline-light" href="obtener_acceso.php">Acceso al Bot</a></li>
                        <li class="nav-item"><a class="nav-link" href="perfil.php">Mi Perfil</a></li>
                        <li class="nav-item"><a class="nav-link" href="crear_publicacion.php">Publicar</a></li>
                        <li class="nav-item"><a class="nav-link" href="logout.php">Cerrar Sesión</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="login.php">Iniciar Sesión</a></li>
                        <li class="nav-item"><a class="nav-link" href="register.php">Registrarse</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    </header>
    <main>
        <!-- Verificación de VIP -->
        <?php if (!isset($user['is_vip']) || $user['is_vip'] == 0): ?>
            <div class="alert-box">
                <h2>No tienes permiso para acceder a esta sección</h2>
                <p>Para acceder a FlixerBOT, necesitas ser un usuario VIP.</p>
                <a href="obtener_acceso.php" class="request-button">Solicitar acceso a FlixerBOT</a>
            </div>
        <?php else: ?>
            <p>Genera señales precisas con algoritmos avanzados para obtener mejores resultados.</p>
            
 <!-- Botón azul añadido -->
        <a href="historial.php" class="history-button">Ver Historial</a>

            <form action="generate_signal.php" method="POST" class="signal-form">
                <label for="platform">Plataforma:</label>
                <select id="platform" name="platform">
                    <option value="Pocket Options">Pocket Options</option>
                </select>

                <label for="strategy">Estrategia:</label>
                <select id="strategy" name="strategy">
                    <option value="FlixerTrade">FlixerTrade</option>
                </select>

                <label for="confirmations">Confirmaciones:</label>
                <input type="text" id="confirmations" name="confirmations" value="80%" readonly>

                <label for="active">Activo:</label>
                <select id="active" name="active">
                    <option value="TODOS">TODOS</option>
                </select>

                <input type="hidden" name="timeframe" value="5M">

                <button type="submit">Generar Señal</button>
            </form>
        <?php endif; ?>
    </main>
    <footer>
        <p>© 2024 FlixerTrade. Todos los derechos reservados.</p>
    </footer>
</body>
</html>