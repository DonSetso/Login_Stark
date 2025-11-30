<?php
// backend/registro.php
session_start();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login.html');
    exit;
}

require_once __DIR__ . '/conexion.php';

$username = trim($_POST['usuario'] ?? '');
$email = trim($_POST['correo'] ?? '');
$password = $_POST['password'] ?? '';
$confirm = $_POST['confirmar'] ?? '';

if (!$username || !$email || !$password || !$confirm) {
    $_SESSION['error'] = "Todos los campos son obligatorios.";
    header('Location: ../register.html?error=' . urlencode($_SESSION['error']));
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = "Correo inválido.";
    header('Location: ../register.html?error=' . urlencode($_SESSION['error']));
    exit;
}

if ($password !== $confirm) {
    $_SESSION['error'] = "Las contraseñas no coinciden.";
    header('Location: ../register.html?error=' . urlencode($_SESSION['error']));
    exit;
}

if (strlen($password) < 6) {
    $_SESSION['error'] = "La contraseña debe tener al menos 6 caracteres.";
    header('Location: ../register.html?error=' . urlencode($_SESSION['error']));
    exit;
}

// Revisar existencia de usuario/email
// Check existing user/email
$stmt = $mysqli->prepare("SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1");
if (! $stmt) {
    error_log('DB prepare failed (registro lookup): ' . $mysqli->error);
    $_SESSION['error'] = "Error interno, inténtalo más tarde.";
    header('Location: ../register.html?error=' . urlencode($_SESSION['error']));
    exit;
}
$stmt->bind_param('ss', $username, $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    $_SESSION['error'] = "El usuario o correo ya está registrado.";
    $stmt->close();
    header('Location: ../register.html?error=' . urlencode($_SESSION['error']));
    exit;
}
$stmt->close();

// Insertar usuario
$hash = password_hash($password, PASSWORD_DEFAULT);
// Insert new user
$stmt = $mysqli->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
if (! $stmt) {
    error_log('DB prepare failed (registro insert): ' . $mysqli->error);
    $_SESSION['error'] = "Error interno, inténtalo más tarde.";
    header('Location: ../register.html?error=' . urlencode($_SESSION['error']));
    exit;
}
$stmt->bind_param('sss', $username, $email, $hash);
$ok = $stmt->execute();
$stmt->close();

if ($ok) {
    $_SESSION['success'] = "Registro exitoso. Inicia sesión.";
    header('Location: ../login.html?success=' . urlencode($_SESSION['success']));
    exit;
} else {
    error_log("Registro error: " . $mysqli->error);
    $_SESSION['error'] = "Ocurrió un error al registrar.";
    header('Location: ../register.html?error=' . urlencode($_SESSION['error']));
    exit;
}
