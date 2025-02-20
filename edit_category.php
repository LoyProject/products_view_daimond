<?php
    include 'db.php';

    $categoryId = isset($_GET['id']) ? intval($_GET['id']) : 0;

    if ($categoryId > 0) {
        $sql = "SELECT name FROM store_category WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $categoryId);
        $stmt->execute();
        $stmt->bind_result($name);
        $stmt->fetch();
        $stmt->close();
    } else {
        die("Invalid category ID.");
    }

    $conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Category</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('name').value = "<?php echo $name; ?>";        
        });

        function editCategory(event) {
            event.preventDefault();

            const formData = new FormData(document.getElementById('editCategoryForm'));
            formData.append('id', "<?php echo $categoryId; ?>");

            Swal.fire({
                title: 'Updating Category...',
                text: 'Please wait while the category is being updated.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            axios.post('db_update_category.php', formData)
                .then(response => {
                    Swal.close();
                    Swal.fire({
                        icon: 'success',
                        title: 'Category Updated',
                        text: response.data.message,
                    }).then(() => {
                        document.getElementById('editCategoryForm').reset();
                        window.location.href = 'category.php';
                    });
                })
                .catch(error => {
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.response?.data?.message || 'An error occurred. Please try again.',
                    });
                });
        }
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
                        <h2 class="text-2xl font-bold">Edit Category</h2>
                        <a href="category.php">
                            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none">
                                Back
                            </button>
                        </a>
                    </div>

                    <form id="editCategoryForm" onsubmit="editCategory(event)">
                        <div class="mb-6">
                            <label for="name" class="block text-gray-700 font-medium mb-2">Category Name</label>
                            <input type="text" id="name" name="name" required autocomplete="off"
                                class="w-full border border-gray-300 shadow-sm px-4 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                        </div>

                       

                        <div class="flex justify-end space-x-4">
                            <button type="button" onclick="document.getElementById('editCategoryForm').reset()"
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none">
                                Clear
                            </button>
                            <button type="submit"
                                class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none">
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
