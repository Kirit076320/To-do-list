<?php
session_start();
require_once(__DIR__ . '/bd.php');

header('Content-Type: application/json');

if (empty($_SESSION['user_id'])) {
    die(json_encode(['success' => false, 'message' => 'Authentification requise.']));
}

if (!isset($_GET['task_id'])) {
    die(json_encode(['success' => false, 'message' => 'ID de tâche manquant.']));
}

$task_id = intval($_GET['task_id']);

// Récupérer la tâche et vérifier qu'elle appartient à une liste de l'utilisateur
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

if (!$task) {
    die(json_encode(['success' => false, 'message' => 'Tâche introuvable ou non autorisée.']));
}

echo json_encode(['success  => false, 'message' => 'Tâche introuvable ou non autorisée.']));
}

echo json_encode(['success' => true, 'task' => $task]);

