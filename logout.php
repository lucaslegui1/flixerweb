<?php
session_start();
session_destroy(); // Destruir la sesi�n
header("Location: login.php"); // Redirigir a la p�gina de inicio de sesi�n
exit();
?>
