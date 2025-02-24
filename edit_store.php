<?php
// Include database connection file
include_once 'db.php';

// Check if form is submitted
if (isset($_POST['update'])) {
    $store_id = $_POST['id'];
    $code = $_POST['code'];
    $store_name = $_POST['store_name'];
    $email = $_POST['email'];
    $facebook = $_POST['facebook'];
    $telegram = $_POST['telegram'];
    $map = $_POST['map'];
    $phone = $_POST['phone'];
    $logo = $_POST['logo'];

    // Update store details
    $query = "UPDATE stores SET code = ?, store_name = ?, email = ?, facebook = ?, telegram = ?, map = ?, phone = ?, logo = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssssssi", $code, $store_name, $email, $facebook, $telegram, $map, $phone, $logo, $store_id);

    if ($stmt->execute()) {
        echo "Store details updated successfully.";
    } else {
        echo "Error updating store details: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}

// Fetch store details
if (isset($_GET['id'])) {
    $store_id = $_GET['id'];
    $query = "SELECT * FROM stores WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $store_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $store = $result->fetch_assoc();

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Store</title>
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
            <nav id="sidebar" class="lg:min-w-[250px] w-max max-lg:min-w-8">
                <div id="sidebar-collapse-menu" style="height: calc(100vh - 72px)"
                    class="bg-white shadow-lg h-screen fixed py-6 px-4 top-[70px] left-0 overflow-auto z-[99] lg:min-w-[250px] lg:w-max max-lg:w-0 max-lg:invisible transition-all duration-500">
                    <ul class="space-y-2 mb-2">
                        <li>
                            <a href="admin_dashboard.php"
                                class="text-gray-800 text-sm flex items-center hover:bg-gray-100 rounded-md px-4 py-2 transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="w-[18px] h-[18px] mr-3"
                                    viewBox="0 0 24 24">
                                    <path d="M3 3h18v2H3V3zm0 6h18v2H3V9zm0 6h18v2H3v-2zm0 6h18v2H3v-2z" data-original="#000000" />
                                </svg>
                                <span>Stores</span>
                            </a>
                        </li>
                    </ul>
                    
                
                    <div class="absolute bottom-4 left-4 text-sm text-gray-500">
                        <p>&copy; 2025 Power by Loy Team.</p>
                    </div>
                </div>
            </nav>

            <div class="main-content w-full overflow-auto p-6">
                <div class="container-xl mx-auto">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold">Edit Store</h2>
                    </div>

                    <form method="post" action="edit_store.php">
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($store['id']); ?>">
                        <div class="mb-4">
                            <label for="code" class="block text-gray-700 font-medium mb-2">Code:</label>
                            <input type="text" name="code" id="code" value="<?php echo htmlspecialchars($store['code']); ?>" required
                                class="w-full border border-gray-300 shadow-sm px-4 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                        </div>
                        <div class="mb-4">
                            <label for="store_name" class="block text-gray-700 font-medium mb-2">Store Name:</label>
                            <input type="text" name="store_name" id="store_name" value="<?php echo htmlspecialchars($store['store_name']); ?>" required
                                class="w-full border border-gray-300 shadow-sm px-4 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                        </div>
                        <div class="mb-4">
                            <label for="email" class="block text-gray-700 font-medium mb-2">Email:</label>
                            <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($store['email']); ?>" required
                                class="w-full border border-gray-300 shadow-sm px-4 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                        </div>
                        <div class="mb-4">
                            <label for="facebook" class="block text-gray-700 font-medium mb-2">Facebook:</label>
                            <input type="text" name="facebook" id="facebook" value="<?php echo htmlspecialchars($store['facebook']); ?>" required
                                class="w-full border border-gray-300 shadow-sm px-4 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                        </div>
                        <div class="mb-4">
                            <label for="telegram" class="block text-gray-700 font-medium mb-2">Telegram:</label>
                            <input type="text" name="telegram" id="telegram" value="<?php echo htmlspecialchars($store['telegram']); ?>" required
                                class="w-full border border-gray-300 shadow-sm px-4 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                        </div>
                        <div class="mb-4">
                            <label for="map" class="block text-gray-700 font-medium mb-2">Map:</label>
                            <input type="text" name="map" id="map" value="<?php echo htmlspecialchars($store['map']); ?>" required
                                class="w-full border border-gray-300 shadow-sm px-4 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                        </div>
                        <div class="mb-4">
                            <label for="phone" class="block text-gray-700 font-medium mb-2">Phone:</label>
                            <input type="text" name="phone" id="phone" value="<?php echo htmlspecialchars($store['phone']); ?>" required
                                class="w-full border border-gray-300 shadow-sm px-4 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                        </div>
                        <div class="mb-4">
                            <label for="logo" class="block text-gray-700 font-medium mb-2">Logo:</label>
                            <input type="text" name="logo" id="logo" value="<?php echo htmlspecialchars($store['logo']); ?>" required
                                class="w-full border border-gray-300 shadow-sm px-4 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" name="update"
                                class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none">
                                Update Store
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>