<?php
    session_start();

    if (!isset($_SESSION['store_id'])) {
        header("Location: store_login.php");
        exit();
    }

    include 'db.php';

    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $id = intval($_GET['id']);

        $stmt = $conn->prepare("DELETE FROM store_category WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $id);

            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Category deleted successfully.']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to delete category.']);
            }

            $stmt->close();
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to prepare delete statement.']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid or missing category ID.']);
    }

    $conn->close();
?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        window.location.href = 'category.php';
    });
</script>
