<?php
require __DIR__ . '/config/db.php';
require __DIR__ . '/includes/header.php';

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

/* ---- WHERE + parámetros POSICIONALES ---- */
$where = '';
$paramsCount = [];
$paramsList  = [];

if ($q !== '') {
    $like = '%' . $q . '%';
    $where = "WHERE nombre LIKE ? OR apellido LIKE ? OR telefono LIKE ?";
    // Para COUNT y para LISTA usamos el mismo patrón de parámetros
    $paramsCount = [$like, $like, $like];
    $paramsList  = [$like, $like, $like];
}

/* ---------- total de registros ---------- */
$sqlCount = "SELECT COUNT(*) FROM contacts $where";
$stmt = $pdo->prepare($sqlCount);
$stmt->execute($paramsCount);          // si no hay búsqueda, $paramsCount = []
$total = (int)$stmt->fetchColumn();
$totalPages = max(1, (int)ceil($total / $perPage));

/* ---------- listado paginado ---------- */
/* OJO: LIMIT/OFFSET concatenados como enteros sanitizados para evitar HY093 */
$limit  = (int)$perPage;
$off    = (int)$offset;

$sqlList = "SELECT * FROM contacts $where
            ORDER BY created_at DESC, id DESC
            LIMIT $limit OFFSET $off";
$stmt = $pdo->prepare($sqlList);
$stmt->execute($paramsList);           // si no hay búsqueda, $paramsList = []
$contacts = $stmt->fetchAll();
?>
<section>
    <h2>Listado de contactos</h2>

    <form method="get" class="search-form">
        <input type="text" name="q" placeholder="Buscar por nombre, apellido o teléfono"
               value="<?= htmlspecialchars($q, ENT_QUOTES, 'UTF-8') ?>">
        <button type="submit">Buscar</button>
        <?php if ($q !== ''): ?>
            <a href="index.php" class="btn-secondary">Limpiar</a>
        <?php endif; ?>
    </form>

    <?php if ($total === 0): ?>
        <p>No hay contactos registrados.</p>
    <?php else: ?>
        <table class="table">
            <thead>
            <tr>
                <th>Nombre</th>
                <th>Teléfono</th>
                <th>Email</th>
                <th class="actions-col">Acciones</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($contacts as $c): ?>
                <tr>
                    <td><?= htmlspecialchars($c['nombre'] . ' ' . $c['apellido'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($c['telefono'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($c['email'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="actions">
                        <a href="view.php?id=<?= (int)$c['id'] ?>">Ver</a>
                        <a href="edit.php?id=<?= (int)$c['id'] ?>">Editar</a>
                        <form action="delete.php" method="post" class="inline-form"
                              onsubmit="return confirm('¿Seguro que deseas eliminar este contacto?');">
                            <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
                            <button type="submit">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                    <?php
                    $query = ['page' => $p];
                    if ($q !== '') $query['q'] = $q;
                    $url = 'index.php?' . http_build_query($query);
                    ?>
                    <a href="<?= htmlspecialchars($url, ENT_QUOTES, 'UTF-8') ?>"
                       class="<?= $p === $page ? 'active' : '' ?>"><?= $p ?></a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
