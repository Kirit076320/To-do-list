$(document).ready(function () {
    loadUserLists();
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
                let listHTML = `
                    <div class="card custom-card" style="margin-bottom: 15px;">
                        <div class="card-body custom-card-body">
                            <h5 class="card-title text-center custom-card-title">
                                <a href="/sql/task.php?list_id=${list.list_id}" class="text-decoration-none">
                                    ${list.list_name}
                                </a>
                            </h5>
                            <ul class="task-list">`;

                if (Array.isArray(list.tasks) && list.tasks.length > 0) {
                    list.tasks.forEach(task => {
                        listHTML += `
                            <li>
                                <strong>${task.task_name}</strong> - ${task.status} <br>
                                <small>${task.description}</small> <br>
                                <span>Priorité: ${task.priority}, Date limite: ${task.due_date}</span>
                            </li>`;
                    });
                } else {
                    listHTML += `<li>Aucune tâche pour cette liste.</li>`;
                }

                listHTML += `</ul></div></div>`;
                container.append(listHTML);
            });
        },
        error: function (xhr, status, error) {
            console.error("Erreur AJAX :", xhr.responseText);
        }
    });
}
