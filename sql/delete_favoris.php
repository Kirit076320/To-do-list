<?php
session_start();
$pdo = new PDO('mysql:host=localhost;dbname=to-do-list;charset=utf8', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION // Active les erreurs SQL
]);

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "Accès refusé."]);
    exit();
}

$user_id = $_SESSION['user_id'];
$task_id = $_GET['task_id'] ?? null;

if (!$task_id) {
    echo json_encode(["error" => "Aucune tâche spécifiée."]);
    exit();
}

// Supprimer la tâche des favoris
try {
    $stmt = $pdo->prepare("DELETE FROM favorites WHERE user_id = ? AND task_id = ?");
    $stmt->execute([$user_id, $task_id]);
    echo json_encode(["success" => true, "message" => "Tâche retirée des favoris"]);
} catch (PDOException $e) {
    echo json_encode(["error" => "Erreur SQL : " . $e->getMessage()]);
}

exit();
?>
