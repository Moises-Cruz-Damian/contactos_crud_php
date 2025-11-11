<?php
require __DIR__ . '/config/db.php';
require __DIR__ . '/includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    $_SESSION['flash_error'] = 'Contacto no válido.';
    header('Location: index.php');
    exit;
}

$sql = "SELECT * FROM contacts WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id]);
$contact = $stmt->fetch();

if (!$contact) {
    $_SESSION['flash_error'] = 'Contacto no encontrado.';
    header('Location: index.php');
    exit;
}

$nombre = $contact['nombre'];
$apellido = $contact['apellido'];
$telefono = $contact['telefono'];
$email = $contact['email'];
$direccion = $contact['direccion'];
$notas = $contact['notas'];

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
        $sql = "UPDATE contacts
                SET nombre = :nombre,
                    apellido = :apellido,
                    telefono = :telefono,
                    email = :email,
                    direccion = :direccion,
                    notas = :notas
                WHERE id = :id";

        $stmt = $pdo->prepare($sql);

        try {
            $stmt->execute([
                ':nombre'    => $nombre,
                ':apellido'  => $apellido,
                ':telefono'  => $telefono,
                ':email'     => $email !== '' ? $email : null,
                ':direccion' => $direccion !== '' ? $direccion : null,
                ':notas'     => $notas !== '' ? $notas : null,
                ':id'        => $id,
            ]);

            $_SESSION['flash_success'] = 'Contacto actualizado correctamente.';
            header('Location: view.php?id=' . $id);
            exit;
        } catch (PDOException $e) {
            error_log('Error al actualizar contacto: ' . $e->getMessage());
            $_SESSION['flash_error'] = 'Ocurrió un error al actualizar el contacto.';
        }
    }
}
?>
<section>
    <h2>Editar contacto</h2>

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
                   value="<?= htmlspecialchars($email ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </label>
        <label>Dirección:
            <input type="text" name="direccion"
                   value="<?= htmlspecialchars($direccion ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </label>
        <label>Notas:
            <textarea name="notas" rows="3"><?= htmlspecialchars($notas ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
        </label>

        <div class="form-actions">
            <button type="submit">Actualizar</button>
            <a href="view.php?id=<?= (int)$id ?>" class="btn-secondary">Cancelar</a>
        </div>
    </form>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
