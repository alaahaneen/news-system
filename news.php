<?php
require_once 'includes/functions.php';
requireLogin();

$msg = $error = '';

// حذف خبر (soft delete)
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("UPDATE news SET deleted = 1 WHERE id = ?")->execute([$id]);
    $msg = 'تم حذف الخبر بنجاح';
}

// جلب جميع الأخبار الغير محذوفة
$news = $pdo->query("
    SELECT n.*, c.name as cat_name, u.name as user_name
    FROM news n
    JOIN categories c ON n.category_id = c.id
    JOIN users u ON n.user_id = u.id
    WHERE n.deleted = 0
    ORDER BY n.created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>الأخبار - نظام إدارة الأخبار</title>
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
    <div class="page-title">جميع الأخبار</div>

    <?php if ($msg): ?><div class="alert alert-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>

    <div class="card">
        <div class="card-header">
            <h3>قائمة الأخبار (<?= count($news) ?>)</h3>
            <a href="add_news.php" class="btn btn-success btn-sm">➕ إضافة خبر</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>الصورة</th>
                    <th>العنوان</th>
                    <th>الفئة</th>
                    <th>بواسطة</th>
                    <th>التاريخ</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($news)): ?>
                    <tr><td colspan="7" style="text-align:center; color:#999; padding:30px">لا توجد أخبار</td></tr>
                <?php else: ?>
                    <?php foreach ($news as $i => $n): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td>
                            <?php if ($n['image'] && file_exists('uploads/' . $n['image'])): ?>
                                <img src="uploads/<?= htmlspecialchars($n['image']) ?>" class="news-img" alt="">
                            <?php else: ?>
                                <div class="no-img">📰</div>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($n['title']) ?></td>
                        <td><span class="badge badge-primary"><?= htmlspecialchars($n['cat_name']) ?></span></td>
                        <td><?= htmlspecialchars($n['user_name']) ?></td>
                        <td><?= date('Y/m/d', strtotime($n['created_at'])) ?></td>
                        <td class="actions">
                            <a href="edit_news.php?id=<?= $n['id'] ?>" class="btn btn-warning btn-sm">✏️ تعديل</a>
                            <a href="?delete=<?= $n['id'] ?>" class="btn btn-danger btn-sm"
                               onclick="return confirm('هل أنت متأكد من حذف هذا الخبر؟')">🗑️ حذف</a>
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
