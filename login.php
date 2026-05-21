<?php
require_once 'includes/functions.php';
if (isLoggedIn()) { header('Location: dashboard.php'); exit; }

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = 'يرجى ملء جميع الحقول';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && md5($password) === $user['password']) {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'البريد الإلكتروني أو كلمة المرور غير صحيحة';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - نظام إدارة الأخبار</title>
    <link rel="stylesheet" href="includes/style.css">
</head>
<body>
<div class="auth-container">
    <div class="auth-box">
        <div class="logo">📰</div>
        <h2>تسجيل الدخول</h2>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>البريد الإلكتروني</label>
                <input type="email" name="email" placeholder="example@email.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label>كلمة المرور</label>
                <input type="password" name="password" placeholder="أدخل كلمة المرور" required>
            </div>
            <button type="submit" class="btn btn-primary">تسجيل الدخول</button>
        </form>

        <div class="auth-link">
            ليس لديك حساب؟ <a href="register.php">إنشاء حساب جديد</a>
        </div>
    </div>
</div>
</body>
</html>
