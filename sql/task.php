<?php
session_start();
require_once(__DIR__ . '/bd.php');

if (empty($_SESSION['user_id'])) {
    die("Erreur : Authentification requise.");
}

if (!isset($_GET['list_id'])) {
    die("Erreur : Aucune liste spécifiée.");
}

$list_id = intval($_GET['list_id']);

$stmt = $pdo->prepare("SELECT * FROM lists WHERE list_id = ? AND user_id = ?");
$stmt->execute([$list_id, $_SESSION['user_id']]);
$list = $stmt->fetch();

if (!$list) {
    die("Erreur : Liste introuvable ou non autorisée.");
}

// Récupérer les tâches triées par position
$stmt = $pdo->prepare("SELECT * FROM tasks WHERE list_id = ? ORDER BY task_id ASC");
$stmt->execute([$list_id]);
$tasks = $stmt->fetchAll();

$stmt = $pdo->prepare("SELECT * FROM lists WHERE parent_list_id = ?");
$stmt->execute([$list_id]);
$sublists = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($list['list_name']); ?> - Tâches</title>
    <link rel="stylesheet" href="/assets/style.css">
    <script src="../assets/js/dragdrop.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        .task-item {
            cursor: move;
            padding: 10px;
            margin-bottom: 8px;
            background: white;
            border-radius: 5px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .task-item.dragging {
            opacity: 0.5;
            transform: scale(1.02);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .drag-handle {
            cursor: grab;
            padding: 8px;
            font-size: 18px;
            color: #666;
        }

        .task-list {
            padding: 15px;
            background: #f8f8f8;
            border-radius: 8px;
            min-height: 50px;
        }

        .task-actions button {
            background: none;
            border: none;
            color: #007BFF;
            cursor: pointer;
            padding: 5px;
        }

        .task-actions button:hover {
            text-decoration: underline;
        }

        .add-task-form, .add-sublist-form {
            margin-top: 15px;
        }

        .add-task-form input,
        .add-task-form textarea,
        .add-sublist-form input {
            display: block;
            width: 100%;
            padding: 8px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .add-task-form button, .add-sublist-form button {
            background: #28a745;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 4px;
            cursor: pointer;
        }

        .add-task-form button:hover, .add-sublist-form button:hover {
            background: #218838;
        }
    </style>
</head>
<body>

<h1><?= htmlspecialchars($list['list_name']); ?></h1>
<a href="../index.php">Retour aux listes</a>

<div class="task-container">
    <div id="tasks-section">
        <ul class="task-list" data-list-id="<?= $list_id ?>">
            <?php foreach ($tasks as $task): ?>
                <li class="task-item"
                    draggable="true"
                    data-task-id="<?= $task['task_id']; ?>"
                    data-list-id="<?= $list_id; ?>"
                    style="background-color: <?= htmlspecialchars($task['color']); ?>;">
                    <div class="drag-handle">☰</div>
                    <span class="task-name"><?= htmlspecialchars($task['task_name']); ?></span>
                    <div class="task-actions">
                        <button class="edit-task" data-task-id="<?= $task['task_id']; ?>">✏️</button>
                        <button class="delete-task" data-task-id="<?= $task['task_id']; ?>">❌</button>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- Formulaire pour ajouter une tâche -->
    <form class="add-task-form" method="POST" action="add_task.php">
        <input type="hidden" name="list_id" value="<?= $list_id ?>">
        <input type="text" name="task_name" placeholder="Nom de la tâche" required>
        <textarea name="description" placeholder="Description (optionnelle)"></textarea>
        <input type="color" name="color" value="#ffffff">
        <button type="submit">Ajouter la tâche</button>
    </form>

    <!-- Formulaire pour ajouter une sous-liste -->
    <form class="add-sublist-form" method="POST" action="sublist.php">
        <input type="hidden" name="parent_list_id" value="<?= $list_id ?>">
        <input type="text" name="sublist_name" placeholder="Nom de la sous-liste" required>
        <button type="submit">Ajouter une sous-liste</button>
    </form>

    <!-- Sous-listes -->
    <div id="sublists-section">
        <?php if ($sublists): ?>
            <h2>Sous-listes</h2>
            <ul>
                <?php foreach ($sublists as $sublist): ?>
                    <li>
                        <a href="?list_id=<?= $sublist['list_id']; ?>">
                            <?= htmlspecialchars($sublist['list_name']); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let draggingElement = null;

        function initDragAndDrop() {
            document.querySelectorAll('.task-item').forEach(task => {
                task.addEventListener('dragstart', handleDragStart);
                task.addEventListener('dragend', handleDragEnd);
                task.addEventListener('dragover', handleDragOver);
                task.addEventListener('drop', handleDrop);
            });
        }

        function handleDragStart(e) {
            draggingElement = e.target;
            e.target.classList.add('dragging');
        }

        function handleDragEnd(e) {
            e.target.classList.remove('dragging');
            draggingElement = null;
        }

        function handleDragOver(e) {
            e.preventDefault();
        }

        function handleDrop(e) {
            e.preventDefault();
            if (draggingElement && e.target.classList.contains('task-item')) {
                e.target.parentNode.insertBefore(draggingElement, e.target);
            }
        }

        initDragAndDrop();

        document.querySelectorAll('.delete-task').forEach(button => {
            button.addEventListener('click', function(e) {
                const taskId = e.target.dataset.taskId;
                if (confirm('Supprimer cette tâche ?')) {
                    fetch('delete_task.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ task_id: taskId })
                    })
                        .then(response => response.json())
                        .then(() => location.reload());
                }
            });
        });
    });
</script>

</body>
</html>
