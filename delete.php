<?php
require __DIR__ . '/config/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['flash_error'] = 'Petición inválida.';
    header('Location: index.php');
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    $_SESSION['flash_error'] = 'Contacto no válido.';
    header('Location: index.php');
    exit;
}

$sql = "DELETE FROM contacts WHERE id = :id";
$stmt = $pdo->prepare($sql);

try {
    $stmt->execute([':id' => $id]);
    $_SESSION['flash_success'] = 'Contacto eliminado correctamente.';
} catch (PDOException $e) {
    error_log('Error al eliminar contacto: ' . $e->getMessage());
    $_SESSION['flash_error'] = 'Ocurrió un error al eliminar el contacto.';
}

header('Location: index.php');
exit;
