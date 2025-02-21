<?php
session_start();
include 'db.php';

$response = array('status' => 'error', 'message' => 'An error occurred.');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate session store_id
    if (!isset($_SESSION['store_id'])) {
        $response['message'] = 'Store ID is missing';
        echo json_encode($response);
        exit;
    }
    $store_id = $_SESSION['store_id'];

    // Validate form inputs
    if (!isset($_POST['name'], $_POST['usd_price'], $_POST['khr_price'], $_POST['product_code'], $_POST['description'], $_POST['category'], $_FILES['image'])) {
        $response['message'] = 'Missing required fields';
        echo json_encode($response);
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
    $image_path = 'uploads/' . $image_name;

    if (!move_uploaded_file($image['tmp_name'], $image_path)) {
        $response['message'] = 'Failed to upload product image';
        echo json_encode($response);
        exit;
    }

    // Insert product into database
    $query = "INSERT INTO store_products (store_id, product_name, usd_price, khr_price, product_code, description, category_id, image) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        $response['message'] = 'SQL Error: ' . $conn->error;
        echo json_encode($response);
        exit;
    }

    $stmt->bind_param("isddssiss", $store_id, $name, $usd_price, $khr_price, $product_code, $description, $category_id, $image_path);

    if (!$stmt->execute()) {
        $response['message'] = 'Execute Error: ' . $stmt->error;
        echo json_encode($response);
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
                $gallery_image_path = 'uploads/' . $gallery_image_name;

                if (!move_uploaded_file($tmp_name, $gallery_image_path)) {
                    $response['message'] = 'Failed to upload gallery image';
                    echo json_encode($response);
                    exit;
                }

                // Insert gallery image into database
                $query = "INSERT INTO product_gallery (product_id, image_path) VALUES (?, ?)";
                $stmt = $conn->prepare($query);

                if (!$stmt) {
                    $response['message'] = 'SQL Error (Gallery): ' . $conn->error;
                    echo json_encode($response);
                    exit;
                }

                $stmt->bind_param("is", $product_id, $gallery_image_path);
                if (!$stmt->execute()) {
                    $response['message'] = 'Execute Error (Gallery): ' . $stmt->error;
                    echo json_encode($response);
                    exit;
                }
                $stmt->close();
            }
        }
    }

    $response['status'] = 'success';
    $response['message'] = 'Product added successfully';
} else {
    $response['message'] = 'Invalid request method';
}

$conn->close();
echo json_encode($response);
?>
