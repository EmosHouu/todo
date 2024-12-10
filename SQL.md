CREATE DATABASE todo_list;  

USE todo_list;  

CREATE TABLE todos (  
    id INT AUTO_INCREMENT PRIMARY KEY,  
    task VARCHAR(255) NOT NULL,  
    status TINYINT(1) NOT NULL DEFAULT 0, -- 0 = 未完成, 1 = 完成  
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP  
);