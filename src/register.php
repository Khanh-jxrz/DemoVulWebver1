<?php
require_once 'config.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    
    // Check username exists
    $check_query = "SELECT * FROM users WHERE username = '$username'";
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        $message = "Username đã tồn tại!";
    } else {
        // Insert user
        $insert_query = "INSERT INTO users (username, password, email, full_name) VALUES ('$username', '$password', '$email', '$full_name')";
        if (mysqli_query($conn, $insert_query)) {
            $user_id = mysqli_insert_id($conn);
            
            // Create profile
            mysqli_query($conn, "INSERT INTO user_profiles (user_id, phone, address, secret_note) VALUES ($user_id, '', '', 'User secret data')");
            
            $message = "Đăng ký thành công! <a href='login.php'>Đăng nhập ngay</a>";
        } else {
            $message = "Đăng ký thất bại!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký - VulnShop</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #34495e; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .register-container { background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); width: 100%; max-width: 450px; }
        h2 { color: #2c3e50; margin-bottom: 20px; text-align: center; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; color: #555; font-weight: bold; }
        input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; }
        button { width: 100%; padding: 12px; background: #27ae60; color: white; border: none; border-radius: 4px; font-size: 16px; cursor: pointer; margin-top: 10px; }
        button:hover { background: #229954; }
        .message { padding: 10px; border-radius: 4px; margin-bottom: 20px; text-align: center; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
        .links { text-align: center; margin-top: 15px; }
        .links a { color: #3498db; text-decoration: none; }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>Đăng ký tài khoản</h2>
        
        <?php if ($message): ?>
            <div class="message <?php echo strpos($message, 'thành công') !== false ? 'success' : 'error'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>Tên đăng nhập:</label>
                <input type="text" name="username" required>
            </div>
            
            <div class="form-group">
                <label>Mật khẩu:</label>
                <input type="password" name="password" required>
            </div>
            
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label>Họ và tên:</label>
                <input type="text" name="full_name" required>
            </div>
            
            <button type="submit">Đăng ký</button>
        </form>
        
        <div class="links">
            Đã có tài khoản? <a href="login.php">Đăng nhập</a> |
            <a href="index.php">Về trang chủ</a>
        </div>
    </div>
</body>
</html>