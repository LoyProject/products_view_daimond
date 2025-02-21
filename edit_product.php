<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include 'db.php';

$product_id = $_GET['id'];
$query = "SELECT * FROM store_products WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Fetch gallery images
$query = "SELECT image_path FROM product_gallery WHERE product_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$gallery_result = $stmt->get_result();
$gallery_images = [];
while ($row = $gallery_result->fetch_assoc()) {
    $gallery_images[] = $row['image_path'];
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <style>
        body {
            font-family: 'Khmer OS Battambang', sans-serif;
        }
    </style>
    <script>
        function editProduct(event) {
            event.preventDefault();
            const formData = new FormData(document.getElementById('editProductForm'));

            Swal.fire({
                title: 'Updating Product...',
                text: 'Please wait while the product is being updated.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            axios.post('db_update_product.php', formData)
                .then(response => {
                    Swal.close();
                    Swal.fire({
                        icon: 'success',
                        title: 'Product Updated',
                        text: 'The product has been updated successfully!'
                    }).then(() => {
                        window.location.href = 'product.php';
                    });
                })
                .catch(error => {
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'There was an error updating the product. Please try again.'
                    });
                });
        }

        function fetchCategories() {
            axios.get(`fetch_categories.php?store_id=${<?php echo $_SESSION['store_id']; ?>}`)
                .then(response => {
                    const categories = response.data;
                    const categorySelect = document.getElementById('category');
                    categorySelect.innerHTML = '<option value="">Select Category</option>';
                    categories.forEach(category => {
                        const option = document.createElement('option');
                        option.value = category.id;
                        option.textContent = category.name;
                        categorySelect.appendChild(option);
                    });
                    document.getElementById('category').value = '<?php echo $product['category_id']; ?>';
                })
                .catch(error => {
                    console.error('Error fetching categories:', error);
                });
        }

        document.addEventListener('DOMContentLoaded', fetchCategories);
    </script>
</head>

<body>
<?php include 'header.php'; ?>
    <div class="relative font-sans pt-[70px] min-h-screen">
        <div class="flex items-start">
            <?php include 'sidebar.php'; ?>

            <div class="main-content w-full overflow-auto p-6">
                <div class="container mx-auto">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold">Edit Product</h2>
                        <a href="product.php">
                            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none">
                                Back
                            </button>
                        </a>
                    </div>

                    <form id="editProductForm" onsubmit="editProduct(event)" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                        <div class="mb-6">
                            <label for="name" class="block text-gray-700 font-medium mb-2">Product Name</label>
                            <input type="text" id="name" name="name" required autocomplete="off"
                                value="<?php echo $product['product_name']; ?>"
                                class="w-full border border-gray-300 shadow-sm px-4 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                        </div>
                        <div class="mb-6">
                            <label for="usd_price" class="block text-gray-700 font-medium mb-2">USD Price</label>
                            <input type="number" id="usd_price" name="usd_price" required
                                value="<?php echo $product['usd_price']; ?>"
                                class="w-full border border-gray-300 shadow-sm px-4 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                        </div>
                        <div class="mb-6">
                            <label for="khr_price" class="block text-gray-700 font-medium mb-2">KHR Price</label>
                            <input type="number" id="khr_price" name="khr_price" required
                                value="<?php echo $product['khr_price']; ?>"
                                class="w-full border border-gray-300 shadow-sm px-4 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                        </div>
                        <div class="mb-6">
                            <label for="product_code" class="block text-gray-700 font-medium mb-2">Product Code</label>
                            <input type="text" id="product_code" name="product_code" required autocomplete="off"
                                value="<?php echo $product['product_code']; ?>"
                                class="w-full border border-gray-300 shadow-sm px-4 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                        </div>
                        <div class="mb-6">
                            <label for="description" class="block text-gray-700 font-medium mb-2">Description</label>
                            <textarea id="description" name="description" required
                                class="w-full border border-gray-300 shadow-sm px-4 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"><?php echo $product['description']; ?></textarea>
                        </div>
                        <div class="mb-6">
                            <label for="category" class="block text-gray-700 font-medium mb-2">Category</label>
                            <select id="category" name="category" required
                                class="w-full border border-gray-300 shadow-sm px-4 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                                <option value="">Select Category</option>
                                <?php
                                // Fetch categories from the database
                                include 'db.php';
                                $store_id = $_SESSION['store_id']; // Assuming store_id is stored in session
                                $query = "SELECT id, name FROM store_category WHERE store_id = ?";
                                $stmt = $conn->prepare($query);
                                $stmt->bind_param("i", $store_id);
                                $stmt->execute();
                                $result = $stmt->get_result();
                                while ($row = $result->fetch_assoc()) {
                                    $selected = ($row['id'] == $product['category_id']) ? 'selected' : '';
                                    echo '<option value="' . $row['id'] . '" ' . $selected . '>' . $row['name'] . '</option>';
                                }
                                $stmt->close();
                                $conn->close();
                                ?>
                            </select>
                        </div>
                        <div class="mb-6">
                            <label for="image" class="block text-gray-700 font-medium mb-2">Product Image</label>
                            <input type="file" id="image" name="image"
                                class="w-full border border-gray-300 shadow-sm px-4 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                            <?php if (!empty($product['image'])): ?>
                                <img src="<?php echo $product['image']; ?>" alt="Product Image" class="mt-4 w-32 h-32 object-cover">
                            <?php endif; ?>
                        </div>
                        <div class="mb-6">
                            <label for="gallery" class="block text-gray-700 font-medium mb-2">Image Gallery</label>
                            <input type="file" id="gallery" name="gallery[]" multiple
                                class="w-full border border-gray-300 shadow-sm px-4 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                            <?php if (!empty($gallery_images)): ?>
                                <div class="mt-4 grid grid-cols-3 gap-4">
                                    <?php foreach ($gallery_images as $gallery_image): ?>
                                        <img src="<?php echo $gallery_image; ?>" alt="Gallery Image" class="w-32 h-32 object-cover">
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="flex justify-end space-x-4">
                            <button type="button" onclick="document.getElementById('editProductForm').reset()"
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none">
                                Clear
                            </button>
                            <button type="submit"
                                class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none">
                                Submit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
