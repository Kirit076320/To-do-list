<?php
session_start();
header('Content-Type: application/json');

$host = "localhost";
$dbname = "to-do-list";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(["error" => "Erreur de connexion : " . $e->getMessage()]);
    exit;
}

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "Utilisateur non connecté"]);
    exit;
}

$user_id = $_SESSION['user_id'];

// Récupérer uniquement les listes de l'utilisateur connecté
$sql = "SELECT * FROM lists WHERE user_id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $user_id]);
$lists = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($lists as &$list) {
    // Récupérer les tâches associées à chaque liste
    $sql = "SELECT * FROM tasks WHERE list_id = :list_id ORDER BY created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['list_id' => $list['list_id']]);
    $list['tasks'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

echo json_encode($lists);
?>
