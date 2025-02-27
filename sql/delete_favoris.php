<?php
session_start();
$pdo = new PDO('mysql:host=localhost;dbname=to-do-list;charset=utf8', 'root', '');

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    die("Accès refusé.");
}

$user_id = $_SESSION['user_id'];
$citation_id = $_GET['id'];

// Supprimer la citation des favoris
$stmt = $pdo->prepare("DELETE FROM favorites WHERE user_id = ? AND citation_id = ?");
$stmt->execute([$user_id, $citation_id]);

header("Location: dashboard_user.php");
exit();
?>
<?php
