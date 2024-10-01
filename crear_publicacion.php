<?php
session_start();
require 'db_connection.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Obtener el ID del usuario
$user_id = $_SESSION['user_id'];

// Consultar si el usuario es VIP
$stmt = $pdo->prepare("SELECT is_vip FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if ($user['is_vip'] != 1) {
    // Si el usuario no es VIP, redirigir o mostrar un mensaje
    echo "<script>alert('Debes ser un usuario VIP para crear publicaciones.'); window.location.href='index.php';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    
    // Inicializar variable de imagen
    $image = null;

    // Subir imagen si está presente
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $image = 'uploads/' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $image);
    }

    // Insertar publicación en la base de datos
    $stmt = $pdo->prepare("INSERT INTO posts (title, content, user_id, image) VALUES (?, ?, ?, ?)");
    $stmt->execute([$title, $content, $user_id, $image]);

    header("Location: index.php");
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
    <link rel="stylesheet" href="template.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap">
    <title>Crear Publicación</title>
    <style>
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
        .container {
            margin-top: 30px; /* Espacio superior */
        }
        footer {
            text-align: center;
            padding: 20px;
            background-color: #343a40;
            color: white;
            margin-top: 40px; /* Espacio antes del pie de página */
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
    <script src="https://cdn.tiny.cloud/1/oas3gsg7und42jqwx0p2c5ql4h16x3yq93z7bboa5dp912aj/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        tinymce.init({
            selector: '#content',
            plugins: 'lists link image table',
            toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright | bullist numlist outdent indent | link image',
            menubar: false,
            setup: function(editor) {
                editor.on('change', function() {
                    editor.save(); // Guarda el contenido en el textarea al cambiar
                });
            }
        });
    </script>
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
						<li class="nav-item"><a class="nav-link btn btn-outline-light" href="flixerbot.php">FlixerBot</a></li>
                        <li class="nav-item"><a class="nav-link" href="perfil.php">Mi Perfil</a></li>
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
        <h2 class="text-center">Crear Nueva Publicación</h2>
        <form action="crear_publicacion.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Título</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="form-group">
                <label for="content">Contenido</label>
                <textarea class="form-control" id="content" name="content" rows="5" required></textarea>
            </div>
            <div class="form-group">
                <label for="image">Imagen (opcional)</label>
                <input type="file" class="form-control-file" id="image" name="image">
            </div>
            <button type="submit" class="btn btn-primary">Publicar</button>
        </form>
    </div>

    <footer>
        <p>&copy; 2024 FlixerTrade. Todos los derechos reservados.</p>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>