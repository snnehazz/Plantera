<?php
require_once __DIR__ . '/config/database.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $conn->prepare("SELECT * FROM plants WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$plant = $stmt->get_result()->fetch_assoc();

if (!$plant) exit('Plant not found.');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $species = trim($_POST['species'] ?? '');
    $interval = max(1, (int)($_POST['watering_interval'] ?? 7));
    $notes = trim($_POST['notes'] ?? '');

    $update = $conn->prepare(
        "UPDATE plants SET name=?, species=?, watering_interval=?, notes=? WHERE id=?"
    );
    $update->bind_param("ssisi", $name, $species, $interval, $notes, $id);
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
    <title>Edit Plant | Plantera</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header class="site-header">
    <a class="logo" href="index.php">plantera<span>.</span></a>
</header>

<main class="container narrow">
    <a class="back" href="plant.php?id=<?= $id ?>">← Back</a>
    <h1>Edit plant</h1>

    <form class="form-card" method="POST">
        <label>Plant name
            <input type="text" name="name" value="<?= htmlspecialchars($plant['name']) ?>" required>
        </label>

        <label>Species
            <input type="text" name="species" value="<?= htmlspecialchars($plant['species']) ?>">
        </label>

        <label>Water every
            <div class="inline-input">
                <input type="number" name="watering_interval" value="<?= (int)$plant['watering_interval'] ?>" min="1">
                <span>days</span>
            </div>
        </label>

        <label>Notes
            <textarea name="notes" rows="5"><?= htmlspecialchars($plant['notes']) ?></textarea>
        </label>

        <button class="button" type="submit">Save changes</button>
    </form>
</main>
</body>
</html>
