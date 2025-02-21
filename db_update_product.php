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

    if ($productId > 0 && !empty($productName) && $usdPrice > 0 && $khrPrice > 0 && !empty($productCode) && !empty($description) && $categoryId > 0) {
        $sql = "UPDATE store_products SET product_name = ?, usd_price = ?, khr_price = ?, product_code = ?, description = ?, category_id = ?";
        if (!empty($image)) {
            $sql .= ", image = ?";
        }
        $sql .= " WHERE id = ?";
        
        $stmt = $conn->prepare($sql);
        if (!empty($image)) {
            $stmt->bind_param("sddssisi", $productName, $usdPrice, $khrPrice, $productCode, $description, $categoryId, $image, $productId);
        } else {
            $stmt->bind_param("sddssii", $productName, $usdPrice, $khrPrice, $productCode, $description, $categoryId, $productId);
        }

        if ($stmt->execute()) {
            if (!empty($gallery)) {
                $stmt->close();
                $stmt = $conn->prepare("DELETE FROM product_gallery WHERE product_id = ?");
                $stmt->bind_param("i", $productId);
                $stmt->execute();
                $stmt->close();

                $stmt = $conn->prepare("INSERT INTO product_gallery (product_id, image_path) VALUES (?, ?)");
                foreach ($gallery as $galleryImage) {
                    $stmt->bind_param("is", $productId, $galleryImage);
                    $stmt->execute();
                }
                $stmt->close();
            }

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