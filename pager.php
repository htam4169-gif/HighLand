<?php
// 1. CẤU HÌNH BẢO MẬT: Đóng trình duyệt là phải đăng nhập lại
session_set_cookie_params(0); 
session_start();

// 2. Nếu chưa đăng nhập thì đá về login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chọn Pager - Highlands POS</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body { 
            background: #f8f9fa; 
            font-family: 'Roboto', sans-serif; 
            display: flex; 
            flex-direction: column; 
            align-items: center; 
            min-height: 100vh;
            margin: 0;
        }

        /* Header */
        .header { 
            width: 100%; 
            background: #343a40; 
            color: white; 
            padding: 15px 30px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header-user { font-weight: 500; font-size: 16px; }
        .btn-logout { color: #ffcdd2; text-decoration: none; font-weight: bold; padding: 5px 10px; border: 1px solid #ffcdd2; border-radius: 4px; transition: 0.2s; }
        .btn-logout:hover { background: #ffcdd2; color: #b22830; }
        
        /* Container chính */
        .main-container {
            width: 100%;
            max-width: 1100px; 
            padding: 30px 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Phần chọn loại đơn */
        .mode-select { display: flex; gap: 20px; margin-bottom: 40px; width: 100%; justify-content: center; }
        .mode-btn { 
            padding: 20px 40px; 
            font-size: 22px; 
            border: none; 
            cursor: pointer; 
            border-radius: 12px; 
            font-weight: 700; 
            opacity: 0.5; 
            transition: all 0.3s ease;
            flex: 1;
            max-width: 300px;
            text-transform: uppercase;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .mode-btn.active { 
            opacity: 1; 
            transform: translateY(-3px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.2);
        }
        .btn-eatin.active { background-color: #28a745; color: white; } 
        .btn-takeaway.active { background-color: #343a40; color: white; } 
        .btn-eatin { background-color: #e9ecef; color: #28a745; border: 2px solid #28a745; }
        .btn-takeaway { background-color: #e9ecef; color: #343a40; border: 2px solid #343a40; }

        .instruction { 
            font-size: 20px; color: #495057; margin-bottom: 25px; font-weight: 500; 
        }

        .pager-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(90px, 1fr)); 
            gap: 15px; 
            width: 100%;
        }

        .pager-btn {
            background-color: #e9ecef; 
            color: #495057;
            font-size: 32px; 
            font-weight: 700;
            border: none;
            border-radius: 12px; 
            cursor: pointer;
            aspect-ratio: 1 / 1; 
            display: flex;
            justify-content: center;
            align-items: center;
            transition: all 0.2s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        .pager-btn:hover { 
            background-color: #dee2e6; 
            transform: translateY(-5px); 
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .pager-btn:active, .pager-btn.selecting {
            background-color: #0d6efd !important; 
            color: white !important;
            transform: scale(0.95); 
            box-shadow: 0 2px 5px rgba(13, 110, 253, 0.3);
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="header-user">Xin chào, <b><?php echo $_SESSION['fullname']; ?></b></div>
        <a href="logout_custom.php" class="btn-logout">Đăng xuất</a>
    </div>

    <div class="main-container">
        <div class="mode-select">
            <button class="mode-btn btn-eatin active" id="btn-eat" onclick="selectMode('Eat-in')">
                <i style="font-size: 30px; display:block; margin-bottom:5px;">🍽️</i> Eat-in (Tại quán)
            </button>
            <button class="mode-btn btn-takeaway" id="btn-take" onclick="selectMode('Take-away')">
                 <i style="font-size: 30px; display:block; margin-bottom:5px;">🛍️</i> Take-Away (Mang về)
            </button>
        </div>

        <div class="instruction">Chọn số thẻ rung (Pager) để bắt đầu:</div>

        <div class="pager-grid">
            <?php 
            for($i=1; $i<=40; $i++){
                echo "<button class='pager-btn' onclick='choosePager(this, $i)'>$i</button>";
            }
            ?>
        </div>
    </div>

    <script>
        let selectedMode = 'Eat-in';

        function selectMode(mode) {
            selectedMode = mode;
            const btnEat = document.getElementById('btn-eat');
            const btnTake = document.getElementById('btn-take');

            if(mode === 'Eat-in') {
                btnEat.classList.add('active');
                btnTake.classList.remove('active');
            } else {
                btnTake.classList.add('active');
                btnEat.classList.remove('active');
            }
        }

        function choosePager(buttonElement, number) {
            buttonElement.classList.add('selecting');
            setTimeout(() => {
                window.location.href = `index.php?pager=${number}&type=${selectedMode}`;
            }, 150);
        }
    </script>
</body>
</html>