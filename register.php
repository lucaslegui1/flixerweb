<?php
require 'db_connection.php';
session_start(); // Iniciar sesión

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];

    // Validar longitud de la contraseña
    if (strlen($password) < 6) {
        $error = "La contraseña debe tener al menos 6 caracteres.";
    } else {
        // Verificar si el nombre de usuario ya existe
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $exists = $stmt->fetchColumn();

        if ($exists) {
            $error = "El nombre de usuario ya existe. Por favor elige otro.";
        } else {
            // Insertar el usuario en la base de datos
            $passwordHashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
            if ($stmt->execute([$username, $passwordHashed, $email])) {
                // Guardar el ID y nombre de usuario en la sesión
                $_SESSION['user_id'] = $pdo->lastInsertId();
                $_SESSION['username'] = $username;

                // Redirigir al inicio
                header("Location: index.php");
                exit();
            } else {
                $error = "Error al registrar el usuario. Intenta nuevamente.";
            }
        }
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
    <title>Registro - FlixerTrade</title>
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
        input[type="password"],
        input[type="email"] {
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
        <h1>Registro de Usuario</h1>
    </header>
    <main>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <form action="register.php" method="POST">
            <label for="username">Nombre de usuario:</label>
            <input type="text" name="username" required>

            <label for="email">Correo electrónico:</label>
            <input type="email" name="email" required>

            <label for="password">Contraseña:</label>
            <input type="password" name="password" required>

            <button type="submit">Registrar</button>
        </form>
        <p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a>.</p>
    </main>
</body>
</html>