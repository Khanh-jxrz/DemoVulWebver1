<!-- <?php
require_once 'config.php';

$product_id = isset($_GET['id']) ? $_GET['id'] : 1;

// Lấy thông tin sản phẩm
$query = "SELECT * FROM products WHERE id = $product_id";
$result = mysqli_query($conn, $query);
$product = mysqli_fetch_assoc($result);

if (!$product) {
    die("Sản phẩm không tồn tại!");
}

// XSS strored
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isLoggedIn()) {
    $comment = $_POST['comment'];
    $user_id = $_SESSION['user_id'];
    
    // VULNERABLE: XSS Stored - không sanitize input
    $query = "INSERT INTO comments (product_id, user_id, comment) VALUES ($product_id, $user_id, '$comment')";
    mysqli_query($conn, $query);
    
    header("Location: product.php?id=$product_id");
    exit;
}

// Lấy comments
$comments_query = "SELECT c.*, u.username FROM comments c JOIN users u ON c.user_id = u.id WHERE c.product_id = $product_id ORDER BY c.created_at DESC";
$comments_result = mysqli_query($conn, $comments_query);
?> -->
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - VulnShop</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; }
        .header { background: #2c3e50; color: white; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .nav a { color: white; text-decoration: none; margin: 0 15px; }
        .product-detail { background: white; padding: 30px; border-radius: 8px; margin-bottom: 20px; }
        .product-detail img { max-width: 400px; border-radius: 8px; }
        .product-info { margin-top: 20px; }
        .price { color: #e74c3c; font-size: 24px; font-weight: bold; margin: 15px 0; }
        .comments-section { background: white; padding: 30px; border-radius: 8px; }
        .comment { border-bottom: 1px solid #eee; padding: 15px 0; }
        .comment-author { font-weight: bold; color: #3498db; }
        .comment-content { margin: 10px 0; }
        .comment-form textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; min-height: 100px; }
        .comment-form button { margin-top: 10px; padding: 10px 20px; background: #3498db; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .warning { background: #f39c12; color: white; padding: 10px; margin: 20px 0; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="container">
            <a href="index.php">← Quay lại</a>
        </div>
    </div>

    <div class="container">
        <div class="product-detail">
            <img src="images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" onerror="this.src='images/default.jpg'">
            <div class="product-info">
                <h1><?php echo htmlspecialchars($product['name']); ?></h1>
                <p class="price"><?php echo number_format($product['price']); ?> VNĐ</p>
                <p><?php echo htmlspecialchars($product['description']); ?></p>
            </div>
        </div>

        <div class="comments-section">
            <h2>Bình luận (<?php echo mysqli_num_rows($comments_result); ?>)</h2>
            
            <?php if (isLoggedIn()): ?>
                <form method="POST" class="comment-form">
                    <textarea name="comment" placeholder="Viết bình luận của bạn..." required></textarea>
                    <button type="submit">Gửi bình luận</button>
                </form>
            <?php else: ?>
                <p><a href="login.php">Đăng nhập</a> để bình luận</p>
            <?php endif; ?>

            <div style="margin-top: 30px;">
                <?php while ($comment = mysqli_fetch_assoc($comments_result)): ?>
                    <div class="comment">
                        <div class="comment-author"><?php echo htmlspecialchars($comment['username']); ?></div>
                        <div class="comment-content">
                            <!-- VULNERABLE: XSS Stored FLAG{flag1xss_ -->
                            <?php echo $comment['comment']; ?>
                        </div>
                        <small style="color: #999;"><?php echo $comment['created_at']; ?></small>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</body>
</html>