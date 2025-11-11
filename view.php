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
?>
<section>
    <h2>Detalle del contacto</h2>

    <div class="card">
        <p><strong>Nombre:</strong>
            <?= htmlspecialchars($contact['nombre'] . ' ' . $contact['apellido'], ENT_QUOTES, 'UTF-8') ?>
        </p>
        <p><strong>Teléfono:</strong>
            <?= htmlspecialchars($contact['telefono'], ENT_QUOTES, 'UTF-8') ?>
        </p>
        <?php if (!empty($contact['email'])): ?>
            <p><strong>Email:</strong>
                <?= htmlspecialchars($contact['email'], ENT_QUOTES, 'UTF-8') ?>
            </p>
        <?php endif; ?>
        <?php if (!empty($contact['direccion'])): ?>
            <p><strong>Dirección:</strong>
                <?= htmlspecialchars($contact['direccion'], ENT_QUOTES, 'UTF-8') ?>
            </p>
        <?php endif; ?>
        <?php if (!empty($contact['notas'])): ?>
            <p><strong>Notas:</strong>
                <?= nl2br(htmlspecialchars($contact['notas'], ENT_QUOTES, 'UTF-8')) ?>
            </p>
        <?php endif; ?>
        <p><strong>Creado:</strong>
            <?= htmlspecialchars($contact['created_at'], ENT_QUOTES, 'UTF-8') ?>
        </p>
    </div>

    <div class="actions actions-detail">
        <a href="edit.php?id=<?= (int)$contact['id'] ?>">Editar</a>
        <form action="delete.php" method="post" class="inline-form"
              onsubmit="return confirm('¿Seguro que deseas eliminar este contacto?');">
            <input type="hidden" name="id" value="<?= (int)$contact['id'] ?>">
            <button type="submit">Eliminar</button>
        </form>
        <a href="index.php" class="btn-secondary">Volver al listado</a>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
