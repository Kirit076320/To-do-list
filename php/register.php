<?php
session_start();
$pdo = new PDO('mysql:host=localhost;dbname=to-do-list;charset=utf8', 'root', '');

// Vérification du formulaire
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Récupération des données du formulaire
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'] ?? '';  // Si "password_confirm" n'est pas défini, on assigne une valeur vide

    // Vérifier que les mots de passe correspondent
    if ($password !== $password_confirm) {
        echo "Les mots de passe ne correspondent pas. <a href=\"register.php\">Retour</a>";
        exit();
    }

    // Vérifier si le nom d'utilisateur est déjà pris
    $stmt = $pdo->prepare("SELECT user_id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        echo "Ce nom d'utilisateur est déjà pris. <a href=\"register.php\">Retour</a>";
        exit();
    }

    // Hasher le mot de passe
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Insérer l'utilisateur sans la colonne 'role' (enlevant 'role')
    $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->execute([$username, $hashed_password]);

    // Authentifier l'utilisateur immédiatement après l'inscription
    $_SESSION['user_id'] = $pdo->lastInsertId(); // Récupérer l'ID de l'utilisateur nouvellement inscrit
    $_SESSION['username'] = $username;
    $_SESSION['last_activity'] = time(); // Ajouter un timestamp de la dernière activité

    header("Location: ../index.php"); // Rediriger vers l'accueil après l'inscription
    exit();
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>S'enregistrer</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="register">
<div class="topnav">
    <a class="active" href="../index.php">Accueil</a>
    <a href="register.php">S'enregistrer</a>
    <a href="login.php">Se connecter</a>
</div>
<div class="tout-wrapper">
    <div class="wrapper">
        <form method="POST">
            <h1 class="register_title">Inscription</h1>
            <input class="input-box" type="text" name="username" placeholder="Nom d'utilisateur" required>
            <input class="input-box" type="password" name="password" placeholder="Mot de passe" required>
            <input class="input-box" type="password" name="password_confirm" placeholder="Confirmer le mot de passe" required>
            <button class="btn" type="submit">S'inscrire</button>
        </form>
        <div class="register-link">
            <p>Déjà un compte ? Connectez-vous <a href="login.php">Connexion</a></p>
        </div>
    </div>
</div>
</body>
</html>
