$(document).ready(function () {
    loadUserLists();

    // Ajout de la recherche en temps réel
    $("#searchInput").on("input", function () {
        let filter = $(this).val().toLowerCase();

        $(".custom-card").each(function () {
            let listName = $(this).attr("data-list-name").toLowerCase();
            let taskColors = $(this).attr("data-task-colors").toLowerCase();

            if (listName.includes(filter) || taskColors.includes(filter)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
});

function loadUserLists() {
    $.ajax({
        url: "../assets/sql/get_user_lists.php",
        type: "GET",
        dataType: "json",
        success: function (data) {
            console.log("Données reçues :", data); // 🔥 Debugging

            if (data.error) {
                $("#task-container").html("<p class='text-danger'>" + data.error + "</p>");
                return;
            }

            if (!Array.isArray(data) || data.length === 0) {
                $("#task-container").html("<div class='no_list'><p>Aucune liste trouvée.</p></div>");
                return;
            }

            let container = $("#task-container");
            container.empty(); // Nettoyer avant de recharger les données

            data.forEach(list => {
                let taskColors = [];

                let listHTML = `
                    <div class="card custom-card" 
                         data-list-name="${list.list_name}" 
                         data-task-colors=""> 
                        <div class="card-body custom-card-body">
                            <h5 class="card-title text-center custom-card-title">
                                <a href="/sql/task.php?list_id=${list.list_id}" class="text-decoration-none">
                                    ${list.list_name}
                                </a>
                            </h5>
                            <ul class="task-list">`;

                if (Array.isArray(list.tasks) && list.tasks.length > 0) {
                    list.tasks.forEach(task => {
                        if (task.color) {
                            taskColors.push(task.color); // Stocker la couleur pour l'attribut
                        }

                        listHTML += `
                            <li>
                                <strong style="filter: blur(5px);">${task.task_name}</strong><br>
                                <small style="filter: blur(5px);">${task.description}</small> <br>
                                <span style="filter: blur(5px);">Priorité: ${task.priority}, Date limite: ${task.due_date}</span>
                            </li>`;
                    });
                } else {
                    listHTML += `<li>Aucune tâche pour cette liste.</li>`;
                }

                listHTML += `</ul></div></div>`;

                // Ajouter les couleurs au `data-task-colors`
                let cardElement = $(listHTML);
                cardElement.attr("data-task-colors", taskColors.join(", "));

                container.append(cardElement);
            });
        },
        error: function (xhr, status, error) {
            console.error("Erreur AJAX :", xhr.responseText);
        }
    });
}
