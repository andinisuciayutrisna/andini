<?php
session_start();
include 'database.php';

if (!isset($_SESSION["user_id"])) {
	header("Location: login.php");
	exit;
}

$user_id = $_SESSION["user_id"]; //ambil user id dari session
// proses insert data
if (isset($_POST['add'])) {

	$q_insert = "INSERT into tasks (tasklabel, taskstatus, user_id, deadline, priority) value (
		'" . $_POST['task'] . "',
		'open',
		'$user_id',
		'" . $_POST['deadline'] . "',
		'" . $_POST['priority'] . "'
		)";
	$run_q_insert = mysqli_query($conn, $q_insert);

	if ($run_q_insert) {
		header('Refresh:0; url=index.php');
	}
}


// proses show data
$q_select = "select * from tasks where user_id = '$user_id' order by taskid desc";
$run_q_select = mysqli_query($conn, $q_select);


// proses delete data
if (isset($_GET['delete'])) {

	$q_delete = "delete from tasks where taskid = '" . $_GET['delete'] . "' ";
	$run_q_delete = mysqli_query($conn, $q_delete);

	header('Refresh:0; url=index.php');
}


// proses update data (close or open)
if (isset($_GET['done'])) {
	$status = 'close';

	if ($_GET['status'] == 'open') {
		$status = 'close';
	} else {
		$status = 'open';
	}

	$q_update = "update tasks set taskstatus = '" . $status . "' where taskid = '" . $_GET['done'] . "' ";
	$run_q_update = mysqli_query($conn, $q_update);

	header('Refresh:0; url=index.php');
}

?>

<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>To Do List</title>
	<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
	<style type="text/css">
		@import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap');

		* {
			padding: 0;
			margin: 0;
			box-sizing: border-box;
		}

		body {
			font-family: 'Roboto', sans-serif;
			background: rgb(250, 153, 166);
			/* fallback for old browsers */
			background: -webkit-linear-gradient(to top, #ffdde1, #ee9ca7);
			/* Chrome 10-25, Safari 5.1-6 */
			background: linear-gradient(to top, #ffdde1, #ee9ca7);
			/* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */


		}

		.container {
			width: 590px;
			height: 100vh;
			margin: 0 auto;
		}

		.header {
			padding: 15px;
			color: #fff;
		}

		.header .title {
			display: flex;
			align-items: center;
			margin-bottom: 7px;
		}

		.header .title i {
			font-size: 24px;
			margin-right: 10px;
		}

		.header .title span {
			font-size: 18px;
		}

		.header .description {
			font-size: 13px;
		}

		.content {
			padding: 15px;
		}

		.card {
			background-color: #fff;
			padding: 15px;
			border-radius: 5px;
			margin-bottom: 10px;
		}

		.input-control {
			width: 100%;
			display: block;
			padding: 0.5rem;
			font-size: 1rem;
			margin-bottom: 10px;
		}

		.text-right {
			text-align: right;
		}

		button {
			padding: 0.5rem 1rem;
			font-size: 1rem;
			cursor: pointer;
			background: rgb(250, 156, 168);
			/* fallback for old browsers */
			background: -webkit-linear-gradient(to top, #ffdde1, #ee9ca7);
			/* Chrome 10-25, Safari 5.1-6 */
			background: linear-gradient(to top, #ffdde1, #ee9ca7);
			/* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */

			color: #fff;
			border: 1px solid;
			border-radius: 3px;
		}

		.task-item {
			display: flex;
			justify-content: space-between;
		}

		.text-orange {
			color: orange;
		}

		.text-red {
			color: red;
		}

		.task-item.done span {
			text-decoration: line-through;
			color: #ccc;
		}

		@media (max-width: 768px) {
			.container {
				width: 100%;
			}
		}
	</style>
</head>

<body>

	<div class="container">

		<div class="header">

			<div class="title">
				<i class='bx bx-sun'></i>
				<span>To Do List</span>
				<a href="logout.php">logout</a>
			</div>

			<div class="description">
				<?= date("l, d M Y") ?>
			</div>

		</div>

		<div class="content">

			<div class="card">

				<form action="" method="post">

					<input type="text" name="task" class="input-control" placeholder="Add task" required>
					<input type="date" name="deadline" class="input-control" placeholder="Deadline" required>
					<select name="priority" class="input-control" placeholder="Priority">
						<option value="low">Low</option>
						<option value="medium">Medium</option>
						<option value="high">High</option>
					</select>

					<div class="text-right">
						<button type="submit" name="add">Add</button>
					</div>

				</form>

			</div>


			<?php

			if (mysqli_num_rows($run_q_select) > 0) {
				while ($r = mysqli_fetch_array($run_q_select)) {
					?>
					<div class="card">
						<div class="task-item <?= $r['taskstatus'] == 'close' ? 'done' : '' ?>">
							<div>
								<input type="checkbox" onclick="window.location.href = '?done=<?= $r['taskid'] ?>&status=<?= $r['taskstatus'] ?>'" <?= $r['taskstatus'] == 'close' ? 'checked' : '' ?>>
								<span><?= $r['tasklabel'] ?></span>
								<span><?= $r['priority'] ?></span>

							</div>
							<div>
								<span><?= $r['deadline'] ?></span>
								<a href="subtask.php?id_task=<?= $r['taskid'] ?>" class="text-orange">Subtasks</a>
								<a href="edit.php?id=<?= $r['taskid'] ?>" class="text-orange" title="Edit"><i class="bx bx-edit"></i></a>
								<a href="?delete=<?= $r['taskid'] ?>" class="text-red" title="Remove" onclick="return confirm('Are you sure ?')"><i class="bx bx-trash"></i></a>
							</div>
						</div>
					</div>
				<?php }
				} else { ?>
				<div>Belum ada task</div>
			<?php } ?>

		</div>

	</div>

</body>

</html>