<?php
    include 'db.php';

    $store_code = $_GET['store_code'] ?? '';
    $search_keyword = $_GET['search'] ?? '';

    try {
        $stmt = $conn->prepare("SELECT * FROM stores WHERE code = ?");
        $stmt->bind_param("s", $store_code);
        $stmt->execute();
        $result = $stmt->get_result();
        $store = $result->fetch_assoc();

        if (!$store) die("Store not found.");

        $stmt = $conn->prepare("SELECT * FROM store_category WHERE store_id = ?");
        $stmt->bind_param("i", $store['id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $categories = $result->fetch_all(MYSQLI_ASSOC);

        $stmt = $conn->prepare("SELECT * FROM store_products");
        $stmt->execute();
        $result = $stmt->get_result();
        $products = $result->fetch_all(MYSQLI_ASSOC);
    } catch (Exception $e) {
        die("Database Error: " . $e->getMessage());
    }

    usort($categories, function($a, $b) {
        return $a['id'] <=> $b['id'];
    });

    usort($products, function($a, $b) {
        return $a['category_id'] <=> $b['category_id'];
    });

    $conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diamond</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@latest/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .category-list-header {
            position: sticky;
            top: 0;
            z-index: 20;
            background-color: white;
        }

        .category-section {
            position: sticky;
            top: 145px;
            z-index: 10;
            background-color: #f3f4f6;
        }

        #product-list {
            position: relative;
            z-index: 1;
        }

        .category-btn {
            transition: background-color 0.3s, color 0.3s;
        }
    </style>
</head>
<body class="bg-gray-100 mx-auto w-full md:w-2/3">
    <div class="category-list-header" id="categoryHeader">
        <div class="container px-4 pt-4 pb-2">
            <div class="overflow-x-scroll whitespace-nowrap" id="categoryList">
                <?php foreach ($categories as $index => $category): ?>
                    <a class="category-btn mb-3 inline-block py-1 px-4 border-2 border-red-500 rounded-full mr-2 <?php echo $index === 0 ? 'bg-red-100 text-red-500' : 'bg-white text-black'; ?> cursor-pointer hover:bg-red-100 hover:text-red-500 font-medium" aria-label="View <?php echo htmlspecialchars($category['name']); ?> category" data-category="<?php echo htmlspecialchars($category['name']); ?>" data-category-id="<?php echo $category['id']; ?>">
                        <?php echo htmlspecialchars($category['name']); ?>
                    </a>
                <?php endforeach; ?>
            </div>
            <div class="mt-4">
                <input type="text" id="searchInput" placeholder="Search categories..." class="w-full py-2 px-4 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-red-500" aria-label="Search for categories">
            </div>
        </div>
    </div>

    <div id="product-list" class="mx-4 md:mx-0">
        <?php $totalProducts = 0; ?>
        <?php foreach ($categories as $category): ?>
            <div class="category-section" data-category="<?php echo htmlspecialchars($category['name']); ?>" data-category-id="<?php echo $category['id']; ?>">
                <div class="container px-4 py-2 bg-gray-100">
                    <div class="flex items-center justify-center">
                        <div class="flex-grow border-t-4 border-gray-300 ml-12"></div>
                        <span class="px-3 text-black font-bold text-lg"><?php echo htmlspecialchars($category['name']); ?></span>
                        <div class="flex-grow border-t-4 border-gray-300 mr-12"></div>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mt-0 md:mt-2 mx-2 product-section" data-category="<?php echo htmlspecialchars($category['name']); ?>" data-category-id="<?php echo $category['id']; ?>">
                <?php 
                $categoryProducts = 0;
                foreach ($products as $product): 
                    if ($product['category_id'] == $category['id']): 
                        $categoryProducts++;
                        $totalProducts++;
                ?>
                    <div class="bg-white mb-4 p-4 rounded-lg shadow-md category-<?php echo $product['category_id']; ?>">
                        <img src="<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>" class="w-full h-48 object-cover mb-4 rounded-lg" loading="lazy">
                        <h2 class="text-xl font-bold mb-2"><?php echo htmlspecialchars($product['product_name']); ?></h2>
                        <p class="text-gray-700 mb-2"><?php echo htmlspecialchars($product['description']); ?></p>
                        <p class="text-gray-900 font-bold mb-4">$<?php echo number_format($product['usd_price'], 2); ?></p>
                        <button class="mt-4 py-2 px-4 bg-red-500 text-white rounded-lg hover:bg-red-600">Buy Now</button>
                    </div>
                <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
        <div id="noMatchMessage" class="text-gray-500 text-center py-4 hidden">No products match your search.</div>
        <?php if ($totalProducts === 0): ?>
            <div class="text-gray-500 text-center py-4">No products available in this store.</div>
        <?php endif; ?>
    </div>

    <footer class="p-4 bg-gray-200 mt-6">
        <p class="text-center text-gray-600">© 2025 Loy Team. All rights reserved.</p>
    </footer>
</body>
<script>
    let scrollTimeout;
    document.addEventListener('scroll', function() {
        clearTimeout(scrollTimeout);
        scrollTimeout = setTimeout(() => {
            const categorySections = document.querySelectorAll('.category-section');
            const categoryButtons = document.querySelectorAll('.category-btn');
            let activeCategory = null;

            categorySections.forEach(section => {
                const rect = section.getBoundingClientRect();
                if (rect.top <= 150 && rect.bottom >= 150) {
                    activeCategory = section.getAttribute('data-category');
                }
            });

            categoryButtons.forEach(button => {
                if (button.getAttribute('data-category') === activeCategory) {
                    button.classList.add('bg-red-100', 'text-red-500');
                    button.classList.remove('bg-white', 'text-black');
                    button.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
                    lastClickedCategory = activeCategory;
                } else {
                    button.classList.remove('bg-red-100', 'text-red-500');
                    button.classList.add('bg-white', 'text-black');
                }
            });
        }, 100);
    }, { passive: true });
    
    document.addEventListener("DOMContentLoaded", function () {
        const categoryButtons = document.querySelectorAll(".category-btn");
        categoryButtons.forEach(button => {
            button.addEventListener("click", function (e) {
                e.preventDefault();
                const selectedCategory = this.dataset.category;
                if (lastClickedCategory === selectedCategory) return;

                lastClickedCategory = selectedCategory;
                categoryButtons.forEach(btn => btn.classList.remove("bg-red-100", "text-red-500"));
                this.classList.add("bg-red-100", "text-red-500");

                const categoryHeader = document.querySelector(`.category-section[data-category="${selectedCategory}"]`);
                if (categoryHeader) {
                    const headerHeight = document.querySelector(".category-list-header").offsetHeight;
                    const yOffset = categoryHeader.getBoundingClientRect().top + window.scrollY - headerHeight;
                    setTimeout(() => {
                        window.scrollTo({ top: Math.max(yOffset, 0), behavior: "smooth" });
                    }, 50);
                }
            });
        });
    });

    document.addEventListener("DOMContentLoaded", function () {
        const searchInput = document.getElementById("searchInput");
        const noMatchMessage = document.getElementById("noMatchMessage");
        const categorySections = document.querySelectorAll(".category-section");
        const productSections = document.querySelectorAll(".product-section");
        const categoryButtons = document.querySelectorAll(".category-btn");
        let lastClickedCategory = null;

        let searchTimeout;
        searchInput.addEventListener("input", function () {
            clearTimeout(searchTimeout);
            const searchTerm = this.value.trim().toLowerCase();
            searchTimeout = setTimeout(() => {
                const categorySections = document.querySelectorAll(".category-section");
                const productSections = document.querySelectorAll(".product-section");
                const categoryButtons = document.querySelectorAll(".category-btn");
                const noMatchMessage = document.getElementById("noMatchMessage");
                let hasMatch = false;

                if (searchTerm) {
                    const words = searchTerm.split(/\s+/);
                    const firstWord = words[0];

                    categoryButtons.forEach(btn => {
                        btn.classList.remove("bg-red-100", "text-red-500");
                        btn.classList.add("bg-white", "text-black");
                    });

                    let firstMatchingButton = null;
                    categoryButtons.forEach(button => {
                        const categoryName = button.getAttribute("data-category").toLowerCase();
                        if (categoryName.includes(firstWord)) {
                            button.style.display = "inline-block";
                            hasMatch = true;
                            if (!firstMatchingButton) {
                                firstMatchingButton = button;
                            }
                        } else {
                            button.style.display = "none";
                        }
                    });

                    if (firstMatchingButton) {
                        firstMatchingButton.classList.add("bg-red-100", "text-red-500");
                        firstMatchingButton.classList.remove("bg-white", "text-black");
                        lastClickedCategory = firstMatchingButton.getAttribute("data-category");
                    }

                    categorySections.forEach(section => {
                        const categoryName = section.getAttribute("data-category").toLowerCase();
                        const productSection = section.nextElementSibling;
                        if (categoryName.includes(firstWord)) {
                            section.style.display = "block";
                            productSection.style.display = "grid";
                            const products = productSection.querySelectorAll('.bg-white');
                            if (products.length > 0) {
                                hasMatch = true;
                            }
                        } else {
                            section.style.display = "none";
                            productSection.style.display = "none";
                        }
                    });

                    noMatchMessage.classList.toggle("hidden", hasMatch);
                } else {
                    categoryButtons.forEach(button => {
                        button.style.display = "inline-block";
                    });
                    categorySections.forEach(section => {
                        section.style.display = "block";
                    });
                    productSections.forEach(section => {
                        section.style.display = "grid";
                    });
                    noMatchMessage.classList.add("hidden");
                    
                    if (lastClickedCategory) {
                        categoryButtons.forEach(btn => {
                            if (btn.getAttribute("data-category") === lastClickedCategory) {
                                btn.classList.add("bg-red-100", "text-red-500");
                                btn.classList.remove("bg-white", "text-black");
                            } else {
                                btn.classList.remove("bg-red-100", "text-red-500");
                                btn.classList.add("bg-white", "text-black");
                            }
                        });
                    }
                }
            }, 300);
        });

        categoryButtons.forEach(button => {
            button.addEventListener("click", function (e) {
                e.preventDefault();
                const selectedCategory = this.getAttribute("data-category");
                if (lastClickedCategory === selectedCategory) return;

                lastClickedCategory = selectedCategory;
                categoryButtons.forEach(btn => {
                    btn.classList.remove("bg-red-100", "text-red-500");
                    btn.classList.add("bg-white", "text-black");
                });
                this.classList.add("bg-red-100", "text-red-500");

                const categoryHeader = document.querySelector(`.category-section[data-category="${selectedCategory}"]`);
                if (categoryHeader) {
                    const headerHeight = document.querySelector(".category-list-header").offsetHeight;
                    const yOffset = categoryHeader.getBoundingClientRect().top + window.scrollY - headerHeight;
                    setTimeout(() => {
                        window.scrollTo({ top: Math.max(yOffset, 0), behavior: "smooth" });
                    }, 50);
                }
            });
        });

        document.addEventListener('scroll', function() {
            const categorySections = document.querySelectorAll('.category-section');
            const categoryButtons = document.querySelectorAll('.category-btn');
            let activeCategory = null;

            categorySections.forEach(section => {
                if (section.style.display !== "none") {
                    const rect = section.getBoundingClientRect();
                    if (rect.top <= 150 && rect.bottom >= 150) {
                        activeCategory = section.getAttribute('data-category');
                    }
                }
            });

            categoryButtons.forEach(button => {
                if (button.getAttribute('data-category') === activeCategory) {
                    button.classList.add('bg-red-100', 'text-red-500');
                    button.classList.remove('bg-white', 'text-black');
                    button.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
                    lastClickedCategory = activeCategory;
                } else {
                    button.classList.remove('bg-red-100', 'text-red-500');
                    button.classList.add('bg-white', 'text-black');
                }
            });
        }, { passive: true });
    });
</script>
</html>
