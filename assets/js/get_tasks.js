$(document).ready(function () {
    loadLists();
});

function loadLists() {
    $.ajax({
        url: "../assets/sql/get_tasks.php", // Assurez-vous que le chemin est correct
        type: "GET",
        dataType: "json",
        success: function (data) {
            console.log("Données reçues :", data); // Debugging

            if (!Array.isArray(data)) {
                console.error("Les données reçues ne sont pas un tableau !");
                return;
            }

            let container = $("#list-container");
            container.empty(); // Vider le conteneur avant d'ajouter les nouvelles données

            data.forEach(list => {
                let listHTML = `
                    <div class="card custom-card">
                        <div class="card-body custom-card-body">
                            <h5 class="card-title text-center custom-card-title">${list.list_name}</h5>
                            <ul class="task-list">`;

                list.tasks.forEach(task => {
                    listHTML += `
                        <li>
                            <strong>${task.task_name}</strong> - ${task.status} 
                            (Priorité: ${task.priority}, Date limite: ${task.due_date})
                        </li>`;
                });

                listHTML += `</ul></div></div>`;
                container.append(listHTML);
            });
        },
        error: function (xhr, status, error) {
            console.error("Erreur AJAX :", xhr.responseText);
        }
    });
}
