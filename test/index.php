<?php
// index.php - Final improved UI/UX for IMA Latur Runathon 2026
// - Fixed alignment and spacing issues, mobile-first and responsive
// - Poster + key summary visible at top on mobile
// - Package buttons stacked, properly aligned with consistent padding
// - Sticky right panel on desktop correctly sized and not overlapping content
// - Floating mobile summary properly positioned with page bottom padding
// - Collapsible detail sections, gallery lightbox
// - "Create Account" removed; Register -> userpanel/register.php?ticket=...
// NOTE: Implement server-side validation & OTP auto-account logic in userpanel/register.php

session_start();

// site config (optional override)
$site_name = 'IMA Latur Runathon 2026';
$app_debug = false;
if (file_exists(__DIR__ . '/config/app_config.php')) {
    @include_once __DIR__ . '/config/app_config.php';
    if (defined('SITE_NAME')) $site_name = SITE_NAME;
    if (defined('APP_DEBUG')) $app_debug = APP_DEBUG;
}

// upload dirs (read-only here)
$uploads_dir = __DIR__ . '/public_assets/uploads';
$poster_dir  = $uploads_dir . '/poster';
$gallery_dir = $uploads_dir . '/gallery';
if (!is_dir($gallery_dir)) @mkdir($gallery_dir, 0755, true);
if (!is_dir($poster_dir)) @mkdir($poster_dir, 0755, true);

// ticket options (server must validate)
$ticket_options = [
    '800'  => ['label' => '5 km',  'price' => 800,  'desc' => 'Fun Run'],
    '1000' => ['label' => '10 km', 'price' => 1000, 'desc' => '10K — timed'],
    '1200' => ['label' => '21 km', 'price' => 1200, 'desc' => 'Half Marathon — chip timing'],
];
$default_ticket = '800';
$sel_ticket = $_GET['ticket'] ?? $default_ticket;
if (!array_key_exists($sel_ticket, $ticket_options)) $sel_ticket = $default_ticket;

// find poster if uploaded
$poster_file = null;
$poster_candidates = glob($poster_dir . '/poster.*');
if (!empty($poster_candidates)) $poster_file = str_replace(__DIR__ . '/', '', $poster_candidates[0]);

// gallery images
$gallery_images = [];
foreach (glob($gallery_dir . '/*') as $img_path) {
    $gallery_images[] = str_replace(__DIR__ . '/', '', $img_path);
}
usort($gallery_images, function ($a, $b) {
    return filemtime(__DIR__ . '/' . $b) <=> filemtime(__DIR__ . '/' . $a);
});

// inline poster placeholder
$placeholder_svg = 'data:image/svg+xml;utf8,' . rawurlencode('
  <svg xmlns="http://www.w3.org/2000/svg" width="1200" height="700" viewBox="0 0 1200 700">
    <rect width="100%" height="100%" fill="#eef6ff"/>
    <g font-family="Inter, Arial, sans-serif" fill="#0f172a">
      <text x="50%" y="44%" text-anchor="middle" font-size="40" font-weight="700">IMA Latur Runathon 2026</text>
      <text x="50%" y="52%" text-anchor="middle" font-size="16" fill="#64748b">(Event poster)</text>
    </g>
  </svg>
');

function h($s){ return htmlspecialchars($s, ENT_QUOTES|ENT_SUBSTITUTE, 'UTF-8'); }
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?php echo h($site_name); ?></title>
  <meta name="description" content="IMA Latur Runathon 2026 — register for 3/5/10/21km. Quick OTP guest checkout.">
  <style>
    :root{
      --bg:#f6fbff;
      --card:#ffffff;
      --muted:#6b7280;
      --accent:#0f172a;
      --primary:#1d4ed8;
      --radius:12px;
      --gap:14px;
      --max-width:1060px;
    }
    *{box-sizing:border-box}
    html,body{height:100%;margin:0;background:var(--bg);color:var(--accent);font-family:Inter,system-ui,-apple-system,"Segoe UI",Roboto,"Noto Sans",Arial; -webkit-font-smoothing:antialiased}
    a{color:inherit}
    .page{max-width:var(--max-width);margin:0 auto;padding:16px}
    .header{display:flex;align-items:center;gap:12px;margin-bottom:12px}
    .logo{width:56px;height:56px;border-radius:10px;background:#e6eefc;display:flex;align-items:center;justify-content:center;overflow:hidden;flex:0 0 56px}
    .logo img{width:100%;height:100%;object-fit:cover}
    .title-block{flex:1;min-width:0}
    .title{font-weight:800;font-size:18px;line-height:1}
    .subtitle{color:var(--muted);font-size:13px;margin-top:4px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
    .powered{font-size:12px;color:var(--muted);margin-left:12px;white-space:nowrap}

    /* container card */
    .card{background:var(--card);border-radius:var(--radius);box-shadow:0 12px 32px rgba(12,18,31,0.06);overflow:hidden}
    .content{display:grid;grid-template-columns:1fr 380px;gap:20px;padding:18px;border-top:1px solid #f1f7ff}
    @media (max-width:880px){ .content{grid-template-columns:1fr;padding:12px} }

    /* Left column */
    .left{display:flex;flex-direction:column;gap:14px}
    .poster{border-radius:10px;overflow:hidden;border:1px solid #eef6ff;background:#fff}
    .poster img{width:100%;height:auto;display:block;max-height:460px;object-fit:cover}

    .key-row{display:flex;justify-content:space-between;gap:12px;align-items:flex-start}
    .key-left{flex:1;min-width:0}
    .event-name{font-weight:800;font-size:18px;margin-bottom:6px}
    .meta{color:var(--muted);font-size:14px;line-height:1.45}

    .badges{display:flex;gap:8px;flex-wrap:wrap;margin-top:8px}
    .badge{background:#eef2ff;color:#0b3a8a;padding:6px 8px;border-radius:999px;font-weight:700;font-size:13px}

    /* pack buttons stacked */
    .packages{display:flex;flex-direction:column;gap:10px;margin-top:8px}
    .pkg{display:flex;align-items:center;justify-content:space-between;padding:12px;border-radius:10px;border:1px solid #eef6ff;background:#fff;cursor:pointer;transition:transform .08s,box-shadow .08s}
    .pkg:hover{transform:translateY(-3px);box-shadow:0 10px 30px rgba(2,8,23,0.06)}
    .pkg.active{border-color:var(--primary);box-shadow:0 14px 36px rgba(37,99,235,0.12)}
    .pkg .meta{display:flex;flex-direction:column;align-items:flex-start;min-width:0}
    .pkg .label{font-weight:800}
    .pkg .desc{font-size:13px;color:var(--muted)}
    .price{background:var(--primary);color:#fff;padding:8px 10px;border-radius:8px;font-weight:900;white-space:nowrap}

    .actions{display:flex;gap:10px;margin-top:8px}
    .btn{flex:1;padding:12px;border-radius:10px;border:none;background:var(--primary);color:#fff;font-weight:900;cursor:pointer}
    .btn.ghost{background:#fff;color:var(--primary);border:1px solid var(--primary)}

    /* details accordions */
    .details{display:flex;flex-direction:column;gap:12px}
    .card-section{background:#fff;padding:12px;border-radius:10px;border:1px solid #f1f7ff}
    .accordion{display:block}
    .accordion .head{display:flex;justify-content:space-between;align-items:center;padding:10px;border-radius:8px;border:1px solid #f1f7ff;background:#fff;cursor:pointer}
    .accordion .body{display:none;padding-top:10px}

    /* gallery */
    .gallery{display:grid;grid-template-columns:repeat(2,1fr);gap:8px}
    .gallery img{width:100%;height:120px;object-fit:cover;border-radius:8px;cursor:pointer}

    /* right column */
    .right{display:flex;flex-direction:column;gap:12px}
    .panel{position:sticky;top:16px;padding:14px;border-radius:10px;border:1px solid #f1f7ff;background:linear-gradient(180deg,#fff,#fbfeff)}
    .panel h3{margin:0 0 6px 0}
    .side-summary{display:flex;justify-content:space-between;align-items:center;margin-top:8px}
    .side-summary .label{font-weight:800}
    .side-summary .price{font-weight:900}

    /* mobile floating summary */
    .mobile-summary{position:fixed;left:12px;right:12px;bottom:16px;margin:auto;max-width:var(--max-width);display:flex;justify-content:space-between;align-items:center;gap:10px;padding:12px;border-radius:12px;background:#fff;box-shadow:0 18px 50px rgba(2,8,23,0.12);border:1px solid #eef6ff;transform:translateY(110%);opacity:0;transition:all .28s}
    .mobile-summary.show{transform:translateY(0);opacity:1}
    .mobile-summary .price{font-weight:900}
    @media(min-width:881px){ .mobile-summary{display:none} }

    /* accessibility focus */
    .pkg:focus, .accordion .head:focus{outline:3px solid rgba(37,99,235,0.12);outline-offset:3px}
    footer{padding:18px;text-align:center;color:var(--muted);font-size:13px}
  </style>
</head>
<body>
  <div class="page">
    <header class="header" role="banner">
      <div class="logo"><img src="public_assets/img/imalatur-logo.png" alt="IMA Latur" onerror="this.style.display='none'"></div>
      <div class="title-block">
        <div class="title"><?php echo h($site_name); ?></div>
        <div class="subtitle">Sunday, 8 Dec 2026 · Bidve Lawns</div>
      </div>
      <div class="powered">Powered by LATUR URBAN CO-OP BANK LTD., Latur</div>
    </header>

    <main class="card" role="main" aria-labelledby="main-heading">
      <div class="content">
        <section class="left">
          <div>
            <div class="event-name" id="main-heading">IMA Latur Runathon 2026</div>
            <div class="poster" aria-hidden="false">
              <img src="<?php echo $poster_file ? h($poster_file) : $placeholder_svg; ?>" alt="Event poster">
            </div>
          </div>

          <div class="key-row" style="margin-top:6px">
            <div class="key-left">
              <div class="meta"><strong>Venue:</strong> Bidve Lawns</div>
              <div class="meta" style="margin-top:6px"><strong>Route:</strong> Bidve Lawns → PVR Chowk → Railway Station Road</div>
              <div class="badges" aria-hidden="false">
                <span class="badge">3 km</span>
                <span class="badge">5 km</span>
                <span class="badge">10 km</span>
                <span class="badge">21 km</span>
              </div>
            </div>
            <div style="text-align:right;min-width:120px">
              <div class="small">Food Partner</div>
              <div style="font-weight:800;margin-top:6px">HOTEL CARNIVAL RESORT</div>
            </div>
          </div>

          <!-- Packages -->
          <div style="margin-top:12px">
            <div class="packages" role="radiogroup" aria-label="Packages">
              <?php foreach($ticket_options as $code => $info):
                $active = ($sel_ticket === $code) ? ' active' : '';
              ?>
                <button class="pkg<?php echo $active; ?>" data-ticket="<?php echo h($code); ?>" type="button" aria-checked="<?php echo $active ? 'true' : 'false'; ?>">
                  <div class="meta">
                    <div class="label"><?php echo h($info['label']); ?></div>
                    <div class="desc"><?php echo h($info['desc']); ?></div>
                  </div>
                  <div class="price">₹<?php echo number_format($info['price']); ?></div>
                </button>
              <?php endforeach; ?>
            </div>

            <div class="actions">
              <button id="registerBtn" class="btn" aria-label="Register">Register — Proceed</button>
              <a class="btn ghost" href="userpanel/login.php">Login</a>
            </div>
          </div>

          <!-- Details (accordion) -->
          <div class="details">
            <div class="card-section">
              <div class="accordion">
                <div class="head" tabindex="0" role="button" aria-expanded="false">Highlights <span class="small">tap to expand</span></div>
                <div class="body">
                  <ul>
                    <li>Most awaited Running event of Latur.</li>
                    <li>21km half marathon with chip timing.</li>
                    <li>Bibs with timing chips for all runner participants.</li>
                    <li>Pre-run warm-up with Zumba; en-route hydration and cheering teams.</li>
                    <li>Post-run refreshments and recovery zone with physiotherapists.</li>
                  </ul>
                </div>
              </div>
            </div>

            <div class="card-section">
              <div class="accordion">
                <div class="head" tabindex="0" role="button" aria-expanded="false">Awards & Benefits <span class="small">tap to expand</span></div>
                <div class="body">
                  <ul>
                    <li>Certificates & Medals for all participants.</li>
                    <li>Special prizes & trophies for winners in different categories.</li>
                    <li>T-shirt provided to participants registering before cutoff date.</li>
                  </ul>
                </div>
              </div>
            </div>

            <div class="card-section">
              <div class="accordion">
                <div class="head" tabindex="0" role="button" aria-expanded="false">Registration Charges <span class="small">tap to expand</span></div>
                <div class="body">
                  <ul>
                    <li>5 km: Rs. 800</li>
                    <li>10 km: Rs. 1,000</li>
                    <li>21 km: Rs. 1,200</li>
                  </ul>
                </div>
              </div>
            </div>

            <div class="card-section">
              <div class="accordion">
                <div class="head" tabindex="0" role="button" aria-expanded="false">T-shirt Sizing Charts <span class="small">tap to expand</span></div>
                <div class="body">
                  <strong>Unisex</strong>
                  <table style="width:100%;border-collapse:collapse;margin-top:8px">
                    <thead><tr><th style="border:1px solid #eef6ff;padding:8px">Body Size</th><th style="border:1px solid #eef6ff;padding:8px">T-Shirt Size</th><th style="border:1px solid #eef6ff;padding:8px">Length</th></tr></thead>
                    <tbody>
                      <tr><td style="border:1px solid #eef6ff;padding:8px">XS</td><td style="border:1px solid #eef6ff;padding:8px">32-33.5</td><td style="border:1px solid #eef6ff;padding:8px">25</td></tr>
                      <tr><td style="border:1px solid #eef6ff;padding:8px">S</td><td style="border:1px solid #eef6ff;padding:8px">35-36.5</td><td style="border:1px solid #eef6ff;padding:8px">26</td></tr>
                      <tr><td style="border:1px solid #eef6ff;padding:8px">M</td><td style="border:1px solid #eef6ff;padding:8px">37-39.5</td><td style="border:1px solid #eef6ff;padding:8px">27</td></tr>
                      <tr><td style="border:1px solid #eef6ff;padding:8px">L</td><td style="border:1px solid #eef6ff;padding:8px">40-42.5</td><td style="border:1px solid #eef6ff;padding:8px">28</td></tr>
                      <tr><td style="border:1px solid #eef6ff;padding:8px">XL</td><td style="border:1px solid #eef6ff;padding:8px">43-45.5</td><td style="border:1px solid #eef6ff;padding:8px">29</td></tr>
                      <tr><td style="border:1px solid #eef6ff;padding:8px">XXL</td><td style="border:1px solid #eef6ff;padding:8px">46-48.5</td><td style="border:1px solid #eef6ff;padding:8px">30</td></tr>
                    </tbody>
                  </table>
                  <hr style="margin:10px 0">
                  <strong>Children</strong>
                  <table style="width:100%;border-collapse:collapse;margin-top:8px">
                    <thead><tr><th style="border:1px solid #eef6ff;padding:8px">Body Size</th><th style="border:1px solid #eef6ff;padding:8px">T-Shirt Size</th><th style="border:1px solid #eef6ff;padding:8px">Length</th></tr></thead>
                    <tbody>
                      <tr><td style="border:1px solid #eef6ff;padding:8px">1-2 YRS</td><td style="border:1px solid #eef6ff;padding:8px">23-24.5</td><td style="border:1px solid #eef6ff;padding:8px">15.5</td></tr>
                      <tr><td style="border:1px solid #eef6ff;padding:8px">3-4 YRS</td><td style="border:1px solid #eef6ff;padding:8px">25-26.5</td><td style="border:1px solid #eef6ff;padding:8px">17</td></tr>
                      <tr><td style="border:1px solid #eef6ff;padding:8px">5-6 YRS</td><td style="border:1px solid #eef6ff;padding:8px">27-28.5</td><td style="border:1px solid #eef6ff;padding:8px">18.5</td></tr>
                      <tr><td style="border:1px solid #eef6ff;padding:8px">7-8 YRS</td><td style="border:1px solid #eef6ff;padding:8px">29-30.5</td><td style="border:1px solid #eef6ff;padding:8px">20</td></tr>
                      <tr><td style="border:1px solid #eef6ff;padding:8px">9-10 YRS</td><td style="border:1px solid #eef6ff;padding:8px">31-32.5</td><td style="border:1px solid #eef6ff;padding:8px">21.5</td></tr>
                      <tr><td style="border:1px solid #eef6ff;padding:8px">11-12 YRS</td><td style="border:1px solid #eef6ff;padding:8px">33-34.5</td><td style="border:1px solid #eef6ff;padding:8px">23</td></tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>

          <?php if (!empty($gallery_images)): ?>
            <div class="card-section">
              <div><strong>Gallery</strong></div>
              <div class="gallery" id="galleryGrid" style="margin-top:8px">
                <?php foreach ($gallery_images as $img): ?>
                  <img src="<?php echo h($img); ?>" alt="Gallery image" loading="lazy">
                <?php endforeach; ?>
              </div>
            </div>
          <?php endif; ?>
        </section>

        <aside class="right">
          <div class="panel" aria-labelledby="registerLabel">
            <h3 id="registerLabel">Quick Register</h3>
            <div class="small">Guest checkout via mobile OTP. Account auto-created on verification.</div>
            <div class="side-summary" style="margin-top:12px">
              <div class="label" id="sideLabel">5 km</div>
              <div class="price" id="sidePrice">₹800</div>
            </div>
            <div style="margin-top:12px">
              <a id="sideRegister" class="btn" href="userpanel/register.php?ticket=<?php echo urlencode($sel_ticket); ?>">Register — Proceed</a>
            </div>
          </div>
        </aside>
      </div>
    </main>

    <footer>
      &copy; <?php echo date('Y'); ?> IMA Latur — Team IMATHON IMA LATUR
      <?php if ($app_debug): ?> <span style="color:#d946ef">DEBUG MODE</span><?php endif; ?>
    </footer>

    <!-- mobile floating summary -->
    <div class="mobile-summary" id="mobileSummary" aria-hidden="true">
      <div>
        <div class="small">Selected</div>
        <div id="mLabel" style="font-weight:900">5 km</div>
      </div>
      <div style="display:flex;align-items:center;gap:12px">
        <div id="mPrice" style="font-weight:900">₹800</div>
        <button id="mRegister" class="btn">Register</button>
      </div>
    </div>

    <!-- lightbox -->
    <div id="lightbox" style="position:fixed;inset:0;display:none;align-items:center;justify-content:center;background:rgba(2,6,23,0.7);z-index:1200">
      <button id="lbClose" style="position:absolute;top:18px;right:18px;background:transparent;border:none;color:#fff;font-size:22px;cursor:pointer">✕</button>
      <img id="lbImage" src="" alt="" style="max-width:94%;max-height:86%;border-radius:8px">
    </div>
  </div>

<script>
(function(){
  var ticketData = <?php echo json_encode($ticket_options, JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT); ?>;
  var initial = '<?php echo addslashes($sel_ticket); ?>';

  var packs = Array.from(document.querySelectorAll('.pkg'));
  var registerBtn = document.getElementById('registerBtn');
  var sideRegister = document.getElementById('sideRegister');
  var mRegister = document.getElementById('mRegister');
  var mSummary = document.getElementById('mobileSummary');
  var mLabel = document.getElementById('mLabel');
  var mPrice = document.getElementById('mPrice');
  var sideLabel = document.getElementById('sideLabel');
  var sidePrice = document.getElementById('sidePrice');

  function setActive(code){
    packs.forEach(function(p){
      var t = p.getAttribute('data-ticket');
      var active = t === code;
      p.classList.toggle('active', active);
      p.setAttribute('aria-checked', active ? 'true' : 'false');
    });
    var href = 'userpanel/register.php?ticket=' + encodeURIComponent(code);
    if(sideRegister) sideRegister.href = href;
    registerBtn.setAttribute('data-href', href);
    if(mRegister) mRegister.setAttribute('data-href', href);

    var info = ticketData[code];
    if(mLabel) mLabel.textContent = info.label + ' • ' + info.desc;
    if(mPrice) mPrice.textContent = '₹' + info.price.toLocaleString();
    if(sideLabel) sideLabel.textContent = info.label;
    if(sidePrice) sidePrice.textContent = '₹' + info.price.toLocaleString();

    // show mobile summary only on narrow screens
    if(window.innerWidth <= 880) mSummary.classList.add('show');
    try{ localStorage.setItem('imalatur_ticket', code); }catch(e){}
    history.replaceState(null,'','?ticket='+encodeURIComponent(code));
  }

  packs.forEach(function(p){
    p.addEventListener('click', function(){ setActive(p.getAttribute('data-ticket'));});
    p.addEventListener('keydown', function(e){ if(e.key==='Enter'||e.key===' ') { e.preventDefault(); p.click(); } });
  });

  var stored = null;
  try{ stored = localStorage.getItem('imalatur_ticket'); }catch(e){}
  setActive(stored || initial);

  function goRegister(el){
    var href = el.getAttribute('data-href') || ('userpanel/register.php?ticket=<?php echo h($sel_ticket); ?>');
    window.location.href = href;
  }
  registerBtn.addEventListener('click', function(){ goRegister(registerBtn); });
  if(mRegister) mRegister.addEventListener('click', function(){ goRegister(mRegister); });

  function updateSummary(){
    var m = document.getElementById('mobileSummary');
    if(window.innerWidth <= 880){ m.setAttribute('aria-hidden','false'); m.classList.add('show'); }
    else { m.setAttribute('aria-hidden','true'); m.classList.remove('show'); }
  }
  window.addEventListener('resize', updateSummary);
  updateSummary();

  // accordions
  Array.from(document.querySelectorAll('.accordion .head')).forEach(function(head){
    var body = head.nextElementSibling;
    head.addEventListener('click', function(){
      var open = body.style.display === 'block';
      body.style.display = open ? 'none' : 'block';
      head.setAttribute('aria-expanded', open ? 'false' : 'true');
    });
    head.addEventListener('keydown', function(e){ if(e.key==='Enter' || e.key===' ') { e.preventDefault(); head.click(); }});
  });

  // gallery lightbox
  var galleryImgs = Array.from(document.querySelectorAll('.gallery img'));
  var lightbox = document.getElementById('lightbox');
  var lbImg = document.getElementById('lbImage');
  var lbClose = document.getElementById('lbClose');
  function openLightbox(src, alt){ lbImg.src = src; lbImg.alt = alt||''; lightbox.style.display = 'flex'; document.body.style.overflow = 'hidden'; }
  function closeLightbox(){ lightbox.style.display = 'none'; lbImg.src = ''; document.body.style.overflow = ''; }
  galleryImgs.forEach(function(img){ img.addEventListener('click', function(){ openLightbox(img.src, img.alt); }); img.addEventListener('keydown', function(e){ if(e.key==='Enter') openLightbox(img.src, img.alt); }); });
  if(lbClose) lbClose.addEventListener('click', closeLightbox);
  lightbox.addEventListener('click', function(e){ if(e.target === lightbox) closeLightbox(); });
  document.addEventListener('keydown', function(e){ if(e.key === 'Escape') closeLightbox(); });

})();
</script>
</body>
</html>