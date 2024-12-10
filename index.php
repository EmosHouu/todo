<?php
include 'db.php';

// 添加新待辦事項
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['task'])) {
    $task = $_POST['task'];
    $sql = "INSERT INTO todos (task, created_at) VALUES (?, NOW())";  // 使用準備語句並記錄當前時間
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $task);
    $stmt->execute();
    $new_id = $stmt->insert_id;
    $stmt->close();

    $sql = "SELECT * FROM todos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $new_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $new_task = $result->fetch_assoc();
    echo json_encode($new_task);
    exit;
}

// 更新待辦事項
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_task'])) {
    $task_id = $_POST['task_id'];
    $task = $_POST['edit_task'];
    $sql = "UPDATE todos SET task = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $task, $task_id);
    $stmt->execute();
    $stmt->close();
    echo json_encode(['id' => $task_id, 'task' => $task]);
    exit;
}

// 讀取待辦事項
$sql = "SELECT * FROM todos";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>待辦清單</title>
    <!-- 引入 Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f5f5;
            color: #333;
            font-family: Arial, sans-serif;
        }

        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .completed {
            text-decoration: line-through;
            color: #999;
        }

        .task {
            display: inline;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            margin-left: 10px;
        }

        .form-control,
        .btn {
            border: none;
            border-radius: 4px;
        }

        .btn-primary {
            background-color: #8b4513;
            color: #fff;
        }

        .btn-success {
            background-color: #556b2f;
            color: #fff;
        }

        .btn-warning {
            background-color: #d2691e;
            color: #fff;
        }

        .btn-danger {
            background-color: #b22222;
            color: #fff;
        }

        .list-group-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            background-color: #fafafa;
            border: 1px solid #ddd;
            margin-bottom: 10px;
            border-radius: 4px;
        }

        .edit-input {
            flex-grow: 1;
            margin-right: 10px;
        }

        .list-group-item.editing {
            height: auto;
        }

        .created-at {
            margin-left: auto;
            text-align: right;
            color: #999;
        }

        .edit-icon {
            cursor: pointer;
            margin-left: 5px;
            color: #8b4513;
        }

        .form-inline .form-control {
            max-width: 400px;
            /* 設置輸入框的最大寬度 */
        }

        @media (max-width: 576px) {
            .form-inline {
                display: flex;
                flex-direction: row;
                align-items: center;
                width: 100%;
            }

            .form-inline .form-control {
                flex-grow: 1;
                margin-right: 10px;
            }

            .list-group-item {
                flex-direction: column;
                align-items: flex-start;
            }

            .actions {
                width: 100%;
                justify-content: flex-start;
            }

            .edit-input {
                width: 100%;
                margin-right: 0;
                margin-bottom: 10px;
            }

            .created-at {
                width: 100%;
                text-align: left;
                margin-top: 10px;
            }

            .form-inline .form-control {
                max-width: 80%;
                /* 設置輸入框的最大寬度 */
            }
        }
    </style>
</head>

<body class="container">
    <h1 class="my-4">待辦事項清單</h1>
    <form id="addTaskForm" class="form-inline mb-4 d-flex flex-row">
        <input type="text" id="taskInput" name="task" class="form-control mr-2" placeholder="新增待辦事項" required>
        <input type="submit" value="添加" class="btn btn-primary">
    </form>

    <ul id="taskList" class="list-group">
        <?php while ($row = $result->fetch_assoc()): ?>
            <li class="list-group-item" data-id="<?php echo $row['id']; ?>">
                <span class="task <?php echo $row['status'] == 1 ? 'completed' : ''; ?>" data-id="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['task']); ?></span>
                <span class="edit-icon" data-id="<?php echo $row['id']; ?>">&#9998;</span>
                <span class="created-at"><?php echo $row['created_at']; ?></span>
                <span class="actions">
                    <?php if ($row['status'] == 0): ?>
                        <a href="update_todo.php?id=<?php echo $row['id']; ?>&action=complete" class="btn btn-success btn-sm">完成</a>
                    <?php else: ?>
                        <a href="update_todo.php?id=<?php echo $row['id']; ?>&action=undo" class="btn btn-warning btn-sm">取消</a>
                    <?php endif; ?>
                    <a href="delete_todo.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">刪除</a>
                </span>
            </li>
        <?php endwhile; ?>
    </ul>

    <!-- 引入 Bootstrap JS 和 Popper.js -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#addTaskForm').on('submit', function(e) {
                e.preventDefault();
                var task = $('#taskInput').val();
                $.ajax({
                    type: 'POST',
                    url: '',
                    data: {
                        task: task
                    },
                    success: function(response) {
                        var newTask = JSON.parse(response);
                        var newTaskHtml = `
                            <li class="list-group-item" data-id="${newTask.id}">
                                <span class="task" data-id="${newTask.id}">${newTask.task}</span>
                                <span class="edit-icon" data-id="${newTask.id}">&#9998;</span>
                                <span class="created-at">${newTask.created_at}</span>
                                <span class="actions">
                                    <a href="update_todo.php?id=${newTask.id}&action=complete" class="btn btn-success btn-sm">完成</a>
                                    <a href="delete_todo.php?id=${newTask.id}" class="btn btn-danger btn-sm">刪除</a>
                                </span>
                            </li>`;
                        $('#taskList').append(newTaskHtml);
                        $('#taskInput').val('');
                    }
                });
            });

            $(document).on('click', '.task, .edit-icon', function() {
                var taskElement = $(this).closest('.list-group-item').find('.task');
                var taskId = taskElement.data('id');
                var taskText = taskElement.text();
                var inputElement = $('<input>', {
                    type: 'text',
                    value: taskText,
                    class: 'form-control edit-input',
                    blur: function() {
                        var newTaskText = $(this).val();
                        $.ajax({
                            type: 'POST',
                            url: '',
                            data: {
                                task_id: taskId,
                                edit_task: newTaskText
                            },
                            success: function(response) {
                                var updatedTask = JSON.parse(response);
                                taskElement.text(updatedTask.task);
                                taskElement.show();
                                inputElement.remove();
                                taskElement.closest('.list-group-item').removeClass('editing');
                            }
                        });
                    },
                    keyup: function(e) {
                        if (e.key === 'Enter') {
                            $(this).blur();
                        }
                    }
                });
                taskElement.hide();
                taskElement.after(inputElement);
                inputElement.focus();
                taskElement.closest('.list-group-item').addClass('editing');
            });
        });
    </script>
</body>

</html>