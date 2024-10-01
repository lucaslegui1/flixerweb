<?php
session_start();
require 'db_connection.php';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$post_id = $_GET['id'];

// Consulta para obtener los detalles de la publicación y la información del autor
$stmt = $pdo->prepare("SELECT posts.id, posts.title, posts.content, posts.created_at, posts.image, users.username, users.profile_picture, users.bio, users.signature, users.is_vip, users.created_at AS user_created_at, users.posts_count, posts.user_id FROM posts JOIN users ON posts.user_id = users.id WHERE posts.id = ?");
$stmt->execute([$post_id]);
$post = $stmt->fetch();

if (!$post) {
    header('Location: index.php');
    exit;
}

// Verifica si el usuario está autenticado y es el autor de la publicación
$is_author = isset($_SESSION['user_id']) && $_SESSION['user_id'] == $post['user_id'];

// Manejo de comentarios
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $comment_content = $_POST['comment'];
    $user_id = $_SESSION['user_id'];

    if (!empty($comment_content)) {
        // Inserta el comentario en la base de datos
        $stmt = $pdo->prepare("INSERT INTO comments (post_id, user_id, content, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$post_id, $user_id, $comment_content]);
    }
}

// Consulta para obtener los comentarios
$stmt = $pdo->prepare("SELECT comments.content, comments.created_at, users.username, users.profile_picture FROM comments JOIN users ON comments.user_id = users.id WHERE comments.post_id = ? ORDER BY comments.created_at DESC");
$stmt->execute([$post_id]);
$comments = $stmt->fetchAll();
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
    <title>Ver Publicación - <?php echo htmlspecialchars($post['title']); ?></title>
    <style>
        /* Estilos existentes */
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
            height: auto; /* Cambiar a auto para evitar que se corte */
            object-fit: cover; /* Mantener la proporción */
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

        .container {
            margin-top: 40px;
        }

        .card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .card-body {
            padding: 20px;
        }

        .profile-container {
            display: flex;
            flex-direction: row;
            align-items: center;
            border: 1px solid #ddd;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            background-color: #f9f9f9;
        }

        .profile-container img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin-right: 20px;
        }

        .profile-info {
            text-align: left;
        }

        .profile-info h4 {
            margin: 0;
            color: #343a40;
        }

        .profile-info p {
            margin: 5px 0;
            color: #777;
        }

        .profile-info .signature {
            font-style: italic;
            color: #555;
        }

        .comment-section {
            margin-top: 40px;
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
        }

        .comment {
            border-bottom: 1px solid #ddd;
            padding: 10px 0;
            display: flex;
            align-items: flex-start;
        }

        .comment img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .comment-content {
            flex-grow: 1;
        }

        .comment-content p {
            margin: 0;
        }

        @media (max-width: 768px) {
            .profile-container {
                flex-direction: column;
                text-align: center;
            }

            .profile-container img {
                margin: 0 auto 10px;
            }
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
        <div class="blog">
            <div class="profile-container">
                <img src="<?php echo htmlspecialchars($post['profile_picture']); ?>" alt="Foto de Perfil">
                <div class="profile-info">
                    <h4><?php echo htmlspecialchars($post['username']); ?></h4>
                    <p>Registrado el: <?php echo date('d/m/Y', strtotime($post['user_created_at'])); ?></p>
                    <p><?php echo htmlspecialchars($post['bio']); ?></p>
                    <?php if ($post['is_vip']): ?>
                        <p><strong>VIP</strong></p>
                    <?php else: ?>
                        <p><strong>No VIP</strong></p>
                    <?php endif; ?>
                </div>
            </div>
<h2 class="text-center"><?php echo htmlspecialchars($post['title']); ?></h2>
            <div class="card">
                <div class="card-body">
                    <p><?php echo nl2br(html_entity_decode(htmlspecialchars($post['content']))); ?></p>
                    <?php if ($post['image']): ?>
                        <img src="<?php echo htmlspecialchars($post['image']); ?>" class="img-fluid" alt="Imagen de la publicación">
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($is_author): ?>
                <a href="edit_post.php?id=<?php echo $post['id']; ?>" class="btn btn-warning">Editar Publicación</a>
            <?php endif; ?>
            <a href="index.php" class="btn btn-secondary">Volver a Inicio</a>

            <div class="comment-section">
                <h3>Comentarios</h3>
                <form action="" method="POST">
                    <div class="form-group">
                        <textarea name="comment" class="form-control" rows="3" placeholder="Deja tu comentario..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Comentar</button>
                </form>

                <?php foreach ($comments as $comment): ?>
                    <div class="comment">
                        <img src="<?php echo htmlspecialchars($comment['profile_picture']); ?>" alt="Foto de Perfil">
                        <div class="comment-content">
                            <strong><?php echo htmlspecialchars($comment['username']); ?></strong>
                            <p><?php echo nl2br(htmlspecialchars($comment['content'])); ?></p>
                            <small><?php echo date('d/m/Y H:i', strtotime($comment['created_at'])); ?></small>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <footer>
        <p>© <?php echo date('Y'); ?> FlixerTrade. Todos los derechos reservados.</p>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            $(window).scroll(function() {
                if ($(this).scrollTop() > 50) {
                    $('.navbar').addClass('scrolled');
                } else {
                    $('.navbar').removeClass('scrolled');
                }
            });
        });
    </script>
</body>
</html>