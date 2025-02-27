<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('assets/bd.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $todo_id = $_POST['todo_id'] ?? null;
    $task_name = $_POST['task_name'] ?? '';

    if ($todo_id && $task_name) {
        $sql = "INSERT INTO tasks (list_id, task_name, status, priority, created_at, updated_at) 
                VALUES (:list_id, :task_name, 'À Faire', 1, NOW(), NOW())";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':list_id', $todo_id);
        $stmt->bindParam(':task_name', $task_name);

        if ($stmt->execute()) {
            echo "Tâche ajoutée avec succès.";
        } else {
            echo "Erreur lors de l'ajout de la tâche.";
        }
    } else {
        echo "Erreur : Données invalides.";
    }
}
?>
