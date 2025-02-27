<?php
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

// Récupérer toutes les listes
$sql = "SELECT * FROM lists";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$lists = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($lists as &$list) {
    // Récupérer les tâches associées à cette liste
    $sql = "SELECT * FROM tasks WHERE list_id = :list_id ORDER BY created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['list_id' => $list['list_id']]);
    $list['tasks'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

echo json_encode($lists);
?>
