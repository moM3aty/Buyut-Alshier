<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'u315614949_BS');
define('DB_USER', 'u315614949_BS');
define('DB_PASS', '=5Dp6=dP1');
define('DB_CHARSET', 'utf8mb4');

// ===== روابط الموقع =====
define('SITE_URL', 'https://buyutalshier.com/');
define('SITE_ROOT',   dirname(__DIR__));
define('UPLOAD_PATH', SITE_ROOT . '/assets/uploads/');
define('UPLOAD_URL',  SITE_URL . 'assets/uploads/');

function get_pdo(): PDO {
    static $pdo = null;
    if ($pdo instanceof PDO) return $pdo;
    try {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        die('خطأ في الاتصال بقاعدة البيانات.');
    }
    return $pdo;
}

if (!is_dir(UPLOAD_PATH)) @mkdir(UPLOAD_PATH, 0755, true);