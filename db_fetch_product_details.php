<?php
    session_start();
    require 'db.php';

    if (isset($_GET['id'])) {
        $productId = intval($_GET['id']);
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$productId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product) {
            echo json_encode(["success" => true, "product" => $product]);
        } else {
            echo json_encode(["success" => false, "message" => "Product not found."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Invalid request."]);
    }

    $conn->close();
?>
