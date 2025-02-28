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
$stmt = $pdo->prepare("SELECT * FROM tasks WHERE list_id = ? ORDER BY task_id ASC"); // Updated line
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
        .task-item {
            cursor: move;
            position: relative;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .task-item.dragging {
            opacity: 0.5;
            transform: scale(1.02);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .task-item.drag-over {
            border-top: 2px solid #0066cc;
        }

        .drag-handle {
            cursor: move;
            padding: 0 8px;
            color: #666;
            font-size: 20px;
            display: inline-block;
            vertical-align: middle;
        }

        .drop-indicator {
            height: 2px;
            background-color: #0066cc;
            margin: 10px 0;
            display: none;
        }

        .drop-indicator.active {
            display: block;
        }

        .task-list {
            min-height: 50px;
            padding: 10px;
            border: 1px dashed #ccc;
            border-radius: 4px;
            margin: 10px 0;
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
                    <div class="task-header">
                        <div class="drag-handle">⋮⋮</div>
                        <span class="task-name"><?= htmlspecialchars($task['task_name']); ?></span>
                        <div class="task-actions">
                            <button class="edit-task" data-task-id="<?= $task['task_id']; ?>">Modifier</button>
                            <button class="delete-task" data-task-id="<?= $task['task_id']; ?>">Supprimer</button>
                        </div>
                    </div>
                    <?php if (!empty($task['description'])): ?>
                        <div class="task-description"><?= nl2br(htmlspecialchars($task['description'])); ?></div>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- Formulaire pour ajouter une tâche -->
    <form id="add-task-form" method="POST" action="add_task.php">
        <input type="hidden" name="list_id" value="<?= $list_id ?>">
        <input type="text" name="task_name" placeholder="Nom de la tâche" required>
        <textarea name="description" placeholder="Description (optionnelle)"></textarea>
        <input type="color" name="color" value="#ffffff">
        <button type="submit">Ajouter la tâche</button>
    </form>
    <!-- Formulaire pour ajouter une sous-liste -->
    <form id="add-sublist-form" method="POST" action="sublist.php">
        <input type="hidden" name="parent_list_id" value="<?= $list_id ?>">
        <input type="text" name="sublist_name" placeholder="Nom de la sous-liste" required>
        <button type="submit">Ajouter une sous-liste</button>
    </form>
    <!-- Sous-listes -->
    <div id="sublists-section">
        <?php if ($sublists): ?>
            <h2>Sous-listes</h2>
            <?php foreach ($sublists as $sublist): ?>

        <form id="add-task-form" method="POST" action="add_task.php">
            <input type="hidden" name="list_id" value="<?= $list_id ?>">
            <input type="text" name="task_name" placeholder="Nom de la tâche" required>
            <textarea name="description" placeholder="Description (optionnelle)"></textarea>
            <input type="color" name="color" value="#ffffff">
            <button type="submit">Ajouter la tâche</button>
            <?php endforeach; ?>


            </form>
        <?php endif; ?>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let draggingElement = null;
        const dropIndicator = document.createElement('div');
        dropIndicator.className = 'drop-indicator';

        function initDragAndDrop() {
            document.querySelectorAll('.task-item').forEach(task => {
                task.addEventListener('dragstart', handleDragStart);
                task.addEventListener('dragend', handleDragEnd);
                task.addEventListener('dragover', handleDragOver);
                task.addEventListener('drop', handleDrop);
            });

            document.querySelectorAll('.task-list').forEach(list => {
                list.addEventListener('dragover', handleListDragOver);
                list.addEventListener('drop', handleListDrop);
            });
        }

        function handleDragStart(e) {
            draggingElement = e.target;
            e.target.classList.add('dragging');
            e.dataTransfer.setData('text/plain', JSON.stringify({
                taskId: e.target.dataset.taskId,
                sourceListId: e.target.dataset.listId
            }));
        }

        function handleDragEnd(e) {
            e.target.classList.remove('dragging');
            draggingElement = null;
            document.querySelectorAll('.drop-indicator').forEach(indicator => indicator.remove());
        }

        function handleDragOver(e) {
            e.preventDefault();
            if (e.target.classList.contains('task-item') && e.target !== draggingElement) {
                const rect = e.target.getBoundingClientRect();
                const midY = rect.top + rect.height / 2;
                if (e.clientY < midY) {
                    e.target.parentElement.insertBefore(dropIndicator, e.target);
                } else {
                    e.target.parentElement.insertBefore(dropIndicator, e.target.nextSibling);
                }
            }
        }

        function handleListDragOver(e) {
            e.preventDefault();
            if (!e.target.classList.contains('task-item') && e.target.classList.contains('task-list')) {
                e.target.appendChild(dropIndicator);
            }
        }

        function handleDrop(e) {
            e.preventDefault();
            handleListDrop(e);
        }

        function handleListDrop(e) {
            e.preventDefault();
            const taskList = e.target.closest('.task-list');
            if (!taskList) return;

            const data = JSON.parse(e.dataTransfer.getData('text/plain'));
            const newListId = taskList.dataset.listId;
            const position = Array.from(taskList.children).indexOf(dropIndicator);

            fetch('move_task.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    taskId: data.taskId,
                    newListId: newListId,
                    position: position
                })
            })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        if (data.sourceListId !== newListId) {
                            // Rafraîchir les deux listes si la tâche a été déplacée entre listes
                            refreshTaskList(data.sourceListId);
                            refreshTaskList(newListId);
                        } else {
                            // Rafraîchir seulement la liste actuelle si réorganisation interne
                            refreshTaskList(newListId);
                        }
                    } else {
                        alert('Erreur lors du déplacement: ' + result.message);
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Erreur lors du déplacement de la tâche');
                });

            dropIndicator.remove();
        }

        function refreshTaskList(listId) {
            fetch(`get_tasks.php?list_id=${listId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const taskList = document.querySelector(`.task-list[data-list-id="${listId}"]`);
                        if (taskList) {
                            taskList.innerHTML = data.html;
                            initDragAndDrop();
                        }
                    }
                })
                .catch(error => console.error('Erreur de rafraîchissement:', error));
        }

        // Initialiser le drag & drop
        initDragAndDrop();
    });
    document.addEventListener('DOMContentLoaded', function() {
        // Initialiser le drag & drop
        initDragAndDrop();

        // Gestionnaire pour le bouton "Modifier"
        document.querySelectorAll('.edit-task').forEach(button => {
            button.addEventListener('click', function(e) {
                const taskId = e.target.dataset.taskId;
                // Ouvrir un formulaire de modification ou une boîte de dialogue
                openEditTaskForm(taskId);
            });
        });

        // Gestionnaire pour le bouton "Supprimer"
        document.querySelectorAll('.delete-task').forEach(button => {
            button.addEventListener('click', function(e) {
                const taskId = e.target.dataset.taskId;
                if (confirm('Voulez-vous vraiment supprimer cette tâche ?')) {
                    deleteTask(taskId);
                }
            });
        });

        function deleteTask(taskId) {
            fetch('delete_task.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ task_id: taskId })
            })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        // Rafraîchir la liste des tâches
                        refreshTaskList(document.querySelector('.task-list').dataset.listId);
                    } else {
                        alert('Erreur lors de la suppression de la tâche.');
                    }
                });
        }

        function openEditTaskForm(taskId) {
            // Implémentez cette fonction pour ouvrir un formulaire de modification
            // Vous pouvez utiliser une boîte de dialogue modale ou rediriger vers une page de modification
            console.log('Modifier la tâche avec l\'ID :', taskId);
        }
    });

</script>

</body>
</html>

