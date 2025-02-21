<?php
session_start();
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $store_id = $_SESSION['store_id'];
    $name = $_POST['name'];
    $usd_price = $_POST['usd_price'];
    $khr_price = $_POST['khr_price'];
    $product_code = $_POST['product_code'];
    $description = $_POST['description'];
    $category_id = $_POST['category'];

    // Handle product image upload
    $image = $_FILES['image'];
    $image_extension = pathinfo($image['name'], PATHINFO_EXTENSION);
    $image_name = uniqid() . '.' . $image_extension;
    $image_path = 'images/' . $image_name;
    if (!move_uploaded_file($image['tmp_name'], $image_path)) {
        echo json_encode(['success' => false, 'message' => 'Failed to upload product image']);
        exit;
    }

    // Insert product into database
    $query = "INSERT INTO store_products (store_id, product_name, usd_price, khr_price, product_code, description, category_id, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Failed to prepare statement']);
        exit;
    }
    $stmt->bind_param("isssssis", $store_id, $name, $usd_price, $khr_price, $product_code, $description, $category_id, $image_path);
    if (!$stmt->execute()) {
        echo json_encode(['success' => false, 'message' => 'Failed to execute statement']);
        exit;
    }
    $product_id = $stmt->insert_id;
    $stmt->close();

    // Handle gallery images upload
    if (isset($_FILES['gallery'])) {
        foreach ($_FILES['gallery']['tmp_name'] as $key => $tmp_name) {
            $gallery_image_extension = pathinfo($_FILES['gallery']['name'][$key], PATHINFO_EXTENSION);
            $gallery_image_name = uniqid() . '.' . $gallery_image_extension;
            $gallery_image_path = 'gallery/' . $gallery_image_name;
            if (!move_uploaded_file($tmp_name, $gallery_image_path)) {
                echo json_encode(['success' => false, 'message' => 'Failed to upload gallery image']);
                exit;
            }

            // Insert gallery image into database
            $query = "INSERT INTO product_galleries (product_id, image_path) VALUES (?, ?)";
            $stmt = $conn->prepare($query);
            if (!$stmt) {
                echo json_encode(['success' => false, 'message' => 'Failed to prepare statement for gallery']);
                exit;
            }
            $stmt->bind_param("is", $product_id, $gallery_image_path);
            if (!$stmt->execute()) {
                echo json_encode(['success' => false, 'message' => 'Failed to execute statement for gallery']);
                exit;
            }
            $stmt->close();
        }
    }

    $conn->close();
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>