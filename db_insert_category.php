<?php
session_start();
include 'db.php';

$response = array('status' => 'error', 'message' => 'An error occurred.');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $categoryName = isset($_POST['name']) ? trim($_POST['name']) : '';
    $storeId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0; // Get store ID from session

    if (!empty($categoryName) && $storeId > 0) {
        $sql = "INSERT INTO store_category (name, store_id) VALUES (?, ?)"; // Ensure correct column name
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $categoryName, $storeId);

        if ($stmt->execute()) {
            $response['status'] = 'success';
            $response['message'] = 'Category added successfully.';
        } else {
            $response['message'] = 'Failed to add category.';
        }

        $stmt->close();
    } else {
        $response['message'] = 'Category name cannot be empty, and user must be logged in.';
    }
}

$conn->close();
echo json_encode($response);
?>