
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
        .container {
            max-width: 800px;
            margin: 20px auto;
        }
        .card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 15px;
            margin-bottom: 15px;
            transition: transform 0.2s ease;
        }
        .card:hover {
            transform: none; /* Désactive le hover */
        }
        .card-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .task-description {
            font-size: 14px;
            color: #555;
            margin-top: 5px;
        }
        .task-info {
            font-size: 13px;
            color: #777;
            margin-top: 5px;
        }
        .task-actions {
            margin-top: 10px;
        }
        .task-actions button {
            background: #007bff;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
        }
        .task-actions button.delete-task {
            background: #dc3545;
        }
    </style>
</head>
<body>

<div class="container">
    <h1><?= htmlspecialchars($list['list_name']); ?></h1>
    <a href="../index.php">Retour aux listes</a>

    <div id="tasks-section">
        <?php foreach ($tasks as $task): ?>
            <div class="card" style="background-color: <?= htmlspecialchars($task['color']); ?>;">
                <div class="card-title"><?= htmlspecialchars($task['task_name']); ?></div>
                <?php if (!empty($task['description'])): ?>
                    <div class="task-description"><?= nl2br(htmlspecialchars($task['description'])); ?></div>
                <?php endif; ?>
                <div class="task-info">
                    Statut: <strong><?= isset($task['status']) ? htmlspecialchars($task['status']) : 'Non défini'; ?></strong><br>
                    Priorité: <strong><?= isset($task['priority']) ? htmlspecialchars($task['priority']) : 'Non définie'; ?></strong><br>
                    Date limite: <strong><?= isset($task['due_date']) ? htmlspecialchars($task['due_date']) : 'Non définie'; ?></strong>
                </div>
                <div class="task-actions">
                    <a href="update_task.php?task_id=<?= $task['task_id']; ?>" class="btn btn-primary">Modifier</a>
                    <button class="btn btn-danger delete-task" data-task-id="<?= $task['task_id']; ?>">Supprimer</button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Formulaire pour ajouter une tâche -->
    <form id="add-task-form" method="POST" action="add_task.php">
        <input type="hidden" name="list_id" value="<?= $list_id ?>">
        <input type="text" name="task_name" placeholder="Nom de la tâche" required>
        <textarea name="description" placeholder="Description (optionnelle)"></textarea>
        <input type="color" name="color" value="#ffffff">
        <button type="submit">Ajouter la tâche</button>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.edit-task').forEach(button => {
            button.addEventListener('click', function(e) {
                const taskId = e.target.dataset.taskId;
                openEditTaskForm(taskId);
            });
        });

        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".delete-task").forEach(button => {
                button.addEventListener("click", function() {
                    const taskId = this.getAttribute("data-task-id");

                    if (confirm("Voulez-vous vraiment supprimer cette tâche ?")) {
                        fetch("../sql/delete_task.php", {
                            method: "POST",
                            headers: { "Content-Type": "application/json" },
                            body: JSON.stringify({ task_id: taskId })
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    alert("Tâche supprimée !");
                                    location.reload();
                                } else {
                                    alert("Erreur : " + data.message);
                                }
                            })
                            .catch(error => {
                                console.error("Erreur :", error);
                            });
                    }
                });
            });
        });
</script>


</body>
</html>






