<?php
require_once 'includes/functions.php';
requireLogin();

$msg = $error = '';

// إضافة فئة
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_cat'])) {
    $name = trim($_POST['name']);
    if (empty($name)) {
        $error = 'يرجى إدخال اسم الفئة';
    } else {
        $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->execute([$name]);
        $msg = 'تمت إضافة الفئة بنجاح';
    }
}

// حذف فئة
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    // تحقق أن لا يوجد أخبار مرتبطة بها
    $count = $pdo->prepare("SELECT COUNT(*) FROM news WHERE category_id = ?");
    $count->execute([$id]);
    if ($count->fetchColumn() > 0) {
        $error = 'لا يمكن حذف هذه الفئة لأنها مرتبطة بأخبار';
    } else {
        $pdo->prepare("DELETE FROM categories WHERE id = ?")->execute([$id]);
        $msg = 'تم حذف الفئة بنجاح';
    }
}

// جلب كل الفئات
$categories = $pdo->query("
    SELECT c.*, COUNT(n.id) as news_count
    FROM categories c
    LEFT JOIN news n ON c.id = n.category_id AND n.deleted = 0
    GROUP BY c.id
    ORDER BY c.created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>الفئات - نظام إدارة الأخبار</title>
    <link rel="stylesheet" href="includes/style.css">
</head>
<body>

<nav class="navbar">
    <div class="brand">📰 نظام إدارة الأخبار</div>
    <div class="nav-links">
        <a href="dashboard.php">الرئيسية</a>
        <a href="categories.php" class="active">الفئات</a>
        <a href="news.php">الأخبار</a>
        <a href="add_news.php">إضافة خبر</a>
        <a href="deleted_news.php">المحذوفات</a>
    </div>
    <div class="user-info">
        <span>👤 <?= htmlspecialchars($_SESSION['user_name']) ?></span>
        <a href="logout.php" class="btn btn-danger btn-sm">خروج</a>
    </div>
</nav>

<div class="container">
    <div class="page-title">إدارة الفئات</div>

    <?php if ($msg): ?><div class="alert alert-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>

    <!-- إضافة فئة -->
    <div class="card">
        <div class="card-header"><h3>إضافة فئة جديدة</h3></div>
        <form method="POST" style="display:flex; gap:12px; align-items:flex-end;">
            <div class="form-group" style="flex:1; margin:0;">
                <label>اسم الفئة</label>
                <input type="text" name="name" placeholder="مثال: أخبار رياضية" required>
            </div>
            <button type="submit" name="add_cat" class="btn btn-success">إضافة</button>
        </form>
    </div>

    <!-- جدول الفئات -->
    <div class="card">
        <div class="card-header"><h3>جميع الفئات (<?= count($categories) ?>)</h3></div>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>اسم الفئة</th>
                    <th>عدد الأخبار</th>
                    <th>تاريخ الإنشاء</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($categories)): ?>
                    <tr><td colspan="5" style="text-align:center; color:#999; padding:30px">لا توجد فئات</td></tr>
                <?php else: ?>
                    <?php foreach ($categories as $i => $cat): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= htmlspecialchars($cat['name']) ?></td>
                        <td><span class="badge badge-primary"><?= $cat['news_count'] ?> خبر</span></td>
                        <td><?= date('Y/m/d', strtotime($cat['created_at'])) ?></td>
                        <td class="actions">
                            <a href="?delete=<?= $cat['id'] ?>" class="btn btn-danger btn-sm"
                               onclick="return confirm('هل أنت متأكد من حذف هذه الفئة؟')">🗑️ حذف</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
