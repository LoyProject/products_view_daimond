<?php
include 'header.php';
include 'db.php';



$store_id = isset($_SESSION['store_id']) ? intval($_SESSION['store_id']) : 0;
$store_name = '';
$store_email = '';
$facebook = '';
$telegram = '';
$map = '';
$phone = '';
$logo = '';
$password = '';

if ($store_id > 0) {
    // Fetch existing store data
    $sql = "SELECT store_name as name, email, facebook, telegram, map, phone, logo, password FROM stores WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $store_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $store_name = $row['name'];
        $store_email = $row['email'];
        $facebook = $row['facebook'];
        $telegram = $row['telegram'];
        $map = $row['map'];
        $phone = $row['phone'];
        $logo = $row['logo'];
        $password = $row['password'];
    }
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $store_id = $_POST['store_id'];
    $store_name = $_POST['store_name'];
    $store_email = $_POST['store_email'];
    $facebook = $_POST['facebook'];
    $telegram = $_POST['telegram'];
    $map = $_POST['map'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];

    // Handle logo upload
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] == UPLOAD_ERR_OK) {
        $logo_tmp_name = $_FILES['logo']['tmp_name'];
        $logo_extension = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
        $logo_name = uniqid('logo_', true) . '.' . $logo_extension;
        $logo_dir = 'uploads/';
        $logo_path = $logo_dir . $logo_name;

        if (move_uploaded_file($logo_tmp_name, $logo_path)) {
            $logo = $logo_path;
        } else {
            $message = "Error uploading logo.";
        }
    }

    if (!empty($password)) {
        $sql = "UPDATE stores SET store_name=?, email=?, facebook=?, telegram=?, map=?, phone=?, logo=?, password=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssi", $store_name, $store_email, $facebook, $telegram, $map, $phone, $logo, $password, $store_id);
    } else {
        $sql = "UPDATE stores SET store_name=?, email=?, facebook=?, telegram=?, map=?, phone=?, logo=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssi", $store_name, $store_email, $facebook, $telegram, $map, $phone, $logo, $store_id);
    }

    if ($stmt->execute()) {
        $message = "Record updated successfully";
    } else {
        $message = "Error updating record: " . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Store</title>
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
                        <h2 class="text-2xl font-bold">Update Store</h2>
                    </div>

                    <?php if (isset($message)): ?>
                    <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                        <?= htmlspecialchars($message) ?>
                    </div>
                    <?php endif; ?>

                    <form method="post" action="" enctype="multipart/form-data">
                        <div class="mb-4" style="display: none;">
                            <label for="store_id" class="block text-sm font-bold mb-2">Store ID:</label>
                            <input type="text" id="store_id" name="store_id" value="<?= htmlspecialchars($store_id) ?>" readonly
                                class="w-full p-2 border border-gray-300 rounded">
                        </div>
                        <div class="mb-4">
                            <label for="store_name" class="block text-sm font-bold mb-2">Store Name:</label>
                            <input type="text" id="store_name" name="store_name" value="<?= htmlspecialchars($store_name) ?>" required
                                class="w-full p-2 border border-gray-300 rounded">
                        </div>
                        <div class="mb-4">
                            <label for="store_email" class="block text-sm font-bold mb-2">Store Email:</label>
                            <input type="email" id="store_email" name="store_email" value="<?= htmlspecialchars($store_email) ?>" required
                                class="w-full p-2 border border-gray-300 rounded">
                        </div>
                        <div class="mb-4">
                            <label for="facebook" class="block text-sm font-bold mb-2">Facebook:</label>
                            <input type="text" id="facebook" name="facebook" value="<?= htmlspecialchars($facebook) ?>"
                                class="w-full p-2 border border-gray-300 rounded">
                        </div>
                        <div class="mb-4">
                            <label for="telegram" class="block text-sm font-bold mb-2">Telegram:</label>
                            <input type="text" id="telegram" name="telegram" value="<?= htmlspecialchars($telegram) ?>"
                                class="w-full p-2 border border-gray-300 rounded">
                        </div>
                        <div class="mb-4">
                            <label for="map" class="block text-sm font-bold mb-2">Map:</label>
                            <input type="text" id="map" name="map" value="<?= htmlspecialchars($map) ?>"
                                class="w-full p-2 border border-gray-300 rounded">
                        </div>
                        <div class="mb-4">
                            <label for="phone" class="block text-sm font-bold mb-2">Phone:</label>
                            <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($phone) ?>"
                                class="w-full p-2 border border-gray-300 rounded">
                        </div>
                        <div class="mb-4">
                            <label for="logo" class="block text-sm font-bold mb-2">Logo:</label>
                            <input type="file" id="logo" name="logo" class="w-full p-2 border border-gray-300 rounded">
                            <?php if ($logo): ?>
                            <img src="<?= htmlspecialchars($logo) ?>" alt="Logo" class="mt-2" width="100">
                            <?php endif; ?>
                        </div>
                        <div class="mb-4">
                            <label for="password" class="block text-sm font-bold mb-2">Password:</label>
                            <input type="password" id="password" name="password" value=""
                                class="w-full p-2 border border-gray-300 rounded">
                        </div>
                        <div>
                            <input type="submit" value="Update"
                                class="bg-green-500 text-white font-bold py-2 px-4 rounded hover:bg-green-700">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>