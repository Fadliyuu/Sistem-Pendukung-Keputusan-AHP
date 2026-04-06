<?php
// Login tidak pakai layout app.php
$csrfToken = csrf_token();
if (current_user()) {
    $role = current_user()['role'];
    redirect($role === 'admin' ? '/admin' : '/ranking');
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Login Sistem Pendukung Keputusan AHP Pegawai Terbaik PT Telkom Satelit Indonesia Regional 6">
  <title>Login — SPK AHP Telkomsat Regional 6</title>
  <!-- Favicon -->
  <link rel="icon" type="image/png" href="/ODF.png">
  <link rel="shortcut icon" href="/ODF.png">
  <link rel="apple-touch-icon" href="/ODF.png">
  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <!-- Custom CSS -->
  <link href="/css/custom.css" rel="stylesheet">
  <style>
    /* ── LOGIN PAGE OVERRIDES ── */
    body { background: #0f0f23; }

    /* ── DESKTOP HERO SIDE ── */
    .login-outer {
      min-height: 100vh;
      display: flex;
    }

    /* LEFT HERO */
    .login-hero-side {
      flex: 1;
      background: linear-gradient(145deg, #0a0000 0%, #4a0000 35%, #cc0000 75%, #ff2200 100%);
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 60px 48px;
      position: relative;
      overflow: hidden;
    }
    /* Animated circles */
    .login-hero-side .bg-circle {
      position: absolute;
      border-radius: 50%;
      border: 1px solid rgba(255,255,255,.08);
      animation: pulseCircle 6s ease-in-out infinite;
    }
    .login-hero-side .bg-circle:nth-child(1) { width:500px; height:500px; top:-150px; right:-150px; animation-delay:0s; }
    .login-hero-side .bg-circle:nth-child(2) { width:350px; height:350px; top:-50px; right:-50px; animation-delay:1s; border-color:rgba(255,255,255,.05); }
    .login-hero-side .bg-circle:nth-child(3) { width:300px; height:300px; bottom:-100px; left:-80px; animation-delay:2s; }
    .login-hero-side .bg-circle:nth-child(4) { width:180px; height:180px; bottom:-30px; left:20px; animation-delay:3s; border-color:rgba(255,255,255,.04); }
    @keyframes pulseCircle {
      0%, 100% { transform: scale(1); opacity:.6; }
      50%       { transform: scale(1.04); opacity:1; }
    }

    .hero-content { position: relative; z-index: 1; text-align: center; max-width: 340px; }
    .hero-logo-wrap {
      width: 84px; height: 84px;
      background: rgba(255,255,255,.12);
      border-radius: 24px;
      display: flex; align-items: center; justify-content: center;
      margin: 0 auto 24px;
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255,255,255,.15);
      box-shadow: 0 8px 32px rgba(0,0,0,.25);
    }
    .hero-logo-wrap img { width: 52px; height: 52px; object-fit: contain; filter: brightness(0) invert(1); }
    .hero-title {
      font-size: 1.75rem; font-weight: 800;
      color: #fff; line-height: 1.25;
      letter-spacing: -.3px;
    }
    .hero-subtitle {
      font-size: .9rem;
      color: rgba(255,255,255,.65);
      margin-top: 10px; margin-bottom: 40px;
      line-height: 1.5;
    }

    /* Feature pills */
    .hero-features { display: flex; flex-direction: column; gap: 12px; width: 100%; }
    .hero-feature {
      display: flex; align-items: center; gap: 14px;
      background: rgba(255,255,255,.08);
      backdrop-filter: blur(8px);
      border: 1px solid rgba(255,255,255,.1);
      border-radius: 14px;
      padding: 14px 18px;
      text-align: left;
      transition: background .25s;
    }
    .hero-feature:hover { background: rgba(255,255,255,.13); }
    .hero-feature .feat-icon {
      width: 38px; height: 38px;
      background: rgba(255,255,255,.15);
      border-radius: 10px;
      display: flex; align-items: center; justify-content: center;
      font-size: 1.1rem; color: #fff;
      flex-shrink: 0;
    }
    .hero-feature .feat-title { font-weight: 700; font-size: .85rem; color: #fff; }
    .hero-feature .feat-desc  { font-size: .72rem; color: rgba(255,255,255,.6); margin-top: 1px; }

    /* RIGHT FORM SIDE */
    .login-form-side {
      width: 460px;
      background: #fff;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 48px 44px;
      position: relative;
    }
    .form-inner { width: 100%; }

    /* Brand tag */
    .form-brand-tag {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: #fff0f0;
      border: 1px solid rgba(204,0,0,.18);
      border-radius: 50px;
      padding: 5px 12px;
      margin-bottom: 20px;
    }
    .form-brand-tag img { width: 20px; height: 20px; object-fit: contain; }
    .form-brand-tag span { font-size: .72rem; font-weight: 700; color: #cc0000; text-transform: uppercase; letter-spacing: .8px; }

    /* Headings */
    .login-heading { font-size: 1.65rem; font-weight: 800; color: #0f1117; letter-spacing: -.4px; line-height: 1.2; }
    .login-subheading { font-size: .85rem; color: #6b7280; margin-top: 8px; margin-bottom: 30px; }

    /* Input groups */
    .login-field-group { margin-bottom: 18px; }
    .login-field-group .field-label {
      font-size: .78rem; font-weight: 700;
      color: #374151; margin-bottom: 7px;
      display: flex; align-items: center; gap: 5px;
    }
    .login-input-wrap {
      position: relative;
    }
    .login-input-wrap .field-icon {
      position: absolute;
      left: 14px; top: 50%;
      transform: translateY(-50%);
      color: #9ca3af;
      font-size: 1rem;
      pointer-events: none;
      transition: color .2s;
    }
    .login-input-wrap input {
      width: 100%;
      padding: 13px 14px 13px 40px;
      font-size: .9rem;
      font-family: 'Inter', sans-serif;
      border: 1.5px solid #e5e7eb;
      border-radius: 12px;
      background: #f9fafb;
      color: #111;
      outline: none;
      transition: border-color .2s, box-shadow .2s, background .2s;
    }
    .login-input-wrap input:focus {
      border-color: #cc0000;
      box-shadow: 0 0 0 4px rgba(204,0,0,.08);
      background: #fff;
    }
    .login-input-wrap input:focus + .field-icon,
    .login-input-wrap:focus-within .field-icon {
      color: #cc0000;
    }
    /* Password toggle */
    .login-input-wrap .pwd-toggle {
      position: absolute;
      right: 12px; top: 50%;
      transform: translateY(-50%);
      background: none; border: none;
      color: #9ca3af; cursor: pointer;
      padding: 4px 6px;
      font-size: 1rem;
      line-height: 1;
      border-radius: 6px;
      transition: color .2s;
    }
    .login-input-wrap .pwd-toggle:hover { color: #cc0000; }
    .login-input-wrap input[type="password"] { padding-right: 44px; }
    .login-input-wrap input[type="text"]     { padding-right: 44px; }

    /* Submit button */
    .login-btn {
      width: 100%;
      padding: 14px;
      background: linear-gradient(135deg, #cc0000 0%, #990000 100%);
      color: #fff;
      font-weight: 700;
      font-size: .9rem;
      border: none;
      border-radius: 12px;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      transition: opacity .2s, transform .15s, box-shadow .2s;
      letter-spacing: .2px;
      box-shadow: 0 4px 18px rgba(204,0,0,.3);
      margin-top: 4px;
    }
    .login-btn:hover {
      opacity: .92;
      transform: translateY(-1px);
      box-shadow: 0 6px 24px rgba(204,0,0,.4);
    }
    .login-btn:active { transform: translateY(0); opacity: 1; }
    .login-btn:disabled { opacity: .6; cursor: not-allowed; transform: none; }

    /* Footer note */
    .login-note {
      margin-top: 24px;
      padding-top: 20px;
      border-top: 1px solid #f0f0f0;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      font-size: .75rem;
      color: #9ca3af;
      text-align: center;
    }
    .login-note i { color: #cc0000; }

    /* ═══ MOBILE OVERRIDE ═══ */
    @media (max-width: 991.98px) {
      body { background: #7a0000; }
      .login-outer { flex-direction: column; min-height: 100vh; background: linear-gradient(160deg,#0a0000 0%,#7a0000 60%,#cc0000 100%); }
      .login-hero-side { display: none; }

      /* Mobile top brand */
      .login-mobile-top {
        display: flex !important;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 40px 24px 52px;
        text-align: center;
        position: relative; z-index: 1;
      }
      .login-mobile-top .mob-logo-wrap {
        width: 70px; height: 70px;
        background: rgba(255,255,255,.15);
        border-radius: 20px;
        display: flex; align-items: center; justify-content: center;
        border: 1px solid rgba(255,255,255,.2);
        backdrop-filter: blur(8px);
        margin-bottom: 16px;
      }
      .login-mobile-top .mob-logo-wrap img {
        width: 44px; height: 44px;
        object-fit: contain;
        filter: brightness(0) invert(1);
      }
      .login-mobile-top .mob-title {
        font-size: 1.2rem; font-weight: 800;
        color: #fff; line-height: 1.25;
      }
      .login-mobile-top .mob-sub {
        font-size: .78rem; color: rgba(255,255,255,.65); margin-top: 6px;
      }

      /* Form card slides up from bottom */
      .login-form-side {
        width: 100%;
        border-radius: 24px 24px 0 0;
        margin-top: -24px;
        position: relative; z-index: 2;
        padding: 28px 22px 40px;
        min-height: 0;
        align-items: flex-start;
        box-shadow: 0 -8px 40px rgba(0,0,0,.2);
      }

      /* Drag handle indicator */
      .login-form-side::before {
        content: '';
        display: block;
        width: 36px; height: 4px;
        background: #e5e7eb;
        border-radius: 2px;
        margin: 0 auto 20px;
      }

      /* Compact headings on mobile */
      .login-heading { font-size: 1.35rem; }
      .login-subheading { margin-bottom: 22px; font-size: .82rem; }
      .form-brand-tag { margin-bottom: 16px; }
    }
  </style>
</head>
<body>

<?php if ($msg = flash('error')): ?>
<div class="toast-container-custom" id="toastContainer">
  <div class="toast-custom toast-error">
    <i class="bi bi-exclamation-circle-fill fs-5"></i>
    <span><?= htmlspecialchars($msg) ?></span>
    <button type="button" class="btn-close btn-close-sm ms-auto" onclick="this.parentElement.remove()"></button>
  </div>
</div>
<?php endif; ?>

<div class="login-outer">

  <!-- ═══ MOBILE TOP BRAND ═══ -->
  <div class="login-mobile-top" style="display:none;">
    <div class="mob-logo-wrap">
      <img src="/ODF.png" alt="Telkomsat ODF">
    </div>
    <div class="mob-title">Sistem Pendukung Keputusan<br>Pegawai Terbaik</div>
    <div class="mob-sub">PT Telkom Satelit Indonesia — Regional 6</div>
  </div>

  <!-- ═══ HERO SIDE (Desktop) ═══ -->
  <div class="login-hero-side">
    <!-- Animated background circles -->
    <div class="bg-circle"></div>
    <div class="bg-circle"></div>
    <div class="bg-circle"></div>
    <div class="bg-circle"></div>

    <div class="hero-content">
      <div class="hero-logo-wrap">
        <img src="/ODF.png" alt="Telkomsat ODF">
      </div>
      <div class="hero-title">Sistem Pendukung<br>Keputusan AHP</div>
      <div class="hero-subtitle">Pegawai Terbaik PT Telkom Satelit<br>Indonesia Regional 6</div>

      <div class="hero-features">
        <div class="hero-feature">
          <div class="feat-icon"><i class="bi bi-diagram-3-fill"></i></div>
          <div>
            <div class="feat-title">Metode AHP</div>
            <div class="feat-desc">Analytical Hierarchy Process berbasis ilmiah</div>
          </div>
        </div>
        <div class="hero-feature">
          <div class="feat-icon"><i class="bi bi-shield-check-fill"></i></div>
          <div>
            <div class="feat-title">Penilaian Objektif</div>
            <div class="feat-desc">Berbasis kriteria terukur & terbobot</div>
          </div>
        </div>
        <div class="hero-feature">
          <div class="feat-icon"><i class="bi bi-graph-up-arrow"></i></div>
          <div>
            <div class="feat-title">Ranking Akurat</div>
            <div class="feat-desc">Hasil transparan & dapat dipertanggungjawabkan</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ═══ FORM SIDE ═══ -->
  <div class="login-form-side">
    <div class="form-inner">

      <!-- Brand tag -->
      <div class="form-brand-tag">
        <img src="/ODF.png" alt="ODF">
        <span>Telkomsat Regional 6</span>
      </div>

      <h1 class="login-heading">Selamat Datang!</h1>
      <p class="login-subheading">Masukkan kredensial Anda untuk mengakses sistem penilaian pegawai.</p>

      <form method="POST" action="/login" id="loginForm" novalidate>
        <input type="hidden" name="_csrf" value="<?= $csrfToken ?>">

        <!-- Username -->
        <div class="login-field-group">
          <div class="field-label">
            <i class="bi bi-person text-danger"></i> Username
          </div>
          <div class="login-input-wrap">
            <i class="bi bi-person field-icon"></i>
            <input
              type="text"
              id="username"
              name="username"
              placeholder="Masukkan username"
              value="<?= htmlspecialchars(old('username', '')) ?>"
              required
              autocomplete="username"
              autofocus
            >
          </div>
        </div>

        <!-- Password -->
        <div class="login-field-group">
          <div class="field-label">
            <i class="bi bi-lock text-danger"></i> Password
          </div>
          <div class="login-input-wrap">
            <i class="bi bi-lock field-icon"></i>
            <input
              type="password"
              id="password"
              name="password"
              placeholder="Masukkan password"
              required
              autocomplete="current-password"
            >
            <button type="button" class="pwd-toggle" id="togglePwd" tabindex="-1" aria-label="Toggle password">
              <i class="bi bi-eye" id="eyeIcon"></i>
            </button>
          </div>
        </div>

        <!-- Submit -->
        <button type="submit" class="login-btn" id="loginBtn">
          <span id="btnContent">
            <i class="bi bi-box-arrow-in-right"></i> Masuk ke Sistem
          </span>
          <span id="btnLoading" style="display:none;">
            <span class="spinner-border spinner-border-sm" role="status"></span>
            Memproses...
          </span>
        </button>
      </form>

      <div class="login-note">
        <i class="bi bi-shield-lock-fill"></i>
        Sistem ini hanya untuk pegawai PT Telkom Satelit Indonesia Regional 6
      </div>

    </div>
  </div>

</div><!-- /.login-outer -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Toast styles
(function() {
  const style = document.createElement('style');
  style.textContent = `
    .toast-container-custom { position:fixed; top:16px; right:16px; z-index:9999; }
    .toast-custom { min-width:300px; padding:14px 18px; border-radius:10px;
      box-shadow:0 4px 20px rgba(0,0,0,.15); display:flex; align-items:center;
      gap:12px; font-size:.875rem; font-weight:500; animation:slideInRight .3s ease; }
    .toast-error { background:#fee2e2; color:#991b1b; border-left:4px solid #ef4444; }
    @keyframes slideInRight { from{transform:translateX(120%);opacity:0} to{transform:translateX(0);opacity:1} }
    @media(max-width:575px) {
      .toast-container-custom { left:10px; right:10px; top:10px; }
      .toast-custom { min-width:unset; width:100%; }
    }
  `;
  document.head.appendChild(style);
})();

// Password toggle
document.getElementById('togglePwd').addEventListener('click', function() {
  const pwd = document.getElementById('password');
  const icon = document.getElementById('eyeIcon');
  pwd.type = pwd.type === 'password' ? 'text' : 'password';
  icon.className = pwd.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
});

// Submit loading
document.getElementById('loginForm').addEventListener('submit', function() {
  document.getElementById('btnContent').style.display = 'none';
  document.getElementById('btnLoading').style.display  = 'inline-flex';
  document.getElementById('loginBtn').disabled = true;
});

// Auto-dismiss toasts
document.querySelectorAll('.toast-custom').forEach(t => {
  setTimeout(() => {
    t.style.opacity = '0';
    t.style.transform = 'translateX(120%)';
    t.style.transition = 'all .4s';
    setTimeout(() => t.remove(), 400);
  }, 4000);
});
</script>
</body>
</html>
