<?php
include 'db.php';

$response = array('status' => 'error', 'message' => 'An error occurred.');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productName = isset($_POST['name']) ? trim($_POST['name']) : '';
    $usdPrice = isset($_POST['usd_price']) ? floatval($_POST['usd_price']) : 0;
    $khrPrice = isset($_POST['khr_price']) ? floatval($_POST['khr_price']) : 0;
    $productCode = isset($_POST['product_code']) ? trim($_POST['product_code']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $categoryId = isset($_POST['category']) ? intval($_POST['category']) : 0;

    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $image = 'uploads/' . uniqid() . '_' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $image);
    }

    $gallery = [];
    if (isset($_FILES['gallery'])) {
        foreach ($_FILES['gallery']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['gallery']['error'][$key] == UPLOAD_ERR_OK) {
                $galleryPath = 'uploads/' . uniqid() . '_' . basename($_FILES['gallery']['name'][$key]);
                move_uploaded_file($tmp_name, $galleryPath);
                $gallery[] = $galleryPath;
            }
        }
    }

    if (!empty($productName) && $usdPrice > 0 && $khrPrice > 0 && !empty($productCode) && !empty($description) && $categoryId > 0) {
        $stmt = $conn->prepare("INSERT INTO store_products (product_name, usd_price, khr_price, product_code, description, category_id, image) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $productName, $usdPrice, $khrPrice, $productCode, $description, $categoryId, $image);

        if ($stmt->execute()) {
            $productId = $stmt->insert_id;

            if (!empty($gallery)) {
                $galleryStmt = $conn->prepare("INSERT INTO product_gallery (product_id, image_path) VALUES (?, ?)");
                foreach ($gallery as $galleryImage) {
                    $galleryStmt->bind_param("is", $productId, $galleryImage);
                    $galleryStmt->execute();
                }
                $galleryStmt->close();
            }

            $response['status'] = 'success';
            $response['message'] = 'Product inserted successfully.';
        } else {
            $response['message'] = 'Failed to insert product: ' . $stmt->error;
        }

        $stmt->close();
    } else {
        $response['message'] = 'Invalid product data.';
    }
}

$conn->close();
echo json_encode($response);
?>