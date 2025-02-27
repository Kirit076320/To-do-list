<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>

    <!-- lien bootstrap -->

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://getbootstrap.com/docs/5.3/assets/css/docs.css" rel="stylesheet">
    <title>Bootstrap Example</title>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" href="../assets/style.css">

</head>
<body class="inscription_body">

<nav class="navbar navbar-expand-lg" style="background-color: #fcc9b9;">
    <div class="container-fluid">
        <a class="navbar-brand" href="../index.html">
            <img src="../assets/img/japan_list.png" alt="Logo">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a href="inscription.php" class="btn btn-inscription" role="button" style="background-color: #DB5A6B;">Inscription</a>
                </li>
                <li class="nav-item">
                    <a href="connexion.php" class="btn btn-connexion" role="button" style="background-color: #b3bcff;">Connexion</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="inscription-container">
    <div class="inscription-form">
        <h1>Inscription</h1>

        <?php
        session_start();
        if (isset($_SESSION['error_message'])) {
            echo "<p class='inscription-error'>" . $_SESSION['error_message'] . "</p>";
            unset($_SESSION['error_message']); // Supprime le message après affichage
        }

        if (isset($_SESSION['success_message'])) {
            echo "<p class='inscription-success'>" . $_SESSION['success_message'] . "</p>";
            unset($_SESSION['success_message']); // Supprime le message après affichage
        }
        ?>

        <form action="../php/register.php" method="POST">
            <div class="input-box">
                <input type="email" name="email" id="email" required>
                <span>Email</span>
            </div>
            <div class="input-box">
                <input type="text" name="username" id="username" required>
                <span>Username</span>
            </div>
            <div class="input-box">
                <input type="password" name="password" id="mdp" minlength="8" required>
                <span>Mot de passe</span>
            </div>
            <div class="input-box">
                <input type="password" name="password_confirm" id="mdp_confirm" minlength="8" required>
                <span>Confirmer le mot de passe</span>
            </div>
            <button type="submit" class="inscription-btn" name="ok">S'inscrire</button>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Popper.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.6/umd/popper.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


</body>
</html>