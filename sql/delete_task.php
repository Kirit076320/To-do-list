<?php
session_start();
require_once(__DIR__ . '/bd.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lire les données JSON brutes de la requête
    $data = json_decode(file_get_contents('php://input'), true);
    $task_id = $data['task_id'] ?? null;

    if ($task_id) {
        // Préparer et exécuter la requête SQL pour supprimer la tâche
        $stmt = $pdo->prepare("DELETE FROM tasks WHERE task_id = ? AND list_id IN (SELECT list_id FROM lists WHERE user_id = ?)");
        $stmt->execute([$task_id, $_SESSION['user_id']]);

        // Vérifier si la suppression a réussi
        echo json_encode(['success' => $stmt->rowCount() > 0]);
    } else {
        echo json_encode(['success' => false, 'message' => 'ID de tâche manquant.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Méthode de requête non autorisée.']);
}
?>
