<?php
session_start();
require_once(__DIR__ . '/bd.php');

header('Content-Type: application/json');

if (empty($_SESSION['user_id'])) {
    die(json_encode(['success' => false, 'message' => 'Authentification requise.']));
}

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!isset($data['taskId']) || !isset($data['newListId']) || !isset($data['position'])) {
    die(json_encode(['success' => false, 'message' => 'Données manquantes.']));
}

$taskId = intval($data['taskId']);
$newListId = intval($data['newListId']);
$position = intval($data['position']);

try {
    $pdo->beginTransaction();

    // Récupérer la tâche et sa position actuelle
    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE task_id = ?");
    $stmt->execute([$taskId]);
    $task = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$task) {
        die(json_encode(['success' => false, 'message' => 'Tâche non trouvée.']));
    }

    $oldListId = $task['list_id'];
    $oldPosition = $task['position'];

    // Mettre à jour les positions dans l'ancienne liste si nécessaire
    if ($oldListId != $newListId) {
        $stmt = $pdo->prepare("UPDATE tasks SET position = position - 1 WHERE list_id = ? AND position > ?");
        $stmt->execute([$oldListId, $oldPosition]);
    }

    // Mettre à jour les positions dans la nouvelle liste
    $stmt = $pdo->prepare("UPDATE tasks SET position = position + 1 WHERE list_id = ? AND position >= ?");
    $stmt->execute([$newListId, $position]);

    // Déplacer la tâche
    $stmt = $pdo->prepare("UPDATE tasks SET list_id = ?, position = ? WHERE task_id = ?");
    $stmt->execute([$newListId, $position, $taskId]);

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'sourceListId' => $oldListId,
        'newListId' => $newListId
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Erreur lors du déplacement de la tâche.']);
}
?>
