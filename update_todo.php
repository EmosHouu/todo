<?php
include 'db.php';

// 檢查是否有待辦事項的 ID 和操作  
if (isset($_GET['id']) && isset($_GET['action'])) {
    $id = $_GET['id'];
    $action = $_GET['action'];

    if ($action == 'complete') {
        // 設置狀態為完成  
        $sql = "UPDATE todos SET status = 1 WHERE id = ?";
    } else if ($action == 'undo') {
        // 設置狀態為未完成  
        $sql = "UPDATE todos SET status = 0 WHERE id = ?";
    }

    // 使用準備語句執行 SQL  
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

// 完成後重定向到主頁  
header("Location: index.php");
$conn->close();
