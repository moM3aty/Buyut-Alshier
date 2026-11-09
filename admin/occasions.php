<?php
require_once __DIR__ . '/includes/init.php';
require_login();

$pageTitle = 'إدارة المناسبات';
$pageSubtitle = 'أضف وعدل المناسبات وصورها المعروضة في الموقع';
$pdo = get_pdo();

// 1. معالجة الحذف
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['do'] ?? '') === 'delete') {
    verify_csrf();
    $pdo->prepare("DELETE FROM occasions WHERE id=?")->execute([(int)$_POST['id']]);
    flash('ok', 'تم حذف المناسبة بنجاح');
    header('Location: occasions.php'); exit;
}

// 2. معالجة الإضافة أو التعديل
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['do'] ?? '') === 'save') {
    verify_csrf();
    $data = [
        'title'       => trim($_POST['title'] ?? ''),
        'subtitle'    => trim($_POST['subtitle'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'sort_order'  => (int)($_POST['sort_order'] ?? 0),
        'is_active'   => isset($_POST['is_active']) ? 1 : 0,
    ];
    
    // معالجة رفع الصورة
    $image = null;
    if (!empty($_FILES['image']['name'])) {
        $image = upload_image($_FILES['image'], 'occ');
    }

    if ($data['title'] === '') {
        flash('error', 'عنوان المناسبة مطلوب');
    } else {
        if (!empty($_POST['id'])) {
            $data['id'] = (int)$_POST['id'];
            if ($image) {
                $data['image'] = $image;
                $pdo->prepare("UPDATE occasions SET title=:title, subtitle=:subtitle, description=:description, image=:image, sort_order=:sort_order, is_active=:is_active WHERE id=:id")->execute($data);
            } else {
                $pdo->prepare("UPDATE occasions SET title=:title, subtitle=:subtitle, description=:description, sort_order=:sort_order, is_active=:is_active WHERE id=:id")->execute($data);
            }
            flash('ok', 'تم تحديث المناسبة بنجاح');
        } else {
            $data['image'] = $image;
            $pdo->prepare("INSERT INTO occasions (title, subtitle, description, image, sort_order, is_active) VALUES (:title, :subtitle, :description, :image, :sort_order, :is_active)")->execute($data);
            flash('ok', 'تمت إضافة المناسبة بنجاح');
        }
    }
    header('Location: occasions.php'); exit;
}

// 3. جلب كل المناسبات
$items = $pdo->query("SELECT * FROM occasions ORDER BY sort_order ASC, id DESC")->fetchAll();

$msg = flash('ok');
$err = flash('error');

include __DIR__ . '/includes/header.php';
?>

<?php if ($msg): ?><div class="alert success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
<?php if ($err): ?><div class="alert error"><?= htmlspecialchars($err) ?></div><?php endif; ?>

<!-- شريط الأدوات -->
<div class="filter-bar animate-up" style="justify-content: space-between; background: #fff; padding: 15px 20px; border-radius: 16px; border: 1px solid var(--border);">
  <div class="search-box" style="max-width: 400px;">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
    <input type="text" id="liveSearch" placeholder="ابحث في المناسبات...">
  </div>
  <button onclick="openOccasionModal()" class="btn btn-primary">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    إضافة مناسبة
  </button>
</div>

<!-- شبكة الكروت -->
<div class="services-grid animate-up delay-1" id="occasionsGrid">
  <?php if (empty($items)): ?>
    <div style="grid-column: 1/-1; text-align:center; padding: 50px; color: #64748b; background: #fff; border-radius: 16px; border: 1px dashed var(--border);">لا توجد مناسبات مضافة حالياً. اضغط "إضافة مناسبة" للبدء.</div>
  <?php else: ?>
    <?php foreach ($items as $it): ?>
    <div class="service-card searchable-card" data-title="<?= htmlspecialchars($it['title']) ?>" data-desc="<?= htmlspecialchars($it['description']) ?>">
      
      <!-- صورة المناسبة -->
      <div class="occ-image-wrapper">
        <?php if ($it['image']): ?>
          <img src="<?= SITE_URL . htmlspecialchars($it['image']) ?>" alt="<?= htmlspecialchars($it['title']) ?>">
        <?php else: ?>
          <div class="occ-image-placeholder">لا توجد صورة</div>
        <?php endif; ?>
      </div>

      <div class="sc-header" style="flex-direction: column; align-items: flex-start; gap: 5px;">
        <div class="sc-title" style="font-size: 18px;"><?= htmlspecialchars($it['title']) ?></div>
        <div style="color: var(--gold); font-size: 12px; font-weight: 800;"><?= htmlspecialchars($it['subtitle']) ?></div>
      </div>
      
      <div class="sc-desc"><?= $it['description'] ? htmlspecialchars($it['description']) : '<i style="color:#cbd5e1">لا يوجد وصف</i>' ?></div>
      
      <div class="sc-footer">
        <span class="sc-status <?= $it['is_active'] ? 'active' : 'inactive' ?>"><?= $it['is_active'] ? 'نشطة' : 'معطلة' ?></span>
        <div class="sc-actions">
          <button onclick='editOccasion(<?= htmlspecialchars(json_encode($it), ENT_QUOTES, "UTF-8") ?>)' class="btn btn-outline btn-sm" style="border-color:var(--primary); color:var(--primary);">تعديل</button>
          <form method="POST" data-confirm="حذف المناسبة؟" style="display:inline;">
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

<!-- ================== مودال إضافة / تعديل المناسبة ================== -->
<div class="modal-overlay" id="occasionModal">
  <div class="modal-box modal-form-box">
    
    <div class="modal-header">
      <h3 id="modalTitle">إضافة مناسبة جديدة</h3>
      <span class="close-modal" onclick="closeOccasionModal()">✕</span>
    </div>
    
    <div class="modal-body">
      <form method="POST" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <input type="hidden" name="do" value="save">
        <input type="hidden" name="id" id="occ_id" value="">
        
        <div class="form-grid">
          <div class="form-group full">
            <label>عنوان المناسبة *</label>
            <input type="text" name="title" id="occ_title" class="form-control" required>
          </div>
          
          <div class="form-group">
            <label>العنوان الفرعي</label>
            <input type="text" name="subtitle" id="occ_subtitle" class="form-control" placeholder="مثال: ليالي الفرح">
          </div>
          
          <div class="form-group">
            <label>الترتيب</label>
            <input type="number" name="sort_order" id="occ_sort" class="form-control" value="0">
          </div>
          
          <div class="form-group full">
            <label>الوصف</label>
            <textarea name="description" id="occ_desc" class="form-control" rows="3"></textarea>
          </div>

          <div class="form-group full">
            <label>صورة المناسبة (اختياري)</label>
            <input type="file" name="image" id="occ_image" class="form-control" accept="image/*" onchange="previewImage(this)">
            <!-- صندوق معاينة الصورة -->
            <img id="imagePreview" class="image-preview-box" alt="معاينة الصورة">
          </div>
          
          <div class="form-group full switch-group">
            <label class="switch">
              <input type="checkbox" name="is_active" id="occ_active" checked>
              <span class="slider"></span>
            </label>
            <label style="margin:0;">تفعيل المناسبة (عرضها في الموقع)</label>
          </div>
        </div>
        
        <div class="form-actions" style="margin-top:25px; border-top:1px solid var(--border); padding-top:20px;">
          <button type="button" onclick="closeOccasionModal()" class="btn btn-outline">إلغاء</button>
          <button type="submit" class="btn btn-primary">حفظ المناسبة</button>
        </div>
      </form>
    </div>
    
  </div>
</div>

<script>
  // الفلترة الحية
  document.getElementById('liveSearch').addEventListener('keyup', function() {
    const term = this.value.toLowerCase();
    document.querySelectorAll('.searchable-card').forEach(card => {
      const title = card.getAttribute('data-title').toLowerCase();
      const desc = card.getAttribute('data-desc').toLowerCase();
      card.style.display = (title.includes(term) || desc.includes(term)) ? 'flex' : 'none';
    });
  });

  // معاينة الصورة
  function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    if (input.files && input.files[0]) {
      const reader = new FileReader();
      reader.onload = function(e) {
        preview.src = e.target.result;
        preview.style.display = 'block';
      }
      reader.readAsDataURL(input.files[0]);
    } else {
      preview.style.display = 'none';
    }
  }

  // فتح المودال للإضافة
  function openOccasionModal() {
    document.getElementById('modalTitle').innerText = 'إضافة مناسبة جديدة';
    document.getElementById('occ_id').value = '';
    document.getElementById('occ_title').value = '';
    document.getElementById('occ_subtitle').value = '';
    document.getElementById('occ_sort').value = '0';
    document.getElementById('occ_desc').value = '';
    document.getElementById('occ_active').checked = true;
    document.getElementById('occ_image').value = ''; // تصفير الصورة
    document.getElementById('imagePreview').style.display = 'none'; // إخفاء المعاينة
    document.getElementById('occasionModal').classList.add('active');
  }

  // فتح المودال للتعديل
  function editOccasion(data) {
    document.getElementById('modalTitle').innerText = 'تعديل المناسبة: ' + data.title;
    document.getElementById('occ_id').value = data.id;
    document.getElementById('occ_title').value = data.title;
    document.getElementById('occ_subtitle').value = data.subtitle || '';
    document.getElementById('occ_sort').value = data.sort_order;
    document.getElementById('occ_desc').value = data.description || '';
    document.getElementById('occ_active').checked = (data.is_active == 1);
    document.getElementById('occ_image').value = ''; // تصفير حقل الصورة
    
    // لو فيه صورة قديمة، نعرضها كمعاينة
    const preview = document.getElementById('imagePreview');
    if (data.image) {
      preview.src = '<?= SITE_URL ?>' + data.image;
      preview.style.display = 'block';
    } else {
      preview.style.display = 'none';
    }
    
    document.getElementById('occasionModal').classList.add('active');
  }

  function closeOccasionModal() {
    document.getElementById('occasionModal').classList.remove('active');
  }
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>