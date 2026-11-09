<?php
require_once __DIR__ . '/includes/init.php';
require_login();

$pageTitle = 'إعدادات الموقع';
$pageSubtitle = 'التحكم في النصوص الأساسية والشعارات';
$pdo = get_pdo();

$fields = [
    'site_name'        => ['اسم الموقع', 'text'],
    'site_tagline'     => ['الشعار الفرعي', 'text'],
    'phone'            => ['رقم الهاتف', 'text'],
    'whatsapp'         => ['رقم واتساب (بصيغة دولية)', 'text'],
    'email'            => ['البريد الإلكتروني', 'email'],
    'hero_title'       => ['عنوان الهيرو الرئيسي', 'text'],
    'hero_subtitle'    => ['العنوان الفرعي', 'text'],
    'hero_lead'        => ['وصف الهيرو', 'textarea'],
    'hero_image'       => ['صورة الهيرو', 'image'],
    'about_title'      => ['عنوان "من نحن"', 'text'],
    'about_text'       => ['نص "من نحن"', 'textarea'],
    'about_image'      => ['صورة "من نحن"', 'image'],
    'meta_description' => ['وصف SEO', 'textarea'],
    'footer_text'      => ['نص الفوتر', 'textarea'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    foreach ($fields as $k => $f) {
        $type = $f[1];
        if ($type === 'image' && !empty($_FILES[$k]['name'])) {
            $path = upload_image($_FILES[$k], $k);
            if ($path) set_setting($k, $path);
        } elseif ($type !== 'image') {
            set_setting($k, trim($_POST[$k] ?? ''));
        }
    }
    flash('ok', 'تم حفظ الإعدادات بنجاح');
    header('Location: settings.php'); exit;
}

$msg = flash('ok');
include __DIR__ . '/includes/header.php';
?>

<?php if ($msg): ?><div class="alert success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

<div class="panel animate-up">
  <form method="POST" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="form-grid">
      <?php foreach ($fields as $k => $f):
        $val = get_setting($k);
        [$label, $type] = $f;
      ?>
      <div class="form-group <?= $type==='textarea'?'full':'' ?>">
        <label><?= htmlspecialchars($label) ?></label>
        <?php if ($type === 'textarea'): ?>
          <textarea name="<?= htmlspecialchars($k) ?>" class="form-control" rows="3"><?= htmlspecialchars($val) ?></textarea>
        <?php elseif ($type === 'image'): ?>
          <div>
            <?php if ($val): ?><img src="<?= SITE_URL . htmlspecialchars($val) ?>" style="height:80px;border-radius:8px;margin-bottom:10px;display:block;object-fit:cover;"><?php endif; ?>
            <input type="file" name="<?= htmlspecialchars($k) ?>" class="form-control" accept="image/*">
          </div>
        <?php else: ?>
          <input type="<?= htmlspecialchars($type) ?>" name="<?= htmlspecialchars($k) ?>" class="form-control" value="<?= htmlspecialchars($val) ?>">
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="form-actions" style="margin-top:25px; border-top:1px solid var(--border); padding-top:20px;">
      <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
    </div>
  </form>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>