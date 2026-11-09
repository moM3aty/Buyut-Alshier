<?php
require_once __DIR__ . '/includes/init.php';
require_login();
$pageTitle = 'لوحة التحكم';
$pageSubtitle = 'أهلاً بك مجدداً، إليك نظرة سريعة على موقعك';

$pdo = get_pdo();
$stats = [
    'services'  => (int)$pdo->query("SELECT COUNT(*) FROM services WHERE is_active=1")->fetchColumn(),
    'gallery'   => (int)$pdo->query("SELECT COUNT(*) FROM gallery WHERE is_active=1")->fetchColumn(),
    'occasions' => (int)$pdo->query("SELECT COUNT(*) FROM occasions WHERE is_active=1")->fetchColumn(),
    'unread'    => (int)$pdo->query("SELECT COUNT(*) FROM messages WHERE is_read=0")->fetchColumn(),
];
$recent = $pdo->query("SELECT * FROM messages ORDER BY created_at DESC LIMIT 5")->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<!-- كروت الإحصائيات -->
<div class="stats-grid animate-up">
  <a href="services.php" class="stat-card">
    <div class="stat-icon bg-green">
      <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
    </div>
    <div class="stat-info">
      <b><?= $stats['services'] ?></b>
      <span>خدمة نشطة</span>
    </div>
  </a>
  
  <a href="occasions.php" class="stat-card">
    <div class="stat-icon bg-blue">
      <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
    </div>
    <div class="stat-info">
      <b><?= $stats['occasions'] ?></b>
      <span>مناسبة مسجلة</span>
    </div>
  </a>

  <a href="gallery.php" class="stat-card">
    <div class="stat-icon bg-gold">
      <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="9" cy="9" r="2"/><path d="M21 15l-5-5L5 21"/></svg>
    </div>
    <div class="stat-info">
      <b><?= $stats['gallery'] ?></b>
      <span>صورة في المعرض</span>
    </div>
  </a>

  <a href="messages.php" class="stat-card">
    <div class="stat-icon bg-red">
      <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
    </div>
    <div class="stat-info">
      <b><?= $stats['unread'] ?></b>
      <span>رسالة غير مقروءة</span>
    </div>
  </a>
</div>

<!-- أحدث الرسائل -->
<div class="panel animate-up delay-1">
  <div class="panel-head">
    <h3>أحدث الرسائل الواردة</h3>
    <a href="messages.php" class="btn btn-outline btn-sm">عرض كل الرسائل</a>
  </div>
  
  <div class="filter-bar">
    <div class="search-box">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      <input type="text" id="liveSearch" placeholder="ابحث في الرسائل الأخيرة...">
    </div>
  </div>

  <div class="table-responsive">
    <table class="table">
      <thead>
        <tr>
          <th>المرسل</th>
          <th>رقم الجوال</th>
          <th>الرسالة</th>
          <th>التاريخ</th>
          <th>الحالة</th>
        </tr>
      </thead>
      <tbody>
        <?php if(empty($recent)): ?>
          <tr><td colspan="5" style="text-align:center;padding:30px;color:#64748b;">لا توجد رسائل حديثة</td></tr>
        <?php else: ?>
          <?php foreach($recent as $m): ?>
          <tr class="searchable-row">
            <td><strong><?= htmlspecialchars($m['name']) ?></strong></td>
            <td dir="ltr"><?= htmlspecialchars($m['phone']) ?></td>
            <td><?= htmlspecialchars(mb_substr($m['message'], 0, 50)) ?>...</td>
            <td><?= date('Y-m-d', strtotime($m['created_at'])) ?></td>
            <td>
              <?php if($m['is_read']): ?>
                <span style="color:#10b981;font-size:12px;font-weight:700;">✓ تم القراءة</span>
              <?php else: ?>
                <span style="color:#f59e0b;font-size:12px;font-weight:700;">● جديدة</span>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>