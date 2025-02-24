<?php
include 'db.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['id'])) {
    header("Location: admin_login.php");
    exit();
}

?>

<div class='flex shadow-md py-1 px-4 sm:px-7 bg-white min-h-[70px] tracking-wide z-[110] fixed top-0 w-full'>
    <div class='flex flex-wrap items-center justify-between gap-4 w-full relative'>    
        <div id="collapseMenu"
            class='max-lg:hidden lg:!block max-lg:before:fixed max-lg:before:bg-black max-lg:before:opacity-50 max-lg:before:inset-0 max-lg:before:z-50'>
            <button id="toggleClose" class='lg:hidden fixed top-2 right-4 z-[100] rounded-full bg-white p-3'>
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 fill-black" viewBox="0 0 320.591 320.591">
                    <path
                        d="M30.391 318.583a30.37 30.37 0 0 1-21.56-7.288c-11.774-11.844-11.774-30.973 0-42.817L266.643 10.665c12.246-11.459 31.462-10.822 42.921 1.424 10.362 11.074 10.966 28.095 1.414 39.875L51.647 311.295a30.366 30.366 0 0 1-21.256 7.288z"
                        data-original="#000000"></path>
                    <path
                        d="M287.9 318.583a30.37 30.37 0 0 1-21.257-8.806L8.83 51.963C-2.078 39.225-.595 20.055 12.143 9.146c11.369-9.736 28.136-9.736 39.504 0l259.331 257.813c12.243 11.462 12.876 30.679 1.414 42.922-.456.487-.927.958-1.414 1.414a30.368 30.368 0 0 1-23.078 7.288z"
                        data-original="#000000"></path>
                </svg>
            </button>

            <div
                class="max-lg:fixed max-lg:bg-white max-lg:w-1/2 max-lg:min-w-[300px] max-lg:top-0 max-lg:left-0 max-lg:p-6 max-lg:h-full max-lg:shadow-md max-lg:overflow-auto z-50">
                <div class='flex items-center max-lg:flex-col-reverse max-lg:ml-auto gap-8'>
                    <div
                        class='flex w-full bg-gray-100 px-4 py-2.5 rounded outline-none border focus-within:border-blue-600 focus-within:bg-transparent transition-all'>
                        <input type='text' placeholder='Search Menu...'
                            class='w-full text-sm bg-transparent rounded outline-none pr-2' />
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 192.904 192.904" width="16px"
                            class="cursor-pointer fill-gray-400">
                            <path
                                d="m190.707 180.101-47.078-47.077c11.702-14.072 18.752-32.142 18.752-51.831C162.381 36.423 125.959 0 81.191 0 36.422 0 0 36.423 0 81.193c0 44.767 36.422 81.187 81.191 81.187 19.688 0 37.759-7.049 51.831-18.751l47.079 47.078a7.474 7.474 0 0 0 5.303 2.197 7.498 7.498 0 0 0 5.303-12.803zM15 81.193C15 44.694 44.693 15 81.191 15c36.497 0 66.189 29.694 66.189 66.193 0 36.496-29.692 66.187-66.189 66.187C44.693 147.38 15 117.689 15 81.193z">
                            </path>
                        </svg>
                    </div>
                    <div class="dropdown-menu relative flex shrink-0 group">               
                        <img src="https://readymadeui.com/team-1.webp" alt="profile-pic"
                            class="w-9 h-9 max-lg:w-16 max-lg:h-16 rounded-full border-2 border-gray-300 cursor-pointer" />

                        <div
                            class="dropdown-content hidden group-hover:block shadow-md p-2 bg-white rounded-md absolute top-9 right-0 w-56">
                            <div class="w-full">                              
                                <hr class="my-2 -mx-2" />
                                <a href="logout.php"
                                    class="text-sm text-gray-800 cursor-pointer flex items-center p-2 rounded-md hover:bg-gray-100 dropdown-item transition duration-300 ease-in-out">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-3 fill-current"
                                        viewBox="0 0 6 6">
                                        <path
                                            d="M3.172.53a.265.266 0 0 0-.262.268v2.127a.265.266 0 0 0 .53 0V.798A.265.266 0 0 0 3.172.53zm1.544.532a.265.266 0 0 0-.026 0 .265.266 0 0 0-.147.47c.459.391.749.973.749 1.626 0 1.18-.944 2.131-2.116 2.131A2.12 2.12 0 0 1 1.06 3.16c0-.65.286-1.228.74-1.62a.265.266 0 1 0-.344-.404A2.667 2.667 0 0 0 .53 3.158a2.66 2.66 0 0 0 2.647 2.663 2.657 2.657 0 0 0 2.645-2.663c0-.812-.363-1.542-.936-2.03a.265.266 0 0 0-.17-.066z"
                                            data-original="#000000" />
                                    </svg>
                                    Logout</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <button id="toggleOpen" class='lg:hidden !ml-7 outline-none'>
            <svg class="w-7 h-7" fill="#000" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd"
                    d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                    clip-rule="evenodd"></path>
            </svg>
        </button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // header
    var toggleOpen = document.getElementById('toggleOpen');
    var toggleClose = document.getElementById('toggleClose');
    var collapseMenu = document.getElementById('collapseMenu');

    function handleClick() {
        if (collapseMenu.style.display === 'block') {
            collapseMenu.style.display = 'none';
        } else {
            collapseMenu.style.display = 'block';
        }
    }

    toggleOpen.addEventListener('click', handleClick);
    toggleClose.addEventListener('click', handleClick);

    // sidebar
    let sidebarToggleBtn = document.getElementById('toggle-sidebar');
    let sidebar = document.getElementById('sidebar');
    let sidebarCollapseMenu = document.getElementById('sidebar-collapse-menu');

    sidebarToggleBtn.addEventListener('click', () => {
        if (!sidebarCollapseMenu.classList.contains('open')) {
            sidebarCollapseMenu.classList.add('open');
            sidebarCollapseMenu.style.cssText = 'width: 250px; visibility: visible; opacity: 1;';
            sidebarToggleBtn.style.cssText = 'left: 236px;';
        } else {
            sidebarCollapseMenu.classList.remove('open');
            sidebarCollapseMenu.style.cssText = 'width: 32px; visibility: hidden; opacity: 0;';
            sidebarToggleBtn.style.cssText = 'left: 10px;';
        }

    });
});

function logout() {
    axios.get('logout.php')
        .then(response => {
            window.location.href = 'store_login.php';
        })
        .catch(error => {
            console.error('Logout error:', error);
        });
}
</script>

<?php

try {
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
    $page = isset($_GET['page']) ? max(intval($_GET['page']), 1) : 1;
    $offset = ($page - 1) * $limit;

    // Get total count of stores
    $total_sql = "SELECT COUNT(*) as total FROM stores";
    $stmt_total = $conn->prepare($total_sql);
    $stmt_total->execute();
    $total_result = $stmt_total->get_result();

    if (!$total_result) {
        throw new Exception("Failed to fetch total records.");
    }

    $total_row = $total_result->fetch_assoc();
    $total_records = $total_row['total'];
    $total_pages = ceil($total_records / $limit);

    // Fetch stores
    $sql = "SELECT id, code, store_name, email, facebook, telegram, map, phone, logo FROM stores ORDER BY id DESC LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stores</title>
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


        <script>
function resetMenuColors() {
    const menuItems = document.querySelectorAll('nav#sidebar a');
    menuItems.forEach(item => {
        item.classList.remove('text-red-500');
        const svg = item.querySelector('svg');
        if (svg) {
            svg.classList.remove('fill-red-500');
        }
    });
}
</script>

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

<button id="toggle-sidebar"
    class='lg:hidden w-8 h-8 z-[100] fixed top-[74px] left-[10px] cursor-pointer bg-[#007bff] flex items-center justify-center rounded-full outline-none transition-all duration-500'>
    <svg xmlns="http://www.w3.org/2000/svg" fill="#fff" class="w-3 h-3" viewBox="0 0 55.752 55.752">
        <path
            d="M43.006 23.916a5.36 5.36 0 0 0-.912-.727L20.485 1.581a5.4 5.4 0 0 0-7.637 7.638l18.611 18.609-18.705 18.707a5.398 5.398 0 1 0 7.634 7.635l21.706-21.703a5.35 5.35 0 0 0 .912-.727 5.373 5.373 0 0 0 1.574-3.912 5.363 5.363 0 0 0-1.574-3.912z"
            data-original="#000000" />
    </svg>
</button>


            <div class="main-content w-full overflow-auto p-6">
                <div class="container-xl mx-auto">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold">Store List</h2>
                        <a href="new_store.php">
                            <button class="bg-green-500 text-white font-bold py-2 px-4 rounded hover:bg-green-700">
                                Add New
                            </button>
                        </a>
                    </div>

                    <table class="w-full table-auto border-collapse">
                        <thead class="text-left">
                            <tr class="bg-gray-200">
                                <th class="p-4 border-b border-slate-300 bg-slate-50">
                                    <p class="block text-sm font-bold leading-none text-slate-500">Code</p>
                                </th>
                                <th class="p-4 border-b border-slate-300 bg-slate-50">
                                    <p class="block text-sm font-bold leading-none text-slate-500">Store Name</p>
                                </th>
                                <th class="p-4 border-b border-slate-300 bg-slate-50">
                                    <p class="block text-sm font-bold leading-none text-slate-500">Email</p>
                                </th>
                                <th class="p-4 border-b border-slate-300 bg-slate-50">
                                    <p class="block text-sm font-bold leading-none text-slate-500">Facebook</p>
                                </th>
                                <th class="p-4 border-b border-slate-300 bg-slate-50">
                                    <p class="block text-sm font-bold leading-none text-slate-500">Telegram</p>
                                </th>
                                <th class="p-4 border-b border-slate-300 bg-slate-50">
                                    <p class="block text-sm font-bold leading-none text-slate-500">Map</p>
                                </th>
                                <th class="p-4 border-b border-slate-300 bg-slate-50">
                                    <p class="block text-sm font-bold leading-none text-slate-500">Phone</p>
                                </th>
                                <th class="p-4 border-b border-slate-300 bg-slate-50">
                                    <p class="block text-sm font-bold leading-none text-slate-500">Logo</p>
                                </th>
                                <th class="p-4 border-b border-slate-300 bg-slate-50">
                                    <p class="block text-sm font-bold leading-none text-slate-500">Action</p>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                            <tr class="hover:bg-gray-100">
                                <td class="px-4 py-2 text-xs border-b border-slate-200"><?= htmlspecialchars($row['code']) ?></td>
                                <td class="px-4 py-2 text-xs border-b border-slate-200"><?= htmlspecialchars($row['store_name']) ?></td>
                                <td class="px-4 py-2 text-xs border-b border-slate-200"><?= htmlspecialchars($row['email']) ?></td>
                                <td class="px-4 py-2 text-xs border-b border-slate-200"><?= htmlspecialchars($row['facebook']) ?></td>
                                <td class="px-4 py-2 text-xs border-b border-slate-200"><?= htmlspecialchars($row['telegram']) ?></td>
                                <td class="px-4 py-2 text-xs border-b border-slate-200"><?= htmlspecialchars($row['map']) ?></td>
                                <td class="px-4 py-2 text-xs border-b border-slate-200"><?= htmlspecialchars($row['phone']) ?></td>
                                <td class="px-4 py-2 text-xs border-b border-slate-200">
                                    <img src="../images_logo/<?= htmlspecialchars($row['logo']) ?>" alt="logo" class="w-10 h-10">
                                </td>
                                <td class="px-4 py-2 border-b border-slate-200">
                                    <button class="mr-4">
                                        <a href="edit_store.php?id=<?= htmlspecialchars($row["id"]) ?>">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                class="w-5 fill-blue-500 hover:fill-blue-700"
                                                viewBox="0 0 348.882 348.882">
                                                <path
                                                    d="m333.988 11.758-.42-.383A43.363 43.363 0 0 0 304.258 0a43.579 43.579 0 0 0-32.104 14.153L116.803 184.231a14.993 14.993 0 0 0-3.154 5.37l-18.267 54.762c-2.112 6.331-1.052 13.333 2.835 18.729 3.918 5.438 10.23 8.685 16.886 8.685h.001c2.879 0 5.693-.592 8.362-1.76l52.89-23.138a14.985 14.985 0 0 0 5.063-3.626L336.771 73.176c16.166-17.697 14.919-45.247-2.783-61.418zM130.381 234.247l10.719-32.134.904-.99 20.316 18.556-.904.99-31.035 13.578zm184.24-181.304L182.553 197.53l-20.316-18.556L294.305 34.386c2.583-2.828 6.118-4.386 9.954-4.386 3.365 0 6.588 1.252 9.082 3.53l.419.383c5.484 5.009 5.87 13.546.861 19.03z"
                                                    data-original="#000000" />
                                                <path
                                                    d="M303.85 138.388c-8.284 0-15 6.716-15 15v127.347c0 21.034-17.113 38.147-38.147 38.147H68.904c-21.035 0-38.147-17.113-38.147-38.147V100.413c0-21.034 17.113-38.147 38.147-38.147h131.587c8.284 0 15-6.716 15-15s-6.716-15-15-15H68.904C31.327 32.266.757 62.837.757 100.413v180.321c0 37.576 30.571 68.147 68.147 68.147h181.798c37.576 0 68.147-30.571 68.147-68.147V153.388c.001-8.284-6.715-15-14.999-15z"
                                                    data-original="#000000" />
                                            </svg>
                                        </a>
                                    </button>
                                    <button class="mr-4">
                                        <a href="delete_store.php?id=<?= htmlspecialchars($row['id']) ?>"
                                            onclick="return confirm('Are you sure you want to delete this store?');">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                class="w-5 fill-red-500 hover:fill-red-700" viewBox="0 0 24 24">
                                                <path
                                                    d="M19 7a1 1 0 0 0-1 1v11.191A1.92 1.92 0 0 1 15.99 21H8.01A1.92 1.92 0 0 1 6 19.191V8a1 1 0 0 0-2 0v11.191A3.918 3.918 0 0 0 8.01 23h7.98A3.918 3.918 0 0 0 20 19.191V8a1 1 0 0 0-1-1Zm1-3h-4V2a1 1 0 0 0-1-1H9a1 1 0 0 0-1 1v2H4a1 1 0 0 0 0 2h16a1 1 0 0 0 0-2ZM10 4V3h4v1Z"
                                                    data-original="#000000" />
                                            </svg>
                                        </a>
                                    </button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center p-4">No stores found</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>

                    <div class="flex justify-between items-center mt-4">
                        <p class="text-sm text-gray-500">Showing <?= $offset + 1 ?> to
                            <?= min($offset + $limit, $total_records) ?> of <?= $total_records ?> entries</p>
                        <div class="flex space-x-2">
                            <?php if ($page > 1): ?>
                            <a href="?page=<?= $page - 1 ?>&limit=<?= $limit ?>">
                                <button
                                    class="bg-gray-200 text-gray-600 text-xs font-bold py-2 px-4 rounded hover:bg-red-500 hover:text-white w-20">
                                    Previous
                                </button>
                            </a>
                            <?php endif; ?>
                            <?php if ($page < $total_pages): ?>
                            <a href="?page=<?= $page + 1 ?>&limit=<?= $limit ?>">
                                <button
                                    class="bg-gray-200 text-gray-600 text-xs font-bold py-2 px-4 rounded hover:bg-red-700 hover:text-white w-20">
                                    Next
                                </button>
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>