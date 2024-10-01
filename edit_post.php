<?php
session_start();
require 'db_connection.php';

if (!isset($_GET['id'])) {
    // Redirecciona al índice si no se proporciona un ID de publicación válido
    header('Location: index.php');
    exit;
}

$post_id = $_GET['id'];

// Consulta para obtener los detalles de la publicación
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->execute([$post_id]);
$post = $stmt->fetch();

if (!$post) {
    // Redirecciona al índice si no se encuentra la publicación
    header('Location: index.php');
    exit;
}

// Verifica si el usuario tiene permiso para editar la publicación
if ($post['user_id'] !== $_SESSION['user_id']) {
    // Redirecciona al índice si el usuario no es el dueño de la publicación
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Se ha enviado el formulario
    $title = $_POST['title'];
    $content = $_POST['content'];
    $image = null; // Inicializar variable de imagen

    // Subir imagen si está presente
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $image = 'uploads/' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $image);
    }

    // Actualiza la publicación en la base de datos
    $updateStmt = $pdo->prepare("UPDATE posts SET title = ?, content = ?, image = ? WHERE id = ?");
    $updateStmt->execute([$title, $content, $image, $post_id]);

    // Redirecciona al usuario a la página de la publicación después de editar
    header('Location: ver_publicacion.php?id=' . $post_id);
    exit;
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
    <title>Editar Publicación - <?php echo htmlspecialchars($post['title']); ?></title>
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


    <div class="container">
        <h2 class="text-center">Editar Publicación</h2>
        <form action="edit_post.php?id=<?php echo $post_id; ?>" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Título</label>
                <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required>
            </div>
            <div class="form-group">
                <label for="content">Contenido</label>
                <textarea class="form-control" id="content" name="content" rows="5" required><?php echo htmlspecialchars($post['content']); ?></textarea>
            </div>
            <div class="form-group">
                <label for="image">Imagen (opcional)</label>
                <input type="file" class="form-control-file" id="image" name="image">
                <small class="form-text text-muted">Deja en blanco si no deseas cambiar la imagen.</small>
            </div>
            <button type="submit" class="btn btn-primary">Actualizar Publicación</button>
        </form>
    </div>

    <footer>
        <p>&copy; 2024 FlixerTrade. Todos los derechos reservados.</p>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>