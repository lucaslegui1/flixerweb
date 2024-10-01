<?php
session_start(); // Iniciar la sesión
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
    <title>FlixerTrade</title>
<meta property="og:title" content="FlixerTrade Web" />
<meta property="og:description" content="Tu aliado en el mundo del trading" />
<meta property="og:image" content="https://flixertrade.online/logo.png" />
<meta property="og:url" content="https://flixertrade.online/" />
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

<div class="hero">
    <img src="logo.png" alt="Logo" class="hero-logo">
    <h1>Bienvenido a FlixerTrade</h1>
    <p>Tu aliado en el mundo del trading</p>
</div>

    <main>
        <section class="blog">
            <div class="container">
                <h2 class="text-center">Últimos Posts</h2>
                <div class="row">
                    <?php
                    require 'db_connection.php';

                    // Definir cuántas publicaciones mostrar por página
                    $limit = 10;

                    // Obtener la página actual desde la URL, si no está definida se establece en 1
                    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                    $offset = ($page - 1) * $limit;

                    // Obtener el total de publicaciones
                    $total_posts_query = $pdo->query("SELECT COUNT(*) FROM posts");
                    $total_posts = $total_posts_query->fetchColumn();
                    $total_pages = ceil($total_posts / $limit);

                    // Obtener las publicaciones del blog con paginación
                    $stmt = $pdo->prepare("SELECT posts.id, posts.title, posts.content, posts.created_at, users.username, posts.image FROM posts JOIN users ON posts.user_id = users.id ORDER BY posts.created_at DESC LIMIT :limit OFFSET :offset");
                    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
                    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
                    $stmt->execute();

                    while ($post = $stmt->fetch()) {
                        echo "<div class='col-md-4'>";
                        echo "<div class='card'>";

                        // Asegurarse de que hay una imagen
                        if (!empty($post['image'])) {
                            echo "<img src='" . htmlspecialchars($post['image']) . "' alt='Imagen de la publicación'>"; // Cambiar a la ruta de la imagen que desees
                        } else {
                            echo "<img src='default.jpg' alt='Imagen de la publicación'>"; // Imagen por defecto
                        }

                        echo "<div class='card-body'>";
                        echo "<h3 class='card-title'>" . htmlspecialchars($post['title']) . "</h3>";
                        echo "<p class='card-text'><em>Por " . htmlspecialchars($post['username']) . " el " . $post['created_at'] . "</em></p>";
                        // Mostrar una vista previa del contenido, limitando el texto
                        echo "<div class='card-text'>" . htmlspecialchars_decode(substr($post['content'], 0, 150)) . "...</div>"; // Limitando a 150 caracteres

                        // Enlace a la publicación completa
                        echo "<a href='ver_publicacion.php?id=" . $post['id'] . "' class='btn btn-primary'>Leer Más</a>";
                        echo "</div></div></div>";
                    }
                    ?>
                </div>

                <!-- Paginación -->
                <nav aria-label="Page navigation">
                    <ul class="pagination">
                        <li class="page-item <?php if($page <= 1){ echo 'disabled'; } ?>">
                            <a class="page-link" href="<?php if($page > 1) { echo "?page=" . ($page - 1); } ?>">Anterior</a>
                        </li>

                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php if($page == $i){ echo 'active'; } ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>

                        <li class="page-item <?php if($page >= $total_pages){ echo 'disabled'; } ?>">
                            <a class="page-link" href="<?php if($page < $total_pages) { echo "?page=" . ($page + 1); } ?>">Siguiente</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> FlixerTrade. Todos los derechos reservados.</p>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
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