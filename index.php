<?php
require_once __DIR__ . '/includes/functions.php';
$pdo = get_pdo();

$services  = $pdo->query("SELECT * FROM services WHERE is_active=1 ORDER BY sort_order,id")->fetchAll();
$occasions = $pdo->query("SELECT * FROM occasions WHERE is_active=1 ORDER BY sort_order,id")->fetchAll();
$gallery   = $pdo->query("SELECT g.*, c.slug AS cat_slug, c.name AS cat_name
                          FROM gallery g LEFT JOIN gallery_categories c ON g.category_id=c.id
                          WHERE g.is_active=1 ORDER BY g.sort_order,g.id")->fetchAll();
$faqs      = $pdo->query("SELECT * FROM faqs WHERE is_active=1 ORDER BY sort_order,id")->fetchAll();
$cats      = $pdo->query("SELECT * FROM gallery_categories ORDER BY sort_order,id")->fetchAll();

$phone   = get_setting('phone', '0530729970');
$wa      = get_setting('whatsapp', '966530729970');
$siteName= get_setting('site_name', 'تأجير خيام وبيوت شعر');
$tagline = get_setting('site_tagline', 'للمناسبات والضيافة');

// دالة مساعدة لعرض النصوص اللي فيها وسوم HTML مسموحة زي <em> و <br>
function safe_em($s) { return strip_tags((string)$s, '<em><strong><br><span>'); }
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="theme-color" content="#ffffff">
  <meta name="description" content="<?= e(get_setting('meta_description')) ?>">
  <meta name="keywords" content="تأجير بيوت شعر, تأجير خيام بالرياض, تأجير جلسات, تأجير كراسي, تأجير مكيفات, مستلزمات حفلات, الرياض">
  <meta property="og:title" content="<?= e($siteName) ?>">
  <meta property="og:description" content="<?= e($tagline) ?>">
  <title><?= e($siteName) ?> | <?= e($phone) ?></title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&family=Amiri:wght@700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css?v=<?= time() ?>">

  <script type="application/ld+json">
  {
    "@context":"https://schema.org",
    "@type":"LocalBusiness",
    "name":<?= json_encode($siteName, JSON_UNESCAPED_UNICODE) ?>,
    "description":<?= json_encode(get_setting('meta_description'), JSON_UNESCAPED_UNICODE) ?>,
    "telephone":"+<?= e($wa) ?>",
    "areaServed":["الرياض","المناطق المحيطة بالرياض"],
    "priceRange":"حسب الطلب"
  }
  </script>
</head>
<body>

  <!-- HEADER -->
  <header class="site-header" id="top">
    <div class="container nav-wrap">
      <a class="brand" href="#top">
        <span class="brand-mark">ب</span>
        <span>
          <strong><?= e($siteName) ?></strong>
          <small><?= e($tagline) ?></small>
        </span>
      </a>

      <button class="menu-toggle" aria-label="فتح القائمة" aria-expanded="false">
        <span></span><span></span><span></span>
      </button>

      <nav class="main-nav">
        <a href="#top">الرئيسية</a>
        <a href="#about">من نحن</a>
        <a href="#services">خدماتنا</a>
        <a href="#occasions">مناسباتنا</a>
        <a href="#why-us">مميزاتنا</a>
        <a href="#gallery">معرض الصور</a>
        <a href="#contact">تواصل معنا</a>
      </nav>

      <a class="nav-call" href="tel:<?= e($phone) ?>">
        <span>اتصل الآن</span>
        <b dir="ltr"><?= e($phone) ?></b>
      </a>
    </div>
  </header>

  <main>
    <!-- HERO SECTION -->
    <section class="hero hero-reference" id="home">
      <div class="container hero-grid">
        <div class="hero-content">
          <div class="eyebrow reveal"><span></span>خدماتنا داخل وخارج الرياض</div>
          <h1 class="reveal"><?= e(get_setting('hero_title')) ?></h1>
          <h2 class="hero-subtitle reveal"><?= e(get_setting('hero_subtitle')) ?></h2>
          <p class="hero-lead reveal"><?= e(get_setting('hero_lead')) ?></p>
          <div class="hero-actions reveal">
            <a class="btn btn-gold" href="tel:<?= e($phone) ?>">اتصل الآن <span>☎</span></a>
            <a class="btn btn-outline" href="https://wa.me/<?= e($wa) ?>" target="_blank" rel="noopener">احجز عبر واتساب</a>
          </div>
          <div class="hero-trust reveal">
            <div><b>بيوت شعر وخيام</b><span>تجهيزات متنوعة</span></div>
            <i></i>
            <div><b>داخل وخارج</b><span>الرياض</span></div>
            <i></i>
            <div><b dir="ltr"><?= e($phone) ?></b><span>للحجز والاستفسار</span></div>
          </div>
        </div>
        <div class="hero-visual reveal">
          <div class="hero-photo-frame">
            <div class="hero-photo">
              <img src="<?= e(get_setting('hero_image', 'imgs/img6.jpeg')) ?>" alt="<?= e($siteName) ?>" fetchpriority="high">
              <span class="hero-photo-label">
                <b>تأجير بيوت الشعر والخيام</b>
                <small>للمناسبات والضيافة</small>
              </span>
            </div>
          </div>
          <div class="hero-sticker"><span>✦</span><b>أجواء أصيلة</b><small>وتجهيز يليق بمناسبتكم</small></div>
          <div class="hero-side-note"><?= e($phone) ?></div>
        </div>
      </div>
      <div class="hero-bottom-line"></div>
    </section>

    <!-- QUICK SERVICES -->
    <section class="quick-services">
      <div class="container quick-grid">
        <div><span class="quick-icon">⌂</span><b>بيوت شعر وخيام</b></div>
        <div><span class="quick-icon">✧</span><b>جلسات وضيافة</b></div>
        <div><span class="quick-icon">❄</span><b>مكيفات ومولدات</b></div>
        <div><span class="quick-icon">♬</span><b>صوتيات ومستلزمات</b></div>
      </div>
    </section>

    <!-- ABOUT -->
    <section class="section about-section" id="about">
      <div class="container about-grid">
        <div class="about-art reveal">
          <div class="about-image">
            <img src="<?= e(get_setting('about_image', 'imgs/img3.jpeg')) ?>" alt="تجهيز بيوت الشعر والخيام للمناسبات" loading="lazy">
            <div class="about-seal">أصالة<br><b>وتميز</b></div>
          </div>
          <div class="about-mini-card"><b>كل احتياجاتك</b><span>في مكان واحد</span></div>
        </div>
        <div class="about-copy reveal">
          <div class="section-kicker">من نحن</div>
          <h2><?= safe_em(get_setting('about_title')) ?></h2>
          <?php
          $aboutParas = preg_split('/\n\s*\n/', get_setting('about_text', ''));
          foreach ($aboutParas as $para):
              if (trim($para) === '') continue;
          ?>
          <p><?= nl2br(e(trim($para))) ?></p>
          <?php endforeach; ?>
          <div class="about-points">
            <span>✓ خيارات متنوعة للمناسبات</span>
            <span>✓ تجهيزات حسب احتياجك</span>
            <span>✓ خدمة داخل وخارج الرياض</span>
            <span>✓ سهولة التواصل والحجز</span>
          </div>
          <a href="https://wa.me/<?= e($wa) ?>" class="text-link" target="_blank" rel="noopener">
            تحدث معنا عبر واتساب <b>←</b>
          </a>
        </div>
      </div>
    </section>

    <!-- SERVICES -->
    <section class="section services-section" id="services">
      <div class="container">
        <div class="section-heading reveal">
          <div><div class="section-kicker">خدماتنا</div><h2>كل ما يلزم <em>حفلتك ومناسبتك</em></h2></div>
          <p>خدمات متعددة لتجهيز المكان وتوفير أجواء مريحة لضيوفك، اختر ما تحتاجه ودع الباقي علينا.</p>
        </div>
        <div class="services-grid">
          <?php foreach ($services as $s): ?>
          <article class="service-card reveal">
            <span class="service-icon"><?= e($s['icon']) ?></span>
            <h3><?= e($s['title']) ?></h3>
            <p><?= e($s['description']) ?></p>
            <a href="#contact">اطلب الخدمة <b>←</b></a>
          </article>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <!-- OCCASIONS -->
    <section class="section occasions-section" id="occasions">
      <div class="container">
        <div class="section-heading reveal">
          <div><div class="section-kicker">لكل مناسبة تجهيزها</div><h2>نجهز أجواء <em>مناسباتكم</em></h2></div>
          <p>مهما كان نوع مناسبتك، نساعدك في اختيار بيت الشعر أو الخيمة والجلسات والتجهيزات التي تناسب طبيعة الحدث وعدد ضيوفك.</p>
        </div>
        <div class="occasions-grid">
          <?php
          $occasionClasses = ['occasion-wedding', 'occasion-family', 'occasion-events', 'occasion-hospitality'];
          foreach ($occasions as $i => $o):
              $cls = $occasionClasses[$i % 4] ?? 'occasion-wedding';
          ?>
          <article class="occasion-card <?= $cls ?> reveal">
            <span class="occasion-number"><?= str_pad($i + 1, 2, '0', STR_PAD_LEFT) ?></span>
            <div>
              <small><?= e($o['subtitle']) ?></small>
              <h3><?= e($o['title']) ?></h3>
              <p><?= e($o['description']) ?></p>
            </div>
          </article>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <!-- WHY US -->
    <section class="why-section" id="why-us">
      <div class="container why-layout">
        <div class="why-copy reveal">
          <div class="section-kicker">اختيار يريحك</div>
          <h2>كل التفاصيل المهمة<br><em>تلقاها في مكان واحد</em></h2>
          <p>بدل ما تتعب في البحث عن كل تجهيز بشكل منفصل، تواصل معنا وحدد احتياجات مناسبتك. نوفر لك مجموعة من خدمات التأجير والضيافة لتنسيق طلبك بصورة أسهل.</p>
          <a class="btn btn-gold" href="tel:<?= e($phone) ?>">ناقش تجهيز مناسبتك <span>←</span></a>
        </div>
        <div class="why-points">
          <article class="why-point reveal"><span>✦</span><div><h3>تنوع في الخدمات</h3><p>من بيوت الشعر والخيام إلى الجلسات والكراسي والطاولات والصوتيات.</p></div></article>
          <article class="why-point reveal"><span>⌖</span><div><h3>داخل وخارج الرياض</h3><p>نستقبل استفساراتكم من الرياض والمناطق الأخرى، ويتم تنسيق الخدمة حسب الموقع.</p></div></article>
          <article class="why-point reveal"><span>☏</span><div><h3>تواصل وحجز مباشر</h3><p>تواصل معنا وشاركنا تاريخ المناسبة وموقعها والتجهيزات المطلوبة.</p></div></article>
          <article class="why-point reveal"><span>♧</span><div><h3>اهتمام بتفاصيل الضيافة</h3><p>خيارات للجلسات والفرش والقهوجيين والصبابين لتكتمل أجواء الاستقبال.</p></div></article>
        </div>
      </div>
    </section>

    <!-- FEATURE BAND -->
    <section class="feature-band">
      <div class="container feature-band-inner reveal">
        <div>
          <span class="section-kicker">تجهيز أسهل</span>
          <h2>من أول اتصال.. <em>إلى اكتمال المناسبة</em></h2>
          <p>أخبرنا عن موقعك وعدد الضيوف ونوع المناسبة، وسنساعدك في تحديد التجهيزات التي تحتاجها.</p>
        </div>
        <a href="tel:<?= e($phone) ?>" class="btn btn-gold">اتصل بنا الآن <span>←</span></a>
      </div>
    </section>

    <!-- GALLERY -->
    <section class="section gallery-section" id="gallery">
      <div class="container">
        <div class="section-heading reveal">
          <div><div class="section-kicker">من أعمالنا</div><h2>شاهد بعض <em>التجهيزات</em></h2></div>
        </div>
        <div class="gallery-filters reveal">
          <button class="filter-btn active" data-filter="all">الكل</button>
          <?php foreach ($cats as $c): ?>
          <button class="filter-btn" data-filter="<?= e($c['slug']) ?>"><?= e($c['name']) ?></button>
          <?php endforeach; ?>
        </div>
        <div class="gallery-grid">
          <?php foreach ($gallery as $g): ?>
          <figure class="gallery-item reveal" data-category="<?= e($g['cat_slug'] ?: 'all') ?>">
            <img src="<?= e($g['image']) ?>" alt="<?= e($g['title'] ?: 'صورة من أعمالنا') ?>" loading="lazy">
            <figcaption><?= e($g['title'] ?: $g['cat_name']) ?></figcaption>
          </figure>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <!-- STEPS -->
    <section class="steps-section">
      <div class="container">
        <div class="section-heading light-heading reveal">
          <div><div class="section-kicker">كيف تطلب؟</div><h2>خطوات بسيطة.. <em>وتكون جاهز</em></h2></div>
          <p>نبدأ بفهم احتياجك حتى تختار التجهيز المناسب لمناسبتك.</p>
        </div>
        <div class="steps-grid">
          <div class="step reveal"><b>01</b><h3>تواصل معنا</h3><p>اتصل أو راسلنا على واتساب.</p></div>
          <div class="step reveal"><b>02</b><h3>حدد احتياجك</h3><p>اذكر نوع المناسبة والموقع والخدمات المطلوبة.</p></div>
          <div class="step reveal"><b>03</b><h3>نسق التجهيزات</h3><p>ناقش معنا الخيارات والتفاصيل المناسبة لك.</p></div>
          <div class="step reveal"><b>04</b><h3>استعد لمناسبتك</h3><p>استمتع بمناسبتك بعد ترتيب احتياجاتك.</p></div>
        </div>
      </div>
    </section>

    <!-- FAQ -->
    <section class="section faq-section">
      <div class="container faq-grid">
        <div class="faq-intro reveal">
          <div class="section-kicker">أسئلة شائعة</div>
          <h2>هل لديك <em>استفسار؟</em></h2>
          <p>إذا لم تجد إجابة سؤالك هنا، تواصل معنا مباشرة وسنساعدك في معرفة التفاصيل.</p>
          <a class="text-link" href="https://wa.me/<?= e($wa) ?>" target="_blank" rel="noopener">راسلنا على واتساب ←</a>
        </div>
        <div class="faq-list reveal">
          <?php foreach ($faqs as $i => $f): ?>
          <details<?= $i === 0 ? ' open' : '' ?>>
            <summary><?= e($f['question']) ?></summary>
            <p><?= e($f['answer']) ?></p>
          </details>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <!-- CONTACT -->
    <section class="contact-section" id="contact">
      <div class="container contact-card reveal">
        <div class="contact-decoration">✦</div>
        <div class="section-kicker">جاهزون لخدمتكم</div>
        <h2>خلّ تجهيز مناسبتك <em>علينا</em></h2>
        <p>للحجز والاستفسار عن تأجير بيوت الشعر والخيام والجلسات وجميع مستلزمات الحفلات داخل وخارج الرياض.</p>
        <a class="contact-number" href="tel:<?= e($phone) ?>" dir="ltr"><?= e($phone) ?></a>
        <div class="contact-actions">
          <a class="btn btn-dark" href="tel:<?= e($phone) ?>">اتصال مباشر</a>
          <a class="btn btn-whatsapp" href="https://wa.me/<?= e($wa) ?>" target="_blank" rel="noopener">تواصل عبر واتساب</a>
        </div>
        <span class="service-area">نخدمكم داخل وخارج الرياض</span>
      </div>
    </section>
  </main>

  <!-- FOOTER -->
  <footer class="site-footer">
    <div class="container footer-main">
      <div class="footer-brand">
        <a class="brand" href="#top">
          <span class="brand-mark">ب</span>
          <span><strong><?= e($siteName) ?></strong><small><?= e($tagline) ?></small></span>
        </a>
        <p><?= e(get_setting('footer_text')) ?></p>
      </div>
      <div>
        <h3>روابط سريعة</h3>
        <a href="#about">من نحن</a><a href="#services">خدماتنا</a><a href="#gallery">معرض الصور</a><a href="#contact">تواصل معنا</a>
      </div>
      <div>
        <h3>خدماتنا</h3>
        <span>بيوت شعر وخيام</span><span>جلسات وكراسي وطاولات</span><span>مكيفات ومولدات</span><span>قهوجيين وصبابين</span>
      </div>
      <div>
        <h3>تواصل معنا</h3>
        <a href="tel:<?= e($phone) ?>" dir="ltr"><?= e($phone) ?></a><span>داخل وخارج الرياض</span>
        <a href="https://wa.me/<?= e($wa) ?>" target="_blank" rel="noopener">واتساب</a>
      </div>
    </div>
    <div class="footer-bottom">
      <div class="container">
        <span>© <span id="year"></span> جميع الحقوق محفوظة</span>
        <p class="designer-credit">تصميم وتنفيذ <a href="https://www.facebook.com/share/199H6ZiBeM/" target="_blank" style="text-decoration: none;"><span class="gmt-text" style="color: var(--primary); font-weight: bold;">GMT Web</span></a></p>
      </div>
    </div>
  </footer>

  <a class="floating-contact floating-phone" href="tel:<?= e($phone) ?>" aria-label="اتصل بنا">☎</a>
  <a class="floating-contact floating-wa" href="https://wa.me/<?= e($wa) ?>" target="_blank" rel="noopener" aria-label="واتساب">
    <svg viewBox="0 0 32 32" aria-hidden="true"><path d="M16 3.2A12.6 12.6 0 0 0 5.2 22.3L3.5 28.7l6.6-1.7A12.6 12.6 0 1 0 16 3.2Zm0 22.8c-2 0-3.9-.5-5.6-1.6l-.4-.2-3.9 1 1-3.8-.3-.4A10.4 10.4 0 1 1 16 26Zm5.7-7.8c-.3-.2-1.8-.9-2.1-1s-.5-.2-.7.2-.8 1-1 1.2-.4.2-.7.1a8.5 8.5 0 0 1-2.5-1.5 9.3 9.3 0 0 1-1.7-2.1c-.2-.3 0-.5.2-.7l.5-.6.2-.5c.1-.2 0-.4 0-.6l-1-2.3c-.2-.6-.5-.5-.7-.5h-.6c-.2 0-.6.1-.9.4s-1.2 1.2-1.2 2.8 1.2 3.2 1.4 3.4 2.4 3.7 5.9 5.1c.8.3 1.4.5 1.9.6.8.2 1.5.2 2 .1.6-.1 1.8-.7 2-1.4.2-.7.2-1.3.1-1.4s-.3-.2-.6-.4Z"/></svg>
  </a>

  <script src="script.js"></script>
</body>
</html>