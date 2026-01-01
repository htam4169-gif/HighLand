<?php
// Thiết lập cookie về 0 để đồng bộ bảo mật
session_set_cookie_params(0); 
session_start();

// Xóa sạch toàn bộ phiên đăng nhập
session_unset();
session_destroy();

// Quay về trang đăng nhập
header("Location: login.php");
exit();
?>