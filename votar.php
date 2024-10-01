<?php
session_start();
require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $signal_id = $_POST['signal_id'] ?? null;
    $vote = $_POST['vote'] ?? null;

    // Debug: mostrar valores recibidos
    error_log("user_id: $user_id, signal_id: $signal_id, vote: $vote");

    // Verificar si el signal_id y el voto son válidos
    if ($signal_id && $vote) {
        // Verificar si el usuario ya votó
        $stmt = $pdo->prepare("SELECT * FROM votes WHERE user_id = ? AND signal_id = ?");
        $stmt->execute([$user_id, $signal_id]);
        $existing_vote = $stmt->fetch();

        try {
            if ($existing_vote) {
                // Si ya votó, deshacer el voto
                $pdo->prepare("DELETE FROM votes WHERE user_id = ? AND signal_id = ?")->execute([$user_id, $signal_id]);
                // Debug: registro de la eliminación
                error_log("Voto eliminado para user_id: $user_id, signal_id: $signal_id");
            } else {
                // Si no ha votado, insertar el nuevo voto
                $pdo->prepare("INSERT INTO votes (user_id, signal_id, vote) VALUES (?, ?, ?)")->execute([$user_id, $signal_id, $vote]);
                // Debug: registro de la inserción
                error_log("Voto registrado para user_id: $user_id, signal_id: $signal_id, vote: $vote");
            }
            // Después de la inserción o eliminación, redirigir a historial.php con el parámetro de página actual
            $current_page = $_POST['current_page'] ?? 1; // Obtener la página actual
            header("Location: historial.php?page=$current_page");
            exit();
        } catch (PDOException $e) {
            // Manejo de errores
            error_log("Error al votar: " . $e->getMessage());
            $_SESSION['error'] = 'Hubo un error al procesar tu voto. Inténtalo de nuevo.';
            header("Location: historial.php");
            exit();
        }
    } else {
        // Manejo de error si falta información
        $_SESSION['error'] = 'Información de voto inválida.';
        header("Location: historial.php");
        exit();
    }
} else {
    // Redirigir a la página de login si no está autenticado
    header("Location: login.php");
    exit();
}
?>