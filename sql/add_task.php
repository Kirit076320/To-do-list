<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once(__DIR__ . '/bd.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Nettoyer la valeur de todo_id et vérifier si elle est correcte
    $todo_id = isset($_POST['todo_id']) ? trim(htmlspecialchars($_POST['todo_id'])) : null;

    // Afficher la valeur nettoyée de todo_id pour débogage
    echo "Valeur nettoyée de todo_id: " . htmlspecialchars($todo_id) . "<br>";

    // Récupérer le nom de la tâche et la couleur
    $task_name = $_POST['task_name'] ?? '';
    $color = $_POST['color'] ?? '#ffffff';  // Valeur par défaut si aucune couleur n'est spécifiée

    // Vérifier si l'ID de la liste et le nom de la tâche sont présents
    if ($todo_id && $task_name) {
        // Vérifier si la liste spécifiée existe dans la base de données
        $checkStmt = $pdo->prepare("SELECT list_id FROM lists WHERE list_id = ?");
        $checkStmt->execute([$todo_id]);

        if (!$checkStmt->fetch()) {
            // Si la liste n'existe pas, afficher un message d'erreur et arrêter l'exécution
            die("Erreur : La liste spécifiée n'existe pas.");
        }

        // Préparer la requête SQL pour ajouter la tâche
        $sql = "INSERT INTO tasks (list_id, task_name, status, priority, color, created_at, updated_at) 
                VALUES (:list_id, :task_name, 'not started', 1, :color, NOW(), NOW())";

        $stmt = $pdo->prepare($sql);
        // Lier les paramètres pour sécuriser la requête
        $stmt->bindParam(':list_id', $todo_id, PDO::PARAM_INT);
        $stmt->bindParam(':task_name', $task_name, PDO::PARAM_STR);
        $stmt->bindParam(':color', $color, PDO::PARAM_STR);

        // Exécuter la requête pour ajouter la tâche
        if ($stmt->execute()) {
            echo "Tâche ajoutée avec succès.";
        } else {
            // Si une erreur se produit lors de l'exécution, afficher un message d'erreur
            echo "Erreur lors de l'ajout de la tâche.";
        }
    } else {
        // Si les données sont manquantes, afficher une erreur
        echo "Erreur : Aucune liste spécifiée ou données invalides.";
    }
}
?>
