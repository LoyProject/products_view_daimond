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

        // Fetch product images from product_gallery
        $product_images = [];
        $stmt = $conn->prepare("SELECT product_id, GROUP_CONCAT(image_path) as images FROM product_gallery GROUP BY product_id");
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $product_images[$row['product_id']] = $row['images'];
        }
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
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .category-list-header {
            position: sticky;
            top: 0;
            z-index: 20;
            background-color: white;
        }

        .category-section {
            position: sticky;
            top: 200px;
            z-index: 10;
            width: 100%;
            padding: 0;
        }

        .category-section .container {
            background-color: 'bg-gray-200';
            width: 100%;
        }

        #product-list {
            position: relative;
            z-index: 1;
        }

        .category-btn {
            transition: background-color 0.3s, color 0.3s;
        }

        /* Add this to your existing CSS */
        #productModal .modal-content {
            padding: 1rem;
            border-radius: 0.5rem;
            background-color: white;
            max-width: 500px;
            width: 100%;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        #productModal .modal-content img {
            width: 100%;
            height: auto;
            border-radius: 0.5rem;
        }

        #productModal .modal-content h2 {
            font-size: 1.25rem;
            font-weight: bold;
            margin-top: 1rem;
        }

        #productModal .modal-content p {
            margin-top: 0.5rem;
            color: #4a4a4a;
        }

        #productModal .modal-content .price {
            font-size: 1.125rem;
            font-weight: bold;
            margin-top: 0.5rem;
        }

        #productModal .modal-content .contact-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1rem;
        }

        #productModal .modal-content .contact-info .social-icons {
            display: flex;
            gap: 0.5rem;
        }

        #productModal .modal-content .contact-info .social-icons a {
            color: #1d4ed8;
            font-size: 1.25rem;
        }

        #productModal .modal-content .contact-info .phone {
            color: #ef4444;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        #productModal .modal-content .close-btn {
            position: absolute;
            top: 0.75rem;
            right: 0.75rem;
            font-size: 1.5rem;
            color: #6b7280;
            cursor: pointer;
        }

        #productModal .modal-content .close-btn:hover {
            color: #374151;
        }
    </style>
</head>
<body class="bg-gray-100 mx-auto w-full md:w-2/3">
    <button id="scrollToTopBtn" 
        class="hidden fixed bottom-6 right-6 border-2 border-red-500 bg-red-100 text-red-500 px-5 py-3 rounded-full shadow-lg hover:bg-red-100 hover:text-red-500 transition duration-300 font-medium">
        ▲ Top
    </button>

    <div class="category-list-header" id="categoryHeader">
        <div class="flex max-w-md px-4 pt-2 items-center">
            <img src='<?php echo htmlspecialchars($store['logo']); ?>' alt="Logo" class="h-12 object-cover">
            <h1 class="text-xl font-bold ml-4"><?php echo htmlspecialchars($store['store_name']); ?></h1>
        </div>
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
                    <div class="flex-grow border-t-4 border-gray-300 ml-6 sm:ml-12"></div>
                    <span class="px-3 text-black font-bold text-lg"><?php echo htmlspecialchars($category['name']); ?></span>
                    <div class="flex-grow border-t-4 border-gray-300 mr-6 sm:mr-12"></div>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4 mt-0 md:mt-2 mx-2 product-section" data-category="<?php echo htmlspecialchars($category['name']); ?>" data-category-id="<?php echo $category['id']; ?>">
            <?php $categoryProducts = 0; ?>
            <?php foreach ($products as $product): ?>
                <?php if ($product['category_id'] == $category['id']): ?>
                <?php $categoryProducts++; ?>
                <?php $totalProducts++; ?>
                <a href="#" class="bg-white shadow rounded-lg product-card category-<?php echo $product['category_id']; ?>" 
                   data-name="<?php echo htmlspecialchars($product['product_name']); ?>" 
                   data-id="<?php echo htmlspecialchars($product['product_code']); ?>" 
                   data-image="<?php echo htmlspecialchars($product_images[$product['id']] ?? $product['image']); ?>"
                   data-description="<?php echo htmlspecialchars($product['description'] ?? 'No description available.'); ?>"
                   data-usd-price="<?php echo htmlspecialchars($product['usd_price'] ?? 'N/A'); ?>"
                   data-khr-price="<?php echo htmlspecialchars($product['khr_price'] ?? 'N/A'); ?>">
                    <img src="<?php echo $product['image']; ?>" 
                         alt="<?php echo htmlspecialchars($product['product_name']); ?>" 
                         class="w-full h-[150px] sm:h-[450px] mb-2 rounded-tl-lg rounded-tr-lg border" 
                         loading="lazy" 
                         onerror="this.onerror=null; this.src='uploads/default-placeholder.png';">
                    <p class="text-gray-500 text-xs font-bold mb-1 px-4">ID: <?php echo htmlspecialchars($product['product_code']); ?></p>
                    <p class="text-sm font-bold mb-6 px-4"><?php echo htmlspecialchars($product['product_name']); ?></p>
                    <!-- < ?php if (!empty($product['khr_price']) && $product['khr_price'] > 0): ?>
                        < ?php echo htmlspecialchars($product['khr_price']); ?>
                    < ?php endif; ?> -->
                </a>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
    <div id="noMatchMessage" class="text-gray-500 text-center py-4 hidden">No products match your search.</div>
    <?php if ($totalProducts === 0): ?>
        <div class="text-gray-500 text-center py-4">No products available in this store.</div>
    <?php endif; ?>
</div>

<!-- ✅ MODAL HTML -->
<div id="productModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full shadow-lg relative">
        <button id="closeModal" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 text-xl">&times;</button>
        <div id="productGallery" class="relative w-full h-48 overflow-hidden rounded">
            <img id="modalImage" src="" class="w-full h-full object-cover">
            <button id="prevImage" class="absolute left-0 top-1/2 transform -translate-y-1/2 bg-gray-800 text-white px-2 py-1 rounded">‹</button>
            <button id="nextImage" class="absolute right-0 top-1/2 transform -translate-y-1/2 bg-gray-800 text-white px-2 py-1 rounded">›</button>
        </div>
        <h2 id="modalTitle" class="text-xl font-bold mt-4"></h2>
        <p id="modalProductID" class="text-gray-600 text-sm mt-1"></p>
        <p id="modalDescription" class="text-gray-700 mt-2"></p>
        <p id="modalPriceUsd" class="text-lg font-bold mt-2 text-blue-500"></p>
        <p id="modalPriceKhr" class="text-lg font-bold mt-2 text-green-500"></p>
        <div class="flex justify-between items-center mt-4">
            <div class="flex space-x-2">
                <a id="modalFacebook" href="#" class="text-blue-500"><i class="fab fa-facebook"></i></a>
                <a id="modalTelegram" href="#" class="text-blue-500"><i class="fab fa-telegram"></i></a>
                <a id="modalMap" href="#" class="text-blue-500"><i class="fas fa-map-marker-alt"></i></a>
            </div>
            <div id="modalPhone" class="text-red-500">
                <i class="fas fa-phone-alt"></i> 
            </div>
        </div>
    </div>
</div>

<!-- ✅ JAVASCRIPT FOR MODAL -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const modal = document.getElementById("productModal");
        const closeModal = document.getElementById("closeModal");
        const modalImage = document.getElementById("modalImage");
        const modalTitle = document.getElementById("modalTitle");
        const modalProductID = document.getElementById("modalProductID");
        const modalDescription = document.getElementById("modalDescription");
        const modalPriceUsd = document.getElementById("modalPriceUsd");
        const modalPriceKhr = document.getElementById("modalPriceKhr");
        const modalFacebook = document.getElementById("modalFacebook");
        const modalTelegram = document.getElementById("modalTelegram");
        const modalMap = document.getElementById("modalMap");
        const modalPhone = document.getElementById("modalPhone");
        const prevImage = document.getElementById("prevImage");
        const nextImage = document.getElementById("nextImage");

        const storePhone = "<?php echo htmlspecialchars($store['phone']); ?>";
        const storeFacebook = "<?php echo htmlspecialchars($store['facebook']); ?>";
        const storeTelegram = "<?php echo htmlspecialchars($store['telegram']); ?>";
        const storeMap = "<?php echo htmlspecialchars($store['map']); ?>";

        let currentImageIndex = 0;
        let imagePaths = [];

        document.querySelectorAll(".product-card").forEach(card => {
            card.addEventListener("click", function (event) {
                event.preventDefault();

                const name = this.getAttribute("data-name");
                const productId = this.getAttribute("data-id");
                const image = this.getAttribute("data-image");
                const description = this.getAttribute("data-description");
                const usdPrice = this.getAttribute("data-usd-price");
                const khrPrice = this.getAttribute("data-khr-price");

                modalTitle.textContent = name;
                modalProductID.textContent = "Product ID: " + productId;
                modalDescription.textContent = description || "No description available.";
                modalPriceUsd.textContent = "USD Price: $" + (usdPrice || "N/A");
                modalPriceKhr.textContent = "KHR Price: ៛" + (khrPrice || "N/A");

                modalFacebook.href = storeFacebook;
                modalTelegram.href = storeTelegram;
                modalMap.href = storeMap;
                modalPhone.innerHTML = `<i class="fas fa-phone-alt"></i> ${storePhone}`;

                imagePaths = image.split(','); // Assuming images are comma-separated
                currentImageIndex = 0;
                modalImage.src = imagePaths[currentImageIndex];

                // Ensure the image is loaded before showing the modal
                modalImage.onload = function() {
                    modal.classList.remove("hidden");
                };

                // Fallback in case the image fails to load
                modalImage.onerror = function() {
                    
                    modalImage.src = imagePaths[currentImageIndex];
                    modal.classList.remove("hidden");
                };
            });
        });

        closeModal.addEventListener("click", function () {
            modal.classList.add("hidden");
        });

        modal.addEventListener("click", function (event) {
            if (event.target === modal) {
                modal.classList.add("hidden");
            }
        });

        prevImage.addEventListener("click", function () {
            if (currentImageIndex > 0) {
                currentImageIndex--;
                modalImage.src = imagePaths[currentImageIndex];
            }
        });

        nextImage.addEventListener("click", function () {
            if (currentImageIndex < imagePaths.length - 1) {
                currentImageIndex++;
                modalImage.src = imagePaths[currentImageIndex];
            }
        });

        document.querySelectorAll("img").forEach(img => {
            img.onerror = function () {
                this.src = 'uploads/default-placeholder.png';
                this.onerror = null;
            };
        });
    });
</script>

    <footer class="p-4 bg-gray-200 mt-6">
        <p class="text-center text-gray-600">© 2025 Loy Team. All rights reserved.</p>
    </footer>

</body>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const modal = document.getElementById("productModal");
        const closeModal = document.getElementById("closeModal");
        const modalImage = document.getElementById("modalImage");
        const modalTitle = document.getElementById("modalTitle");
        const modalDescription = document.getElementById("modalDescription");
        const modalPrice = document.getElementById("modalPrice");

        document.querySelectorAll(".product-card").forEach(card => {
            card.addEventListener("click", function (event) {
                event.preventDefault();

                const name = this.getAttribute("data-name");
                const image = this.getAttribute("data-image");
                const description = this.getAttribute("data-description");
                const price = this.getAttribute("data-price");

                modalTitle.textContent = name;
                modalImage.src = image;
                modalDescription.textContent = description || "No description available.";
                modalPrice.textContent = "Price: $" + (price || "N/A");

                modal.classList.remove("hidden");
            });
        });

        closeModal.addEventListener("click", function () {
            modal.classList.add("hidden");
        });

        modal.addEventListener("click", function (event) {
            if (event.target === modal) {
                modal.classList.add("hidden");
            }
        });
    });
</script>


<script>
    const scrollToTopBtn = document.getElementById("scrollToTopBtn");
    window.addEventListener("scroll", () => {
        if (window.scrollY > 200) {
            scrollToTopBtn.classList.remove("hidden");
        } else {
            scrollToTopBtn.classList.add("hidden");
        }
    });
    scrollToTopBtn.addEventListener("click", () => {
        window.scrollTo({ top: 0, behavior: "smooth" });
    });

    document.addEventListener("DOMContentLoaded", function () {
        const searchInput = document.getElementById("searchInput");
        const noMatchMessage = document.getElementById("noMatchMessage");
        const categorySections = document.querySelectorAll(".category-section");
        const productSections = document.querySelectorAll(".product-section");
        const categoryButtons = document.querySelectorAll(".category-btn");
        let lastClickedCategory = null;
        let isScrolling = false;
        let scrollTimeout;

        function highlightButton(activeCategory) {
            categoryButtons.forEach(button => {
                const buttonCategory = button.getAttribute('data-category');
                if (buttonCategory === activeCategory && button.style.display !== "none") {
                    button.classList.add('bg-red-100', 'text-red-500');
                    button.classList.remove('bg-white', 'text-black');
                    button.scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'nearest', 
                        inline: 'center' 
                    });
                } else {
                    button.classList.remove('bg-red-100', 'text-red-500');
                    button.classList.add('bg-white', 'text-black');
                }
            });
        }

        function handleScroll() {
            if (scrollTimeout) return;
            scrollTimeout = requestAnimationFrame(() => {
                let activeCategory = null;
                categorySections.forEach(section => {
                    if (section.style.display !== "none") {
                        const rect = section.getBoundingClientRect();
                        if (rect.top <= 200 && rect.bottom >= 100) {
                            activeCategory = section.getAttribute('data-category');
                        }
                    }
                });
                if (activeCategory && activeCategory !== lastClickedCategory) {
                    lastClickedCategory = activeCategory;
                    highlightButton(activeCategory);
                } else if (!activeCategory && lastClickedCategory) {
                    highlightButton(lastClickedCategory);
                }
                scrollTimeout = null;
            });
        }

        function handleCategoryClick(e) {
            e.preventDefault();
            const selectedCategory = this.getAttribute("data-category");
            if (lastClickedCategory === selectedCategory) return;

            isScrolling = true;
            lastClickedCategory = selectedCategory;
            highlightButton(selectedCategory);

            const categoryHeader = document.querySelector(`.category-section[data-category="${selectedCategory}"]`);
            if (categoryHeader) {
                const headerHeight = document.querySelector(".category-list-header").offsetHeight;
                const yOffset = categoryHeader.getBoundingClientRect().top + window.scrollY - headerHeight + 5;
                window.scrollTo({ 
                    top: Math.max(yOffset, 0), 
                    behavior: "smooth" 
                });
                setTimeout(() => { isScrolling = false; }, 500);
            }
        }

        let searchTimeout;
        searchInput.addEventListener("input", function () {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const searchTerm = this.value.trim().toLowerCase();
                let hasMatch = false;

                if (searchTerm) {
                    const words = searchTerm.split(/\s+/);
                    const firstWord = words[0];

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

                    categorySections.forEach(section => {
                        const categoryName = section.getAttribute("data-category").toLowerCase();
                        const productSection = section.nextElementSibling;
                        if (categoryName.includes(firstWord)) {
                            section.style.display = "block";
                            productSection.style.display = "grid";
                            if (productSection.querySelectorAll('.bg-white').length > 0) {
                                hasMatch = true;
                            }
                        } else {
                            section.style.display = "none";
                            productSection.style.display = "none";
                        }
                    });

                    if (firstMatchingButton) {
                        lastClickedCategory = firstMatchingButton.getAttribute("data-category");
                        highlightButton(lastClickedCategory);
                    }

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
                    highlightButton(lastClickedCategory || categoryButtons[0].getAttribute('data-category'));
                }
            }, 300);
        });

        categoryButtons.forEach(button => {
            button.addEventListener("click", handleCategoryClick);
        });

        document.addEventListener('scroll', handleScroll, { passive: true });

        document.querySelectorAll("img").forEach(img => {
            img.onerror = function () {
                this.src = 'uploads/default-placeholder.png';
                this.onerror = null;
            };
        });

        if (categoryButtons.length > 0) {
            const initialCategory = categoryButtons[0].getAttribute('data-category');
            lastClickedCategory = initialCategory;
            highlightButton(initialCategory);
        }

        if (!firstMatchingButton && categoryButtons.length > 0) {
            firstMatchingButton = categoryButtons[0];
        }
    });
</script>
</html>
