<?php
require_once __DIR__ . '/includes/init.php';
require_login();
if (!is_admin()) { header('Location: index.php'); exit; }

$pageTitle = 'المستخدمون';
$pageSubtitle = 'إدارة حسابات الدخول للوحة التحكم';
$pdo = get_pdo();
$currentId = (int)$_SESSION['user']['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['do'] ?? '') === 'delete') {
    verify_csrf();
    $tid = (int)$_POST['id'];
    if ($tid !== $currentId) {
        $pdo->prepare("DELETE FROM users WHERE id=?")->execute([$tid]);
        flash('ok', 'تم حذف المستخدم بنجاح');
    } else {
        flash('error', 'لا يمكنك حذف حسابك الحالي');
    }
    header('Location: users.php'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['do'] ?? '') === 'save') {
    verify_csrf();
    $u = trim($_POST['username'] ?? '');
    $n = trim($_POST['name'] ?? '');
    $em = trim($_POST['email'] ?? '');
    $role = ($_POST['role'] ?? 'editor') === 'admin' ? 'admin' : 'editor';
    $pass = $_POST['password'] ?? '';

    if (!empty($_POST['id'])) {
        $uid = (int)$_POST['id'];
        if ($pass) {
            $pdo->prepare("UPDATE users SET username=?, name=?, email=?, role=?, password=? WHERE id=?")->execute([$u, $n, $em, $role, password_hash($pass, PASSWORD_DEFAULT), $uid]);
        } else {
            $pdo->prepare("UPDATE users SET username=?, name=?, email=?, role=? WHERE id=?")->execute([$u, $n, $em, $role, $uid]);
        }
        flash('ok', 'تم تحديث المستخدم بنجاح');
    } else {
        if ($u && $n && strlen($pass) >= 6) {
            try {
                $pdo->prepare("INSERT INTO users (username, password, name, email, role) VALUES (?,?,?,?,?)")->execute([$u, password_hash($pass, PASSWORD_DEFAULT), $n, $em, $role]);
                flash('ok', 'تمت إضافة المستخدم بنجاح');
            } catch (Exception $e) { flash('error', 'اسم المستخدم مستخدم بالفعل'); }
        } else { flash('error', 'يرجى ملء الحقول وكلمة السر (6 أحرف على الأقل)'); }
    }
    header('Location: users.php'); exit;
}

$items = $pdo->query("SELECT * FROM users ORDER BY id ASC")->fetchAll();
$msg = flash('ok'); $err = flash('error');
include __DIR__ . '/includes/header.php';
?>

<?php if ($msg): ?><div class="alert success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
<?php if ($err): ?><div class="alert error"><?= htmlspecialchars($err) ?></div><?php endif; ?>

<div class="filter-bar animate-up" style="justify-content: space-between; background: #fff; padding: 15px 20px; border-radius: 16px; border: 1px solid var(--border); margin-bottom: 20px;">
  <div class="search-box" style="max-width: 400px;">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
    <input type="text" id="liveSearch" placeholder="ابحث بالاسم أو اسم المستخدم...">
  </div>
  <button onclick="openUserModal()" class="btn btn-primary">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    إضافة مستخدم
  </button>
</div>

<div class="panel animate-up delay-1">
  <div class="table-responsive">
    <table class="table">
      <thead>
        <tr>
          <th>الاسم</th>
          <th>اسم المستخدم</th>
          <th>الصلاحية</th>
          <th>آخر دخول</th>
          <th>إجراءات</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($items as $it): ?>
        <tr class="searchable-row">
          <td><strong><?= htmlspecialchars($it['name']) ?></strong><br><small style="color:var(--muted);font-size:11px;"><?= htmlspecialchars($it['email']) ?></small></td>
          <td><?= htmlspecialchars($it['username']) ?></td>
          <td>
            <?php if($it['role']==='admin'): ?>
              <span class="sc-status" style="background:#fef3c7;color:#92400e;">مدير</span>
            <?php else: ?>
              <span class="sc-status active">محرر</span>
            <?php endif; ?>
          </td>
          <td style="font-size:12px;"><?= $it['last_login'] ? date('Y-m-d', strtotime($it['last_login'])) : '—' ?></td>
          <td>
            <button onclick='editUser(<?= htmlspecialchars(json_encode($it), ENT_QUOTES, "UTF-8") ?>)' class="btn btn-outline btn-sm" style="border-color:var(--primary); color:var(--primary);">تعديل</button>
            <?php if ((int)$it['id'] !== $currentId): ?>
            <form method="POST" data-confirm="حذف المستخدم؟" style="display:inline;">
              <?= csrf_field() ?><input type="hidden" name="do" value="delete"><input type="hidden" name="id" value="<?= (int)$it['id'] ?>">
              <button type="submit" class="btn btn-danger btn-sm">حذف</button>
            </form>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- مودال المستخدم -->
<div class="modal-overlay" id="userModal">
  <div class="modal-box modal-form-box">
    <div class="modal-header">
      <h3 id="modalTitle">إضافة مستخدم</h3>
      <span class="close-modal" onclick="closeUserModal()">✕</span>
    </div>
    <div class="modal-body">
      <form method="POST">
        <?= csrf_field() ?>
        <input type="hidden" name="do" value="save">
        <input type="hidden" name="id" id="user_id" value="">
        <div class="form-grid">
          <div class="form-group">
            <label>الاسم الكامل *</label>
            <input type="text" name="name" id="user_name" class="form-control" required>
          </div>
          <div class="form-group">
            <label>اسم المستخدم *</label>
            <input type="text" name="username" id="user_username" class="form-control" required>
          </div>
          <div class="form-group">
            <label>البريد الإلكتروني</label>
            <input type="email" name="email" id="user_email" class="form-control">
          </div>
          <div class="form-group">
            <label>الصلاحية</label>
            <select name="role" id="user_role" class="form-control">
              <option value="editor">محرر</option>
              <option value="admin">مدير</option>
            </select>
          </div>
          <div class="form-group full">
            <label>كلمة المرور <span id="passReq">*</span></label>
            <input type="password" name="password" id="user_pass" class="form-control" minlength="6" placeholder="اتركها فارغة لعدم التغيير عند التعديل">
          </div>
        </div>
        <div class="form-actions" style="margin-top:25px; border-top:1px solid var(--border); padding-top:20px;">
          <button type="button" onclick="closeUserModal()" class="btn btn-outline">إلغاء</button>
          <button type="submit" class="btn btn-primary">حفظ</button>
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

  function openUserModal() {
    document.getElementById('modalTitle').innerText = 'إضافة مستخدم';
    document.getElementById('user_id').value = '';
    document.getElementById('user_name').value = '';
    document.getElementById('user_username').value = '';
    document.getElementById('user_email').value = '';
    document.getElementById('user_role').value = 'editor';
    document.getElementById('user_pass').value = '';
    document.getElementById('user_pass').required = true;
    document.getElementById('passReq').style.display = 'inline';
    document.getElementById('userModal').classList.add('active');
  }

  function editUser(data) {
    document.getElementById('modalTitle').innerText = 'تعديل المستخدم: ' + data.name;
    document.getElementById('user_id').value = data.id;
    document.getElementById('user_name').value = data.name;
    document.getElementById('user_username').value = data.username;
    document.getElementById('user_email').value = data.email || '';
    document.getElementById('user_role').value = data.role;
    document.getElementById('user_pass').value = '';
    document.getElementById('user_pass').required = false;
    document.getElementById('passReq').style.display = 'none';
    document.getElementById('userModal').classList.add('active');
  }

  function closeUserModal() {
    document.getElementById('userModal').classList.remove('active');
  }
</script>
<?php include __DIR__ . '/includes/footer.php'; ?>