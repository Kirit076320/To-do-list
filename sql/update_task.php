<?php
session_start();
require_once(__DIR__ . '/bd.php');

if (empty($_SESSION['user_id'])) {
    die("Erreur : Authentification requise.");
}

if (!isset($_GET['task_id'])) {
    die("Erreur : Aucune tâche spécifiée.");
}

$task_id = intval($_GET['task_id']);

// Vérifier si la tâche appartient à une liste de l'utilisateur
$stmt = $pdo->prepare("
    SELECT t.*, l.user_id 
    FROM tasks t 
    JOIN lists l ON t.list_id = l.list_id 
    WHERE t.task_id = ? AND l.user_id = ?
");
$stmt->execute([$task_id, $_SESSION['user_id']]);
$task = $stmt->fetch();

if (!$task) {
    die("Erreur : Tâche introuvable ou non autorisée.");
}

// Si formulaire soumis, mettre à jour la tâche
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $task_name = trim($_POST["task_name"]);
    $description = trim($_POST["description"]);
    $status = trim($_POST["status"]);
    $priority = intval($_POST["priority"]);
    $due_date = !empty($_POST["due_date"]) ? $_POST["due_date"] : null;
    $color = $_POST["color"];

    $stmt = $pdo->prepare("
        UPDATE tasks 
        SET task_name = ?, description = ?, status = ?, priority = ?, due_date = ?, color = ? 
        WHERE task_id = ?
    ");
    $stmt->execute([$task_name, $description, $status, $priority, $due_date, $color, $task_id]);

    header("Location: task.php?list_id=" . $task['list_id']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier la tâche</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 400px;
        }

        h1 {
            text-align: center;
            font-size: 22px;
            color: #444;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-top: 10px;
            font-weight: bold;
            font-size: 14px;
        }

        input, textarea, select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        textarea {
            height: 80px;
            resize: none;
        }

        input[type="color"] {
            height: 40px;
            padding: 0;
            border: none;
            cursor: pointer;
        }

        button {
            background: #007bff;
            color: white;
            padding: 10px;
            margin-top: 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background: #0056b3;
        }

        a {
            display: block;
            text-align: center;
            margin-top: 10px;
            color: #007bff;
            text-decoration: none;
            font-size: 14px;
        }

        a:hover {
            text-decoration: underline;
        }

    </style>


</head>
<body>
<div class="container">
    <h1>Modifier la tâche</h1>
    <form method="POST">
        <label>Nom de la tâche :</label>
        <input type="text" name="task_name" value="<?= htmlspecialchars($task['task_name']); ?>" required>

        <label>Description :</label>
        <textarea name="description"><?= htmlspecialchars($task['description'] ?? ''); ?></textarea>

        <label>Statut :</label>
        <input type="text" name="status" value="<?= htmlspecialchars($task['status'] ?? ''); ?>">

        <label>Priorité :</label>
        <input type="number" name="priority" value="<?= htmlspecialchars($task['priority'] ?? ''); ?>" min="0" max="5">

        <label>Date limite :</label>
        <input type="date" name="due_date" value="<?= htmlspecialchars($task['due_date'] ?? ''); ?>">

        <label>Couleur :</label>
        <input type="color" name="color" value="<?= htmlspecialchars($task['color'] ?? '#ffffff'); ?>">

        <button type="submit">Modifier</button>
        <a href="task.php?list_id=<?= $task['list_id']; ?>">Annuler</a>
    </form>
</div>
</body>
</html>
