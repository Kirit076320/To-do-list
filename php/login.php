<?php
session_start();
$pdo = new PDO('mysql:host=localhost;dbname=to-do-list;charset=utf8', 'root', '');

// Vérification du formulaire
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['role'] = $user['role']; // 'admin' ou 'user'
        if ($_SESSION['role'] == 'admin') {
            echo "<script>alert('Bienvenue $email')</script>";
            header("Refresh:1; url=/index.php");
        } elseif ($_SESSION['role'] == 'user') {
            echo "<script>alert('Bienvenue $email')</script>";
            header("Refresh:1; url=/index.php");
        }
        exit();
    } else {
        echo "Identifiants incorrects !";
        header("Refresh:0.5; url=login.php");
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Se connecter</title>
    <link rel="stylesheet" href="/assets/style.css"
</head>
<body class="login">
<div class="topnav">
    <a class="active" href="/index.php">Accueil</a>
    <a href="/php/register.php">S'enregistrer</a>
    <a href="/php/login.php">Se connecter</a>
</div>
<div class="tout-wrapper">
    <div class="wrapper">
        <form method="POST" class="form">
            <div class="container">
                <h1 class="login_title">Se connecter</h1>
                <input class="input-box" type="text" name="username" placeholder="Nom d'utilisateur" required>
                <input class="input-box" type="password" name="password" placeholder="Mot de passe" required>
                <button class="login_submit btn" type="submit">Se connecter</button>
                <div class="register-link">
                    <p>Pas de compte ? <a href="../php/register.php">S'enregistrer</a></p>
                </div>
            </div>
        </form>
    </div>
</div>-->