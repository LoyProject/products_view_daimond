<?php
require 'config/db.php'; // Include database connection

// Debugging: Check if `$pdo` is defined
if (!isset($pdo)) {
    die("Database connection not established.");
}

$store_code = $_GET['store_code'] ?? ''; // Get store code from URL

// Fetch store details
$stmt = $pdo->prepare("SELECT id, store_name FROM stores WHERE code = ?");
$stmt->execute([$store_code]);
$store = $stmt->fetch();

if (!$store) {
    die("Store not found.");
}

// Fetch categories and products for this store
$stmt = $pdo->prepare("
    SELECT p.id, p.product_name AS product_name, p.usd_price, p.image, c.name AS category_name
    FROM store_products p
    JOIN store_category c ON p.category_id = c.id
    WHERE c.store_id = ?
");
$stmt->execute([$store['id']]);
$products = $stmt->fetchAll();

if (!$store) {
    die("Store not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($store['store_name']); ?> - Product List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f8f8;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            padding: 20px 0;
            font-size: 24px;
            color: #333;
        }

        .product-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            padding: 20px;
        }

        .product-card {
            width: 200px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 10px;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .product-card img {
            width: 100%;
            height: auto;
            border-radius: 8px;
            margin-bottom: 10px;
        }

        .product-card h2 {
            font-size: 16px;
            margin: 10px 0;
            color: #333;
        }

        .product-card p {
            font-size: 14px;
            color: #555;
            margin: 5px 0;
        }
    </style>
</head>
<body>

    <h1><?php echo htmlspecialchars($store['store_name']); ?> - Product List</h1>

    <?php if (!empty($products)): ?>
        <div class="product-container">
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <img src="<?php echo !empty($product['image']) ? htmlspecialchars($product['image']) : 'images/default.jpg'; ?>" 
                         alt="<?php echo htmlspecialchars($product['product_name']); ?>" 
                         onerror="this.onerror=null; this.src='images/default.jpg';">
                    <h2><?php echo htmlspecialchars($product['product_name']); ?></h2>
                    <p>Category: <?php echo !empty($product['category_name']) ? htmlspecialchars($product['category_name']) : 'Uncategorized'; ?></p>
                    <p>Price: $<?php echo number_format((float) $product['usd_price'], 2); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No products found for this store.</p>
    <?php endif; ?>

</body>

</html>


