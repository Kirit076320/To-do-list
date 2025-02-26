<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>To-do list</title>

    <!-- lien bootstrap -->

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://getbootstrap.com/docs/5.3/assets/css/docs.css" rel="stylesheet">
    <title>Bootstrap Example</title>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" href="../assets/style.css">

    <link rel="shortcut icon" href="../assets/img/japan_list.png" type="image/x-icon">
</head>
<body class="accueil_body">

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
                    <a href="favoris.php" class="btn btn-inscription" role="button" style="background-color: #DB5A6B;">❤️ Favoris ❤️</a>
                </li>
                <li class="nav-item">
                    <a href="connexion.php" class="btn btn-connexion" role="button" style="background-color: #b3bcff;">Déconnexion</a>
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


<!-- Inclusion de jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Popper.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.6/umd/popper.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script src="../assets/js/get_tasks.js"></script>

<script>
    document.getElementById("searchInput").addEventListener("input", function () {
        let value = this.value.toLowerCase();
        let cards = document.querySelectorAll(".custom-card");

        cards.forEach(card => {
            let taskName = card.querySelector(".card-title").textContent.toLowerCase();
            let cardText = card.innerText.toLowerCase();

            // Vérifier si la valeur correspond soit au nom, soit à une couleur mentionnée
            if (taskName.includes(value) || cardText.includes(value)) {
                card.parentElement.style.display = "block";
            } else {
                card.parentElement.style.display = "none";
            }
        });
    });
</script>


</body>
</html>
