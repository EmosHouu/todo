<?php  
include 'db.php';  

// 預設值為空  
$task = "";  
$id = 0;  

// 檢查是否傳遞了待辦事項的 ID  
if (isset($_GET['id'])) {  
    $id = $_GET['id'];  
    
    // 讀取該 ID 的待辦事項  
    $sql = "SELECT * FROM todos WHERE id = ?";  
    $stmt = $conn->prepare($sql);  
    $stmt->bind_param("i", $id); // "i" 表示整數  
    $stmt->execute();  
    $result = $stmt->get_result();  
    
    if ($result->num_rows > 0) {  
        $row = $result->fetch_assoc();  
        $task = $row['task']; // 獲取待辦事項的當前值  
    }  
    $stmt->close();  
}  

// 處理編輯表單提交  
if ($_SERVER["REQUEST_METHOD"] == "POST") {  
    $task = $_POST['task'];  

    // 更新待辦事項  
    if (!empty($task) && $id > 0) {  
        $sql = "UPDATE todos SET task = ? WHERE id = ?";  
        $stmt = $conn->prepare($sql);  
        $stmt->bind_param("si", $task, $id); // "si" 表示 string 和整數  
        $stmt->execute();  
        $stmt->close();  
        header("Location: index.php"); // 完成後重定向到主頁  
        exit();  
    }  
}  
?>  

<!DOCTYPE html>  
<html lang="zh-Hant">  
<head>  
    <meta charset="UTF-8">  
    <meta name="viewport" content="width=device-width, initial-scale=1.0">  
    <title>編輯待辦事項</title>  
</head>  
<body>  
    <h1>編輯待辦事項</h1>  
    <form action="" method="post">  
        <input type="text" name="task" value="<?php echo htmlspecialchars($task); ?>" required>  
        <input type="submit" value="更新">  
    </form>  

    <a href="index.php">返回待辦事項清單</a> <!-- 返回主頁連結 -->  
</body>  
</html>  

<?php  
$conn->close();  
?>