<?php
include 'db.php';

$response = array('status' => 'error', 'message' => 'An error occurred.');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $categoryName = isset($_POST['name']) ? trim($_POST['name']) : '';

    if (!empty($categoryName)) {
        $sql = "INSERT INTO store_category (name) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $categoryName);

        if ($stmt->execute()) {
            $response['status'] = 'success';
            $response['message'] = 'Category added successfully.';
        } else {
            $response['message'] = 'Failed to add category.';
        }

        $stmt->close();
    } else {
        $response['message'] = 'Category name cannot be empty.';
    }
}

$conn->close();
echo json_encode($response);
?>