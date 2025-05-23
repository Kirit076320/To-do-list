<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
    <!-- Fonts Google -->
    <link href="https://fonts.googleapis.com/css2?family=Bangers&family=Fjalla+One&display=swap" rel="stylesheet">
</head>
<body class="connexion_body">

<nav class="navbar navbar-expand-lg" style="background-color: #fcc9b9;">
    <div class="container-fluid">
        <a class="navbar-brand" href="../index.php">
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

<div class="login-container">

    <form class="login-form" method="POST" action="../php/login.php">
        <h1>Connexion</h1>
        <div class="input-box">
            <input type="text" name="email" required ">
            <span style="color:white;">E-mail</span>
            <i></i>
        </div>
        <div class="input-box">
            <input type="password" name="password" required>
            <span style="color:white;">Mot de passe</span>
            <i></i>
        </div>
        <button type="submit" name="ok" class="login-btn">Se connecter</button>
        <br><br>
        <p><a href="mdp_oublier.php" class="link-light link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover">Mot de passe oublié ?</a></p>
        <p><a href="inscription.php" class="link-warning link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover">S'inscrire ici.</a></p>

    </form>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Popper.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.6/umd/popper.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
