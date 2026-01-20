<?php
require_once 'config.php';

if (!isLoggedIn() || !isAdmin()) {
    die("Access Denied! You must be admin.");
}

// SSRF Vulnerability
$ssrf_result = '';
if (isset($_GET['fetch_url'])) {
    $url = $_GET['fetch_url'];
    
    // VULNERABLE: SSRF - không filter internal URLs
    $ssrf_result = @file_get_contents($url);
    
    if ($ssrf_result === false) {
        $ssrf_result = "Failed to fetch URL: $url";
    }
}

// Lấy danh sách users
$users_query = "SELECT id, username, email, role, created_at FROM users";
$users_result = mysqli_query($conn, $users_query);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - VulnShop</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; }
        .header { background: #c0392b; color: white; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .nav a { color: white; text-decoration: none; margin: 0 15px; }
        .panel { background: white; padding: 30px; border-radius: 8px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #34495e; color: white; }
        .ssrf-form { margin-top: 20px; }
        .ssrf-form input { width: 70%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        .ssrf-form button { padding: 10px 20px; background: #e74c3c; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .ssrf-result { margin-top: 20px; padding: 15px; background: #ecf0f1; border-radius: 4px; white-space: pre-wrap; word-wrap: break-word; max-height: 400px; overflow-y: auto; }
        .warning { background: #e74c3c; color: white; padding: 10px; margin: 20px 0; border-radius: 4px; }
        .hint { background: #3498db; color: white; padding: 10px; margin: 10px 0; border-radius: 4px; font-size: 14px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="container">
            <div class="nav">
                <div>
                    <strong>👑 Admin Panel</strong>
                    <a href="index.php">Trang chủ</a>
                    <a href="profile.php">Hồ sơ</a>
                </div>
                <div>
                    <a href="logout.php">Đăng xuất</a>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="warning">
            🔥 <strong>ADMIN AREA:</strong> Chỉ dành cho quản trị viên.
        </div>

        <div class="panel">
            <h2>Danh sách người dùng</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user = mysqli_fetch_assoc($users_result)): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo $user['role'] === 'admin' ? '👑 Admin' : '👤 User'; ?></td>
                            <td><?php echo $user['created_at']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="panel">
            <h2>🌐 URL Fetcher </h2>          
            <form method="GET" class="ssrf-form">
                <input type="text" name="fetch_url" placeholder="Enter URL to fetch..." value="<?php echo isset($_GET['fetch_url']) ? htmlspecialchars($_GET['fetch_url']) : ''; ?>">
                <button type="submit">Fetch URL</button>
            </form>

            <?php if ($ssrf_result): ?>
                <div class="ssrf-result">
                    <strong>Result:</strong><br>
                    <?php echo htmlspecialchars($ssrf_result); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>