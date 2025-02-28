$(document).ready(function () {
    loadLists();
});

function loadLists() {
    $.ajax({
        url: "../assets/sql/get_tasks.php",
        type: "GET",
        dataType: "json",
        success: function (data) {
            console.log("Données reçues :", data); // 🔥 Debugging

            if (!Array.isArray(data) || data.length === 0) {
                console.error("Aucune liste trouvée !");
                $("#list-container").html("<p>Aucune liste trouvée.</p>");
                return;
            }

            let container = $("#task-container");
            container.empty(); // Vider l'affichage avant d'ajouter les nouvelles données

            data.forEach(list => {
                let listHTML =
                    <div class="card custom-card" style="border-left: 5px solid ${list.color}; margin-bottom: 15px;">
                        <div class="card-body custom-card-body">
                            <h5 class="card-title text-center custom-card-title">${list.list_name}</h5>
                            <ul class="task-list" id="task-list-${list.list_id}">;

                                // Vérifie si la liste a des tâches avant de les afficher
                                if (Array.isArray(list.tasks) && list.tasks.length > 0) {
                                    list.tasks.forEach(task => {
                                        listHTML +=
                                            <li>
                                                <strong>${task.task_name}</strong> - ${task.status} <br>
                                                <small>${task.description}</small> <br>
                                                <span>Priorité: ${task.priority}, Date limite: ${task.due_date}</span>
                                            </li>;
                                    });
                                    } else {
                                        listHTML += <li>Aucune tâche pour cette liste.</li>;
                                    }

                                    listHTML += </ul></div></div>;
                                        container.append(listHTML);
                                    });
                                },
                                error: function (xhr, status, error) {
                                    console.error("Erreur AJAX :", xhr.responseText);
                                }
                                });