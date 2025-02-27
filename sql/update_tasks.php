<?php
session_start();
$pdo = new PDO('mysql:host=localhost;dbname=to-do-list;charset=utf8', 'root', '');

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "Utilisateur non connecté"]);
    exit();
}

// Vérification si la requête est bien en POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $task_id = $_POST['task_id'] ?? null;
    $new_status = $_POST['status'] ?? null;
    $new_name = $_POST['name'] ?? null;

    if (!$task_id) {
        echo json_encode(["error" => "ID de tâche manquant"]);
        exit();
    }

    // Préparer la requête de mise à jour
    $fields = [];
    $params = [];

    if ($new_status) {
        $fields[] = "status = ?";
        $params[] = $new_status;
    }
    if ($new_name) {
        $fields[] = "task_name = ?";
        $params[] = $new_name;
    }

    if (!empty($fields)) {
        $params[] = $task_id;
        $sql = "UPDATE tasks SET " . implode(", ", $fields) . " WHERE task_id = ? AND user_id = ?";
        $params[] = $_SESSION['user_id'];

        $stmt = $pdo->prepare($sql);
        if ($stmt->execute($params)) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["error" => "Erreur lors de la mise à jour"]);
        }
    } else {
        echo json_encode(["error" => "Aucune modification détectée"]);
    }
} else {
    echo json_encode(["error" => "Requête invalide"]);
}
