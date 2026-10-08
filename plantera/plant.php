<?php
require_once __DIR__ . '/config/database.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $conn->prepare("SELECT * FROM plants WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$plant = $result->fetch_assoc();

if (!$plant) {
    http_response_code(404);
    exit('Plant not found.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['water'])) {
    $today = date('Y-m-d');
    $next = date('Y-m-d', strtotime($today . ' +' . max(1, (int)$plant['watering_interval']) . ' days'));

    $update = $conn->prepare("UPDATE plants SET last_watered = ?, next_watering = ? WHERE id = ?");
    $update->bind_param("ssi", $today, $next, $id);
    $update->execute();

    header("Location: plant.php?id=" . $id);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($plant['name']) ?> | Plantera</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header class="site-header">
    <a class="logo" href="index.php">plantera<span>.</span></a>
    <nav>
        <a href="index.php">My Plants</a>
        <a href="add-plant.php">Add Plant</a>
    </nav>
</header>

<main class="container narrow">
    <a class="back" href="index.php">← My plants</a>

    <section class="profile">
        <div class="profile-image">
            <?php if (!empty($plant['photo'])): ?>
                <img src="<?= htmlspecialchars($plant['photo']) ?>" alt="">
            <?php else: ?>
                <span>🌿</span>
            <?php endif; ?>
        </div>

        <div>
            <p class="eyebrow">PLANT PROFILE</p>
            <h1><?= htmlspecialchars($plant['name']) ?></h1>
            <p class="muted"><?= htmlspecialchars($plant['species'] ?: 'Species not added yet') ?></p>
        </div>
    </section>

    <div class="care-card">
        <div>
            <span class="label">NEXT WATERING</span>
            <strong><?= htmlspecialchars($plant['next_watering'] ?: 'Not set') ?></strong>
        </div>
        <form method="POST">
            <button class="button" name="water" value="1">💧 Watered</button>
        </form>
    </div>

    <div class="details">
        <div><span>Water every</span><strong><?= (int)$plant['watering_interval'] ?> days</strong></div>
        <div><span>Last watered</span><strong><?= htmlspecialchars($plant['last_watered'] ?: 'Not recorded') ?></strong></div>
        <div><span>Added</span><strong><?= htmlspecialchars($plant['created_at']) ?></strong></div>
    </div>

    <?php if ($plant['notes']): ?>
        <section class="notes">
            <h2>Notes</h2>
            <p><?= nl2br(htmlspecialchars($plant['notes'])) ?></p>
        </section>
    <?php endif; ?>
</main>
</body>
</html>
