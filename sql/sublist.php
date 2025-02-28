<?php
session_start();
require_once(__DIR__ . '/bd.php');

if (empty($_SESSION['user_id'])) {
    die("Erreur : Authentification requise. <a href='../index.php'>Retour</a>");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sublist_name'])) {
    $parent_list_id = $_POST['parent_list_id'] ?? null;
    $sublist_name = trim($_POST['sublist_name']);

    if ($parent_list_id && !empty($sublist_name)) {
        $stmt = $pdo->prepare("INSERT INTO lists (user_id, list_name, parent_list_id, created_at, updated_at) VALUES (:user_id, :list_name, :parent_list_id, NOW(), NOW())");
        $stmt->execute([
            ':user_id' => $_SESSION['user_id'],
            ':list_name' => $sublist_name,
            ':parent_list_id' => $parent_list_id,
        ]);

        echo "Sous-liste créée avec succès.";
    } else {
        echo "Erreur : Données invalides.";
    }
} else {
    echo "Erreur : Méthode de requête non autorisée. <a href='../index.php'>Retour</a>";
}
?>
