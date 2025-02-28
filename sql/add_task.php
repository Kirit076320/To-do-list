<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
$pdo = new PDO('mysql:host=localhost;dbname=to-do-list;charset=utf8', 'root', '');

//Vérifier si list_id est défini dans la session
if (!isset($_SESSION['list_id'])) {
    echo "Erreur : list_id n'est pas défini dans la session.";
    exit;
}
$list_id = $_SESSION['list_id'];

// Afficher le list_id pour le débogage
echo "List ID récupéré de la session : " . htmlspecialchars($list_id) . "<br>";

require_once(__DIR__ . '/bd.php');

function displayTasks($pdo, $list_id) {
    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE list_id = ? ORDER BY created_at ASC");
    $stmt->execute([$list_id]);
    $tasks = $stmt->fetchAll();

    foreach ($tasks as $task) {
        echo "<div class='task' style='background-color: " . htmlspecialchars($task['color']) . ";'>";
        echo htmlspecialchars($task['task_name']);
        echo "</div>";
    }
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

function listExists($pdo, $list_id) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM lists WHERE list_id = ?");
    $stmt->execute([$list_id]);
    return $stmt->fetchColumn() > 0;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $task_name = $_POST['task_name'] ?? '';
    $color = $_POST['color'] ?? '#ffffff';

    if ($list_id && $task_name && listExists($pdo, $list_id)) {
        addTask($pdo, $list_id, $task_name, $color);
    } else {
        echo "Erreur : Aucune liste spécifiée, données invalides ou liste inexistante.";
    }
}

// Afficher les tâches existantes
if (isset($list_id)) {
    displayTasks($pdo, $tasks_id);
}
?>
