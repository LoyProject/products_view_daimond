<?php
include 'db.php';

$store_code = $_GET['store_code'] ?? ''; // Get store code from URL
$search_keyword = $_GET['search'] ?? ''; // Get search keyword from URL

// Fetch store details
$stmt = $conn->prepare("SELECT id, store_name FROM stores WHERE code = ?");
$stmt->bind_param("s", $store_code);
$stmt->execute();
$result = $stmt->get_result();
$store = $result->fetch_assoc();

if (!$store) {
    die("Store not found.");
}

// Fetch categories and products for this store
$search_condition = $search_keyword ? "AND p.product_name LIKE ?" : "";
$query = "
    SELECT p.id, p.product_name AS product_name, p.usd_price, p.image, c.name AS category_name
    FROM store_products p
    JOIN store_category c ON p.category_id = c.id
    WHERE c.store_id = ? $search_condition
";
$stmt = $conn->prepare($query);
if ($search_keyword) {
    $search_param = "%" . $search_keyword . "%";
    $stmt->bind_param("is", $store['id'], $search_param);
} else {
    $stmt->bind_param("i", $store['id']);
}
$stmt->execute();
$products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Group products by category
$productsByCategory = [];
foreach ($products as $product) {
    $productsByCategory[$product['category_name']][] = $product;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WEB PAGE</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@latest/dist/tailwind.min.css" rel="stylesheet">
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.category-btn').forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                document.querySelectorAll('.category-btn').forEach(btn => btn.classList.remove(
                    'text-red-500'));
                this.classList.add('text-red-500');
                const categoryName = this.getAttribute('data-category');
                const categorySection = document.getElementById(categoryName);
                if (categorySection) {
                    categorySection.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });
    });

    function changeCategory(event, categoryName) {
        event.preventDefault();
        document.getElementById('category-title').textContent = categoryName;
    }
    </script>
</head>

<body class="bg-gray-100 mx-auto w-full md:w-2/3">
    <!-- Sticky Container for Category List and Title -->
    <div class="sticky top-0 z-50">
        <!-- Category List --->
        <div class="container p-4 bg-white">
            <div class="overflow-x-auto whitespace-nowrap">
                <?php foreach ($productsByCategory as $categoryName => $categoryProducts): ?>
                <a href="#"
                    class="category-btn mb-3 inline-block py-1 px-4 border-2 border-red-500 rounded-full mr-2 bg-transparent text-black cursor-pointer hover:text-red-500 font-medium"
                    data-category="<?php echo htmlspecialchars($categoryName); ?>"
                    onclick="changeCategory(event, '<?php echo htmlspecialchars($categoryName); ?>')">
                    <?php echo htmlspecialchars($categoryName); ?>
                </a>
                <?php endforeach; ?>
            </div>
            <div class="mt-2">
                <form method="GET" action="">
                    <input type="hidden" name="store_code" value="<?php echo htmlspecialchars($store_code); ?>">
                    <input type="text" name="search" placeholder="Search..."
                        class="w-full py-2 px-4 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-red-500"
                        value="<?php echo htmlspecialchars($search_keyword); ?>">
                </form>
            </div>
        </div>

        <!-- Category Title --->
        <div class="flex items-center justify-center py-2 bg-gray-100">
            <div class="flex-grow border-t-4 border-gray-300"></div>
            <span id="category-title" class="px-3 text-black font-bold text-lg">
                <?php echo htmlspecialchars(array_key_first($productsByCategory)); ?>
            </span>
            <div class="flex-grow border-t-4 border-gray-300"></div>
        </div>
        <script>
        document.addEventListener('scroll', function() {
            let categoryTitle = document.getElementById('category-title');
            let sections = document.querySelectorAll('.category-section');
            let scrollPosition = window.scrollY + window.innerHeight / 2;

            sections.forEach(section => {
                let sectionTop = section.offsetTop;
                let sectionHeight = section.offsetHeight;
                if (scrollPosition >= sectionTop && scrollPosition < sectionTop + sectionHeight) {
                    categoryTitle.textContent = section.querySelector('h2').textContent;
                }
            });
        });
        </script>
    </div>

    <!-- Body --->
    <?php if (!empty($products)): ?>
    <?php foreach ($productsByCategory as $categoryName => $categoryProducts): ?>
    <div id="<?php echo htmlspecialchars($categoryName); ?>" class="category-section mb-8">
        <h2 class="text-2xl font-bold mb-4">
            <?php echo htmlspecialchars($categoryName); ?>
        </h2>
        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mx-4 md:mx-0">
            <?php foreach ($categoryProducts as $product): ?>
            <div class="bg-white p-4 rounded-lg shadow-md">
                <img src="<?php echo htmlspecialchars($product['image']); ?>"
                    alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                    class="w-full h-48 object-cover mb-4 rounded-lg">
                <h2 class="text-xl font-bold mb-2">
                    <?php echo htmlspecialchars($product['product_name']); ?>
                </h2>
                <p class="text-gray-700">Price: $
                    <?php echo htmlspecialchars($product['usd_price']); ?>
                </p>
                <button class="mt-4 py-2 px-4 bg-red-500 text-white rounded-lg hover:bg-red-600">Buy Now</button>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endforeach; ?>
    <?php else: ?>
    <p>No products found for this store.</p>
    <?php endif; ?>
    <footer class="mt-4 p-4 bg-gray-200">
        <p class="text-center text-gray-600">© 2025 Loy Team. All rights reserved.</p>
    </footer>
</body>

</html>