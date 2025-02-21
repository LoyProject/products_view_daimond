<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate session store_id
    if (!isset($_SESSION['store_id'])) {
        echo json_encode(['success' => false, 'message' => 'Store ID is missing']);
        exit;
    }
    $store_id = $_SESSION['store_id'];

    // Validate form inputs
    if (!isset($_POST['name'], $_POST['usd_price'], $_POST['khr_price'], $_POST['product_code'], $_POST['description'], $_POST['category'], $_FILES['image'])) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit;
    }

    // Sanitize inputs
    $name = trim($_POST['name']);
    $usd_price = floatval($_POST['usd_price']);
    $khr_price = floatval($_POST['khr_price']);
    $product_code = trim($_POST['product_code']);
    $description = trim($_POST['description']);
    $category_id = intval($_POST['category']);

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
    $query = "INSERT INTO store_products (store_id, product_name, usd_price, khr_price, product_code, description, category_id, image) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'SQL Error: ' . $conn->error]);
        exit;
    }

    $stmt->bind_param("issdssiss", $store_id, $name, $usd_price, $khr_price, $product_code, $description, $category_id, $image_path);

    if (!$stmt->execute()) {
        echo json_encode(['success' => false, 'message' => 'Execute Error: ' . $stmt->error]);
        exit;
    }

    $product_id = $stmt->insert_id;
    $stmt->close();

    // Handle gallery images upload
    if (isset($_FILES['gallery']) && count($_FILES['gallery']['tmp_name']) > 0) {
        foreach ($_FILES['gallery']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['gallery']['error'][$key] == 0) {
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
                    echo json_encode(['success' => false, 'message' => 'SQL Error (Gallery): ' . $conn->error]);
                    exit;
                }

                $stmt->bind_param("is", $product_id, $gallery_image_path);
                if (!$stmt->execute()) {
                    echo json_encode(['success' => false, 'message' => 'Execute Error (Gallery): ' . $stmt->error]);
                    exit;
                }
                $stmt->close();
            }
        }
    }

    $conn->close();
    echo json_encode(['success' => true, 'message' => 'Product added successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
