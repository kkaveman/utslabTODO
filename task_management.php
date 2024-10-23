<?php
require_once 'config.php';
require_once 'functions.php';

if (!is_logged_in()) {
    redirect('login.php');
}

require('navbar.php');

$user_id = $_SESSION['user_id'];
$list_id = isset($_GET['list_id']) ? intval($_GET['list_id']) : 0;

// Verify that the list belongs to the current user
$query = "SELECT * FROM todo_lists WHERE id = ? AND user_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ii", $list_id, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$list = mysqli_fetch_assoc($result);

if (!$list) {
    redirect('dashboard.php');
}

// Add a new task
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_task'])) {
    $task_title = sanitize_input($_POST['task_title']);
    $query = "INSERT INTO tasks (list_id, title) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "is", $list_id, $task_title);
    mysqli_stmt_execute($stmt);
}

// Toggle task completion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['toggle_task'])) {
    $task_id = sanitize_input($_POST['task_id']);
    $query = "UPDATE tasks SET completed = NOT completed WHERE id = ? AND list_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ii", $task_id, $list_id);
    mysqli_stmt_execute($stmt);
}

// Delete a task
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_task'])) {
    $task_id = sanitize_input($_POST['task_id']);
    $query = "DELETE FROM tasks WHERE id = ? AND list_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ii", $task_id, $list_id);
    mysqli_stmt_execute($stmt);
}

// Search and filter functionality
$search_term = isset($_GET['search']) ? sanitize_input($_GET['search']) : '';
$filter = isset($_GET['filter']) ? sanitize_input($_GET['filter']) : 'all';

$where_clause = $search_term ? "AND title LIKE ?" : "";
$where_clause .= $filter == 'completed' ? " AND completed = 1" : ($filter == 'incomplete' ? " AND completed = 0" : "");

$query = "SELECT * FROM tasks 
          WHERE list_id = ? $where_clause
          ORDER BY created_at DESC";

$stmt = mysqli_prepare($conn, $query);

if ($search_term) {
    $search_term = "%$search_term%";
    mysqli_stmt_bind_param($stmt, "is", $list_id, $search_term);
} else {
    mysqli_stmt_bind_param($stmt, "i", $list_id);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$tasks = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Management - <?php echo htmlspecialchars($list['title']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
</head>
<body class="h-full">
    <div class="min-h-full">

        <header class="bg-white shadow">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                <h1 class="text-3xl font-bold tracking-tight text-gray-900">Task Management for "<?php echo htmlspecialchars($list['title']); ?>"</h1>
            </div>
        </header>

        <main>
            <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">
                <div class="px-4 py-6 sm:px-0">
                    <div class="bg-white overflow-hidden shadow rounded-lg divide-y divide-gray-200">
                       

                        <div class="px-4 py-5 sm:px-6">
                        <div class="flex justify-between items-center mb-6">
                            <div>
                            <h2 class="text-lg font-medium text-gray-900">Add a New Task</h2>
                            </div>
                                <a href="dashboard.php" class="text-blue-600 hover:underline">← Back to Dashboard</a>
                        </div>
                       
                            
                            <form method="POST" action="" class="mt-5 sm:flex sm:items-center">
                                <div class="w-full sm:max-w-xs">
                                    <label for="task_title" class="sr-only">New task</label>
                                    <input type="text" name="task_title" id="task_title" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder=" New task" required>
                                </div>
                                <button type="submit" name="add_task" class="mt-3 inline-flex w-full items-center justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 sm:ml-3 sm:mt-0 sm:w-auto">Add Task</button>
                            </form>
                        </div>
                        <div class="px-4 py-5 sm:p-6">
                            <h2 class="text-lg font-medium text-gray-900 mb-5">Search and Filter Tasks</h2>
                            <form method="GET" action="" class="space-y-4 sm:flex sm:items-center sm:space-y-0 sm:space-x-4">
                                <input type="hidden" name="list_id" value="<?php echo $list_id; ?>">
                                <div class="w-full sm:max-w-xs">
                                    <label for="search" class="sr-only">Search tasks</label>
                                    <input type="text" name="search" id="search" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder=" Search tasks..." value="<?php echo htmlspecialchars($search_term); ?>">
                                </div>
                                <div>
                                    <label for="filter" class="sr-only">Filter tasks</label>
                                    <select name="filter" id="filter" class="block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                        <option value="all" <?php echo $filter == 'all' ? 'selected' : ''; ?>>All Tasks</option>
                                        <option value="completed" <?php echo $filter == 'completed' ? 'selected' : ''; ?>>Completed Tasks</option>
                                        <option value="incomplete" <?php echo $filter == 'incomplete' ? 'selected' : ''; ?>>Incomplete Tasks</option>
                                    </select>
                                </div>
                                <button type="submit" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Search and Filter</button>
                            </form>
                        </div>
                        <div class="px-4 py-5 sm:p-6">
                            <h2 class="text-lg font-medium text-gray-900 mb-5">Tasks</h2>
                            <ul class="divide-y divide-gray-200">
                                <?php foreach ($tasks as $task): ?>
                                    <li class="py-4 flex items-center justify-between">
                                        <div class="flex items-center">
                                            <form method="POST" action="" class="mr-3">
                                                <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                                <input type="checkbox" name="toggle_task" onchange="this.form.submit()" <?php echo $task['completed'] ? 'checked' : ''; ?> class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                            </form>
                                            <span class="text-sm font-medium text-gray-900 <?php echo $task['completed'] ? 'line-through text-gray-500' : ''; ?>"><?php echo htmlspecialchars($task['title']); ?></span>
                                        </div>
                                        <form method="POST" action="">
                                            <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                            <button type="submit" name="delete_task" onclick="return confirm('Are you sure you want to delete this task?')" class="text-red-600 hover:text-red-900">
                                                <i class="fas fa-trash-alt"></i>
                                                <span class="sr-only">Delete</span>
                                            </button>
                                        </form>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>