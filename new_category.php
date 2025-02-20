<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Category</title>
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
        function addCategory(event) {
            event.preventDefault();
            const formData = new FormData(document.getElementById(event.target.id));

            Swal.fire({
                title: 'Adding Category...',
                text: 'Please wait while the category is being added.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            axios.post('db_insert_category.php', formData)
                .then(response => {
                    Swal.close();
                    Swal.fire({
                        icon: 'success',
                        title: 'Category Added',
                        text: 'The category has been added successfully!'
                    }).then(() => {
                        window.location.href = 'category.php';
                    });
                    document.getElementById('addCategoryForm').reset();
                })
                .catch(error => {
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'There was an error adding the category. Please try again.'
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
                        <h2 class="text-2xl font-bold">Create New Category</h2>
                        <a href="category.php">
                            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none">
                                Back
                            </button>
                        </a>
                    </div>

                    <form id="addCategoryForm" onsubmit="addCategory(event)">
                        <div class="mb-6">
                            <label for="name" class="block text-gray-700 font-medium mb-2">Category Name</label>
                            <input type="text" id="name" name="name" required autocomplete="off"
                                class="w-full border border-gray-300 shadow-sm px-4 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                        </div>

                        <div class="flex justify-end space-x-4">
                            <button type="button" onclick="document.getElementById('addCategoryForm').reset()"
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
