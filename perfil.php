<?php
session_start();
require 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Obtener información del usuario a través de GET, para visualizar un perfil específico
$user_id = isset($_GET['id']) ? $_GET['id'] : $_SESSION['user_id'];

// Obtener información del usuario, incluyendo si es VIP
$stmt = $pdo->prepare("SELECT username, email, profile_picture, bio, signature, is_vip FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    echo "Usuario no encontrado.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $user_id == $_SESSION['user_id']) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $bio = $_POST['bio'] ?? null;
    $signature = $_POST['signature'] ?? null;

    // Manejo de la imagen de perfil
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
        $profile_picture = 'profile_pics/' . uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['profile_picture']['tmp_name'], $profile_picture);
    } else {
        $profile_picture = $user['profile_picture']; // Mantener la imagen actual si no se sube una nueva
    }

    // Actualizar información del usuario
    $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, bio = ?, profile_picture = ?, signature = ? WHERE id = ?");
    $stmt->execute([$username, $email, $bio, $profile_picture, $signature, $_SESSION['user_id']]);
    
    header("Location: perfil.php?id=" . $_SESSION['user_id']); // Redirigir después de actualizar
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
    <title>Perfil de Usuario - FlixerTrade</title>
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
        .profile-container {
            margin: 50px auto;
            max-width: 800px;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .profile-header {
            text-align: center;
        }
        .profile-header img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
        }
        .profile-header h2 {
            margin: 20px 0 10px;
        }
        .profile-header p {
            color: #777;
        }
        .vip-status {
            font-size: 1.2rem;
            color: <?php echo $user['is_vip'] ? '#FFD700' : '#777'; ?>;
        }
        .profile-body {
            margin-top: 30px;
        }
        .profile-body .bio,
        .profile-body .signature {
            margin-bottom: 20px;
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

<!-- Copia del diseño del navbar -->
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


<div class="profile-container">
 <div class="profile-header">
    <img src="<?php echo htmlspecialchars($user['profile_picture'] ?: 'default_profile.png'); ?>" alt="Foto de Perfil">
    <h2><?php echo htmlspecialchars($user['username']); ?></h2>
    <p><?php echo htmlspecialchars($user['email']); ?></p>
    <p class="vip-status">
        <?php echo $user['is_vip'] ? 'Usuario VIP' : 'Usuario No VIP'; ?>
    </p>
</div>

    <div class="profile-body">
        <div class="bio">
            <h3>Biografía</h3>
            <p><?php echo htmlspecialchars($user['bio'] ?? 'Este usuario no ha agregado una biografía.'); ?></p>
        </div>
        <div class="signature">
            <h3>Firma</h3>
            <p><?php echo htmlspecialchars($user['signature'] ?? 'Este usuario no ha agregado una firma.'); ?></p>
        </div>
    </div>

    <?php if ($user_id == $_SESSION['user_id']): ?>
    <!-- Formulario para editar perfil si es el usuario dueño del perfil -->
    <form action="perfil.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="username">Nombre de Usuario</label>
            <input type="text" name="username" id="username" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>">
        </div>
        <div class="form-group">
            <label for="email">Correo Electrónico</label>
            <input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>">
        </div>
        <div class="form-group">
            <label for="bio">Biografía</label>
            <textarea name="bio" id="bio" class="form-control"><?php echo htmlspecialchars($user['bio']); ?></textarea>
        </div>
        <div class="form-group">
            <label for="signature">Firma</label>
            <textarea name="signature" id="signature" class="form-control"><?php echo htmlspecialchars($user['signature']); ?></textarea>
        </div>
        <div class="form-group">
            <label for="profile_picture">Foto de Perfil</label>
            <input type="file" name="profile_picture" id="profile_picture" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
    </form>
    <?php endif; ?>
</div>

<!-- Pie de página -->
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