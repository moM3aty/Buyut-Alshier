<?php
$user = $_SESSION['user'];
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$unread = 0;
try { $unread = (int)get_pdo()->query("SELECT COUNT(*) FROM messages WHERE is_read=0")->fetchColumn(); } catch (Exception $e) {}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($pageTitle ?? 'لوحة التحكم') ?> | بيت الشعر</title>
<link rel="stylesheet" href="../assets/css/admin.css?v=<?= time() ?>">
</head>
<body>

<aside class="sidebar" id="sidebar">
  <div class="sidebar-head">
    <div class="brand-icon">ب</div>
    <div class="brand-text">
      <strong>بيت الشعر</strong>
      <small>لوحة التحكم</small>
    </div>
    <button class="sidebar-close" data-toggle="sidebar">✕</button>
  </div>

  <nav class="sidebar-nav">
    <div class="nav-section">الرئيسية</div>
    <a href="index.php" class="<?= $currentPage==='index'?'active':'' ?>">لوحة التحكم</a>
    <a href="messages.php" class="<?= $currentPage==='messages'?'active':'' ?>">
      الرسائل <?php if ($unread): ?><span class="nav-badge"><?= $unread ?></span><?php endif; ?>
    </a>

    <div class="nav-section">المحتوى</div>
    <a href="services.php" class="<?= $currentPage==='services'?'active':'' ?>">الخدمات</a>
    <a href="occasions.php" class="<?= $currentPage==='occasions'?'active':'' ?>">المناسبات</a>
    <a href="gallery.php" class="<?= $currentPage==='gallery'?'active':'' ?>">معرض الصور</a>
    <a href="faqs.php" class="<?= $currentPage==='faqs'?'active':'' ?>">الأسئلة الشائعة</a>

    <div class="nav-section">الإدارة</div>
    <a href="settings.php" class="<?= $currentPage==='settings'?'active':'' ?>">إعدادات الموقع</a>
    <?php if (($user['role'] ?? '') === 'admin'): ?>
    <a href="users.php" class="<?= $currentPage==='users'?'active':'' ?>">المستخدمون</a>
    <?php endif; ?>
  </nav>

  <div class="sidebar-foot">
    <div class="user-card">
      <div class="user-avatar"><?= htmlspecialchars(mb_substr($user['name'],0,1)) ?></div>
      <div class="user-info">
        <strong><?= htmlspecialchars($user['name']) ?></strong>
        <small><?= $user['role']==='admin' ? 'مدير' : 'محرر' ?></small>
      </div>
    </div>
    <a href="logout.php" class="logout-link">تسجيل الخروج</a>
  </div>
</aside>
<div class="sidebar-overlay"></div>

<main class="main">
  <header class="topbar">
    <div style="display:flex;align-items:center;gap:15px">
      <button class="mobile-menu-btn" data-toggle="sidebar">☰</button>
      <div class="topbar-title">
        <h1><?= htmlspecialchars($pageTitle ?? 'لوحة التحكم') ?></h1>
        <p><?= htmlspecialchars($pageSubtitle ?? '') ?></p>
      </div>
    </div>
    <div class="topbar-actions">
      <a href="<?= SITE_URL ?>" target="_blank" class="btn btn-outline btn-sm">زيارة الموقع ↗</a>
    </div>
  </header>
  <div class="content animate-up">