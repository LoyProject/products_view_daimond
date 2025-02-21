<?php
include 'db.php';

$response = array('status' => 'error', 'message' => 'An error occurred.');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $productName = isset($_POST['name']) ? trim($_POST['name']) : '';
    $usdPrice = isset($_POST['usd_price']) ? floatval($_POST['usd_price']) : 0;
    $khrPrice = isset($_POST['khr_price']) ? floatval($_POST['khr_price']) : 0;
    $productCode = isset($_POST['product_code']) ? trim($_POST['product_code']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $categoryId = isset($_POST['category']) ? intval($_POST['category']) : 0;

    if ($productId > 0 && !empty($productName) && $usdPrice > 0 && $khrPrice > 0 && !empty($productCode) && !empty($description) && $categoryId > 0) {
        $sql = "UPDATE store_products SET product_name = ?, usd_price = ?, khr_price = ?, product_code = ?, description = ?, category_id = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sddssii", $productName, $usdPrice, $khrPrice, $productCode, $description, $categoryId, $productId);

        if ($stmt->execute()) {
            $response['status'] = 'success';
            $response['message'] = 'Product updated successfully.';
        } else {
            $response['message'] = 'Failed to update product.';
        }

        $stmt->close();
    } else {
        $response['message'] = 'Invalid product data.';
    }
}

$conn->close();
echo json_encode($response);
?>