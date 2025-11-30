<?php
session_start();

// Página principal simplificada: solo un saludo minimalista.
if (!isset($_SESSION['user_id'])) {
	header('Location: login.html');
	exit;
}

$displayName = htmlspecialchars($_SESSION['username'] ?? 'Usuario', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Principal</title>
	<link rel="stylesheet" href="css/log_Style.css">
</head>
<body>
	<main style="min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px;">
		<div style="text-align:center;max-width:800px;width:100%;">
			<h1 style="font-size:2.8rem;margin:0 0 8px;color:#fff;">Bienvenido, <?= $displayName ?></h1>
			<p style="margin:12px 0 0;font-size:1rem;color:#dfe">¡Has iniciado sesión correctamente.</p>
			<p style="margin-top:18px;"><a href="logout.php" style="color:#ffdddd;text-decoration:none;">Cerrar sesión</a></p>
		</div>
	</main>
</body>
</html>
