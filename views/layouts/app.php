<?php
$user = current_user();
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
function nav_active(string $prefix, string $currentPath): string {
    return str_starts_with($currentPath, $prefix) ? 'active' : '';
}
$isAdmin = $user && $user['role'] === 'admin';
$firstLetter = strtoupper(substr($user['nama'] ?? $user['username'] ?? 'U', 0, 1));
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Sistem Pendukung Keputusan AHP Pegawai Terbaik - PT Telkom Satelit Indonesia Regional 6">
  <title><?= $pageTitle ?? 'SPK AHP' ?> — Telkomsat Regional 6</title>
  <!-- Favicon / Tab Icon -->
  <link rel="icon" type="image/png" href="/ODF.png">
  <link rel="shortcut icon" href="/ODF.png">
  <link rel="apple-touch-icon" href="/ODF.png">
  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <!-- Custom CSS -->
  <link href="/css/custom.css" rel="stylesheet">
</head>
<body>

<!-- ═══ SIDEBAR OVERLAY (mobile) ═══ -->
<div id="sidebar-overlay" onclick="closeSidebar()"></div>

<!-- ═══ SIDEBAR ═══ -->
<nav id="sidebar">
  <!-- Brand -->
  <a class="sidebar-brand" href="<?= $isAdmin ? '/admin' : '/ranking' ?>">
    <img src="/ODF.png" alt="Telkomsat Logo">
    <div class="brand-text">
      <div class="brand-title">Telkomsat Regional 6</div>
      <div class="brand-sub">Sistem Pendukung Keputusan</div>
    </div>
  </a>

  <?php if ($isAdmin): ?>
  <!-- ADMIN NAVIGATION -->
  <div class="sidebar-section">Utama</div>
  <a href="/admin" class="nav-link <?= $path === '/admin' ? 'active' : '' ?>">
    <i class="bi bi-grid-1x2-fill"></i> Dashboard
  </a>

  <div class="sidebar-section">Data Master</div>
  <a href="/employees" class="nav-link <?= nav_active('/employees', $path) ?>">
    <i class="bi bi-people-fill"></i> Data Pegawai
  </a>
  <a href="/criteria" class="nav-link <?= nav_active('/criteria', $path) ?>">
    <i class="bi bi-tags-fill"></i> Data Kriteria
  </a>
  <a href="/users" class="nav-link <?= nav_active('/users', $path) ?>">
    <i class="bi bi-person-badge-fill"></i> Manajemen User
  </a>

  <div class="sidebar-section">Proses AHP</div>
  <a href="/ahp/pairwise" class="nav-link <?= nav_active('/ahp/pairwise', $path) ?>">
    <i class="bi bi-table"></i> Perbandingan Berpasangan
  </a>
  <a href="/ahp/calculate" class="nav-link <?= nav_active('/ahp/calculate', $path) ?>">
    <i class="bi bi-calculator-fill"></i> Hitung Bobot AHP
  </a>
  <a href="/ahp/scores" class="nav-link <?= nav_active('/ahp/scores', $path) ?>">
    <i class="bi bi-pencil-square"></i> Input Nilai Pegawai
  </a>
  <a href="/ranking" class="nav-link <?= $path === '/ranking' ? 'active' : '' ?>">
    <i class="bi bi-trophy-fill"></i> Hasil Ranking
  </a>

  <div class="sidebar-section">Laporan</div>
  <a href="/report" class="nav-link <?= nav_active('/report', $path) ?>">
    <i class="bi bi-file-earmark-bar-graph-fill"></i> Laporan Penilaian
  </a>

  <?php else: ?>
  <!-- PEGAWAI NAVIGATION -->
  <div class="sidebar-section">Menu</div>
  <a href="/ranking" class="nav-link <?= $path === '/ranking' ? 'active' : '' ?>">
    <i class="bi bi-trophy-fill"></i> Hasil Ranking
  </a>
  <a href="/my-scores" class="nav-link <?= $path === '/my-scores' ? 'active' : '' ?>">
    <i class="bi bi-bar-chart-fill"></i> Nilai Saya
  </a>
  <?php endif; ?>

  <!-- FOOTER -->
  <div class="sidebar-footer">
    <div class="user-info">
      <div class="avatar"><?= $firstLetter ?></div>
      <div>
        <div class="user-name"><?= htmlspecialchars($user['nama'] ?? $user['username'] ?? '') ?></div>
        <div class="user-role"><?= $isAdmin ? 'Administrator' : 'Pegawai' ?></div>
      </div>
    </div>
    <form method="POST" action="/logout">
      <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">
      <button type="submit" class="logout-btn">
        <i class="bi bi-box-arrow-left"></i> Keluar
      </button>
    </form>
  </div>
</nav>

<!-- ═══ MAIN CONTENT ═══ -->
<div id="main-content">
  <!-- Top Header -->
  <header class="top-header">
    <button class="hamburger" onclick="toggleSidebar()" aria-label="Toggle menu">
      <i class="bi bi-list"></i>
    </button>
    <div class="page-title"><?= $pageTitle ?? 'Dashboard' ?></div>
    <div class="header-right">
      <span class="badge-role <?= $isAdmin ? 'badge-admin' : 'badge-pegawai' ?>">
        <?= $isAdmin ? 'Admin' : 'Pegawai' ?>
      </span>
    </div>
  </header>

  <!-- Flash Messages -->
  <div class="toast-container-custom" id="toastContainer">
    <?php if ($msg = flash('success')): ?>
    <div class="toast-custom toast-success" id="toast-success">
      <i class="bi bi-check-circle-fill fs-5"></i>
      <span><?= htmlspecialchars($msg) ?></span>
      <button type="button" class="btn-close btn-close-sm ms-auto" onclick="this.parentElement.remove()"></button>
    </div>
    <?php endif; ?>
    <?php if ($msg = flash('error')): ?>
    <div class="toast-custom toast-error" id="toast-error">
      <i class="bi bi-exclamation-circle-fill fs-5"></i>
      <span><?= htmlspecialchars($msg) ?></span>
      <button type="button" class="btn-close btn-close-sm ms-auto" onclick="this.parentElement.remove()"></button>
    </div>
    <?php endif; ?>
  </div>

  <!-- Page Content -->
  <main class="page-body">
    <?= $content ?? '' ?>
  </main>

  <!-- Footer -->
  <footer class="page-footer">
    &copy; <?= date('Y') ?> Sistem Pendukung Keputusan AHP &mdash; PT Telkom Satelit Indonesia Regional 6
  </footer>
</div>

<!-- ═══ MOBILE BOTTOM NAV ═══ -->
<?php if ($isAdmin): ?>
<nav class="mobile-bottom-nav">
  <a href="/admin" class="<?= $path === '/admin' ? 'active' : '' ?>">
    <i class="bi bi-grid-1x2-fill"></i>Dashboard
  </a>
  <a href="/employees" class="<?= nav_active('/employees', $path) ?>">
    <i class="bi bi-people-fill"></i>Pegawai
  </a>
  <a href="/ahp/calculate" class="<?= nav_active('/ahp', $path) ?>">
    <i class="bi bi-calculator-fill"></i>AHP
  </a>
  <a href="/ranking" class="<?= $path === '/ranking' ? 'active' : '' ?>">
    <i class="bi bi-trophy-fill"></i>Ranking
  </a>
  <a href="/report" class="<?= nav_active('/report', $path) ?>">
    <i class="bi bi-file-earmark-bar-graph-fill"></i>Laporan
  </a>
</nav>
<?php else: ?>
<nav class="mobile-bottom-nav">
  <a href="/ranking" class="<?= $path === '/ranking' ? 'active' : '' ?>">
    <i class="bi bi-trophy-fill"></i>Ranking
  </a>
  <a href="/my-scores" class="<?= $path === '/my-scores' ? 'active' : '' ?>">
    <i class="bi bi-bar-chart-fill"></i>Nilai Saya
  </a>
</nav>
<?php endif; ?>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Sidebar toggle
function toggleSidebar() {
  document.getElementById('sidebar').classList.toggle('open');
  document.getElementById('sidebar-overlay').classList.toggle('show');
}
function closeSidebar() {
  document.getElementById('sidebar').classList.remove('open');
  document.getElementById('sidebar-overlay').classList.remove('show');
}

// Auto-dismiss toasts
document.querySelectorAll('.toast-custom').forEach(t => {
  setTimeout(() => { t.style.opacity='0'; t.style.transform='translateX(120%)'; t.style.transition='all .4s'; setTimeout(()=>t.remove(), 400); }, 4000);
});
</script>
<?= $extraJs ?? '' ?>
</body>
</html>
