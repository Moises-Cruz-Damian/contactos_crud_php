<?php

$host     = "sql204.ezyro.com";
$db_name  = "ezyro_40415301_agenda_contactos";
$username = "ezyro_40415301";
$password = "****";   
$charset  = "utf8mb4";

$dsn = "mysql:host=$host;dbname=$db_name;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    
    die("Error de conexión a BD: " . $e->getMessage());
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

