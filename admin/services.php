<?php
require_once __DIR__ . '/includes/init.php';
require_login();

$pageTitle = 'إدارة الخدمات';
$pageSubtitle = 'أضف وعدل الخدمات المعروضة في موقعك';
$pdo = get_pdo();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['do'] ?? '') === 'delete') {
    verify_csrf();
    $pdo->prepare("DELETE FROM services WHERE id=?")->execute([(int)$_POST['id']]);
    flash('ok', 'تم حذف الخدمة بنجاح');
    header('Location: services.php'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['do'] ?? '') === 'save') {
    verify_csrf();
    $data = [
        'title'       => trim($_POST['title'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'icon'        => trim($_POST['icon'] ?? '✦'),
        'sort_order'  => (int)($_POST['sort_order'] ?? 0),
        'is_active'   => isset($_POST['is_active']) ? 1 : 0,
    ];
    
    if ($data['title'] === '') {
        flash('error', 'اسم الخدمة مطلوب');
    } else {
        if (!empty($_POST['id'])) {
            $data['id'] = (int)$_POST['id'];
            $pdo->prepare("UPDATE services SET title=:title, description=:description, icon=:icon, sort_order=:sort_order, is_active=:is_active WHERE id=:id")->execute($data);
            flash('ok', 'تم تحديث الخدمة بنجاح');
        } else {
            $pdo->prepare("INSERT INTO services (title, description, icon, sort_order, is_active) VALUES (:title, :description, :icon, :sort_order, :is_active)")->execute($data);
            flash('ok', 'تمت إضافة الخدمة بنجاح');
        }
    }
    header('Location: services.php'); exit;
}

$items = $pdo->query("SELECT * FROM services ORDER BY sort_order ASC, id DESC")->fetchAll();
$msg = flash('ok'); $err = flash('error');

include __DIR__ . '/includes/header.php';
?>

<?php if ($msg): ?><div class="alert success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
<?php if ($err): ?><div class="alert error"><?= htmlspecialchars($err) ?></div><?php endif; ?>

<!-- شريط الأدوات -->
<div class="filter-bar animate-up" style="justify-content: space-between; background: #fff; padding: 15px 20px; border-radius: 16px; border: 1px solid var(--border);">
  <div class="search-box" style="max-width: 400px;">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
    <input type="text" id="liveSearch" placeholder="ابحث في الخدمات...">
  </div>
  <button onclick="openServiceModal()" class="btn btn-primary">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    إضافة خدمة
  </button>
</div>

<!-- شبكة الكروت -->
<div class="services-grid animate-up delay-1" id="servicesGrid">
  <?php if (empty($items)): ?>
    <div style="grid-column: 1/-1; text-align:center; padding: 50px; color: #64748b; background: #fff; border-radius: 16px; border: 1px dashed var(--border);">لا توجد خدمات مضافة حالياً. اضغط "إضافة خدمة" للبدء.</div>
  <?php else: ?>
    <?php foreach ($items as $it): ?>
    <div class="service-card searchable-card" data-title="<?= htmlspecialchars($it['title']) ?>" data-desc="<?= htmlspecialchars($it['description']) ?>">
      <div class="sc-header">
        <div class="sc-icon"><?= htmlspecialchars($it['icon']) ?></div>
        <div class="sc-title"><?= htmlspecialchars($it['title']) ?></div>
      </div>
      <div class="sc-desc"><?= $it['description'] ? htmlspecialchars($it['description']) : '<i style="color:#cbd5e1">لا يوجد وصف</i>' ?></div>
      <div class="sc-footer">
        <span class="sc-status <?= $it['is_active'] ? 'active' : 'inactive' ?>"><?= $it['is_active'] ? 'نشطة' : 'معطلة' ?></span>
        <div class="sc-actions">
          <button onclick="editService(<?= htmlspecialchars(json_encode($it)) ?>)" class="btn btn-outline btn-sm" style="border-color:var(--primary); color:var(--primary);">تعديل</button>
          <form method="POST" data-confirm="حذف الخدمة؟" style="display:inline;">
            <?= csrf_field() ?>
            <input type="hidden" name="do" value="delete">
            <input type="hidden" name="id" value="<?= (int)$it['id'] ?>">
            <button type="submit" class="btn btn-danger btn-sm">حذف</button>
          </form>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<!-- ================== مودال إضافة / تعديل الخدمة ================== -->
<div class="modal-overlay" id="serviceModal">
  <div class="modal-box modal-form-box">
    
    <!-- هيدر المودال الجديد -->
    <div class="modal-header">
      <h3 id="modalTitle">إضافة خدمة جديدة</h3>
      <span class="close-modal" onclick="closeServiceModal()">✕</span>
    </div>
    
    <!-- محتوى الفورم -->
    <div class="modal-body">
      <form method="POST">
        <?= csrf_field() ?>
        <input type="hidden" name="do" value="save">
        <input type="hidden" name="id" id="service_id" value="">
        
        <div class="form-grid">
          <div class="form-group full">
            <label>اسم الخدمة *</label>
            <input type="text" name="title" id="service_title" class="form-control" required>
          </div>
          
          <div class="form-group">
            <label>الأيقونة (Emoji)</label>
            <input type="text" name="icon" id="service_icon" class="form-control" placeholder="✦">
          </div>
          
          <div class="form-group">
            <label>الترتيب</label>
            <input type="number" name="sort_order" id="service_sort" class="form-control" value="0">
          </div>
          
          <div class="form-group full">
            <label>الوصف</label>
            <textarea name="description" id="service_desc" class="form-control" rows="3"></textarea>
          </div>
          
          <!-- تنسيق جديد ومريح لمفتاح التفعيل -->
          <div class="form-group full switch-group">
            <label class="switch">
              <input type="checkbox" name="is_active" id="service_active" checked>
              <span class="slider"></span>
            </label>
            <label style="margin:0;">تفعيل الخدمة (عرضها في الموقع)</label>
          </div>
        </div>
        
        <div class="form-actions" style="margin-top:25px; border-top:1px solid var(--border); padding-top:20px;">
          <button type="button" onclick="closeServiceModal()" class="btn btn-outline">إلغاء</button>
          <button type="submit" class="btn btn-primary">حفظ الخدمة</button>
        </div>
      </form>
    </div>
    
  </div>
</div>

<script>
  document.getElementById('liveSearch').addEventListener('keyup', function() {
    const term = this.value.toLowerCase();
    document.querySelectorAll('.searchable-card').forEach(card => {
      const title = card.getAttribute('data-title').toLowerCase();
      const desc = card.getAttribute('data-desc').toLowerCase();
      card.style.display = (title.includes(term) || desc.includes(term)) ? 'flex' : 'none';
    });
  });

  function openServiceModal() {
    document.getElementById('modalTitle').innerText = 'إضافة خدمة جديدة';
    document.getElementById('service_id').value = '';
    document.getElementById('service_title').value = '';
    document.getElementById('service_icon').value = '✦';
    document.getElementById('service_sort').value = '0';
    document.getElementById('service_desc').value = '';
    document.getElementById('service_active').checked = true;
    document.getElementById('serviceModal').classList.add('active');
  }

  function editService(data) {
    document.getElementById('modalTitle').innerText = 'تعديل الخدمة: ' + data.title;
    document.getElementById('service_id').value = data.id;
    document.getElementById('service_title').value = data.title;
    document.getElementById('service_icon').value = data.icon;
    document.getElementById('service_sort').value = data.sort_order;
    document.getElementById('service_desc').value = data.description || '';
    document.getElementById('service_active').checked = (data.is_active == 1);
    document.getElementById('serviceModal').classList.add('active');
  }

  function closeServiceModal() {
    document.getElementById('serviceModal').classList.remove('active');
  }
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>