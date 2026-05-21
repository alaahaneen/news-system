<?php
require_once 'includes/functions.php';
requireLogin();

// إحصائيات
$totalNews     = $pdo->query("SELECT COUNT(*) FROM news WHERE deleted = 0")->fetchColumn();
$totalCats     = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
$deletedNews   = $pdo->query("SELECT COUNT(*) FROM news WHERE deleted = 1")->fetchColumn();
$totalUsers    = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

// آخر 5 أخبار
$latestNews = $pdo->query("
    SELECT n.*, c.name as cat_name, u.name as user_name
    FROM news n
    JOIN categories c ON n.category_id = c.id
    JOIN users u ON n.user_id = u.id
    WHERE n.deleted = 0
    ORDER BY n.created_at DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم - نظام إدارة الأخبار</title>
    <link rel="stylesheet" href="includes/style.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar">
    <div class="brand">📰 نظام إدارة الأخبار</div>
    <div class="nav-links">
        <a href="dashboard.php" class="<?= getCurrentPage() === 'dashboard.php' ? 'active' : '' ?>">الرئيسية</a>
        <a href="categories.php" class="<?= getCurrentPage() === 'categories.php' ? 'active' : '' ?>">الفئات</a>
        <a href="news.php" class="<?= getCurrentPage() === 'news.php' ? 'active' : '' ?>">الأخبار</a>
        <a href="add_news.php" class="<?= getCurrentPage() === 'add_news.php' ? 'active' : '' ?>">إضافة خبر</a>
        <a href="deleted_news.php" class="<?= getCurrentPage() === 'deleted_news.php' ? 'active' : '' ?>">المحذوفات</a>
    </div>
    <div class="user-info">
        <span>👤 <?= htmlspecialchars($_SESSION['user_name']) ?></span>
        <a href="logout.php" class="btn btn-danger btn-sm">خروج</a>
    </div>
</nav>

<div class="container">
    <div class="page-title">لوحة التحكم</div>

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="number"><?= $totalNews ?></div>
            <div class="label">إجمالي الأخبار</div>
        </div>
        <div class="stat-card green">
            <div class="number"><?= $totalCats ?></div>
            <div class="label">الفئات</div>
        </div>
        <div class="stat-card red">
            <div class="number"><?= $deletedNews ?></div>
            <div class="label">الأخبار المحذوفة</div>
        </div>
        <div class="stat-card orange">
            <div class="number"><?= $totalUsers ?></div>
            <div class="label">المستخدمون</div>
        </div>
    </div>

    <!-- Latest News -->
    <div class="card">
        <div class="card-header">
            <h3>آخر الأخبار المضافة</h3>
            <a href="news.php" class="btn btn-info btn-sm">عرض الكل</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>العنوان</th>
                    <th>الفئة</th>
                    <th>بواسطة</th>
                    <th>التاريخ</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($latestNews)): ?>
                    <tr><td colspan="5" style="text-align:center; color:#999; padding:30px">لا توجد أخبار بعد</td></tr>
                <?php else: ?>
                    <?php foreach ($latestNews as $i => $n): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= htmlspecialchars($n['title']) ?></td>
                        <td><span class="badge badge-primary"><?= htmlspecialchars($n['cat_name']) ?></span></td>
                        <td><?= htmlspecialchars($n['user_name']) ?></td>
                        <td><?= date('Y/m/d', strtotime($n['created_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Quick Links -->
    <div class="card">
        <div class="card-header"><h3>روابط سريعة</h3></div>
        <div style="display:flex; gap:12px; flex-wrap:wrap;">
            <a href="add_news.php" class="btn btn-success">➕ إضافة خبر جديد</a>
            <a href="categories.php" class="btn btn-info">📂 إدارة الفئات</a>
            <a href="news.php" class="btn btn-primary">📰 عرض جميع الأخبار</a>
            <a href="deleted_news.php" class="btn btn-danger">🗑️ الأخبار المحذوفة</a>
        </div>
    </div>
</div>

</body>
</html>
