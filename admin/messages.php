<?php
require_once __DIR__ . '/includes/init.php';
require_login();

$pageTitle = 'صندوق الرسائل';
$pageSubtitle = 'استعرض رسائل العملاء وتواصل معهم';
$pdo = get_pdo();

// 1. معالجة الحذف
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['do'] ?? '') === 'delete') {
    verify_csrf();
    $pdo->prepare("DELETE FROM messages WHERE id=?")->execute([(int)$_POST['id']]);
    flash('ok', 'تم حذف الرسالة بنجاح');
    header('Location: messages.php'); exit;
}

// 2. عرض رسالة محددة (وتحويلها لمقروءة)
$view = null;
if (!empty($_GET['id'])) {
    $s = $pdo->prepare("SELECT * FROM messages WHERE id=?");
    $s->execute([(int)$_GET['id']]);
    $view = $s->fetch();
    
    if ($view && !$view['is_read']) {
        $pdo->prepare("UPDATE messages SET is_read=1 WHERE id=?")->execute([$view['id']]);
        $view['is_read'] = 1;
    }
}

// 3. جلب كل الرسائل
$items = $pdo->query("SELECT * FROM messages ORDER BY created_at DESC")->fetchAll();
$msg = flash('ok');

include __DIR__ . '/includes/header.php';
?>

<?php if ($msg): ?><div class="alert success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

<?php if ($view): ?>
<!-- ================= عرض رسالة واحدة ================= -->
<div class="panel animate-up" style="background: #fff; border-right: 4px solid var(--primary);">
  <div class="panel-head">
    <div>
      <h3 style="font-size: 18px; margin-bottom: 5px;">رسالة من: <?= htmlspecialchars($view['name']) ?></h3>
      <span style="color: var(--muted); font-size: 13px;"><?= date('Y-m-d \a\t H:i', strtotime($view['created_at'])) ?></span>
    </div>
    <a href="messages.php" class="btn btn-outline btn-sm">← رجوع للصندوق</a>
  </div>
  
  <div style="padding: 10px 0;">
    <div style="display: flex; gap: 20px; margin-bottom: 20px; background: #f8fafc; padding: 15px; border-radius: 10px;">
      <div>
        <span style="display:block; font-size:11px; color:var(--muted); font-weight:800;">رقم الجوال</span>
        <a href="tel:<?= htmlspecialchars($view['phone']) ?>" dir="ltr" style="color:var(--primary); font-weight:800; font-size:15px;"><?= htmlspecialchars($view['phone']) ?></a>
      </div>
      <?php if($view['email']): ?>
      <div>
        <span style="display:block; font-size:11px; color:var(--muted); font-weight:800;">البريد الإلكتروني</span>
        <a href="mailto:<?= htmlspecialchars($view['email']) ?>" style="color:var(--primary); font-weight:800; font-size:15px;"><?= htmlspecialchars($view['email']) ?></a>
      </div>
      <?php endif; ?>
    </div>

    <div style="background: #fff; border: 1px solid var(--border); padding: 20px; border-radius: 12px; font-size: 15px; line-height: 1.8; color: #334155;">
      <?= nl2br(htmlspecialchars($view['message'])) ?>
    </div>
  </div>

  <div class="form-actions" style="border:none; padding-top:20px; justify-content: flex-start;">
    <a href="https://wa.me/<?= preg_replace('/\D/', '', $view['phone']) ?>" target="_blank" class="btn btn-primary">
      الرد عبر واتساب
    </a>
    <form method="POST" data-confirm="حذف الرسالة؟">
      <?= csrf_field() ?>
      <input type="hidden" name="do" value="delete">
      <input type="hidden" name="id" value="<?= (int)$view['id'] ?>">
      <button type="submit" class="btn btn-danger">حذف الرسالة</button>
    </form>
  </div>
</div>

<?php else: ?>
<!-- ================= صندوق الرسائل (القائمة) ================= -->
<div class="panel animate-up delay-1">
  <div class="panel-head">
    <h3>كل الرسائل (<?= count($items) ?>)</h3>
  </div>
  
  <!-- شريط البحث -->
  <div class="filter-bar">
    <div class="search-box">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      <input type="text" id="liveSearch" placeholder="ابحث بالاسم، رقم الجوال، أو نص الرسالة...">
    </div>
  </div>

  <div class="table-responsive">
    <table class="table">
      <thead>
        <tr>
          <th style="width: 50px;">الحالة</th>
          <th>المرسل</th>
          <th>رقم الجوال</th>
          <th>نص الرسالة</th>
          <th>التاريخ</th>
          <th>إجراءات</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($items)): ?>
          <tr><td colspan="6" style="text-align:center; padding: 40px; color: #64748b;">لا توجد رسائل في الصندوق حالياً.</td></tr>
        <?php else: ?>
          <?php foreach ($items as $it): ?>
          <tr class="searchable-row" style="<?= $it['is_read'] ? '' : 'background: #f8fafc; font-weight: 600;' ?>">
            <td>
              <?php if ($it['is_read']): ?>
                <span style="display:inline-block; width:10px; height:10px; border-radius:50%; background:#cbd5e1;"></span>
              <?php else: ?>
                <span style="display:inline-block; width:10px; height:10px; border-radius:50%; background:var(--primary); box-shadow: 0 0 8px rgba(16,185,129,0.5);"></span>
              <?php endif; ?>
            </td>
            <td>
              <strong style="color: var(--ink);"><?= htmlspecialchars($it['name']) ?></strong>
            </td>
            <td dir="ltr" style="text-align: right;"><?= htmlspecialchars($it['phone']) ?></td>
            <td><?= htmlspecialchars(mb_substr($it['message'], 0, 70)) ?>...</td>
            <td style="font-size: 12px; color: var(--muted);"><?= date('Y-m-d', strtotime($it['created_at'])) ?></td>
            <td>
              <a href="?id=<?= (int)$it['id'] ?>" class="btn btn-primary btn-sm" style="padding: 6px 12px;">قراءة</a>
              <form method="POST" data-confirm="حذف الرسالة؟" style="display:inline-block;">
                <?= csrf_field() ?>
                <input type="hidden" name="do" value="delete">
                <input type="hidden" name="id" value="<?= (int)$it['id'] ?>">
                <button type="submit" class="btn btn-danger btn-sm" style="padding: 6px 12px; margin-right: 5px;">حذف</button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>