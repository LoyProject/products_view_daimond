<?php

include 'header.php';

try {
    $store_id = $_SESSION['store_id']; // Get the store ID from session
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
    $page = isset($_GET['page']) ? max(intval($_GET['page']), 1) : 1;
    $offset = ($page - 1) * $limit;

    // Get total count of products for the store
    $total_sql = "SELECT COUNT(*) as total FROM store_products sp JOIN store_category c ON sp.category_id = c.id WHERE c.store_id = ?";
    $stmt_total = $conn->prepare($total_sql);
    $stmt_total->bind_param("i", $store_id);
    $stmt_total->execute();
    $total_result = $stmt_total->get_result();

    if (!$total_result) {
        throw new Exception("Failed to fetch total records.");
    }

    $total_row = $total_result->fetch_assoc();
    $total_records = $total_row['total'];
    $total_pages = ceil($total_records / $limit);

    // Fetch products for the store
    $sql = "SELECT sp.id, sp.product_name, sp.usd_price, sp.khr_price, sp.product_code, sp.description, sp.category_id, sp.image, c.name 
            FROM store_products sp 
            JOIN store_category c ON sp.category_id = c.id 
            WHERE c.store_id = ? 
            ORDER BY sp.id DESC 
            LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iii', $store_id, $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Battambang:wght@100;300;400;700;900&family=Noto+Sans+Khmer:wght@100..900&family=Siemreap&display=swap"
        rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <style>
    body {
        font-family: "Noto Sans Khmer", serif;
    }
    </style>
</head>

<body>

    <div class="relative font-sans pt-[70px] min-h-screen">
        <div class="flex">
            <?php include 'sidebar.php'; ?>

            <div class="main-content w-full overflow-auto p-6">
                <div class="container-xl mx-auto">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold">Product List</h2>
                        <a href="new_product.php">
                            <button class="bg-green-500 text-white font-bold py-2 px-4 rounded hover:bg-green-700">
                                Add New
                            </button>
                        </a>
                    </div>

                    <table class="w-full table-auto border-collapse">
                        <thead class="text-left">
                            <tr class="bg-gray-200">
                                <th class="p-4 border-b border-slate-300 bg-slate-50">
                                    <p class="block text-sm font-bold leading-none text-slate-500">ID</p>
                                </th>
                                <th class="p-4 border-b border-slate-300 bg-slate-50">
                                    <p class="block text-sm font-bold leading-none text-slate-500">Product Name</p>
                                </th>
                                <th class="p-4 border-b border-slate-300 bg-slate-50">
                                    <p class="block text-sm font-bold leading-none text-slate-500">USD Price</p>
                                </th>
                                <th class="p-4 border-b border-slate-300 bg-slate-50">
                                    <p class="block text-sm font-bold leading-none text-slate-500">KHR Price</p>
                                </th>
                                <th class="p-4 border-b border-slate-300 bg-slate-50">
                                    <p class="block text-sm font-bold leading-none text-slate-500">Product Code</p>
                                </th>
                                <th class="p-4 border-b border-slate-300 bg-slate-50">
                                    <p class="block text-sm font-bold leading-none text-slate-500">Description</p>
                                </th>
                                <th class="p-4 border-b border-slate-300 bg-slate-50">
                                    <p class="block text-sm font-bold leading-none text-slate-500">Category</p>
                                </th>
                                <th class="p-4 border-b border-slate-300 bg-slate-50">
                                    <p class="block text-sm font-bold leading-none text-slate-500">Image</p>
                                </th>
                                <th class="p-4 border-b border-slate-300 bg-slate-50">
                                    <p class="block text-sm font-bold leading-none text-slate-500">Action</p>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                            <tr class="hover:bg-gray-100">
                                <td class="px-4 py-2 text-xs border-b border-slate-200"><?= $row['id'] ?></td>
                                <td class="px-4 py-2 text-xs border-b border-slate-200">
                                    <?= htmlspecialchars($row['product_name']) ?>
                                </td>
                                <td class="px-4 py-2 text-xs border-b border-slate-200">
                                    <?= htmlspecialchars($row['usd_price']) ?>
                                </td>
                                <td class="px-4 py-2 text-xs border-b border-slate-200">
                                    <?= htmlspecialchars($row['khr_price']) ?>
                                </td>
                                <td class="px-4 py-2 text-xs border-b border-slate-200">
                                    <?= htmlspecialchars($row['product_code']) ?>
                                </td>
                                <td class="px-4 py-2 text-xs border-b border-slate-200">
                                    <?= htmlspecialchars($row['description']) ?>
                                </td>
                                <td class="px-4 py-2 text-xs border-b border-slate-200">
                                    <?= htmlspecialchars($row['name']) ?>
                                </td>
                                <td class="px-4 py-2 text-xs border-b border-slate-200">
                                    <img src="<?= htmlspecialchars($row['image']) ?>" alt="Product Image" class="w-16 h-16 object-cover">
                                </td>
                                <td class="px-4 py-2 border-b border-slate-200">
                                    <button class="mr-4">
                                        <a href="edit_product.php?id=<?= htmlspecialchars($row["id"]) ?>">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                class="w-5 fill-blue-500 hover:fill-blue-700"
                                                viewBox="0 0 348.882 348.882">
                                                <path
                                                    d="m333.988 11.758-.42-.383A43.363 43.363 0 0 0 304.258 0a43.579 43.579 0 0 0-32.104 14.153L116.803 184.231a14.993 14.993 0 0 0-3.154 5.37l-18.267 54.762c-2.112 6.331-1.052 13.333 2.835 18.729 3.918 5.438 10.23 8.685 16.886 8.685h.001c2.879 0 5.693-.592 8.362-1.76l52.89-23.138a14.985 14.985 0 0 0 5.063-3.626L336.771 73.176c16.166-17.697 14.919-45.247-2.783-61.418zM130.381 234.247l10.719-32.134.904-.99 20.316 18.556-.904.99-31.035 13.578zm184.24-181.304L182.553 197.53l-20.316-18.556L294.305 34.386c2.583-2.828 6.118-4.386 9.954-4.386 3.365 0 6.588 1.252 9.082 3.53l.419.383c5.484 5.009 5.87 13.546.861 19.03z"
                                                    data-original="#000000" />
                                                <path
                                                    d="M303.85 138.388c-8.284 0-15 6.716-15 15v127.347c0 21.034-17.113 38.147-38.147 38.147H68.904c-21.035 0-38.147-17.113-38.147-38.147V100.413c0-21.034 17.113-38.147 38.147-38.147h131.587c8.284 0 15-6.716 15-15s-6.716-15-15-15H68.904C31.327 32.266.757 62.837.757 100.413v180.321c0 37.576 30.571 68.147 68.147 68.147h181.798c37.576 0 68.147-30.571 68.147-68.147V153.388c.001-8.284-6.715-15-14.999-15z"
                                                    data-original="#000000" />
                                            </svg>
                                        </a>
                                    </button>
                                    <button class="mr-4">
                                        <a href="delete_product.php?id=<?= htmlspecialchars($row['id']) ?>"
                                            onclick="return confirm('Are you sure you want to delete this product?');">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                class="w-5 fill-red-500 hover:fill-red-700" viewBox="0 0 24 24">
                                                <path
                                                    d="M19 7a1 1 0 0 0-1 1v11.191A1.92 1.92 0 0 1 15.99 21H8.01A1.92 1.92 0 0 1 6 19.191V8a1 1 0 0 0-2 0v11.191A3.918 3.918 0 0 0 8.01 23h7.98A3.918 3.918 0 0 0 20 19.191V8a1 1 0 0 0-1-1Zm1-3h-4V2a1 1 0 0 0-1-1H9a1 1 0 0 0-1 1v2H4a1 1 0 0 0 0 2h16a1 1 0 0 0 0-2ZM10 4V3h4v1Z"
                                                    data-original="#000000" />
                                            </svg>
                                        </a>
                                    </button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center p-4">No products found</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>

                    <div class="flex justify-between items-center mt-4">
                        <p class="text-sm text-gray-500">Showing <?= $offset + 1 ?> to
                            <?= min($offset + $limit, $total_records) ?> of <?= $total_records ?> entries</p>
                        <div class="flex space-x-2">
                            <?php if ($page > 1): ?>
                            <a href="?page=<?= $page - 1 ?>&limit=<?= $limit ?>">
                                <button
                                    class="bg-gray-200 text-gray-600 text-xs font-bold py-2 px-4 rounded hover:bg-red-500 hover:text-white w-20">
                                    Previous
                                </button>
                            </a>
                            <?php endif; ?>
                            <?php if ($page < $total_pages): ?>
                            <a href="?page=<?= $page + 1 ?>&limit=<?= $limit ?>">
                                <button
                                    class="bg-gray-200 text-gray-600 text-xs font-bold py-2 px-4 rounded hover:bg-red-700 hover:text-white w-20">
                                    Next
                                </button>
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>