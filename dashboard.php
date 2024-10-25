<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}


$user_id = $_SESSION['user_id'];
$query = $db->prepare("SELECT * FROM todo_lists WHERE user_id = ?");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();


$username_query = $db->prepare("SELECT username FROM users WHERE id = ?");
$username_query->bind_param("i", $user_id);
$username_query->execute();
$username_result = $username_query->get_result()->fetch_assoc();
$username = htmlspecialchars($username_result['username']);

$formatted_username = ucfirst(strtolower($username));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
<div class="container-fluid">

    <nav class="navbar navbar-expand-lg navbar-light">
        <img src="assets/inversedumn.png">
        <span class="navbar-text">Hello, <?php echo $formatted_username; ?></span>
        <button class="navbar-toggler" type="button" id="sidebarToggle">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse">
            <div class="w-100 d-flex align-items-center"> 
                <input type="text" id="taskSearch" class="form-control" placeholder="Search tasks...">
            </div>
        </div>
        <div class="ml-auto">
            <form method="GET" action="profile.php" class="d-inline">
                <button type="submit" class="btn btn-secondary custom-profile-btn">
                    <img src="assets/profile.png" style="width: 20px; height: 20px;"> 
                    Profile
                </button>
            </form>
            <form method="POST" action="logout.php" class="d-inline">
                <button type="submit" class="btn btn-danger custom-logout-btn">
                    <img src="assets/logout.png" style="width: 20px; height: 20px;">
                    Logout
                </button>
            </form>
        </div>
    </nav>

    <div class="row">

        <div class="col-3 sidebar p-0">
            <h6 class="ml-3 mb-2">Your To-Do Lists</h6>
            <hr class="custom-line">


            <button class="btn btn-add-list" data-toggle="modal" data-target="#addListModal">
                <img src="assets/tambah.png" style="width: 20px; height: 20px; margin-right: 10px; align-items: center; justify-content: center;"> 
                Add List
            </button>
            
            <?php if ($result->num_rows > 0): ?>
                <?php while ($list = $result->fetch_assoc()): ?>
                    <div class="todo-list" id="list-<?php echo $list['id']; ?>" onclick="loadTasks(<?php echo $list['id']; ?>)">
                        <button class="delete-list-btn" onclick="deleteList(<?php echo $list['id']; ?>)">
                            <img src="assets/delete.png" alt="Delete" />
                        </button>
                        <span class="list-name"><?php echo htmlspecialchars($list['title']); ?></span>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No todo lists available. Please create one!</p>
            <?php endif; ?>
        </div>


        <div class="col-9 tasks-container">
            <div id="search-results" style="display: none;"></div> 

            <h3 id="task-header">Select a list to view tasks</h3>


            <div class="dropdown mb-3">
                <button class="btn custom-filter-btn dropdown-toggle" type="button" id="filterDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <img src="assets/sort.png" style="width: 23px; height: 23px; margin-right: 10px; align-items: center; justify-content: center;"> 
                    Filter Tasks
                </button>
                <div class="dropdown-menu" aria-labelledby="filterDropdown">
                    <a class="dropdown-item" href="#" onclick="filterTasks('all')">All Tasks</a>
                    <a class="dropdown-item" href="#" onclick="filterTasks('complete')">Completed Tasks</a>
                    <a class="dropdown-item" href="#" onclick="filterTasks('incomplete')">Incomplete Tasks</a>
                </div>
            </div>


            <div class="d-flex align-items-center">
                <button class="btn btn-success mt-3 custom-add-task-btn" id="addTaskButton" 
                        style="display:none;" data-toggle="modal" data-target="#addTaskModal">
                        <img src="assets/tambah.png" style="width: 20px; height: 20px;"> 
                    Add Task
                </button>
            </div>

            

            <div id="tasks">

            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="addListModal" tabindex="-1" role="dialog" aria-labelledby="addListModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addListModalLabel">Add New List</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="text" id="listName" class="form-control" placeholder="Enter list name" required>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="addList()">Add List</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="addTaskModal" tabindex="-1" role="dialog" aria-labelledby="addTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTaskModalLabel">Add New Task</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="text" id="taskName" class="form-control" placeholder="Enter task name" required>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveTaskButton" onclick="addTask()">Add Task</button>
            </div>
        </div>
    </div>
</div>

<script>
    let currentListId = null;

    function setCurrentListId(listId) {
        currentListId = listId;
    }

    function loadTasks(listId) {
        currentListId = listId; 
        
        $('.todo-list').removeClass('active');
        
        
        $('#list-' + listId).addClass('active');

        $.ajax({
            url: 'load_tasks.php',
            type: 'GET',
            data: { list_id: listId },
            success: function(response) {
                const data = JSON.parse(response); 
                $('#tasks').empty(); 

                
                $('#task-header').text(data.list_name);

                
                $('#addTaskButton').show();

                
                if (data.tasks.length > 0) {
                    data.tasks.forEach(task => {
                        const checked = task.completed ? 'checked' : '';
                        $('#tasks').append(`
                            <div class="task d-flex justify-content-between align-items-center">
                                <div>
                                    <input type="checkbox" class="task-checkbox" data-task-id="${task.id}" ${checked} onchange="toggleTaskComplete(${task.id}, this.checked)">
                                    ${task.description}
                                </div>
                                <button class="btn btn-danger btn-sm delete-task-btn" onclick="deleteTask(${task.id})">
                                    <img src="assets/delete.png" style="width: 23px; height: 23px;">
                                
                                Delete
                                </button>
                            </div>
                        `);
                    });
                } else {
                    $('#tasks').append('<p>No tasks available for this list.</p>');
                }
            },
            error: function() {
                $('#tasks').html('<p>Error loading tasks. Please try again later.</p>');
            }
        });
    }

    function filterTasks(filter) {
        if (currentListId === null) {
            alert('Please select a list first!');
            return;
        }
        
        $.ajax({
            url: 'filter_tasks.php',
            type: 'POST',
            data: { list_id: currentListId, filter: filter },
            success: function(response) {
                const data = JSON.parse(response);
                $('#tasks').empty();
                
                if (data.tasks.length > 0) {
                    data.tasks.forEach(task => {
                        const checked = task.completed ? 'checked' : '';
                        $('#tasks').append(`
                            <div class="task d-flex justify-content-between align-items-center">
                                <div>
                                    <input type="checkbox" class="task-checkbox" data-task-id="${task.id}" ${checked}>
                                    ${task.description}
                                </div>
                                <button class="btn btn-danger btn-sm delete-task-btn" onclick="deleteTask(${task.id})">Delete</button>
                            </div>
                        `);
                    });
                } else {
                    $('#tasks').append('<p>No tasks available for this filter.</p>');
                }
            },
            error: function(xhr, status, error) {
                alert('An error occurred: ' + xhr.responseText);
            }
        });
    }

    function deleteTask(taskId) {
        if (confirm('Are you sure you want to delete this task?')) {
            $.ajax({
                url: 'delete_task.php',
                type: 'POST',
                data: JSON.stringify({ task_id: taskId }),
                contentType: 'application/json',
                success: function(response) {
                    const data = JSON.parse(response);
                    if (data.success) {
                        alert('Task deleted successfully.');
                        loadTasks(currentListId); 
                    } else {
                        alert('Failed to delete the task: ' + data.message);
                    }
                },
                error: function(xhr, status, error) {
                    alert('An error occurred: ' + xhr.responseText);
                }
            });
        }
    }

    function deleteList(listId) {
        if (confirm('Are you sure you want to delete this list? This action cannot be undone.')) {
            $.ajax({
                url: 'delete_list.php',
                type: 'POST',
                data: { list_id: listId },
                success: function(response) {
                    if (response === 'success') {
                        alert('List deleted successfully.');
                        location.reload(); 
                    } else if (response === 'not_logged_in') {
                        alert('You need to be logged in to delete a list.');
                    } else if (response === 'error') {
                        alert('An error occurred while trying to delete the list.');
                    }
                }
            });
        }
    }

    function toggleTaskComplete(taskId, currentStatus) {
        const newStatus = currentStatus ? 1 : 0; 

        fetch('mark_complete.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ task_id: taskId, completed: newStatus })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Task status updated successfully');
            
                const checkbox = $(`.task-checkbox[data-task-id="${taskId}"]`);
                checkbox.prop('checked', newStatus === 1);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }

    function addList() {
        const listName = $('#listName').val(); 
        if (listName.trim() === '') {
            alert('Please enter a list name.');
            return;
        }

        $.ajax({    
            url: 'new_list.php', 
            type: 'POST',
            data: { list_name: listName }, 
            success: function(response) {
                if (response === 'success') {
                    alert('List added successfully.');
                    location.reload(); 
                } else {
                    alert('Failed to add the list: ' + response);
                }
            },
            error: function(xhr, status, error) {
                alert('An error occurred: ' + xhr.responseText);
            }
        });
    }


    function addTask() {
        const taskName = $('#taskName').val(); 

        if (!taskName) {
            alert('Please enter a task name.');
            return;
        }

        if (currentListId === null) {
            alert('Please select a list first!');
            return;
        }

        $.ajax({
            url: 'new_task.php', 
            type: 'POST',
            data: {
                task_name: taskName,
                list_id: currentListId 
            },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    $('#addTaskModal').modal('hide'); 
                    $('#taskName').val(''); 

                    
                    loadTasks(currentListId);
                } else {
                    alert('Failed to add task: ' + data.message);
                }
            },
            error: function(xhr, status, error) {
                alert('An error occurred: ' + xhr.responseText);
            }
        });
    }

    

    
    $(document).ready(function() {
        $('#sidebarToggle').click(function() {
            $('.sidebar').toggleClass('show'); 
            $('#taskSearch').toggle(); 
        });
    });

    $(document).on('change', '.task-checkbox', function(event) {
        event.preventDefault(); 
        const taskId = $(this).data('task-id');
        const currentStatus = $(this).is(':checked') ? 1 : 0;
        toggleTaskComplete(taskId, currentStatus);
    });
    
    $(document).on('input', '#taskSearch', function() {
        const searchQuery = $(this).val().trim();
        if (searchQuery.length === 0) {
            $('#search-results').hide();
            return;
        }

        $.ajax({
            url: 'search_tasks.php', 
            type: 'GET',
            data: { query: searchQuery },
            success: function(response) {
                console.log(response);
                const data = JSON.parse(response);
                $('#search-results').empty();

                if (data.tasks.length > 0) {
                    $('#search-results').append(`<p>Search results for "${searchQuery}":</p>`);
                    data.tasks.forEach(task => {
                        const status = task.completed ? 'completed' : 'not completed'; 
                        const checked = task.completed ? 'checked' : '';
                        
                        $('#search-results').append(`
                            <div class="task d-flex justify-content-between align-items-center">
                                <div>
                                    <input type="checkbox" class="task-checkbox" ${checked} onclick="updateTaskStatus(${task.id}, this.checked)" /> 
                                    ${task.description} in ${task.list_name} with status ${status}
                                </div>
                                <button class="btn btn-danger btn-sm delete-task-btn" onclick="deleteTask(${task.id})">Delete</button>
                            </div>
                        `);
                    });
                    $('#search-results').show(); 
                } 
                else {
                    $('#search-results').append('<p>No tasks found.</p>');
                    $('#search-results').show();
                }
            },
            error: function(xhr, status, error) {
                alert('An error occurred: ' + xhr.responseText);
            }
        });
    });

    
    function updateTaskStatus(taskId, isCompleted) {
        const taskData = {
            task_id: taskId,
            completed: isCompleted ? 1 : 0
        };

        console.log('Sending taskData:', taskData);

        $.ajax({
            url: 'mark_complete.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(taskData),
            success: function(response) {
                console.log('Response from server:', response); 
                try {
                    const result = JSON.parse(response);  response
                    if (result.success) {
                        alert('Task status updated successfully');
                    } else {
                        alert('Error: ' + result.message);
                    }
                } catch (error) {
                    alert('Error parsing response: ' + error.message);
                }
            },
            error: function(xhr, status, error) {
                alert('AJAX Error: ' + status + ': ' + error);
            }
        });
    }
    

</script>
</body>
</html>
