<?php
require_once 'includes/functions.php';
requireLogin();

$msg = $error = '';

// جلب الفئات
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title']);
    $category_id = (int)$_POST['category_id'];
    $details     = trim($_POST['details']);
    $user_id     = $_SESSION['user_id'];
    $image       = null;

    if (empty($title) || empty($category_id) || empty($details)) {
        $error = 'يرجى ملء جميع الحقول المطلوبة';
    } else {
        // رفع الصورة
        if (!empty($_FILES['image']['name'])) {
            $allowed = ['jpg','jpeg','png','gif','webp'];
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed)) {
                $error = 'نوع الصورة غير مسموح به. المسموح: jpg, png, gif, webp';
            } elseif ($_FILES['image']['size'] > 5 * 1024 * 1024) {
                $error = 'حجم الصورة كبير جداً (الحد الأقصى 5 ميجا)';
            } else {
                $image = uniqid() . '.' . $ext;
                move_uploaded_file($_FILES['image']['tmp_name'], 'uploads/' . $image);
            }
        }

        if (empty($error)) {
            $stmt = $pdo->prepare("INSERT INTO news (title, category_id, details, image, user_id) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$title, $category_id, $details, $image, $user_id]);
            $msg = 'تمت إضافة الخبر بنجاح!';
            $_POST = []; // clear form
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إضافة خبر - نظام إدارة الأخبار</title>
    <link rel="stylesheet" href="includes/style.css">
</head>
<body>

<nav class="navbar">
    <div class="brand">📰 نظام إدارة الأخبار</div>
    <div class="nav-links">
        <a href="dashboard.php">الرئيسية</a>
        <a href="categories.php">الفئات</a>
        <a href="news.php">الأخبار</a>
        <a href="add_news.php" class="active">إضافة خبر</a>
        <a href="deleted_news.php">المحذوفات</a>
    </div>
    <div class="user-info">
        <span>👤 <?= htmlspecialchars($_SESSION['user_name']) ?></span>
        <a href="logout.php" class="btn btn-danger btn-sm">خروج</a>
    </div>
</nav>

<div class="container">
    <div class="page-title">إضافة خبر جديد</div>

    <?php if ($msg): ?><div class="alert alert-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>

    <?php if (empty($categories)): ?>
        <div class="alert alert-danger">
            لا توجد فئات! يرجى <a href="categories.php">إضافة فئة</a> أولاً قبل إضافة خبر.
        </div>
    <?php else: ?>
    <div class="card">
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>عنوان الخبر *</label>
                <input type="text" name="title" placeholder="أدخل عنوان الخبر" value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label>الفئة *</label>
                <select name="category_id" required>
                    <option value="">-- اختر الفئة --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= (($_POST['category_id'] ?? '') == $cat['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>تفاصيل الخبر *</label>
                <textarea name="details" placeholder="أدخل تفاصيل الخبر كاملة" required><?= htmlspecialchars($_POST['details'] ?? '') ?></textarea>
            </div>
            <div class="form-group">
                <label>صورة الخبر (اختياري)</label>
                <input type="file" name="image" accept="image/*">
                <small style="color:#777; font-size:12px;">الصيغ المقبولة: jpg, png, gif, webp - الحد الأقصى: 5 ميجا</small>
            </div>
            <div style="display:flex; gap:12px;">
                <button type="submit" class="btn btn-success">➕ إضافة الخبر</button>
                <a href="news.php" class="btn btn-info">← العودة للأخبار</a>
            </div>
        </form>
    </div>
    <?php endif; ?>
</div>

</body>
</html>
