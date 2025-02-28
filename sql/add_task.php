<?php
session_start();
require_once(__DIR__ . '/bd.php');

if (empty($_SESSION['user_id'])) {
    header('Location: ../pages/connexion.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validation de list_id
    $list_id = (int)($_POST['list_id'] ?? 0);
    if ($list_id <= 0) {
        die("ID de liste invalide.");
    }

    // Vérifier l'appartenance de la liste
    $stmt = $pdo->prepare("SELECT list_id FROM lists WHERE list_id = ? AND user_id = ?");
    $stmt->execute([$list_id, $_SESSION['user_id']]);
    if (!$stmt->fetch()) {
        die("Cette liste ne vous appartient pas ou n'existe pas.");
    }

    // Ajouter la tâche...
    // (code existant)
}