<?php
require_once __DIR__ . '/includes/init.php';

if (!empty($_SESSION['user']['id'])) {
    header('Location: ' . SITE_URL . 'admin/index.php'); exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $u = trim($_POST['username'] ?? '');
    $p = $_POST['password'] ?? '';
    if ($u && $p) {
        $stmt = get_pdo()->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
        $stmt->execute([$u]);
        $row = $stmt->fetch();
        if ($row && password_verify($p, $row['password'])) {
            session_regenerate_id(true);
            $_SESSION['user'] = ['id'=>(int)$row['id'], 'username'=>$row['username'], 'name'=>$row['name'], 'role'=>$row['role']];
            get_pdo()->prepare("UPDATE users SET last_login=NOW() WHERE id=?")->execute([$row['id']]);
            header('Location: ' . SITE_URL . 'admin/index.php'); exit;
        }
        $error = 'اسم المستخدم أو كلمة المرور غير صحيحة';
    } else {
        $error = 'يرجى ملء جميع الحقول';
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>تسجيل الدخول | لوحة التحكم</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&family=Amiri:wght@700&display=swap" rel="stylesheet">
<style>
  :root {
    --primary: #f2a000;
    --primary-dark: #d98900;
    --green: #3e982b;
    --bg: #0a0f1e;
    --surface: rgba(30, 41, 59, 0.7);
    --border: rgba(255, 255, 255, 0.1);
    --text: #f8fafc;
    --text-muted: #94a3b8;
  }

  * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Cairo', sans-serif; }
  
  body {
    background-color: var(--bg);
    color: var(--text);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    position: relative;
  }

  /* الخلفية المتحركة (Blobs) */
  .blob {
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    z-index: 0;
    animation: float 15s infinite alternate ease-in-out;
  }
  .blob-1 {
    width: 500px; height: 500px;
    background: rgba(242, 160, 0, 0.25);
    top: -150px; right: -100px;
  }
  .blob-2 {
    width: 400px; height: 400px;
    background: rgba(62, 152, 43, 0.2);
    bottom: -100px; left: -100px;
    animation-delay: -5s;
  }
  .blob-3 {
    width: 300px; height: 300px;
    background: rgba(242, 160, 0, 0.15);
    top: 50%; left: 30%;
    animation-delay: -10s;
  }

  @keyframes float {
    0% { transform: translate(0, 0) scale(1); }
    100% { transform: translate(-40px, 40px) scale(1.1); }
  }

  /* الحاوية الرئيسية (Split Screen) */
  .login-container {
    position: relative;
    z-index: 1;
    display: flex;
    width: 100%;
    max-width: 1100px;
    height: 650px;
    background: rgba(15, 23, 42, 0.4);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid var(--border);
    border-radius: 30px;
    overflow: hidden;
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
    margin: 20px;
  }

  /* القسم الأيسر (الصورة والعبارات) */
  .login-left {
    flex: 1;
    background: linear-gradient(135deg, rgba(15,23,42,0.8), rgba(15,23,42,0.95)), url('https://images.unsplash.com/photo-1543116789-9346fb36b0c6?q=80&w=1000&auto=format&fit=crop') center/cover;
    padding: 50px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    position: relative;
  }

  .brand {
    display: flex;
    align-items: center;
    gap: 15px;
  }
  .brand-icon {
    width: 55px; height: 55px;
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    border-radius: 16px;
    display: grid; place-items: center;
    font-size: 30px; font-weight: 900; color: #0f172a;
    font-family: 'Amiri', serif;
    box-shadow: 0 10px 20px rgba(242, 160, 0, 0.3);
  }
  .brand-text h2 { font-size: 22px; font-weight: 800; margin-bottom: 2px; }
  .brand-text p { color: var(--text-muted); font-size: 13px; }

  .welcome-text h1 {
    font-size: 38px;
    font-weight: 900;
    line-height: 1.3;
    margin-bottom: 20px;
    background: linear-gradient(to left, #fff, #f2a000);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
  }
  .welcome-text p {
    color: var(--text-muted);
    font-size: 15px;
    line-height: 1.8;
    max-width: 90%;
  }

  .features { display: flex; gap: 20px; }
  .feature {
    display: flex; align-items: center; gap: 10px;
    font-size: 13px; font-weight: 700; color: #e2e8f0;
  }
  .feature-icon {
    width: 32px; height: 32px;
    background: rgba(242, 160, 0, 0.15);
    border: 1px solid rgba(242, 160, 0, 0.3);
    border-radius: 8px;
    display: grid; place-items: center;
    color: var(--primary);
  }

  /* القسم الأيمن (الفورم) */
  .login-right {
    flex: 1;
    padding: 60px 50px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    background: rgba(15, 23, 42, 0.3);
  }

  .form-header { margin-bottom: 40px; }
  .form-header h2 { font-size: 28px; font-weight: 900; margin-bottom: 8px; }
  .form-header p { color: var(--text-muted); font-size: 14px; }

  .alert-error {
    background: rgba(239, 68, 68, 0.1);
    border: 1px solid rgba(239, 68, 68, 0.3);
    color: #fca5a5;
    padding: 12px 16px;
    border-radius: 12px;
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 25px;
    display: flex; align-items: center; gap: 10px;
  }

  .input-group {
    position: relative;
    margin-bottom: 25px;
  }
  .input-group input {
    width: 100%;
    padding: 16px 45px 16px 16px;
    background: rgba(15, 23, 42, 0.6);
    border: 2px solid var(--border);
    border-radius: 14px;
    color: #fff;
    font-size: 15px;
    font-weight: 600;
    transition: all 0.3s ease;
    outline: none;
  }
  .input-group input:focus {
    border-color: var(--primary);
    background: rgba(15, 23, 42, 0.9);
    box-shadow: 0 0 0 4px rgba(242, 160, 0, 0.15);
  }
  .input-group label {
    position: absolute;
    right: 45px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
    font-size: 14px;
    font-weight: 600;
    pointer-events: none;
    transition: all 0.3s ease;
  }
  /* Floating Label Magic */
  .input-group input:focus ~ label,
  .input-group input:not(:placeholder-shown) ~ label {
    top: -10px;
    right: 15px;
    font-size: 12px;
    color: var(--primary);
    background: #151f32;
    padding: 0 8px;
    border-radius: 4px;
  }
  .input-icon {
    position: absolute;
    right: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
    transition: 0.3s;
  }
  .input-group input:focus ~ .input-icon { color: var(--primary); }

  .btn-login {
    width: 100%;
    padding: 16px;
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    color: #0f172a;
    border: none;
    border-radius: 14px;
    font-size: 16px;
    font-weight: 900;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 10px 25px rgba(242, 160, 0, 0.3);
    margin-top: 10px;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 10px;
  }
  .btn-login:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 35px rgba(242, 160, 0, 0.4);
  }

  .back-link {
    text-align: center;
    margin-top: 25px;
  }
  .back-link a {
    color: var(--text-muted);
    text-decoration: none;
    font-size: 13px;
    font-weight: 600;
    transition: 0.3s;
  }
  .back-link a:hover { color: var(--primary); }

  /* Responsive */
  @media (max-width: 900px) {
    .login-container { flex-direction: column; height: auto; max-width: 480px; }
    .login-left { display: none; }
    .login-right { padding: 40px 30px; }
  }
</style>
</head>
<body>

<div class="blob blob-1"></div>
<div class="blob blob-2"></div>
<div class="blob blob-3"></div>

<div class="login-container">
  
  <!-- القسم الأيسر -->
  <div class="login-left">
    <div class="brand">
      <div class="brand-icon">ب</div>
      <div class="brand-text">
        <h2>بيت الشعر والخيام</h2>
        <p>لوحة التحكم</p>
      </div>
    </div>

    <div class="welcome-text">
      <h1>مرحباً بك مجدداً<br>في عالمك الرقمي</h1>
      <p>تحكم في كل تفاصيل موقعك من مكان واحد. أضف خدماتك، عدل صورك، واستقبل رسائل عملائك بكل سهولة واحترافية.</p>
    </div>

    <div class="features">
      <div class="feature">
        <div class="feature-icon">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
        </div>
        إدارة شاملة
      </div>
      <div class="feature">
        <div class="feature-icon">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
        </div>
        حماية فائقة
      </div>
    </div>
  </div>

  <!-- القسم الأيمن (الفورم) -->
  <div class="login-right">
    <div class="form-header">
      <h2>تسجيل الدخول</h2>
      <p>أدخل بياناتك للوصول إلى لوحة التحكم</p>
    </div>

    <?php if ($error): ?>
      <div class="alert-error">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form method="POST" autocomplete="off">
      <?= csrf_field() ?>
      
      <div class="input-group">
        <input type="text" name="username" id="username" placeholder=" " required autofocus>
        <label for="username">اسم المستخدم</label>
        <svg class="input-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
      </div>

      <div class="input-group">
        <input type="password" name="password" id="password" placeholder=" " required>
        <label for="password">كلمة المرور</label>
        <svg class="input-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
      </div>

      <button type="submit" class="btn-login">
        دخول آمن
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
      </button>
    </form>

    <div class="back-link">
      <a href="<?= SITE_URL ?>">← العودة إلى الموقع الرئيسي</a>
    </div>
  </div>

</div>

</body>
</html>