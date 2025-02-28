<?php
session_start();
require_once(__DIR__ . '/bd.php');

header('Content-Type: application/json');

if (empty($_SESSION['user_id'])) {
    die(json_encode(['success' => false, 'message' => 'Authentification requise.']));
}

$data = json_decode(file_get_contents('php://input'), true);
$task_id = $data['task_id'] ?? null;
$task_name = $data['task_name'] ?? null;
$description = $data['description'] ?? '';
$color = $data['color'] ?? '#ffffff';

if ($task_id && $task_name) {
    // Vérifier que la tâche appartient à une liste de l'utilisateur
    $stmt = $pdo->prepare("
        SELECT t.* FROM tasks t
        JOIN lists l ON t.list_id = l.list_id
        WHERE t.task_id = ? AND (
            l.user_id = ? OR
            l.list_id IN (SELECT list_id FROM lists WHERE parent_list_id IN (SELECT list_id FROM lists WHERE user_id = ?))
        )
    ");
    $stmt->execute([$task_id, $_SESSION['user_id'], $_SESSION['user_id']]);
    $task = $stmt->fetch();

    if ($task) {
        // Mettre à jour la tâche
        $stmt = $pdo->prepare("UPDATE tasks SET task_name = ?, description = ?, color = ? WHERE task_id = ?");
        $result = $stmt->execute([$task_name, $description, $color, $task_id]);

        echo json_encode(['success' => $result]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Tâche introuvable ou non autorisée.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Données manquantes.']);
}
?>
