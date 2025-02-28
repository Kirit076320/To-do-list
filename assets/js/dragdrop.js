function initDragAndDrop() {
    document.querySelectorAll('.task-item').forEach(task => {
        task.addEventListener('dragstart', handleDragStart);
        task.addEventListener('dragend', handleDragEnd);
        task.addEventListener('dragover', handleDragOver);
        task.addEventListener('drop', handleDrop);
    });

    document.querySelectorAll('.task-list').forEach(list => {
        list.addEventListener('dragover', handleListDragOver);
        list.addEventListener('drop', handleListDrop);
    });
}

function handleDragStart(e) {
    draggingElement = e.target;
    e.target.classList.add('dragging');
    e.dataTransfer.setData('text/plain', JSON.stringify({
        taskId: e.target.dataset.taskId,
        sourceListId: e.target.dataset.listId
    }));
}

function handleDragEnd(e) {
    e.target.classList.remove('dragging');
    draggingElement = null;
    document.querySelectorAll('.drop-indicator').forEach(indicator => indicator.remove());
}

function handleDragOver(e) {
    e.preventDefault();
    if (e.target.classList.contains('task-item') && e.target !== draggingElement) {
        const rect = e.target.getBoundingClientRect();
        const midY = rect.top + rect.height / 2;
        if (e.clientY < midY) {
            e.target.parentElement.insertBefore(dropIndicator, e.target);
        } else {
            e.target.parentElement.insertBefore(dropIndicator, e.target.nextSibling);
        }
    }
}

function handleListDragOver(e) {
    e.preventDefault();
    if (!e.target.classList.contains('task-item') && e.target.classList.contains('task-list')) {
        e.target.appendChild(dropIndicator);
    }
}

function handleListDrop(e) {
    e.preventDefault();
    const taskList = e.target.closest('.task-list');
    if (!taskList) return;

    const data = JSON.parse(e.dataTransfer.getData('text/plain'));
    const newListId = taskList.dataset.listId;
    const position = Array.from(taskList.children).indexOf(dropIndicator);

    fetch('move_task.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            taskId: data.taskId,
            newListId: newListId,
            position: position
        })
    })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                if (data.sourceListId !== newListId) {
                    // Rafraîchir les deux listes si la tâche a été déplacée entre listes
                    refreshTaskList(data.sourceListId);
                    refreshTaskList(newListId);
                } else {
                    // Rafraîchir seulement la liste actuelle si réorganisation interne
                    refreshTaskList(newListId);
                }
            } else {
                alert('Erreur lors du déplacement: ' + result.message);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors du déplacement de la tâche');
        });

    dropIndicator.remove();
}
