<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once(__DIR__ . '/bd.php');

// Vérifier si l'utilisateur est connecté
if (empty($_SESSION['user_id'])) {
    die("Erreur : Authentification requise.");
}

// Vérifier si list_id est passé en paramètre POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $list_id = $_POST['list_id'] ?? null;
    $task_name = $_POST['task_name'] ?? '';
    $color = $_POST['color'] ?? '#ffffff';

    // Vérifier si list_id est défini et valide
    if ($list_id && $task_name && listExists($pdo, $list_id)) {
        addTask($pdo, $list_id, $task_name, $color);
    } else {
        echo "Erreur : Aucune liste spécifiée, données invalides ou liste inexistante.";
    }
} else {
    echo "Erreur : Méthode de requête non autorisée.";
}

function listExists($pdo, $list_id) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM lists WHERE list_id = ? AND user_id = ?");
    $stmt->execute([$list_id, $_SESSION['user_id']]);
    return $stmt->fetchColumn() > 0;
}

function addTask($pdo, $list_id, $task_name, $color) {
    $sql = "INSERT INTO tasks (list_id, task_name, status, priority, color, created_at, updated_at)
            VALUES (:list_id, :task_name, 'not started', 1, :color, NOW(), NOW())";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':list_id', $list_id, PDO::PARAM_INT);
    $stmt->bindParam(':task_name', $task_name, PDO::PARAM_STR);
    $stmt->bindParam(':color', $color, PDO::PARAM_STR);

    if ($stmt->execute()) {
        echo "Tâche ajoutée avec succès.";
    } else {
        echo "Erreur lors de l'ajout de la tâche.";
    }
}
?>
