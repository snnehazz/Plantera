<?php
require_once __DIR__ . '/config/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $species = trim($_POST['species'] ?? '');
    $watering_interval = (int)($_POST['watering_interval'] ?? 7);
    $last_watered = $_POST['last_watered'] ?: date('Y-m-d');
    $notes = trim($_POST['notes'] ?? '');

    if ($name === '') {
        $error = 'Plant name is required.';
    } else {
        $next_watering = date('Y-m-d', strtotime($last_watered . ' +' . max(1, $watering_interval) . ' days'));

        $stmt = $conn->prepare(
            "INSERT INTO plants (name, species, watering_interval, last_watered, next_watering, notes)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("ssisss", $name, $species, $watering_interval, $last_watered, $next_watering, $notes);
        $stmt->execute();

        header('Location: index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Plant | Plantera</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header class="site-header">
    <a class="logo" href="index.php">plantera<span>.</span></a>
    <nav>
        <a href="index.php">My Plants</a>
        <a class="active" href="add-plant.php">Add Plant</a>
    </nav>
</header>

<main class="container narrow">
    <a class="back" href="index.php">← Back</a>
    <h1>Add a plant</h1>
    <p class="muted">Start with the basics. You can add more details later.</p>

    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form class="form-card" method="POST">
        <label>
            Plant name
            <input type="text" name="name" placeholder="e.g. Monstera" required>
        </label>

        <label>
            Species <span class="optional">optional</span>
            <input type="text" name="species" placeholder="e.g. Monstera deliciosa">
        </label>

        <label>
            Water every
            <div class="inline-input">
                <input type="number" name="watering_interval" value="7" min="1" required>
                <span>days</span>
            </div>
        </label>

        <label>
            Last watered
            <input type="date" name="last_watered" value="<?= date('Y-m-d') ?>">
        </label>

        <label>
            Notes <span class="optional">optional</span>
            <textarea name="notes" rows="4" placeholder="Anything you want to remember..."></textarea>
        </label>

        <button class="button" type="submit">Add plant</button>
    </form>
</main>
</body>
</html>
