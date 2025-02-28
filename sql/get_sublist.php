<?php
session_start();
require_once(__DIR__ . '/bd.php');

header('Content-Type: application/json');

if (empty($_SESSION['user_id'])) {
    die(json_encode(['success' => false, 'message' => 'Authentification requise.']));
}

if (!isset($_GET['parent_list_id'])) {
    die(json_encode(['success' => false, 'message' => 'ID de liste parente manquant.']));
}

$parent_list_id = intval($_GET['parent_list_id']);

// Vérifier que la liste parente appartient à l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM lists WHERE list_id = ? AND user_id = ?");
$stmt->execute([$parent_list_id, $_SESSION['user_id']]);
$list = $stmt->fetch();

if (!$list) {
    die(json_encode(['success' => false, 'message' => 'Liste parente introuvable ou non autorisée.']));
}

// Récupérer les sous-listes
$stmt = $pdo->prepare("SELECT * FROM lists WHERE parent_list_id = ?");
$stmt->execute([$parent_list_id]);
$sublists = $stmt->fetchAll();

// Générer le HTML
ob_start();
if ($sublists): ?>
    <h2>Sous-listes</h2>
    <ul class="sublist-list">
        <?php foreach ($sublists as $sublist): ?>
            <li class="sublist-item" data-sublist-id="<?= $sublist['list_id']; ?>">
                <h3><?= htmlspecialchars($sublist['list_name']); ?></h3>
                <a href="task.php?list_id=<?= $sublist['list_id']; ?>">Voir les détails</a>

                <!-- Affichage des tâches de la sous-liste -->
                <div class="sublist-tasks" id="sublist-tasks-<?= $sublist['list_id']; ?>">
                    <?php
                    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE list_id = ?");
                    $stmt->execute([$sublist['list_id']]);
                    $sublistTasks = $stmt->fetchAll();

                    if ($sublistTasks): ?>
                        <ul class="task-list">
                            <?php foreach ($sublistTasks as $task): ?>
                                <li class="task-item" data-task-id="<?= $task['task_id']; ?>" style="background-color: <?= htmlspecialchars($task['color']); ?>;">
                                    <div class="task-header">
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
                    <?php else: ?>
                        <p>Aucune tâche pour cette sous-liste.</p>
                    <?php endif; ?>
                </div>

                <!-- Formulaire pour ajouter une tâche à la sous-liste -->
                <form class="add-sublist-task-form" data-sublist-id="<?= $sublist['list_id']; ?>">
                    <input type="hidden" name="list_id" value="<?= $sublist['list_id']; ?>">
                    <input type="text" name="task_name" placeholder="Nom de la tâche" required>
                    <textarea name="description" placeholder="Description (optionnelle)"></textarea>
                    <input type="color" name="color" value="#ffffff">
                    <button type="submit">Ajouter la tâche</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <!-- Pas de sous-listes -->
<?php endif;

$html = ob_get_clean();

echo json_encode(['success' => true, 'html' => $html]);

