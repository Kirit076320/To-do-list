<?php
session_start();
require_once(__DIR__ . '/bd.php');

header('Content-Type: application/json');

if (empty($_SESSION['user_id'])) {
    die(json_encode(['success' => false, 'message' => 'Authentification requise.']));
}

if (!isset($_POST['list_id']) || !isset($_POST['task_name'])) {
    die(json_encode(['success' => false, 'message' => 'Données manquantes.']));
}

$list_id = intval($_POST['list_id']);
$task_name = trim($_POST['task_name']);
$description = isset($_POST['description']) ? trim($_POST['description']) : '';
$color = isset($_POST['color']) ? $_POST['color'] : '#ffffff';

// Vérifier que la liste appartient à l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM lists WHERE list_id = ? AND (user_id = ? OR EXISTS (SELECT 1 FROM lists WHERE list_id = ? AND parent_list_id IN (SELECT list_id FROM lists WHERE user_id = ?)))");
$stmt->execute([$list_id, $_SESSION['user_id'], $list_id, $_SESSION['user_id']]);
$list = $stmt->fetch();

if (!$list) {
    die(json_encode(['success' => false, 'message' => 'Liste introuvable ou non autorisée.']));
}

// Ajouter la tâche
$stmt = $pdo->prepare("INSERT INTO tasks (list_id, task_name, description, color) VALUES (?, ?, ?, ?)");
$result = $stmt->execute([$list_id, $task_name, $description, $color]);

if ($result) {
    echo json_encode(['success' => true, 'task_id' => $pdo->lastInsertId()]);
} else {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout de la tâche.']);
}

