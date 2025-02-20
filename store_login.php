<?php
session_start();
include 'db.php';

$response = array('status' => 'error', 'message' => 'Invalid code or password.');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = isset($_POST['code']) ? trim($_POST['code']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (!empty($code) && !empty($password)) {
        $sql = "SELECT id, password FROM stores WHERE code = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $code);
        $stmt->execute();
        $stmt->bind_result($userId, $hashedPassword);
        $stmt->fetch();
        $stmt->close();

        if ($userId && password_verify($password, $hashedPassword)) {
            $_SESSION['user_id'] = $userId;
            $_SESSION['user_code'] = $code;
            $response['status'] = 'success';
            $response['message'] = 'Login successful.';
            header('Location: category.php');
            exit;
        }
    }
}

$conn->close();
echo json_encode($response);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Store Login</title>
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
            <h2 class="text-2xl font-bold mb-6 text-center">Store Login</h2>
            <form id="loginForm" method="POST" action="store_login.php">
                <div class="mb-4">
                    <label for="code" class="block text-gray-700 font-medium mb-2">Code</label>
                    <input type="text" id="code" name="code" required autocomplete="off"
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