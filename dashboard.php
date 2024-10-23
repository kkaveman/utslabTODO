<?php
require_once 'config.php';
require_once 'functions.php';

if (!is_logged_in()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];
require('navbar.php');

// Create a new to-do list
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_list'])) {
    $list_title = sanitize_input($_POST['list_title']);
    $query = "INSERT INTO todo_lists (user_id, title) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "is", $user_id, $list_title);
    mysqli_stmt_execute($stmt);
}

// Delete a to-do list
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_list'])) {
    $list_id = sanitize_input($_POST['list_id']);
    $query = "DELETE FROM todo_lists WHERE id = ? AND user_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ii", $list_id, $user_id);
    mysqli_stmt_execute($stmt);
}

// Fetch user's to-do lists with task counts
$query = "SELECT l.*, 
          (SELECT COUNT(*) FROM tasks t WHERE t.list_id = l.id) as task_count,
          (SELECT COUNT(*) FROM tasks t WHERE t.list_id = l.id AND t.completed = 1) as completed_count
          FROM todo_lists l WHERE l.user_id = ? ORDER BY l.created_at DESC";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$todo_lists = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Online To-Do List</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
</head>
<body class="h-full">
    <div class="min-h-full">
        

        <header class="bg-white shadow">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                <h1 class="text-3xl font-bold tracking-tight text-gray-900">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
            </div>
        </header>

        <main>
            <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">
                <div class="px-4 py-6 sm:px-0">
                    <div class="bg-white overflow-hidden shadow rounded-lg divide-y divide-gray-200">
                        <div class="px-4 py-5 sm:px-6">
                            <h2 class="text-lg font-medium text-gray-900">Create a New To-Do List</h2>
                            <form method="POST" action="" class="mt-5 sm:flex sm:items-center">
                                <div class="w-full sm:max-w-xs">
                                    <label for="list_title" class="sr-only">List Title</label>
                                    <input type="text" name="list_title" id="list_title" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder=" List Title" required>
                                </div>
                                <button type="submit" name="create_list" class="mt-3 inline-flex w-full items-center justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 sm:ml-3 sm:mt-0 sm:w-auto">Create List</button>
                            </form>
                        </div>
                        <div class="px-4 py-5 sm:p-6">
                            <h2 class="text-lg font-medium text-gray-900 mb-5">Your To-Do Lists</h2>
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                <?php foreach ($todo_lists as $list): ?>
                                    <div class="bg-white overflow-hidden shadow rounded-lg">
                                        <div class="px-4 py-5 sm:p-6">
                                            <h3 class="text-lg font-medium text-gray-900 truncate"><?php echo htmlspecialchars($list['title']); ?></h3>
                                            <div class="mt-2 max-w-xl text-sm text-gray-500">
                                                <p><?php echo $list['completed_count']; ?>/<?php echo $list['task_count']; ?> completed</p>
                                            </div>
                                            <div class="mt-5">
                                                <a href="task_management.php?list_id=<?php echo $list['id']; ?>" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Manage Tasks</a>
                                                <form method="POST" action="" class="mt-3 inline-block">
                                                    <input type="hidden" name="list_id" value="<?php echo $list['id']; ?>">
                                                    <button type="submit" name="delete_list" onclick="return confirm('Are you sure you want to delete this list?')" class="inline-flex items-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600">Delete List</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>