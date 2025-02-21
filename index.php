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
    <script src="https://cdn.tailwindcss.com"></script>
    <title><?php echo htmlspecialchars($store['store_name']); ?> - Product List</title>
</head>

<body>
    <div class="font-[sans-serif] bg-gray-100">
        <div class="p-4 mx-auto lg:max-w-7xl md:max-w-4xl sm:max-w-xl max-sm:max-w-sm">
            <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-800 mb-6 sm:mb-10">Premium Sneakers</h2>
            <?php if (!empty($products)): ?>
            <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 max-xl:gap-4 gap-6">
                <?php foreach ($products as $product): ?>
                <div class="bg-white rounded p-4 cursor-pointer hover:-translate-y-1 transition-all relative">
                    <div class="mb-4 bg-gray-100 rounded p-2">
                        <img src="<?php echo !empty($product['image']) ? htmlspecialchars($product['image']) : 'images/default.jpg'; ?>"
                            alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                            class="aspect-[33/35] w-full object-contain" />
                    </div>

                    <div>
                        <div class="flex gap-2">
                            <h5 class="text-base font-bold text-gray-800">
                                <?php echo htmlspecialchars($product['product_name']); ?></h5>
                            <h6 class="text-base text-gray-800 font-bold ml-auto">
                                $<?php echo number_format((float) $product['usd_price'], 2); ?></h6>
                        </div>
                        <p class="text-gray-500 text-[13px] mt-2"></p>
                        <div class="flex items-center gap-2 mt-4">
                            <div class="bg-pink-100 hover:bg-pink-200 w-12 h-9 flex items-center justify-center rounded cursor-pointer"
                                title="Wishlist">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16px" class="fill-pink-600 inline-block"
                                    viewBox="0 0 64 64">
                                    <path
                                        d="M45.5 4A18.53 18.53 0 0 0 32 9.86 18.5 18.5 0 0 0 0 22.5C0 40.92 29.71 59 31 59.71a2 2 0 0 0 2.06 0C34.29 59 64 40.92 64 22.5A18.52 18.52 0 0 0 45.5 4ZM32 55.64C26.83 52.34 4 36.92 4 22.5a14.5 14.5 0 0 1 26.36-8.33 2 2 0 0 0 3.27 0A14.5 14.5 0 0 1 60 22.5c0 14.41-22.83 29.83-28 33.14Z"
                                        data-original="#000000"></path>
                                </svg>
                            </div>
                            <button type="button"
                                class="text-sm px-2 h-9 font-semibold w-full bg-blue-600 hover:bg-blue-700 text-white tracking-wide ml-auto outline-none border-none rounded">Add
                                to cart</button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <p>No products found for this store.</p>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>