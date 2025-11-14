<?php

$host = '127.0.0.1'; 
$port = 3307;
$db_name = 'agenda_contactos';
$username = 'root';
$password = '123456789'; 
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;port=$port;dbname=$db_name;charset=$charset";




$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,      
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,            
    PDO::ATTR_EMULATE_PREPARES   => false,                       
];

try {
     $pdo = new PDO($dsn, $username, $password, $options);
} catch (\PDOException $e) {
     error_log("Error de conexión a BD: " . $e->getMessage());
     throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}