function addTask(todoId) {
    console.log('Ajout d\'une tâche à la liste:', todoId);
    var taskInput = prompt("Entrez la nouvelle tâche :");

    if (taskInput && taskInput.trim() !== "") {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'add_task.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onload = function() {
            console.log('Réponse du serveur:', xhr.responseText);
            if (xhr.status == 200) {
                var taskList = document.getElementById('tasks-' + todoId);
                var newTask = document.createElement('li');
                newTask.textContent = taskInput;
                taskList.appendChild(newTask);
            } else {
                console.error('Erreur lors de l\'ajout de la tâche.');
            }
        };

        xhr.send('todo_id=' + todoId + '&task_name=' + encodeURIComponent(taskInput));
    } else {
        console.log("Erreur : Tâche vide ou annulée.");
    }
}
xhr.onload = function() {
    console.log('Réponse du serveur:', xhr.responseText);
    console.log('Statut de la requête:', xhr.status);

    if (xhr.status == 200) {
        try {
            var response = JSON.parse(xhr.responseText);
            if (response.success) {
                document.getElementById('todo-lists-container').innerHTML += response.html;
                console.log('Nouvelle To-Do List ajoutée !');
            } else {
                console.error('Erreur:', response.error);
            }
        } catch (e) {
            console.error('Erreur de parsing JSON:', e);
        }
    } else {
        console.error('Erreur dans la réponse:', xhr.status);
    }
};
// Drag and Drop
function allowDrop(e) {
    e.preventDefault();
}

function drop(e) {
    e.preventDefault();
    const taskId = e.dataTransfer.getData("text/plain");
    const task = document.getElementById(taskId);
    e.target.appendChild(task); // Déplace la tâche
    updateTaskStatus(taskId, e.target.id); // À implémenter (envoi AJAX vers PHP)
}

// Ajout de tâche
function addTask(todoId) {
    const taskId = `task_${Date.now()}`;
    const task = document.createElement("li");
    task.id = taskId;
    task.draggable = true;
    task.textContent = "Nouvelle tâche";
    task.addEventListener("dragstart", (e) => {
        e.dataTransfer.setData("text/plain", e.target.id);
    });
    document.getElementById(`todo-tasks-${todoId}`).appendChild(task);
}