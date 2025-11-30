<?php

$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'usuarios';

$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($mysqli->connect_errno) {
    error_log("MySQL connection error: " . $mysqli->connect_error);
    die("Error de conexión a la base de datos.");
}
$mysqli->set_charset("utf8mb4");
