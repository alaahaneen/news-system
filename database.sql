-- إنشاء قاعدة البيانات
CREATE DATABASE IF NOT EXISTS news_system CHARACTER SET utf8 COLLATE utf8_general_ci;
USE news_system;

-- جدول المستخدمين
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- جدول الفئات
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- جدول الأخبار
CREATE TABLE IF NOT EXISTS news (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    category_id INT NOT NULL,
    details TEXT NOT NULL,
    image VARCHAR(255),
    user_id INT NOT NULL,
    deleted TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- بيانات تجريبية - مستخدم افتراضي (password: 123456)
INSERT INTO users (name, email, password) VALUES 
('المدير', 'admin@news.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- فئات تجريبية
INSERT INTO categories (name) VALUES 
('أخبار رياضية'),
('أخبار سياسية'),
('أخبار اقتصادية');

-- أخبار تجريبية
INSERT INTO news (title, category_id, details, image, user_id) VALUES
('الهلال يفوز ببطولة الدوري', 1, 'فاز فريق الهلال ببطولة الدوري السعودي للمحترفين بعد موسم مميز.', NULL, 1),
('اجتماع قمة دولية', 2, 'عُقد اجتماع القمة الدولية لمناقشة القضايا الراهنة.', NULL, 1),
('ارتفاع أسعار النفط', 3, 'شهدت أسعار النفط ارتفاعاً ملحوظاً في الأسواق العالمية.', NULL, 1);
