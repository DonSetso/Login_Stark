<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login.html');
    exit;
}

require_once __DIR__ . '/conexion.php';

$username = trim($_POST['usuario'] ?? '');
$password = $_POST['password'] ?? '';

if (!$username || !$password) {
    $_SESSION['error'] = "Proporciona usuario y contraseña.";
    header('Location: ../login.html?error=' . urlencode($_SESSION['error']));
    exit;
}

// Buscar por username o email
// Prepare statement and check for errors
$stmt = $mysqli->prepare("SELECT id, username, password_hash FROM users WHERE username = ? OR email = ? LIMIT 1");
if (! $stmt) {
    error_log('DB prepare failed (login lookup): ' . $mysqli->error);
    $_SESSION['error'] = "Error interno, inténtalo más tarde.";
    header('Location: ../login.html?error=' . urlencode($_SESSION['error']));
    exit;
}
$stmt->bind_param('ss', $username, $username);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    $_SESSION['error'] = "Usuario o contraseña incorrectos.";
    $stmt->close();
    header('Location: ../login.html?error=' . urlencode($_SESSION['error']));
    exit;
}

if (! $stmt->bind_result($id, $dbUser, $hash)) {
    error_log('DB bind_result failed (login lookup)');
    $stmt->close();
    $_SESSION['error'] = "Error interno, inténtalo más tarde.";
    header('Location: ../login.html?error=' . urlencode($_SESSION['error']));
    exit;
}
$stmt->fetch();
$stmt->close();

if (password_verify($password, $hash)) {
    // Login exitoso
    session_regenerate_id(true);
    $_SESSION['user_id'] = $id;
    $_SESSION['username'] = $dbUser;
    header('Location: ../index.php');
    exit;
} else {
    $_SESSION['error'] = "Usuario o contraseña incorrectos.";
    header('Location: ../login.html?error=' . urlencode($_SESSION['error']));
    exit;
}
