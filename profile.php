<?php
require_once 'config.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// IDOR Vulnerability - không check quyền sở hữu
$view_user_id = isset($_GET['user_id']) ? $_GET['user_id'] : $_SESSION['user_id'];

// Lấy thông tin user
$query = "SELECT * FROM users WHERE id = $view_user_id";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Lấy profile
$profile_query = "SELECT * FROM user_profiles WHERE user_id = $view_user_id";
$profile_result = mysqli_query($conn, $profile_query);
$profile = mysqli_fetch_assoc($profile_result);

// Upload avatar (File Upload Vulnerability)
$upload_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar'])) {
    $file = $_FILES['avatar'];
    $filename = $file['name'];
    $tmp_name = $file['tmp_name'];
    
    // VULNERABLE
    $upload_path = UPLOAD_DIR . $filename;
    
    if (move_uploaded_file($tmp_name, $upload_path)) {
        // Update avatar trong database
        mysqli_query($conn, "UPDATE users SET avatar = '$filename' WHERE id = " . $_SESSION['user_id']);
        $upload_message = "Upload thành công! File: $filename";
        
        // Refresh
        header("Location: profile.php");
        exit;
    } else {
        $upload_message = "Upload thất bại!";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hồ sơ - VulnShop</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; }
        .header { background: #2c3e50; color: white; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; padding: 20px; }
        .nav a { color: white; text-decoration: none; margin: 0 15px; }
        .profile-card { background: white; padding: 30px; border-radius: 8px; margin-bottom: 20px; }
        .avatar { width: 150px; height: 150px; border-radius: 50%; object-fit: cover; }
        .profile-info { margin-top: 20px; }
        .profile-info p { margin: 10px 0; padding: 10px; background: #f8f9fa; border-radius: 4px; }
        .upload-form { margin-top: 20px; padding: 20px; background: #ecf0f1; border-radius: 4px; }
        .upload-form input { margin: 10px 0; }
        .upload-form button { padding: 10px 20px; background: #3498db; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .warning { background: #e74c3c; color: white; padding: 10px; margin: 20px 0; border-radius: 4px; }
        .idor-hint { background: #f39c12; color: white; padding: 10px; margin: 20px 0; border-radius: 4px; }
        .success { background: #2ecc71; color: white; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="container">
            <a href="index.php">← Quay lại trang chủ</a>
        </div>
    </div>

    <div class="container">


        <?php if ($view_user_id != $_SESSION['user_id']): ?>

        <?php endif; ?>

        <?php if ($upload_message): ?>
            <div class="success"><?php echo $upload_message; ?></div>
        <?php endif; ?>

        <div class="profile-card">
            <h2>Hồ sơ người dùng</h2>
            
            <?php if ($user): ?>
                <img class="avatar" src="<?php echo UPLOAD_DIR . htmlspecialchars($user['avatar']); ?>" alt="Avatar" onerror="this.src='images/default.jpg'">
                
                <div class="profile-info">
                    <p><strong>ID:</strong> <?php echo $user['id']; ?></p>
                    <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
                    <p><strong>Họ tên:</strong> <?php echo htmlspecialchars($user['full_name']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                    <p><strong>Vai trò:</strong> <?php echo $user['role'] === 'admin' ? '👑 Administrator' : '👤 User'; ?></p>
                    
                    <?php if ($profile): ?>
                        <p><strong>Điện thoại:</strong> <?php echo htmlspecialchars($profile['phone']); ?></p>
                        <p><strong>Địa chỉ:</strong> <?php echo htmlspecialchars($profile['address']); ?></p>
                        <p style="background: #ffe6e6;"><strong>⚠️ Thông tin bí mật:</strong> <?php echo htmlspecialchars($profile['secret_note']); ?></p>
                    <?php endif; ?>
                </div>

                <?php if ($view_user_id == $_SESSION['user_id']): ?>
                    <div class="upload-form">
                        <h3>Upload Avatar</h3>
                        <form method="POST" enctype="multipart/form-data">
                            <input type="file" name="avatar" required>
                            <button type="submit">Upload Avatar</button>
                        </form>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <p>Không tìm thấy người dùng!</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>