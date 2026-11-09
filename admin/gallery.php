<?php
require_once __DIR__ . '/includes/init.php';
require_login();

$pageTitle = 'معرض الصور';
$pageSubtitle = 'إدارة صور المعرض والتصنيفات';
$pdo = get_pdo();

// 1. معالجة الحذف
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['do'] ?? '') === 'delete') {
    verify_csrf();
    $pdo->prepare("DELETE FROM gallery WHERE id=?")->execute([(int)$_POST['id']]);
    flash('ok', 'تم حذف الصورة بنجاح');
    header('Location: gallery.php'); exit;
}

// 2. حذف تصنيف
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['do'] ?? '') === 'delete_cat') {
    verify_csrf();
    $pdo->prepare("DELETE FROM gallery_categories WHERE id=?")->execute([(int)$_POST['id']]);
    flash('ok', 'تم حذف التصنيف بنجاح');
    header('Location: gallery.php'); exit;
}

// 3. إضافة تصنيف
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['do'] ?? '') === 'add_cat') {
    verify_csrf();
    $name = trim($_POST['cat_name'] ?? '');
    $slug = trim($_POST['cat_slug'] ?? '');
    if ($name && $slug) {
        try {
            $pdo->prepare("INSERT INTO gallery_categories (slug, name) VALUES (?, ?)")->execute([$slug, $name]);
            flash('ok', 'تمت إضافة التصنيف بنجاح');
        } catch (Exception $e) {
            flash('error', 'الـ Slug مستخدم بالفعل');
        }
    } else {
        flash('error', 'الاسم والـ Slug مطلوبان');
    }
    header('Location: gallery.php'); exit;
}

// 4. حفظ صورة (إضافة / تعديل)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['do'] ?? '') === 'save') {
    verify_csrf();
    $image = null;
    if (!empty($_FILES['image']['name'])) {
        $image = upload_image($_FILES['image'], 'gal');
    }

    $data = [
        'title'       => trim($_POST['title'] ?? ''),
        'category_id' => (int)($_POST['category_id'] ?? 0) ?: null,
        'sort_order'  => (int)($_POST['sort_order'] ?? 0),
    ];

    if (!empty($_POST['id'])) {
        $data['id'] = (int)$_POST['id'];
        if ($image) {
            $data['image'] = $image;
            $pdo->prepare("UPDATE gallery SET title=:title, category_id=:category_id, image=:image, sort_order=:sort_order WHERE id=:id")->execute($data);
        } else {
            $pdo->prepare("UPDATE gallery SET title=:title, category_id=:category_id, sort_order=:sort_order WHERE id=:id")->execute($data);
        }
        flash('ok', 'تم تحديث الصورة بنجاح');
    } else {
        if (!$image) {
            flash('error', 'الصورة مطلوبة للإضافة');
            header('Location: gallery.php'); exit;
        }
        $data['image'] = $image;
        $pdo->prepare("INSERT INTO gallery (title, category_id, image, sort_order) VALUES (:title, :category_id, :image, :sort_order)")->execute($data);
        flash('ok', 'تمت إضافة الصورة بنجاح');
    }
    header('Location: gallery.php'); exit;
}

// جلب البيانات
$cats = $pdo->query("SELECT * FROM gallery_categories ORDER BY sort_order, id")->fetchAll();
$items = $pdo->query("SELECT g.*, c.name AS cat_name, c.slug AS cat_slug FROM gallery g LEFT JOIN gallery_categories c ON g.category_id=c.id ORDER BY g.sort_order ASC, g.id DESC")->fetchAll();

$msg = flash('ok'); $err = flash('error');

include __DIR__ . '/includes/header.php';
?>

<?php if ($msg): ?><div class="alert success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
<?php if ($err): ?><div class="alert error"><?= htmlspecialchars($err) ?></div><?php endif; ?>

<!-- شريط الأدوات العلوي -->
<div class="filter-bar animate-up" style="justify-content: space-between; background: #fff; padding: 15px 20px; border-radius: 16px; border: 1px solid var(--border); margin-bottom: 20px;">
  <div class="search-box" style="max-width: 350px;">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
    <input type="text" id="liveSearch" placeholder="ابحث بعنوان الصورة...">
  </div>
  <div style="display: flex; gap: 10px;">
    <button onclick="openCategoryModal()" class="btn btn-outline">
      🏷️ إدارة التصنيفات
    </button>
    <button onclick="openImageModal()" class="btn btn-primary">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      رفع صورة
    </button>
  </div>
</div>

<!-- أزرار فلترة التصنيفات -->
<div class="gallery-filters-bar animate-up">
  <button class="filter-pill active" data-filter="all">الكل (<?= count($items) ?>)</button>
  <?php foreach ($cats as $c): 
    $count = count(array_filter($items, fn($i) => $i['category_id'] == $c['id']));
  ?>
    <button class="filter-pill" data-filter="<?= htmlspecialchars($c['slug']) ?>"><?= htmlspecialchars($c['name']) ?> (<?= $count ?>)</button>
  <?php endforeach; ?>
</div>

<!-- شبكة الصور -->
<div class="gallery-admin-grid animate-up delay-1" id="galleryGrid">
  <?php if (empty($items)): ?>
    <div style="grid-column: 1/-1; text-align:center; padding: 60px; color: #64748b; background: #fff; border-radius: 16px; border: 1px dashed var(--border);">
      المعرض فاضي. اضغط "رفع صورة" لإضافة أول صورة.
    </div>
  <?php else: ?>
    <?php foreach ($items as $it): ?>
      <div class="gal-card searchable-card" 
           data-title="<?= htmlspecialchars(mb_strtolower($it['title'])) ?>" 
           data-category="<?= htmlspecialchars($it['cat_slug'] ?? '') ?>">
        <img src="<?= SITE_URL . htmlspecialchars($it['image']) ?>" alt="<?= htmlspecialchars($it['title']) ?>" loading="lazy">
        <div class="gal-overlay">
          <div class="gal-actions-top">
            <button class="gal-btn-icon" onclick='editImage(<?= htmlspecialchars(json_encode($it), ENT_QUOTES, "UTF-8") ?>)' title="تعديل">✏️</button>
            <form method="POST" data-confirm="حذف الصورة؟" style="display:inline;">
              <?= csrf_field() ?>
              <input type="hidden" name="do" value="delete">
              <input type="hidden" name="id" value="<?= (int)$it['id'] ?>">
              <button type="submit" class="gal-btn-icon del" title="حذف">🗑️</button>
            </form>
          </div>
          <div>
            <div class="gal-title"><?= htmlspecialchars($it['title'] ?: 'بدون عنوان') ?></div>
            <?php if($it['cat_name']): ?><span class="gal-cat"><?= htmlspecialchars($it['cat_name']) ?></span><?php endif; ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<!-- ================== مودال رفع / تعديل صورة ================== -->
<div class="modal-overlay" id="imageModal">
  <div class="modal-box modal-form-box">
    <div class="modal-header">
      <h3 id="imgModalTitle">رفع صورة جديدة</h3>
      <span class="close-modal" onclick="closeImageModal()">✕</span>
    </div>
    <div class="modal-body">
      <form method="POST" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <input type="hidden" name="do" value="save">
        <input type="hidden" name="id" id="img_id" value="">
        
        <div class="form-grid">
          <div class="form-group full">
            <label>عنوان الصورة (اختياري)</label>
            <input type="text" name="title" id="img_title" class="form-control" placeholder="مثال: بيت شعر في الرياض">
          </div>
          
          <div class="form-group">
            <label>التصنيف</label>
            <select name="category_id" id="img_category" class="form-control">
              <option value="">بدون تصنيف</option>
              <?php foreach ($cats as $c): ?>
                <option value="<?= (int)$c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          
          <div class="form-group">
            <label>الترتيب</label>
            <input type="number" name="sort_order" id="img_sort" class="form-control" value="0">
          </div>
          
          <div class="form-group full">
            <label>الصورة <span id="imgRequired">(مطلوبة)</span></label>
            <input type="file" name="image" id="img_file" class="form-control" accept="image/*" onchange="previewGalImage(this)">
            <img id="galImagePreview" class="image-preview-box" alt="معاينة">
          </div>
        </div>
        
        <div class="form-actions" style="margin-top:25px; border-top:1px solid var(--border); padding-top:20px;">
          <button type="button" onclick="closeImageModal()" class="btn btn-outline">إلغاء</button>
          <button type="submit" class="btn btn-primary">حفظ الصورة</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ================== مودال إدارة التصنيفات ================== -->
<div class="modal-overlay" id="categoryModal">
  <div class="modal-box modal-form-box">
    <div class="modal-header">
      <h3>إدارة التصنيفات</h3>
      <span class="close-modal" onclick="closeCategoryModal()">✕</span>
    </div>
    <div class="modal-body">
      
      <!-- إضافة تصنيف جديد -->
      <form method="POST" style="background: #f8fafc; padding: 15px; border-radius: 12px; margin-bottom: 20px;">
        <?= csrf_field() ?>
        <input type="hidden" name="do" value="add_cat">
        <div class="form-grid">
          <div class="form-group">
            <label>اسم التصنيف</label>
            <input type="text" name="cat_name" class="form-control" required placeholder="مثال: جلسات">
          </div>
          <div class="form-group">
            <label>Slug (بالإنقلش)</label>
            <input type="text" name="cat_slug" class="form-control" required placeholder="sessions">
          </div>
          <div class="form-group full">
            <button type="submit" class="btn btn-primary" style="width: 100%;">إضافة التصنيف</button>
          </div>
        </div>
      </form>

      <!-- قائمة التصنيفات -->
      <h4 style="margin-bottom: 15px; font-size: 14px; font-weight: 800;">التصنيفات الحالية (<?= count($cats) ?>)</h4>
      <div style="display: flex; flex-direction: column; gap: 8px;">
        <?php foreach ($cats as $c): 
          $count = count(array_filter($items, fn($i) => $i['category_id'] == $c['id']));
        ?>
          <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px 15px; background: #fff; border: 1px solid var(--border); border-radius: 10px;">
            <div>
              <strong style="font-size: 14px;"><?= htmlspecialchars($c['name']) ?></strong>
              <span style="color: var(--muted); font-size: 12px; margin-right: 8px;">(<?= $count ?> صورة)</span>
            </div>
            <form method="POST" data-confirm="حذف التصنيف؟ سيتم فصل صوره عنه." style="display:inline;">
              <?= csrf_field() ?>
              <input type="hidden" name="do" value="delete_cat">
              <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
              <button type="submit" class="btn btn-danger btn-sm">حذف</button>
            </form>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</div>

<script>
  // ====== الفلترة ======
  let currentCategory = 'all';
  const searchInput = document.getElementById('liveSearch');
  const filterPills = document.querySelectorAll('.filter-pill');

  function applyFilters() {
    const term = searchInput.value.toLowerCase();
    document.querySelectorAll('.searchable-card').forEach(card => {
      const titleMatch = card.getAttribute('data-title').includes(term);
      const catMatch = (currentCategory === 'all') || (card.getAttribute('data-category') === currentCategory);
      card.style.display = (titleMatch && catMatch) ? 'block' : 'none';
    });
  }

  searchInput.addEventListener('keyup', applyFilters);
  filterPills.forEach(pill => {
    pill.addEventListener('click', function() {
      filterPills.forEach(p => p.classList.remove('active'));
      this.classList.add('active');
      currentCategory = this.dataset.filter;
      applyFilters();
    });
  });

  // ====== Modal الصور ======
  function previewGalImage(input) {
    const preview = document.getElementById('galImagePreview');
    if (input.files && input.files[0]) {
      const reader = new FileReader();
      reader.onload = e => { preview.src = e.target.result; preview.style.display = 'block'; };
      reader.readAsDataURL(input.files[0]);
    } else { preview.style.display = 'none'; }
  }

  function openImageModal() {
    document.getElementById('imgModalTitle').innerText = 'رفع صورة جديدة';
    document.getElementById('img_id').value = '';
    document.getElementById('img_title').value = '';
    document.getElementById('img_category').value = '';
    document.getElementById('img_sort').value = '0';
    document.getElementById('img_file').value = '';
    document.getElementById('img_file').required = true;
    document.getElementById('imgRequired').style.display = 'inline';
    document.getElementById('galImagePreview').style.display = 'none';
    document.getElementById('imageModal').classList.add('active');
  }

  function editImage(data) {
    document.getElementById('imgModalTitle').innerText = 'تعديل الصورة';
    document.getElementById('img_id').value = data.id;
    document.getElementById('img_title').value = data.title || '';
    document.getElementById('img_category').value = data.category_id || '';
    document.getElementById('img_sort').value = data.sort_order;
    document.getElementById('img_file').value = '';
    document.getElementById('img_file').required = false;
    document.getElementById('imgRequired').style.display = 'none';
    
    const preview = document.getElementById('galImagePreview');
    if (data.image) {
      preview.src = '<?= SITE_URL ?>' + data.image;
      preview.style.display = 'block';
    } else {
      preview.style.display = 'none';
    }
    document.getElementById('imageModal').classList.add('active');
  }

  function closeImageModal() {
    document.getElementById('imageModal').classList.remove('active');
  }

  // ====== Modal التصنيفات ======
  function openCategoryModal() { document.getElementById('categoryModal').classList.add('active'); }
  function closeCategoryModal() { document.getElementById('categoryModal').classList.remove('active'); }
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>