<?php

include 'db.php';
session_start();

$response = array('status' => 'error', 'message' => 'Invalid name or password.');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (!empty($name) && !empty($password)) {
        $sql = "SELECT id, password FROM users WHERE name = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $stmt->bind_result($adminId, $hashedPassword);
        $stmt->fetch();
        $stmt->close();

        if ($adminId && password_verify($password, $hashedPassword)) {
            $_SESSION['id'] = $adminId; // ✅ Store admin_id in session
            $_SESSION['name'] = $name;
            $response['status'] = 'success';
            $response['message'] = 'Login successful.';
            header('Location: admin_dashboard.php'); // ✅ Redirect to admin dashboard
            exit;
        }
    }
}

$conn->close();

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
    body {
        font-family: 'Roboto', sans-serif;
    }
    </style>
</head>

<body>
    <div class="min-h-screen flex items-center justify-center bg-gray-100">
        <div class="bg-white p-8 rounded shadow-md w-full max-w-md">
            <h2 class="text-2xl font-bold mb-6 text-center">Admin Login</h2>
            <form id="loginForm" method="POST" action="admin_login.php">
                <div class="mb-4">
                    <label for="name" class="block text-gray-700 font-medium mb-2">Name</label>
                    <input type="text" id="name" name="name" required autocomplete="off"
                        class="w-full border border-gray-300 shadow-sm px-4 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
                <div class="mb-6">
                    <label for="password" class="block text-gray-700 font-medium mb-2">Password</label>
                    <input type="password" id="password" name="password" required autocomplete="off"
                        class="w-full border border-gray-300 shadow-sm px-4 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
                <div class="flex justify-end">
                    <button type="submit"
                        class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none">
                        Login
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>