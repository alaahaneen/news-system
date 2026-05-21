<?php
require_once 'includes/functions.php';
if (isLoggedIn()) { header('Location: dashboard.php'); exit; }

$error = $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($name) || empty($email) || empty($password)) {
        $error = 'يرجى ملء جميع الحقول';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'البريد الإلكتروني مستخدم مسبقاً';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $hashed]);
            $success = 'تم إنشاء الحساب بنجاح! يمكنك الآن تسجيل الدخول.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إنشاء حساب - نظام إدارة الأخبار</title>
    <link rel="stylesheet" href="includes/style.css">
</head>
<body>
<div class="auth-container">
    <div class="auth-box">
        <div class="logo">📰</div>
        <h2>إنشاء حساب جديد</h2>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>الاسم الكامل</label>
                <input type="text" name="name" placeholder="أدخل اسمك" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label>البريد الإلكتروني</label>
                <input type="email" name="email" placeholder="example@email.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label>كلمة المرور</label>
                <input type="password" name="password" placeholder="أدخل كلمة المرور" required>
            </div>
            <button type="submit" class="btn btn-primary">إنشاء الحساب</button>
        </form>

        <div class="auth-link">
            لديك حساب؟ <a href="login.php">تسجيل الدخول</a>
        </div>
    </div>
</div>
</body>
</html>
