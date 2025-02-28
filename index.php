<?php
session_start();
$pdo = new PDO('mysql:host=localhost;dbname=to-do-list;charset=utf8', 'root', '');

// Vérification si l'utilisateur est connecté
if (isset($_SESSION['user_id'])) {
    // Si l'utilisateur est connecté, récupérer ses To-Do Lists
    $user_id = $_SESSION['user_id'];
    $stmt = $pdo->prepare("SELECT * FROM lists WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $lists = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page d'accueil</title>

    <!-- lien bootstrap -->

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <title>Bootstrap Example</title>


    <link rel="stylesheet" href="assets/style.css">

    <link rel="shortcut icon" href="assets/img/japan_list.png" type="image/x-icon">

    <style>
        .todo-section {
            display: none;
        }
    </style>
</head>
<body class="index_body">

<?php
// Si l'utilisateur est connecté, afficher la section des To-Do Lists
if (isset($_SESSION['user_id'])): ?>
    <div class="todo-section" style="display:block;">

        <nav class="navbar navbar-expand-lg" style="background-color: #fcc9b9;">
            <div class="container-fluid">
                <a class="navbar-brand" href="index.php">
                    <img src="assets/img/japan_list.png" alt="Logo">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a href="php/logout.php" class="btn btn-connexion" role="button" style="background-color: #b3bcff;">Déconnexion</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container-fluid custom-container" style="margin-top: 8rem">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <input type="text" id="searchInput" class="form-control" placeholder="Rechercher par nom ou couleur...">
                </div>
            </div>
        </div>

        <div class="container-fluid custom-container" style="margin-top: 3rem">
            <div class="row justify-content-center" id="task-container">
                <!-- Les tâches seront chargées ici dynamiquement -->
            </div>
        </div>

        <h1>Vos To-Do Lists</h1>

        <!-- Affichage des To-Do Lists -->
        <?php if ($lists): ?>
            <ul>
                <?php foreach ($lists as $list): ?>
                    <li>
                        <a href="../sql/task.php?list_id=<?= $list['list_id']; ?>"><?= $list['list_name']; ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Aucune To-Do List trouvée.</p>
        <?php endif; ?>

        <!-- Formulaire pour créer une nouvelle To-Do List -->
        <h2>Créer une nouvelle To-Do List</h2>
        <form action="../sql/create_todo.php" method="POST">
            <input type="text" name="list_name" placeholder="Nom de la liste" required>
            <button type="submit">Créer la liste</button>
        </form>
    </div>
<?php else: ?>

    <nav class="navbar navbar-expand-lg" style="background-color: #fcc9b9;">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.html">
                <img src="assets/img/japan_list.png" alt="Logo">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a href="pages/inscription.php" class="btn btn-inscription" role="button" style="background-color: #DB5A6B;">Inscription</a>
                    </li>
                    <li class="nav-item">
                        <a href="pages/connexion.php" class="btn btn-connexion" role="button" style="background-color: #b3bcff;">Connexion</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="hero-section">
        <h1 class="hero-title">Organisez votre vie en un clic ! 📝</h1>
        <p class="hero-subtitle">
            Créez vos propres To-Do Lists et boostez votre productivité dès maintenant !
            Ne laissez plus jamais une tâche vous échapper. Inscrivez-vous et prenez le contrôle de votre quotidien ! ✅🔥
        </p>

        <a href="pages/inscription.php" class="hero-btn">Inscrivez-vous</a>
    </div>
<?php endif; ?>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Popper.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.6/umd/popper.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script src="assets/js/get_tasks.js"></script>

</body>
</html>