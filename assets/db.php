<?php
$host = 'localhost';      // Hôte de la base de données
$dbname = 'to-do-list';    // Nom de la base de données
$username = 'root';       // Nom d'utilisateur de la base de données
$password = '';           // Mot de passe de la base de données (vide si vous n'en avez pas)

try {
    // Créer une nouvelle connexion PDO à la base de données
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

    // Définir le mode d'erreur PDO à exception pour faciliter le débogage
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Optionnel: définir le jeu de caractères à UTF-8 pour éviter les problèmes d'encodage
    $pdo->exec("SET NAMES 'utf8mb4'");

} catch (PDOException $e) {
    // Si la connexion échoue, afficher l'erreur
    echo "Erreur de connexion : " . $e->getMessage();
    exit;
}
?>
