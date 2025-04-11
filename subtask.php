<?php
session_start();
include 'database.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id_task'])) {
    header("Location: index.php");
    exit;
}

$id_task = $_GET['id_task'];
$user_id = $_SESSION["user_id"];

// Fetch Task Details
$q_task = "SELECT * FROM tasks WHERE taskid = '$id_task' AND user_id = '$user_id'";
$run_q_task = mysqli_query($conn, $q_task);
$task = mysqli_fetch_assoc($run_q_task);

if (!$task) {
    header("Location: index.php");
    exit;
}

// Insert Subtask
if (isset($_POST['add_subtask'])) {
    $q_insert_subtask = "INSERT INTO subtask (id_task, Judul, Status) VALUES ('$id_task', '" . $_POST['subtask'] . "', 'open')";
    mysqli_query($conn, $q_insert_subtask);
    header("Location: subtask.php?id_task=$id_task");
}

// Update Subtask Status
if (isset($_GET['done'])) {
    $status = ($_GET['status'] == 'open') ? 'close' : 'open';
    mysqli_query($conn, "UPDATE subtask SET Status = '$status' WHERE id = '" . $_GET['done'] . "'");
    header("Location: subtask.php?id_task=$id_task");
}

// Delete Subtask
if (isset($_GET['delete'])) {
    mysqli_query($conn, "DELETE FROM subtask WHERE id = '" . $_GET['delete'] . "'");
    header("Location: subtask.php?id_task=$id_task");
}

// Fetch Subtask
$q_subtask = "SELECT * FROM subtask WHERE id_task = '$id_task' ORDER BY id DESC";
$run_q_subtask = mysqli_query($conn, $q_subtask);
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Subtask</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(to top, #ffdde1, #ee9ca7);
            height: 100vh;
        }

        .container {
            width: 590px;
            margin: 20px auto;
        }

        .card {
            background-color: #fff;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .task-item {
            display: flex;
            justify-content: space-between;
        }

        .task-item.done span {
            text-decoration: line-through;
            color: #ccc;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card">
            <h2>Subtask for: <?= $task['tasklabel'] ?></h2>
            <a href="index.php">Back to Tasks</a>
        </div>

        <div class="card">
            <form action="" method="post">
                <input type="text" name="subtask" placeholder="Add subtask" required>
                <button type="submit" name="add_subtask">Add Subtask</button>
            </form>
        </div>

        <?php while ($subtask = mysqli_fetch_array($run_q_subtask)) { ?>
            <div class="card">
                <div class="task-item <?= $subtask['Status'] == 'close' ? 'done' : '' ?>">
                    <div>
                        <input type="checkbox" onclick="window.location.href='?id_task=<?= $id_task ?>&done=<?= $subtask['id'] ?>&status=<?= $subtask['Status'] ?>'" <?= $subtask['Status'] == 'close' ? 'checked' : '' ?>>
                        <span><?= $subtask['Judul'] ?></span>
                    </div>
                    <div>
                        <a href="?id_task=<?= $id_task ?>&delete=<?= $subtask['id'] ?>" class="text-red" title="Remove" onclick="return confirm('Are you sure?')"><i class="bx bx-trash"></i></a>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</body>

</html>