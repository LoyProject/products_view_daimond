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
    SELECT p.id, p.product_name AS product_name, p.usd_price, p.image, p.description, c.name AS category_name
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
                <?php
                if (empty($productsByCategory)) {
                    echo 'No products found';
                } else {
                    echo htmlspecialchars(array_key_first($productsByCategory));
                }
                ?>
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
            <div class="bg-white rounded p-4 cursor-pointer hover:-translate-y-1 transition-all relative">
                <div class="mb-4 bg-gray-100 rounded p-2">
                    <img src="<?php echo !empty($product['image']) ? htmlspecialchars($product['image']) : 'images/default.jpg'; ?>"
                        alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                        class="aspect-[33/35] w-full object-contain" />
                </div>
                <div>
                    <div class="flex gap-2">
                        <h5 class="text-base font-bold text-gray-800">
                            <?php echo htmlspecialchars($product['product_name']); ?>
                        </h5>
                        <h6 class="text-base text-gray-800 font-bold ml-auto">
                            $
                            <?php echo number_format((float) $product['usd_price'], 2); ?>
                        </h6>
                    </div>
                    <p class="text-gray-500 text-[13px] mt-2"><?php echo htmlspecialchars($product['description']);?>
                    </p>
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
    </div>
    <?php endforeach; ?>
    <?php endif; ?>

    <footer class="mt-4 p-4 bg-gray-200">
        <p class="text-center text-gray-600">© 2025 Loy Team. All rights reserved.</p>
    </footer>
</body>

</html>
