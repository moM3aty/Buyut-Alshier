<?php
require_once __DIR__ . '/includes/init.php';
require_login();

$pageTitle = 'الأسئلة الشائعة';
$pageSubtitle = 'إدارة الأسئلة والأجوبة المعروضة في الموقع';
$pdo = get_pdo();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['do'] ?? '') === 'delete') {
    verify_csrf();
    $pdo->prepare("DELETE FROM faqs WHERE id=?")->execute([(int)$_POST['id']]);
    flash('ok', 'تم حذف السؤال بنجاح');
    header('Location: faqs.php'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['do'] ?? '') === 'save') {
    verify_csrf();
    $data = [
        'question' => trim($_POST['question'] ?? ''),
        'answer'   => trim($_POST['answer'] ?? ''),
        'sort_order' => (int)($_POST['sort_order'] ?? 0),
        'is_active'  => isset($_POST['is_active']) ? 1 : 0,
    ];
    
    if ($data['question'] === '' || $data['answer'] === '') {
        flash('error', 'السؤال والإجابة مطلوبان');
    } else {
        if (!empty($_POST['id'])) {
            $data['id'] = (int)$_POST['id'];
            $pdo->prepare("UPDATE faqs SET question=:question, answer=:answer, sort_order=:sort_order, is_active=:is_active WHERE id=:id")->execute($data);
            flash('ok', 'تم تحديث السؤال بنجاح');
        } else {
            $pdo->prepare("INSERT INTO faqs (question, answer, sort_order, is_active) VALUES (:question, :answer, :sort_order, :is_active)")->execute($data);
            flash('ok', 'تمت إضافة السؤال بنجاح');
        }
    }
    header('Location: faqs.php'); exit;
}

$items = $pdo->query("SELECT * FROM faqs ORDER BY sort_order ASC, id DESC")->fetchAll();
$msg = flash('ok'); $err = flash('error');

include __DIR__ . '/includes/header.php';
?>

<?php if ($msg): ?><div class="alert success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
<?php if ($err): ?><div class="alert error"><?= htmlspecialchars($err) ?></div><?php endif; ?>

<div class="filter-bar animate-up" style="justify-content: space-between; background: #fff; padding: 15px 20px; border-radius: 16px; border: 1px solid var(--border); margin-bottom: 20px;">
  <div class="search-box" style="max-width: 400px;">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
    <input type="text" id="liveSearch" placeholder="ابحث في الأسئلة...">
  </div>
  <button onclick="openFaqModal()" class="btn btn-primary">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    إضافة سؤال
  </button>
</div>

<div class="panel animate-up delay-1">
  <div class="table-responsive">
    <table class="table">
      <thead>
        <tr>
          <th>السؤال</th>
          <th>الإجابة</th>
          <th>الحالة</th>
          <th>إجراءات</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($items)): ?>
          <tr><td colspan="4" style="text-align:center; padding: 40px; color: #64748b;">لا توجد أسئلة مضافة حالياً.</td></tr>
        <?php else: ?>
          <?php foreach ($items as $it): ?>
          <tr class="searchable-row">
            <td><strong><?= htmlspecialchars($it['question']) ?></strong></td>
            <td style="font-size: 13px; color: var(--muted); max-width: 400px;"><?= htmlspecialchars(mb_substr($it['answer'], 0, 100)) ?>...</td>
            <td>
              <?php if ($it['is_active']): ?>
                <span class="sc-status active">نشط</span>
              <?php else: ?>
                <span class="sc-status inactive">معطل</span>
              <?php endif; ?>
            </td>
            <td>
              <button onclick='editFaq(<?= htmlspecialchars(json_encode($it), ENT_QUOTES, "UTF-8") ?>)' class="btn btn-outline btn-sm" style="border-color:var(--primary); color:var(--primary);">تعديل</button>
              <form method="POST" data-confirm="حذف السؤال؟" style="display:inline;">
                <?= csrf_field() ?><input type="hidden" name="do" value="delete"><input type="hidden" name="id" value="<?= (int)$it['id'] ?>">
                <button type="submit" class="btn btn-danger btn-sm">حذف</button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- مودال الأسئلة -->
<div class="modal-overlay" id="faqModal">
  <div class="modal-box modal-form-box">
    <div class="modal-header">
      <h3 id="modalTitle">إضافة سؤال جديد</h3>
      <span class="close-modal" onclick="closeFaqModal()">✕</span>
    </div>
    <div class="modal-body">
      <form method="POST">
        <?= csrf_field() ?>
        <input type="hidden" name="do" value="save">
        <input type="hidden" name="id" id="faq_id" value="">
        <div class="form-grid">
          <div class="form-group full">
            <label>السؤال *</label>
            <input type="text" name="question" id="faq_question" class="form-control" required>
          </div>
          <div class="form-group full">
            <label>الإجابة *</label>
            <textarea name="answer" id="faq_answer" class="form-control" rows="4" required></textarea>
          </div>
          <div class="form-group">
            <label>الترتيب</label>
            <input type="number" name="sort_order" id="faq_sort" class="form-control" value="0">
          </div>
          <div class="form-group full switch-group">
            <label class="switch">
              <input type="checkbox" name="is_active" id="faq_active" checked>
              <span class="slider"></span>
            </label>
            <label style="margin:0;">تفعيل السؤال (عرضه في الموقع)</label>
          </div>
        </div>
        <div class="form-actions" style="margin-top:25px; border-top:1px solid var(--border); padding-top:20px;">
          <button type="button" onclick="closeFaqModal()" class="btn btn-outline">إلغاء</button>
          <button type="submit" class="btn btn-primary">حفظ السؤال</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  document.getElementById('liveSearch').addEventListener('keyup', function() {
    const term = this.value.toLowerCase();
    document.querySelectorAll('.searchable-row').forEach(row => {
      row.style.display = row.textContent.toLowerCase().includes(term) ? '' : 'none';
    });
  });

  function openFaqModal() {
    document.getElementById('modalTitle').innerText = 'إضافة سؤال جديد';
    document.getElementById('faq_id').value = '';
    document.getElementById('faq_question').value = '';
    document.getElementById('faq_answer').value = '';
    document.getElementById('faq_sort').value = '0';
    document.getElementById('faq_active').checked = true;
    document.getElementById('faqModal').classList.add('active');
  }

  function editFaq(data) {
    document.getElementById('modalTitle').innerText = 'تعديل السؤال';
    document.getElementById('faq_id').value = data.id;
    document.getElementById('faq_question').value = data.question;
    document.getElementById('faq_answer').value = data.answer;
    document.getElementById('faq_sort').value = data.sort_order;
    document.getElementById('faq_active').checked = (data.is_active == 1);
    document.getElementById('faqModal').classList.add('active');
  }

  function closeFaqModal() {
    document.getElementById('faqModal').classList.remove('active');
  }
</script>
<?php include __DIR__ . '/includes/footer.php'; ?>