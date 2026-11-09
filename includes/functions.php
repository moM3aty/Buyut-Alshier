<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
require_once __DIR__ . '/../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => isset($_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

/* ===== Helpers عامة ===== */
function e(?string $v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function redirect(string $url): void { header("Location: $url"); exit; }
function flash(string $key, ?string $msg = null) {
    if ($msg !== null) { $_SESSION['flash'][$key] = $msg; return; }
    $v = $_SESSION['flash'][$key] ?? null; unset($_SESSION['flash'][$key]); return $v;
}

/* ===== CSRF ===== */
function csrf_token(): string {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function csrf_field(): string {
    return '<input type="hidden" name="_csrf" value="' . csrf_token() . '">';
}
function verify_csrf(): void {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
    $t = $_POST['_csrf'] ?? '';
    if (!$t || !hash_equals($_SESSION['csrf'] ?? '', $t)) {
        http_response_code(419); die('انتهت صلاحية الجلسة. أعد المحاولة.');
    }
}

/* ===== Auth ===== */
function auth_user(): ?array { return $_SESSION['user'] ?? null; }
function is_logged_in(): bool { return !empty($_SESSION['user']['id']); }
function require_login(): void {
    if (!is_logged_in()) redirect(SITE_URL . 'admin/login.php');
}
function is_admin(): bool {
    return is_logged_in() && ($_SESSION['user']['role'] ?? '') === 'admin';
}

/* ===== Settings ===== */
function get_setting(string $key, string $default = ''): string {
    static $cache = null;
    if ($cache === null) {
        $cache = [];
        foreach (get_pdo()->query("SELECT setting_key, setting_value FROM settings") as $r) {
            $cache[$r['setting_key']] = $r['setting_value'];
        }
    }
    return $cache[$key] ?? $default;
}
function set_setting(string $key, string $value): void {
    $stmt = get_pdo()->prepare(
        "INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)
         ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)"
    );
    $stmt->execute([$key, $value]);
}

function upload_image(array $file, string $prefix = 'img'): ?string {
    if (!isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) return null;
    if ($file['size'] > 5 * 1024 * 1024) return null;
    
    // فحص MIME بطريقة بديلة (fallback لو finfo مش متاح)
    $mime = '';
    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
    } elseif (function_exists('mime_content_type')) {
        $mime = mime_content_type($file['tmp_name']);
    } else {
        // fallback من الامتداد
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $map = ['jpg'=>'image/jpeg','jpeg'=>'image/jpeg','png'=>'image/png','webp'=>'image/webp','gif'=>'image/gif'];
        $mime = $map[$ext] ?? '';
    }
    
    $allowed = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp','image/gif'=>'gif'];
    if (!isset($allowed[$mime])) return null;
    
    $ext = $allowed[$mime];
    $name = $prefix . '_' . date('YmdHis') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    if (!move_uploaded_file($file['tmp_name'], UPLOAD_PATH . $name)) return null;
    return 'assets/uploads/' . $name;
}

/* ===== Render helpers لـ admin ===== */
function page_title(string $t): string { return e($t) . ' | لوحة التحكم'; }