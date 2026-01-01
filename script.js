let cart = [];
let currentOrderType = 'Eat-in';


function addToCart(id, name, price, category) {
    let item = cart.find(i => i.id === id);
    if (item) {
        item.quantity++;
    } else {
        cart.push({ id: id, name: name, price: price, quantity: 1, note: "", category: category, isCombo: false });
    }
    renderCart();
}

function renderCart() {
    let container = document.getElementById('order-list');
    let totalElement = document.getElementById('total-price');
    if (!container || !totalElement) return;

    container.innerHTML = '';
    let total = 0;

    cart.forEach((item, index) => {
        let lineTotal = item.price * item.quantity;
        total += lineTotal;
        let noteHtml = item.note ? `<div style="color:#c0392b;font-size:13px;font-style:italic;margin-top:3px;padding-left:5px;">Ghi chú: ${item.note}</div>` : '';
        let comboBadge = item.isCombo ? `<span style="background:orange;color:white;font-size:10px;padding:2px 4px;border-radius:3px;font-weight:bold;margin-right:5px;">COMBO</span>` : '';
        
        container.innerHTML += `
            <div class="order-item">
                <div style="flex:1;"><div>${comboBadge}<b>${item.name}</b></div>${noteHtml}<div style="font-size:12px;color:#666;">${item.price.toLocaleString()} đ</div></div>
                <div style="display:flex;align-items:center;gap:5px;">
                    <button onclick="updateQty(${index}, -1)" style="width:25px;cursor:pointer;">-</button>
                    <span style="font-weight:bold;min-width:20px;text-align:center;">${item.quantity}</span>
                    <button onclick="updateQty(${index}, 1)" style="width:25px;cursor:pointer;">+</button>
                </div>
                <div style="margin-left:10px;font-weight:bold;">${lineTotal.toLocaleString()}</div>
            </div>`;
    });
    totalElement.innerText = total.toLocaleString() + ' đ';
}

function applyCombo() {
    let drinkIndex = -1, cakeIndex = -1;
    for (let i = 0; i < cart.length; i++) {
        if (cart[i].isCombo) continue;
        let isCake = cart[i].category && (cart[i].category.includes('Bánh') || cart[i].category.includes('Snack') || cart[i].category.includes('Mì'));
        if (isCake && cakeIndex === -1) cakeIndex = i;
        else if (!isCake && drinkIndex === -1) drinkIndex = i;
    }
    if (drinkIndex !== -1 && cakeIndex !== -1) {
        let drink = cart[drinkIndex], cake = cart[cakeIndex];
        let comboPrice = Math.ceil(((drink.price + cake.price) * 0.85) / 1000) * 1000;
        let newCombo = { id: 'combo_'+Date.now(), name: `Combo: ${drink.name} + ${cake.name}`, price: comboPrice, quantity: 1, note: drink.note+(cake.note?`, ${cake.note}`:""), category: 'Combo', isCombo: true };
        let maxIdx = Math.max(drinkIndex, cakeIndex), minIdx = Math.min(drinkIndex, cakeIndex);
        cart.splice(maxIdx, 1); cart.splice(minIdx, 1); cart.push(newCombo);
        renderCart();
        alert("✅ Đã ghép Combo (Giảm 15%)!");
    } else { alert("⚠️ Không tìm thấy cặp (1 Nước + 1 Bánh) lẻ nào!"); }
}

function updateQty(index, change) {
    cart[index].quantity += change;
    if (cart[index].quantity <= 0) cart.splice(index, 1);
    renderCart();
}
function setOrderType(type) {
    currentOrderType = type;
    let btnEat = document.getElementById('btn-eatin'), btnTake = document.getElementById('btn-takeaway');
    if(btnEat && btnTake) {
        if(type === 'Eat-in') {
            btnEat.className='active'; btnEat.style.background='#2ecc71'; btnEat.style.color='white'; btnEat.style.border='2px solid red'; 
            btnTake.className=''; btnTake.style.background='#eee'; btnTake.style.color='black'; btnTake.style.border='1px solid #aaa';
        } else {
            btnTake.className='active'; btnTake.style.background='#2ecc71'; btnTake.style.color='white'; btnTake.style.border='2px solid red';
            btnEat.className=''; btnEat.style.background='#eee'; btnEat.style.color='black'; btnEat.style.border='1px solid #aaa';
        }
    }
}

function submitOrder() {
    if (cart.length === 0) { alert("Chưa có món nào!"); return; }
    let pager = document.getElementById('pager-input').value || '';
    fetch('save_order.php', {
        method: 'POST', headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ cart: cart, orderType: currentOrderType, pager: pager })
    })
    .then(response => response.text())
    .then(data => {
       
        alert("✅ " + data); 
        
        printInvoice(pager);
        cart = []; renderCart(); document.getElementById('pager-input').value = '';
    })
    .catch(error => { console.error(error); alert("❌ Lỗi kết nối!"); });
}


function printInvoice(pager) {
    let date = new Date().toLocaleString('vi-VN');
    let total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);

    let billContent = `
        <html><head><title>Hóa Đơn</title>
        <style>body{font-family:'Courier New';width:300px;margin:0 auto;padding:10px}.center{text-align:center}.bold{font-weight:bold}.right{text-align:right}.line{border-bottom:1px dashed #000;margin:10px 0}table{width:100%;font-size:13px}</style>
        </head><body>
        <div class="center"><div class="bold" style="font-size:18px">HIGHLANDS COFFEE</div><div>ĐC: Hoàng Huy Commerce</div><div>Hotline: 1900 1755</div></div>
        <div class="line"></div><div>Thu ngân: Thu ngân 1</div><div>${date}</div><div>Hình thức: <span class="bold">${currentOrderType}</span></div><div>Pager: <span class="bold">${pager}</span></div><div class="line"></div>
        <table><tr><th align="left">Món</th><th align="center">SL</th><th align="right">Tiền</th></tr>`;

    cart.forEach(item => {
        let noteText = item.note ? `<br><i style="font-size:11px">(${item.note})</i>` : '';
        billContent += `<tr><td style="padding-top:5px;">${item.name} ${noteText}</td><td align="center">${item.quantity}</td><td class="right">${(item.price*item.quantity).toLocaleString()}</td></tr>`;
    });

    billContent += `</table><div class="line"></div><div class="right bold" style="font-size:18px">TỔNG: ${total.toLocaleString()} đ</div><div class="line"></div>
    <div class="center" style="font-style:italic;font-size:12px;margin-top:20px">Cảm ơn quý khách!</div>
    
    <script>
        window.onload = function() { window.print(); }
        // Khi cửa sổ hóa đơn bị tắt -> Chuyển trang cha sang admin.php
        window.onbeforeunload = function() {
            if (window.opener && !window.opener.closed) {
                window.opener.location.href = 'admin.php';
            }
        }
    </script>
    </body></html>`;

    let printWindow = window.open('', '', 'height=600,width=400');
    printWindow.document.write(billContent);
    printWindow.document.close();
    printWindow.focus();
}