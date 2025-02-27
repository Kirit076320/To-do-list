<?php
session_start();
$pdo = new PDO('mysql:host=localhost;dbname=to-do-list;charset=utf8', 'root', '');

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    die("Accès refusé.");
}

$user_id = $_SESSION['user_id'];
$task_id = $_GET['task_id'];

// Supprimer la citation des favoris
$stmt = $pdo->prepare("DELETE FROM favorites WHERE user_id = ? AND task_id = ?");
$stmt->execute([$user_id, $task_id]);

header("Location: ../index.php");
exit();
?>
<?php
