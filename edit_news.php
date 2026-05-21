<?php
require_once 'includes/functions.php';
requireLogin();

$msg = $error = '';

// جلب الخبر
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT * FROM news WHERE id = ? AND deleted = 0");
$stmt->execute([$id]);
$newsItem = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$newsItem) {
    header('Location: news.php');
    exit;
}

// جلب الفئات
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title']);
    $category_id = (int)$_POST['category_id'];
    $details     = trim($_POST['details']);
    $image       = $newsItem['image']; // keep old image

    if (empty($title) || empty($category_id) || empty($details)) {
        $error = 'يرجى ملء جميع الحقول المطلوبة';
    } else {
        // رفع صورة جديدة
        if (!empty($_FILES['image']['name'])) {
            $allowed = ['jpg','jpeg','png','gif','webp'];
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed)) {
                $error = 'نوع الصورة غير مسموح به';
            } elseif ($_FILES['image']['size'] > 5 * 1024 * 1024) {
                $error = 'حجم الصورة كبير جداً';
            } else {
                // حذف الصورة القديمة
                if ($newsItem['image'] && file_exists('uploads/' . $newsItem['image'])) {
                    unlink('uploads/' . $newsItem['image']);
                }
                $image = uniqid() . '.' . $ext;
                move_uploaded_file($_FILES['image']['tmp_name'], 'uploads/' . $image);
            }
        }

        if (empty($error)) {
            $stmt = $pdo->prepare("UPDATE news SET title=?, category_id=?, details=?, image=? WHERE id=?");
            $stmt->execute([$title, $category_id, $details, $image, $id]);
            $msg = 'تم تعديل الخبر بنجاح!';
            // تحديث البيانات المحلية
            $newsItem['title']       = $title;
            $newsItem['category_id'] = $category_id;
            $newsItem['details']     = $details;
            $newsItem['image']       = $image;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تعديل خبر - نظام إدارة الأخبار</title>
    <link rel="stylesheet" href="includes/style.css">
</head>
<body>

<nav class="navbar">
    <div class="brand">📰 نظام إدارة الأخبار</div>
    <div class="nav-links">
        <a href="dashboard.php">الرئيسية</a>
        <a href="categories.php">الفئات</a>
        <a href="news.php" class="active">الأخبار</a>
        <a href="add_news.php">إضافة خبر</a>
        <a href="deleted_news.php">المحذوفات</a>
    </div>
    <div class="user-info">
        <span>👤 <?= htmlspecialchars($_SESSION['user_name']) ?></span>
        <a href="logout.php" class="btn btn-danger btn-sm">خروج</a>
    </div>
</nav>

<div class="container">
    <div class="page-title">تعديل الخبر</div>

    <?php if ($msg): ?><div class="alert alert-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>

    <div class="card">
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>عنوان الخبر *</label>
                <input type="text" name="title" value="<?= htmlspecialchars($newsItem['title']) ?>" required>
            </div>
            <div class="form-group">
                <label>الفئة *</label>
                <select name="category_id" required>
                    <option value="">-- اختر الفئة --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= $newsItem['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>تفاصيل الخبر *</label>
                <textarea name="details" required><?= htmlspecialchars($newsItem['details']) ?></textarea>
            </div>
            <div class="form-group">
                <label>صورة الخبر</label>
                <?php if ($newsItem['image'] && file_exists('uploads/' . $newsItem['image'])): ?>
                    <div style="margin-bottom:10px;">
                        <img src="uploads/<?= htmlspecialchars($newsItem['image']) ?>" style="height:80px; border-radius:8px;" alt="">
                        <small style="display:block; color:#777; margin-top:5px;">الصورة الحالية - ارفع صورة جديدة لاستبدالها</small>
                    </div>
                <?php endif; ?>
                <input type="file" name="image" accept="image/*">
            </div>
            <div style="display:flex; gap:12px;">
                <button type="submit" class="btn btn-warning">💾 حفظ التعديلات</button>
                <a href="news.php" class="btn btn-info">← العودة للأخبار</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>
