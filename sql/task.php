<?php
session_start();
require_once(__DIR__ . '/bd.php');

if (empty($_SESSION['user_id'])) {
    die("Erreur : Authentification requise.");
}

// Vérifier si un ID de liste est passé en paramètre
if (!isset($_GET['list_id'])) {
    die("Erreur : Aucune liste spécifiée.");
}

$list_id = intval($_GET['list_id']);

// Vérifier si la liste appartient à l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM lists WHERE list_id = ? AND user_id = ?");
$stmt->execute([$list_id, $_SESSION['user_id']]);
$list = $stmt->fetch();

if (!$list) {
    die("Erreur : Liste introuvable ou non autorisée.");
}

// Récupérer les tâches de cette liste
$stmt = $pdo->prepare("SELECT * FROM tasks WHERE list_id = ? ORDER BY color ASC");
$stmt->execute([$list_id]);
$tasks = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($list['list_name']); ?> - Tâches</title>
    <link rel="stylesheet" href="/assets/style.css">
    <script src="/assets/script.js" defer></script>
</head>
<body>

<h1><?= htmlspecialchars($list['list_name']); ?></h1>
<a href="index.php">Retour aux listes</a>

<div class="task-container">
    <?php if ($tasks): ?>
        <ul class="task-list">
            <?php foreach ($tasks as $task): ?>
                <li class="task-item" data-task-id="<?= $task['task_id']; ?>" style="background-color: <?= htmlspecialchars($task['color']); ?>;">
                    <?= htmlspecialchars($task['task_name']); ?>
                    <button class="delete-task" data-task-id="<?= $task['task_id']; ?>">Supprimer</button>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Aucune tâche pour cette liste.</p>
    <?php endif; ?>

    <!-- Formulaire pour ajouter une tâche -->
    <form class="add-task-form" action="add_task.php" method="POST">
        <input type="hidden" name="list_id" value="<?= $list_id; ?>">
        <input type="text" name="task_name" placeholder="Nouvelle tâche" required>
        <input type="color" name="color" value="#ffffff"> <!-- Sélecteur de couleur -->
        <button type="submit">Ajouter</button>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.delete-task').forEach(button => {
            button.addEventListener('click', (e) => {
                const taskId = e.target.dataset.taskId;
                fetch('/assets/delete_task.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ task_id: taskId })
                }).then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            e.target.parentElement.remove();
                        }
                    });
            });
        });
    });
</script>

</body>
</html>
