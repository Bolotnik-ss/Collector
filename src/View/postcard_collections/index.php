<?php
// Проверка на авторизацию
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit();
}

$page_title = 'Коллекции открыток';
include __DIR__ . '/../layout/header.php';
?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title"><?php echo $page_title; ?></h1>
        <?php if (!empty($collections)): ?>
            <a href="/postcard_collection/create" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Создать коллекцию
            </a>
        <?php endif; ?>
    </div>

    <?php if (empty($collections)): ?>
    <div class="text-center py-5">
            <i class="bi bi-card-image display-1 text-muted"></i>
            <h3 class="mt-3">У вас пока нет коллекций</h3>
        <p class="text-muted">Создайте свою первую коллекцию открыток</p>
            <a href="/postcard_collection/create" class="btn btn-primary mt-3">
            <i class="bi bi-plus-lg"></i> Создать коллекцию
            </a>
        </div>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php foreach ($collections as $collection): ?>
                <div class="col">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($collection['name']); ?></h5>
                            <p class="card-text text-muted"><?php echo htmlspecialchars($collection['description']); ?></p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-primary">
                                    <i class="bi bi-card-image"></i> <?php echo $collection['postcard_count']; ?> открыток
                                </span>
                                <div class="btn-group">
                                    <a href="/postcard_collection/<?php echo $collection['id']; ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="/postcard_collection/edit/<?php echo $collection['id']; ?>" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="/postcard_collection/delete/<?php echo $collection['id']; ?>" 
                                       class="btn btn-sm btn-outline-danger"
                                       onclick="return confirm('Вы уверены, что хотите удалить эту коллекцию?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-muted">
                            <small>Создано: <?php echo date('d.m.Y', strtotime($collection['created_at'])); ?></small>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?> 