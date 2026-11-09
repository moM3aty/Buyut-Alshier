<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// المسار الصحيح للوصول لمجلد includes الرئيسي
require_once __DIR__ . '/../../includes/functions.php';

try {
    $pdo = get_pdo();
    $pdo->query("SELECT 1");
} catch (Exception $e) {
    die('خطأ في الاتصال بقاعدة البيانات: ' . htmlspecialchars($e->getMessage()));
}

if (!function_exists('flash')) {
    function flash(string $key, ?string $msg = null) {
        if ($msg !== null) { $_SESSION['flash'][$key] = $msg; return null; }
        $v = $_SESSION['flash'][$key] ?? null; unset($_SESSION['flash'][$key]); return $v;
    }
}
if (!function_exists('csrf_field')) {
    function csrf_token(): string {
        if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
        return $_SESSION['csrf'];
    }
    function csrf_field(): string { return '<input type="hidden" name="_csrf" value="' . csrf_token() . '">'; }
    function verify_csrf(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        $t = $_POST['_csrf'] ?? '';
        if (!$t || !hash_equals($_SESSION['csrf'] ?? '', $t)) { http_response_code(419); die('انتهت صلاحية الجلسة.'); }
    }
}
function admin_url(string $path = ''): string { return SITE_URL . 'admin/' . ltrim($path, '/'); }