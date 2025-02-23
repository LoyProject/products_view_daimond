<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WEB PAGE</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@latest/dist/tailwind.min.css" rel="stylesheet">
    <script>
        document.querySelectorAll('.category-btn').forEach(button => {
            button.addEventListener('click', function () {
                document.querySelectorAll('.category-btn').forEach(btn => btn.classList.remove('text-red-500'));
                this.classList.add('text-red-500');
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
                <?php for ($i = 1; $i <= 10; $i++): ?>
                    <a href="#" class="category-btn mb-3 inline-block py-1 px-4 border-2 border-red-500 rounded-full mr-2 bg-transparent text-black cursor-pointer hover:text-red-500 font-medium" onclick="changeCategory(event, 'Category <?php echo $i; ?>')">
                        Category <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
            </div>
            <div class="mt-2">
                <input type="text" placeholder="Search..." class="w-full py-2 px-4 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-red-500">
            </div>
        </div>

        <!-- Category Title --->
        <div class="flex items-center justify-center py-2 bg-gray-100">
            <div class="flex-grow border-t-4 border-gray-300"></div>
            <span id="category-title" class="px-3 text-black font-bold text-lg">Category 1</span>
            <div class="flex-grow border-t-4 border-gray-300"></div>
        </div>
    </div>

    <!-- Body --->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mx-4 md:mx-0">
        <?php for ($i = 1; $i <= 100; $i++): ?>
            <div class="bg-white p-4 rounded-lg shadow-md">
                <h2 class="text-xl font-bold mb-2">Product <?php echo $i; ?></h2>
                <p class="text-gray-700">Description for product <?php echo $i; ?>.</p>
                <button class="mt-4 py-2 px-4 bg-red-500 text-white rounded-lg hover:bg-red-600">Buy Now</button>
            </div>
        <?php endfor; ?>
    </div>
    <footer class="mt-4 p-4 bg-gray-200">
        <p class="text-center text-gray-600">© 2025 Loy Team. All rights reserved.</p>
    </footer>
</body>
</html>
