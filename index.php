<?php 

session_set_cookie_params(0); 
session_start();

if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
}

require_once 'db.php'; 
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Highlands POS System</title>
    <link rel="stylesheet" href="style.css">
    <style>
        
        .action-buttons { display: flex; gap: 8px; margin-top: 10px; margin-bottom: 5px; }
        .btn-func { flex: 1; background-color: #636e72; color: white; border: none; border-radius: 4px; padding: 8px 0; font-size: 12px; font-weight: bold; cursor: pointer; display: flex; flex-direction: column; align-items: center; justify-content: center; transition: all 0.2s; box-shadow: 0 2px 4px rgba(0,0,0,0.2); }
        .btn-func .icon { font-size: 16px; margin-bottom: 2px; }
        .btn-func:hover { background-color: #2d3436; transform: translateY(-2px); }
        .btn-func:active { transform: scale(0.95); }

        .top-nav { display: flex; justify-content: space-between; align-items: center; padding-bottom: 10px; border-bottom: 1px solid #ddd; margin-bottom: 10px; }
        .btn-nav { background: #34495e; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 12px; font-weight: bold; text-decoration: none; }
        .btn-nav:hover { background: #2c3e50; }

        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.6); justify-content: center; align-items: center; }
        .modal-content { background-color: white; padding: 20px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.3); animation: popupFade 0.3s; text-align: center; }
        @keyframes popupFade { from {transform: translateY(-20px); opacity: 0;} to {transform: translateY(0); opacity: 1;} }
        .modal-header { font-size: 20px; font-weight: bold; margin-bottom: 15px; color: #b22830; text-transform: uppercase; border-bottom: 2px solid #b22830; padding-bottom: 10px; }
        
        .topping-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-bottom: 20px; }
        .btn-topping { background: #fff; border: 1px solid #ddd; padding: 10px 5px; border-radius: 6px; cursor: pointer; font-weight: 500; color: #333; transition: 0.2s; display: flex; flex-direction: column; align-items: center; }
        .btn-topping small { color: #e74c3c; font-weight: bold; margin-top: 4px; }
        .btn-topping:hover { background: #b22830; color: white; border-color: #b22830; }
        .btn-topping:hover small { color: #fff; }
        .btn-close-modal { background: #34495e; color: white; border: none; padding: 10px 30px; border-radius: 5px; cursor: pointer; font-weight: bold; }
        
        #note-input { width: 100%; padding: 15px; border: 2px solid #b22830; border-radius: 8px; font-size: 20px; margin-bottom: 15px; font-family: Arial, sans-serif; }
    </style>
</head>
<body>
    <div class="container">
        <div class="left-panel">
            <div class="top-nav">
                <a href="pager.php" class="btn-nav">⬅ VỀ PAGER</a>
                <a href="logout_custom.php" class="btn-nav" style="background:#e74c3c;">ĐĂNG XUẤT</a>
            </div>

            <div class="header-info">
                <div>NV: <?php echo isset($_SESSION['fullname']) ? $_SESSION['fullname'] : 'Thu ngân 1'; ?></div>
                <div>Ngày: <?php echo date("d/m/Y"); ?></div>
            </div>

            <div class="order-settings">
                <div class="toggle-group">
                    <button id="btn-eatin" class="active" onclick="setOrderType('Eat-in')">Eat-in</button>
                    <button id="btn-takeaway" onclick="setOrderType('Take-away')">Take-Away</button>
                </div>
                <input type="number" id="pager-input" placeholder="Số Pager (thẻ rung)" class="pager-box">

                <div class="action-buttons">
                    <button class="btn-func" onclick="openToppingModal()">
                        <span class="icon">🧀</span> Topping
                    </button>
                    <button class="btn-func" onclick="openNoteModal()">
                        <span class="icon">📝</span> Note
                    </button>
                    <button class="btn-func" onclick="applyCombo()">
                        <span class="icon">🍟</span> Combo
                    </button>
                </div>
            </div>

            <div class="order-list" id="order-list"></div>

            <div class="payment-section">
                <div class="total-row">
                    <span>Tổng tiền:</span>
                    <span id="total-price" class="price-text">0</span>
                </div>
                <button class="btn-pay" onclick="submitOrder()">THANH TOÁN</button>
            </div>
        </div>

        <div class="right-panel">
            <div class="menu-grid">
                <?php 
                $sql = "SELECT p.*, c.color_code, c.name as category_name 
                        FROM products p 
                        JOIN categories c ON p.category_id = c.id 
                        ORDER BY p.category_id ASC";
                $result = mysqli_query($conn, $sql);
                while($row = mysqli_fetch_assoc($result)): 
                ?>
                    <div class="product-card" style="background-color: <?php echo $row['color_code']; ?>"
                         onclick="addToCart(<?php echo $row['id']; ?>, '<?php echo $row['name']; ?>', <?php echo $row['price']; ?>, '<?php echo $row['category_name']; ?>')">
                        <span class="prod-name"><?php echo $row['name']; ?></span>
                        <span class="prod-price"><?php echo number_format($row['price'], 0, ',', '.'); ?></span>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>

    <div id="modalTopping" class="modal">
        <div class="modal-content">
            <div class="modal-header">THÊM TOPPING</div>
            <div class="topping-grid">
                <button class="btn-topping" onclick="applyTopping('Kem Sữa Mặn', 9000)">Kem Sữa Mặn<br><small>+9k</small></button>
                <button class="btn-topping" onclick="applyTopping('Kem Tươi', 9000)">Kem Tươi<br><small>+9k</small></button>
                <button class="btn-topping" onclick="applyTopping('TC Trắng', 9000)">TC Trắng<br><small>+9k</small></button>
                <button class="btn-topping" onclick="applyTopping('Thạch Đào', 9000)">Thạch Đào<br><small>+9k</small></button>
                <button class="btn-topping" onclick="applyTopping('Hạt Sen', 15000)">Hạt Sen<br><small>+15k</small></button>
                <button class="btn-topping" onclick="applyTopping('Thêm Espresso', 10000)">Espresso<br><small>+10k</small></button>
            </div>
            <button class="btn-close-modal" onclick="closeModal('modalTopping')">ĐÓNG LẠI</button>
        </div>
    </div>

    <div id="modalNote" class="modal">
        <div class="modal-content" style="width: 500px;">
            <div class="modal-header">GHI CHÚ MÓN ĂN</div>
            <input type="text" id="note-input" placeholder="Ví dụ: Ít ngọt, 50% đá, mang về..." autocomplete="off">
            <div style="display:flex; gap:10px;">
                 <button class="btn-close-modal" style="flex:1; background:#95a5a6" onclick="closeNoteModal()">HỦY</button>
                 <button class="btn-close-modal" style="flex:1; background:#b22830" onclick="saveNote()">LƯU GHI CHÚ</button>
            </div>
        </div>
    </div>
    
    <script src="script.js?v=<?php echo time(); ?>"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('pager')) document.getElementById('pager-input').value = urlParams.get('pager');
            if (urlParams.get('type') && typeof setOrderType === 'function') setOrderType(urlParams.get('type'));
        });

        function openModal(id) {
            if (cart.length === 0) { alert("⚠️ Vui lòng chọn món trước!"); return; }
            document.getElementById(id).style.display = "flex";
        }
        function closeModal(id) { document.getElementById(id).style.display = "none"; }
        window.onclick = function(event) { if (event.target.classList.contains('modal')) event.target.style.display = "none"; }

        function openToppingModal() { openModal('modalTopping'); }
        function applyTopping(name, price) {
            if(cart.length > 0) {
                let item = cart[cart.length - 1];
                item.name += " + " + name;
                item.price += price;
                renderCart();
            }
        }

        function openNoteModal() {
            openModal('modalNote');
            let input = document.getElementById('note-input');
            input.value = ""; input.focus();
        }
        function closeNoteModal() { closeModal('modalNote'); }
        function saveNote() {
            let noteText = document.getElementById('note-input').value.trim();
            if(cart.length > 0) { cart[cart.length - 1].note = noteText; renderCart(); }
            closeNoteModal();
        }
        document.getElementById('note-input').addEventListener('keypress', function(e) {
            if(e.key === 'Enter') { e.preventDefault(); saveNote(); }
        });
    </script>
</body>
</html>