<?php
include 'db.php';

$response = array('status' => 'error', 'message' => 'An error occurred.');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $categoryId = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $categoryName = isset($_POST['name']) ? trim($_POST['name']) : '';

    if ($categoryId > 0 && !empty($categoryName)) {
        $sql = "UPDATE store_category SET name = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $categoryName, $categoryId);

        if ($stmt->execute()) {
            $response['status'] = 'success';
            $response['message'] = 'Category updated successfully.';
        } else {
            $response['message'] = 'Failed to update category.';
        }

        $stmt->close();
    } else {
        $response['message'] = 'Invalid category ID or name.';
    }
}

$conn->close();
echo json_encode($response);
?>