<?php
require __DIR__ . '/config/db.php';
require __DIR__ . '/includes/header.php';

$nombre = $apellido = $telefono = $email = $direccion = $notas = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $notas = trim($_POST['notas'] ?? '');

    if ($nombre === '') {
        $errors[] = 'El nombre es obligatorio.';
    }
    if ($apellido === '') {
        $errors[] = 'El apellido es obligatorio.';
    }
    if ($telefono === '') {
        $errors[] = 'El teléfono es obligatorio.';
    }
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'El email no es válido.';
    }

    if (empty($errors)) {
        $sql = "INSERT INTO contacts (nombre, apellido, telefono, email, direccion, notas)
                VALUES (:nombre, :apellido, :telefono, :email, :direccion, :notas)";
        $stmt = $pdo->prepare($sql);

        try {
            $stmt->execute([
                ':nombre'    => $nombre,
                ':apellido'  => $apellido,
                ':telefono'  => $telefono,
                ':email'     => $email !== '' ? $email : null,
                ':direccion' => $direccion !== '' ? $direccion : null,
                ':notas'     => $notas !== '' ? $notas : null,
            ]);

            $_SESSION['flash_success'] = 'Contacto creado correctamente.';
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            error_log('Error al crear contacto: ' . $e->getMessage());
            $_SESSION['flash_error'] = 'Ocurrió un error al crear el contacto.';
        }
    }
}
?>
<section>
    <h2>Nuevo contacto</h2>

    <?php if (!empty($errors)): ?>
        <div class="alert error">
            <ul>
                <?php foreach ($errors as $err): ?>
                    <li><?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" class="form">
        <label>Nombre*:
            <input type="text" name="nombre" required
                   value="<?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?>">
        </label>
        <label>Apellido*:
            <input type="text" name="apellido" required
                   value="<?= htmlspecialchars($apellido, ENT_QUOTES, 'UTF-8') ?>">
        </label>
        <label>Teléfono*:
            <input type="tel" name="telefono" required
                   value="<?= htmlspecialchars($telefono, ENT_QUOTES, 'UTF-8') ?>">
        </label>
        <label>Email:
            <input type="email" name="email"
                   value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>">
        </label>
        <label>Dirección:
            <input type="text" name="direccion"
                   value="<?= htmlspecialchars($direccion, ENT_QUOTES, 'UTF-8') ?>">
        </label>
        <label>Notas:
            <textarea name="notas" rows="3"><?= htmlspecialchars($notas, ENT_QUOTES, 'UTF-8') ?></textarea>
        </label>

        <div class="form-actions">
            <button type="submit">Guardar</button>
            <a href="index.php" class="btn-secondary">Cancelar</a>
        </div>
    </form>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
