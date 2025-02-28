<?php
session_start();
require_once(__DIR__ . '/bd.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('task.php'), true);
    $task_id = $data['task_id'] ?? null;

    if ($task_id) {
        $stmt = $pdo->prepare("DELETE FROM tasks WHERE task_id = ? AND list_id IN (SELECT list_id FROM lists WHERE user_id = ?)");
        $stmt->execute([$task_id, $_SESSION['user_id']]);

        echo json_encode(['success' => $stmt->rowCount() > 0]);
    } else {
        echo json_encode(['success' => false]);
    }
} else {
    echo json_encode(['success' => false]);
}
?>
