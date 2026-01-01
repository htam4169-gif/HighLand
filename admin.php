<?php 
session_set_cookie_params(0); 
session_start();

if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
}

require_once 'db.php'; 


if (isset($_POST['add_product'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $price = $_POST['price'];
    $cate = $_POST['category_id'];
    $sql = "INSERT INTO products (name, price, category_id) VALUES ('$name', '$price', '$cate')";
    mysqli_query($conn, $sql);
    header("Location: admin.php"); 
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM products WHERE id=$id");
    header("Location: admin.php");
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hệ Thống Quản Lý Highlands - Admin</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding: 20px; background: #f4f7f6; }
        .container { max-width: 1200px; margin: auto; }
        h1 { color: #b22830; border-left: 5px solid #b22830; padding-left: 15px; }
        table { border-collapse: collapse; width: 100%; margin-top: 10px; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        th, td { border: 1px solid #eee; padding: 12px; text-align: left; }
        th { background-color: #b22830; color: white; text-transform: uppercase; font-size: 12px; }
        .box { background: white; padding: 20px; margin-bottom: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .btn-del { color: #e74c3c; text-decoration: none; font-weight: bold; }
        .btn-nav { display: inline-block; padding: 10px 20px; background: #34495e; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; margin-top: 20px; border:none; cursor:pointer; }
        .btn-excel { background: #27ae60; color: white; padding: 8px 15px; border-radius: 5px; border: none; font-weight: bold; cursor: pointer; float: right; margin-bottom: 10px; }
        .btn-excel:hover { background: #219150; }
        .note-text { color: #c0392b; font-size: 11px; font-style: italic; font-weight: bold; }
        .stats-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        tr:hover { background-color: #f9f9f9; }
        .hidden-row { display: none; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Quản Lý Highlands Coffee</h1>
        
        <div class="stats-grid">
            <div class="box">
                <h2>📊 Doanh Thu Hệ Thống</h2>
                <?php 
                $sql_rev = "SELECT SUM(total_amount) as total FROM orders";
                $rev_row = mysqli_fetch_assoc(mysqli_query($conn, $sql_rev));
                $sql_count = "SELECT COUNT(*) as count FROM orders";
                $count_row = mysqli_fetch_assoc(mysqli_query($conn, $sql_count));
                ?>
                <p>Tổng số đơn hàng: <b style="font-size: 18px;"><?php echo $count_row['count']; ?></b></p>
                <p>Tổng doanh thu: <b style="color:#27ae60; font-size: 24px;"><?php echo number_format($rev_row['total']); ?> đ</b></p>
            </div>

            <div class="box">
                <h2>🆕 Thêm Món Mới</h2>
                <form method="POST">
                    <input type="text" name="name" placeholder="Tên món" required style="padding: 8px; width: 150px;">
                    <input type="number" name="price" placeholder="Giá tiền" required style="padding: 8px; width: 100px;">
                    <select name="category_id" style="padding: 8px;">
                        <?php
                        $cates = mysqli_query($conn, "SELECT * FROM categories ORDER BY name ASC");
                        while($c = mysqli_fetch_assoc($cates)) {
                            echo "<option value='".$c['id']."'>".$c['name']."</option>";
                        }
                        ?>
                    </select>
                    <button type="submit" name="add_product" style="padding: 8px 15px; background: #27ae60; color:white; border:none; border-radius:4px; cursor:pointer; font-weight:bold;">THÊM</button>
                </form>
            </div>
        </div>

        <div class="box">
            <button class="btn-excel" onclick="exportToExcel()">📥 XUẤT BÁO CÁO EXCEL</button>
            <h2>📜 Lịch Sử Đơn Hàng</h2>
            <table id="orderTable">
                <thead>
                    <tr>
                        <th>Mã Đơn</th>
                        <th>Thời Gian</th>
                        <th>Loại</th>
                        <th>Thẻ Rung</th>
                        <th>Chi Tiết Món & Ghi Chú</th>
                        <th>Tổng Tiền (đ)</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                $orders = mysqli_query($conn, "SELECT * FROM orders ORDER BY id DESC");
                $order_count = 0;
                while($o = mysqli_fetch_assoc($orders)):
                    $order_count++;
                    $order_id = $o['id'];
                    $row_class = ($order_count > 10) ? 'hidden-row' : ''; 
                ?>
                <tr class="<?php echo $row_class; ?>">
                    <td><b>#<?php echo $order_id; ?></b></td>
                    <td><?php echo date("H:i d/m/Y", strtotime($o['created_at'])); ?></td>
                    <td><?php echo $o['order_type']; ?></td>
                    <td><?php echo $o['pager_number']; ?></td>
                    <td>
                        <?php 
                        $details = mysqli_query($conn, "SELECT * FROM order_details WHERE order_id = $order_id");
                        $items = [];
                        while($d = mysqli_fetch_assoc($details)){
                            $item_str = $d['product_name'] . " (x" . $d['quantity'] . ")";
                            if(!empty($d['note'])) $item_str .= " [Note: " . $d['note'] . "]";
                            $items[] = $item_str;
                            echo "• " . $d['product_name'] . " (x" . $d['quantity'] . ")";
                            if(!empty($d['note'])) echo " <br><span class='note-text'>=> Note: " . $d['note'] . "</span>";
                            echo "<br>";
                        }
                        ?>
                    </td>
                    <td style="font-weight:bold;"><?php echo number_format($o['total_amount'], 0, '', ''); ?></td>
                </tr>
                <?php endwhile; ?>
                </tbody>
            </table>

            <?php if ($order_count > 10): ?>
                <div style="text-align: center; margin-top: 15px;" id="div-show-more">
                    <button id="btn-show-more" onclick="showAllOrders()" style="padding: 10px 20px; cursor: pointer; background: #eee; border: 1px solid #ccc; border-radius: 5px; font-weight: bold;">
                        ▼ Xem thêm đơn hàng cũ
                    </button>
                </div>
            <?php endif; ?>
        </div>

        <div class="box">
            <h2>☕ Danh Sách Sản Phẩm (Sắp xếp theo Loại)</h2>
            <table>
                <tr>
                    <th width="60">STT</th> 
                    <th>Tên Món</th>
                    <th>Giá</th>
                    <th>Loại</th>
                    <th>Hành động</th>
                </tr>
                <?php 
                
                $sql_prods = "SELECT p.*, c.name as cname FROM products p JOIN categories c ON p.category_id = c.id ORDER BY c.name ASC, p.name ASC";
                $prods = mysqli_query($conn, $sql_prods);
                $stt = 1; 
                while($row = mysqli_fetch_assoc($prods)): 
                ?>
                <tr>
                    <td><b><?php echo $stt++; ?></b></td> <td><b><?php echo $row['name']; ?></b></td>
                    <td><?php echo number_format($row['price']); ?> đ</td>
                    <td><span style="background:#eee; padding:4px 8px; border-radius:4px; font-size:12px;"><?php echo $row['cname']; ?></span></td>
                    <td><a href="admin.php?delete=<?php echo $row['id']; ?>" class="btn-del" onclick="return confirm('Xóa món này khỏi Menu?');">Xóa</a></td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
        
        <div style="display: flex; gap: 10px;">
            <a href="index.php" class="btn-nav">🛒 QUAY LẠI BÁN HÀNG</a>
            <button onclick="location.href='logout_custom.php'" class="btn-nav" style="background:#e74c3c;">🔴 ĐĂNG XUẤT</button>
        </div>
    </div>

    <script>
    
    function showAllOrders() {
        const hiddenRows = document.querySelectorAll('.hidden-row');
        hiddenRows.forEach(row => row.classList.remove('hidden-row'));
        document.getElementById('div-show-more').style.display = 'none';
    }

       function exportToExcel() {
        let table = document.getElementById("orderTable");
        let html = table.outerHTML;
                let url = 'data:application/vnd.ms-excel;charset=utf-8,\uFEFF' + encodeURIComponent(html);
        let link = document.createElement("a");
        link.href = url;
        let today = new Date().toISOString().slice(0, 10);
        link.download = "Bao-Cao-Highlands-" + today + ".xls";
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
    </script>
</body>
</html>