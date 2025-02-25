<?php
session_start();
include('db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Vérification de l'existence de l'utilisateur
    $sql = "SELECT * FROM users WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    // Si l'utilisateur est trouvé dans la base de données
    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Vérification du mot de passe
        if (password_verify($password, $user['password'])) {
            // Connexion réussie, création de la session utilisateur
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            header("Location: dashboard.php");  // Rediriger vers le tableau de bord ou la page principale
            exit;
        } else {
            // Mot de passe incorrect
            $error = "Mot de passe incorrect.";
        }
    } else {
        // Utilisateur non trouvé
        $error = "Aucun utilisateur trouvé avec cet email.";
    }
}
?>

<?php if (isset($error)): ?>
    <div class="error"><?= htmlspecialchars($error); ?></div>
<?php endif; ?>

<form method="POST">
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Mot de passe" required>
    <button type="submit">Se connecter</button>
</form>
