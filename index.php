<?php
session_start();
$pdo = new PDO('mysql:host=localhost;dbname=to-do-list;charset=utf8', 'root', '');

// Vérification si l'utilisateur est connecté
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Récupération des listes de l'utilisateur
    $stmt = $pdo->prepare("SELECT * FROM lists WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $lists = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List</title>
    <link rel="stylesheet" href="/assets/style.css">
    <script src="/assets/script.js" defer></script>
</head>
<body>

<?php if (isset($_SESSION['user_id'])): ?>
    <div class="todo-section">
        <h1>Vos To-Do Lists</h1>

        <!-- Affichage des To-Do Lists et tâches -->
        <?php if ($lists): ?>
            <?php foreach ($lists as $list): ?>
                <div class="todo-list">
                    <h2><?= htmlspecialchars($list['list_name']); ?></h2>
                    <ul class="task-list" data-list-id="<?= $list['list_id']; ?>">
                        <?php
                        $stmt = $pdo->prepare("SELECT * FROM tasks WHERE list_id = ?");
                        $stmt->execute([$list['list_id']]);
                        $tasks = $stmt->fetchAll();
                        ?>
                        <?php foreach ($tasks as $task): ?>
                            <li class="task-item" data-task-id="<?= $task['task_id']; ?>">
                                <?= htmlspecialchars($task['task_name']); ?>
                                <button class="delete-task" data-task-id="<?= $task['task_id']; ?>">Supprimer</button>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <form class="add-task-form" data-list-id="<?= $list['list_id']; ?>">
                        <input type="text" name="task_name" placeholder="Nouvelle tâche" required>
                        <button type="submit">Ajouter</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Aucune To-Do List trouvée.</p>
        <?php endif; ?>

        <!-- Formulaire pour créer une nouvelle To-Do List -->
        <h2>Créer une nouvelle To-Do List</h2>
        <form action="/assets/create_todo.php" method="POST">
            <input type="text" name="list_name" placeholder="Nom de la liste" required>
            <button type="submit">Créer</button>
        </form>
    </div>
<?php else: ?>
    <a href="/assets/login.php"><button>Se connecter</button></a>
<?php endif; ?>

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
