<?php
session_start();
require_once(__DIR__ . '/bd.php');

if (empty($_SESSION['user_id'])) {
    die("Erreur : Authentification requise.");
}

// Création de la liste
$stmt = $pdo->prepare("
    INSERT INTO lists (user_id, list_name, color, created_at, updated_at) 
    VALUES (:user_id, :list_name, :color, NOW(), NOW())
");
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['list_name'])) {
$list_name = trim($_POST['list_name']);
if (empty($list_name)) {
    die("Erreur : Le nom de la liste ne peut pas être vide.");
}

$title = "To-Do List :" . $list_name;
$color = "#ffffff";

$stmt->execute([
    ':user_id' => $_SESSION['user_id'],
    ':list_name' => $title,
    ':color' => $color
]);

$todo_id = $pdo->lastInsertId(); // ID réel de la liste
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <!-- Meta tags -->
    <script src="../assets/script.js" defer></script>
</head>
<body>
<div class='todo-list' data-list-id="<?= $todo_id ?>">
    <!-- Structure générée dynamiquement -->
</div>
</body>
</html>