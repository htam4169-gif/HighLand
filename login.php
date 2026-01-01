<?php
session_start();
require_once 'db.php';

// Xử lý đăng nhập
if (isset($_POST['login'])) {
    $u = $_POST['username'];
    $p = $_POST['password'];
    
    // Sử dụng Prepared Statement để bảo mật hơn (chống hack SQL Injection)
    $stmt = $conn->prepare("SELECT * FROM users WHERE username=? AND password=?");
    $stmt->bind_param("ss", $u, $p);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['fullname'] = $row['fullname'];
        
        header("Location: pager.php"); 
        exit();
    } else {
        $error = "Tài khoản hoặc mật khẩu không đúng!";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập hệ thống - Highlands Coffee</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        
        body {
            font-family: 'Roboto', sans-serif;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            
            /* SỬA Ở ĐÂY: Thêm 'images/' vào đường dẫn */
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('images/bg-login.jpg'); 
            
            background-size: cover;
            background-position: center;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            width: 100%;
            max-width: 400px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            text-align: center;
            border-top: 5px solid #b22830;
        }

        .logo-img {
            width: 120px;
            margin-bottom: 20px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

        h2 {
            color: #b22830;
            margin-bottom: 5px;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .subtitle {
            color: #666;
            font-size: 14px;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: 500;
            font-size: 14px;
        }

        input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
            transition: 0.3s;
            background-color: #f9f9f9;
        }

        input:focus {
            border-color: #b22830;
            outline: none;
            background-color: white;
            box-shadow: 0 0 0 3px rgba(178, 40, 48, 0.1);
        }

        button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(to right, #b22830, #d94048);
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            transition: transform 0.2s, box-shadow 0.2s;
            margin-top: 10px;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(178, 40, 48, 0.4);
        }

        .error {
            background-color: #ffebee;
            color: #c62828;
            padding: 10px;
            border-radius: 5px;
            font-size: 14px;
            margin-bottom: 20px;
            border: 1px solid #ffcdd2;
        }

        .footer-text {
            margin-top: 20px;
            font-size: 12px;
            color: #888;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <img src="images/logo.png" alt="Logo Highlands" class="logo-img">
        
        <h2>HIGHLANDS POS System</h2>
        <p class="subtitle">Hệ thống quản lý bán hàng</p>

        <?php if(isset($error)) echo "<div class='error'>⚠️ $error</div>"; ?>

        <form method="POST">
            <div class="form-group">
                <label>Tên đăng nhập</label>
                <input type="text" name="username" placeholder="Nhập tên đăng nhập..." required autocomplete="off">
            </div>
            
            <div class="form-group">
                <label>Mật khẩu</label>
                <input type="password" name="password" placeholder="Nhập mật khẩu..." required>
            </div>
            
            <button type="submit" name="login">Đăng Nhập</button>
        </form>

        <div class="footer-text">
            &copy; 2025 Highlands Coffee POS Project
        </div>
    </div>

</body>
</html>