<?php
session_start();
require 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    // Si el usuario no está logueado, redirige a la página de inicio
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['content'], $_POST['post_id'])) {
        $content = trim($_POST['content']);
        $post_id = $_POST['post_id'];
        $user_id = $_SESSION['user_id'];

        if (!empty($content)) {
            // Inserta el comentario en la base de datos
            $stmt = $pdo->prepare("INSERT INTO comments (post_id, user_id, content, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$post_id, $user_id, $content]);

            // Redirige de nuevo a la página de la publicación
            header("Location: ver_publicacion.php?id=" . $post_id);
            exit;
        } else {
            // Si el contenido está vacío, redirige a la publicación con un mensaje de error
            header("Location: ver_publicacion.php?id=" . $post_id . "&error=empty_content");
            exit;
        }
    }
}

// Redirige a la página de inicio si algo sale mal
header('Location: index.php');
exit;