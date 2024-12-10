<?php  
$servername = "localhost"; // 通常是 localhost  
$username = "root"; // 資料庫使用者名稱  
$password = "root"; // 資料庫密碼  
$dbname = "todo_list"; // 資料庫名稱  

// 創建連接  
$conn = new mysqli($servername, $username, $password, $dbname);  

// 檢查連接  
if ($conn->connect_error) {  
    die("連接失敗: " . $conn->connect_error);  
}  
?>