<?php
require_once __DIR__ . '/config/database.php';

$plants = [];
$result = $conn->query("SELECT * FROM plants ORDER BY created_at DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $plants[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plantera | My Plants</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header class="site-header">
    <a class="logo" href="index.php">plantera<span>.</span></a>
    <nav>
        <a class="active" href="index.php">My Plants</a>
        <a href="add-plant.php">Add Plant</a>
    </nav>
</header>

<main class="container">
    <section class="hero">
        <div>
            <p class="eyebrow">YOUR DIGITAL GARDEN</p>
            <h1>Take care of what you grow.</h1>
            <p class="muted">Keep your plants, watering schedule, and growth notes in one quiet little corner.</p>
        </div>
        <a class="button" href="add-plant.php">+ Add plant</a>
    </section>

    <section>
        <div class="section-heading">
            <h2>My plants</h2>
            <span><?= count($plants) ?> plant<?= count($plants) === 1 ? '' : 's' ?></span>
        </div>

        <?php if (!$plants): ?>
            <div class="empty">
                <h3>No plants yet 🌱</h3>
                <p>Add your first plant and we'll start tracking it.</p>
                <a class="button" href="add-plant.php">Add your first plant</a>
            </div>
        <?php else: ?>
            <div class="plant-grid">
                <?php foreach ($plants as $plant): ?>
                    <a class="plant-card" href="plant.php?id=<?= (int)$plant['id'] ?>">
                        <div class="plant-image">
                            <?php if (!empty($plant['photo'])): ?>
                                <img src="<?= htmlspecialchars($plant['photo']) ?>" alt="">
                            <?php else: ?>
                                <span>🌿</span>
                            <?php endif; ?>
                        </div>
                        <div class="plant-info">
                            <h3><?= htmlspecialchars($plant['name']) ?></h3>
                            <p><?= htmlspecialchars($plant['species'] ?: 'Plant') ?></p>
                            <?php if ($plant['next_watering']): ?>
                                <small>Next watering: <?= htmlspecialchars($plant['next_watering']) ?></small>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</main>
</body>
</html>
