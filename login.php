<?php
session_start(); // Iniciar sesión
require 'db_connection.php'; // Conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Verificar las credenciales
    $stmt = $pdo->prepare("SELECT id, password, is_vip FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Guardar el ID y el nombre de usuario en la sesión
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['is_vip'] = $user['is_vip'];

        // Redirigir al inicio
        header("Location: index.php");
        exit();
    } else {
        $error = "Credenciales incorrectas. Intenta nuevamente.";
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
    <link rel="stylesheet" href="template.css"> <!-- Asegúrate de tener el CSS del template -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap">
    <title>Iniciar Sesión - FlixerTrade</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
        }

        header {
            text-align: center;
            padding: 40px;
            background-color: #343a40; /* Color del header */
            color: white;
        }

        main {
            max-width: 400px;
            margin: 40px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            margin-bottom: 20px;
            color: #333;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .error {
            color: red;
            margin-top: 10px;
        }

        p {
            text-align: center;
            color: #555;
        }

        a {
            color: #007bff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <header>
        <h1>Iniciar Sesión</h1>
    </header>
    <main>
        <form action="login.php" method="POST">
            <label for="username">Nombre de usuario:</label>
            <input type="text" name="username" required>

            <label for="password">Contraseña:</label>
            <input type="password" name="password" required>

            <button type="submit">Iniciar Sesión</button>
        </form>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <p>¿No tienes una cuenta? <a href="register.php">Regístrate aquí</a>.</p>
    </main>
</body>
</html>