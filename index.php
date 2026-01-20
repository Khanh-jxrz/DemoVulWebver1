<?php
require_once 'config.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';
$error_msg = '';

$products = [];
if ($search) {
    // VULNERABLE: SQL Injection Union-based
    $query = "SELECT id, name, description, price, image FROM products WHERE name LIKE '%$search%' OR description LIKE '%$search%'";
    $result = mysqli_query($conn, $query);
    
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $products[] = $row;
        }
    } else {
        $error_msg = mysqli_error($conn);
    }
} else {
    $result = mysqli_query($conn, "SELECT id, name, description, price, image FROM products LIMIT 10");
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VulnShop - Cửa hàng điện tử</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; }
        .header { background: #2c3e50; color: white; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .nav { display: flex; justify-content: space-between; align-items: center; }
        .nav a { color: white; text-decoration: none; margin: 0 15px; }
        .search-box { margin: 20px 0; }
        .search-box input { padding: 10px; width: 300px; border: 1px solid #ddd; }
        .search-box button { padding: 10px 20px; background: #3498db; color: white; border: none; cursor: pointer; }
        .products { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; margin-top: 20px; }
        .product-card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .product-card img { width: 100%; height: 200px; object-fit: cover; border-radius: 4px; }
        .product-card h3 { margin: 10px 0; color: #2c3e50; }
        .product-card .price { color: #e74c3c; font-size: 20px; font-weight: bold; }
        .user-info { color: #ecf0f1; }
        .warning { background: #f39c12; color: white; padding: 10px; margin: 20px 0; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="container">
            <div class="nav">
                <div>
                    <a href="index.php"><strong>VulnShop</strong></a>
                    <a href="index.php">Trang chủ</a>
                    <?php if (isLoggedIn()): ?>
                        <a href="profile.php">Hồ sơ</a>
                        <?php if (isAdmin()): ?>
                            <a href="admin.php">Admin Panel</a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                <div class="user-info">
                    <?php if (isLoggedIn()): ?>
                        Xin chào, <?php echo $_SESSION['username']; ?> |
                        <a href="logout.php">Đăng xuất</a>
                    <?php else: ?>
                        <a href="login.php">Đăng nhập</a> |
                        <a href="register.php">Đăng ký</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="search-box">
            <form method="GET">
                <input type="text" name="search" placeholder="Tìm kiếm sản phẩm..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit">Tìm kiếm</button>
            </form>
            <?php if ($search): ?>
                <!-- XSS Reflected Vulnerability -->
                <p>Kết quả tìm kiếm cho: <strong><?php echo $search; ?></strong></p>
            <?php endif; ?>
            <?php if ($error_msg): ?>
                <p style="color: #e74c3c; margin-top: 10px;"><strong>SQL Error:</strong> <?php echo htmlspecialchars($error_msg); ?></p>
            <?php endif; ?>
        </div>

        <div class="products">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <img src="images/<?php echo htmlspecialchars($product['image']); ?>" alt="Product" onerror="this.src='images/default.jpg'">
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p><?php echo htmlspecialchars(substr($product['description'], 0, 100)); ?>...</p>
                        <p class="price"><?php echo number_format($product['price']); ?> VNĐ</p>
                        <?php if (isset($product['id']) && is_numeric($product['id'])): ?>
                            <a href="product.php?id=<?php echo $product['id']; ?>">Xem chi tiết</a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <?php if (empty($products) && $search && !$error_msg): ?>
            <p>Không tìm thấy sản phẩm nào.</p>
        <?php endif; ?>
    </div>
</body>
</html>