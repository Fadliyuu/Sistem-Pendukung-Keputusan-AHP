<?php
// ── Global error handler ──
ini_set('display_errors', 0);
set_exception_handler(function (Throwable $e) {
    // Bersihkan output buffer jika ada
    while (ob_get_level()) ob_end_clean();
    http_response_code(500);
    $msg  = htmlspecialchars($e->getMessage());
    $file = htmlspecialchars($e->getFile());
    $line = $e->getLine();
    echo "<!doctype html><html lang='id'><head>
  <meta charset='utf-8'><meta name='viewport' content='width=device-width,initial-scale=1'>
  <title>Error — SPK AHP</title>
  <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css' rel='stylesheet'>
  <link href='/css/custom.css' rel='stylesheet'>
</head><body style='padding:2rem;'>
  <div class='alert alert-danger'><h5>⚠️ Terjadi Kesalahan</h5>
  <p><strong>Pesan:</strong> {$msg}</p>
  <p class='small text-muted'>{$file}:{$line}</p></div>
  <a href='/admin' class='btn btn-telkom'>← Kembali ke Dashboard</a>
</body></html>";
    exit;
});

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require __DIR__ . '/../vendor/autoload.php';
} else {
    http_response_code(500);
    echo '<h3>Vendor tidak ditemukan.</h3><p>Jalankan <code>composer install</code> terlebih dahulu.</p>';
    exit;
}

use App\Firestore;
use App\Ahp;
use App\Auth;
use App\Controllers\{
    AuthController,
    EmployeesController,
    CriteriaController,
    AhpController,
    ReportController,
    UserController
};

// ═══ Bootstrap Instances ═══
$fs  = new Firestore();
$ahp = new Ahp();
$authService = new Auth($fs);

$authC = new AuthController($authService, $fs, $ahp);
$empC  = new EmployeesController($fs);
$critC = new CriteriaController($fs);
$ahpC  = new AhpController($fs, $ahp);
$repC  = new ReportController($fs, $ahp);
$userC = new UserController($fs);

// ═══ Routing ═══
$method = $_SERVER['REQUEST_METHOD'];
$path   = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
$path   = rtrim($path, '/') ?: '/';
$isPost = $method === 'POST';

// Verify CSRF for all POST (sudah dilakukan di masing-masing controller)

// ─── Public routes ───
if ($path === '/login') {
    if ($isPost) $authC->login();
    else $authC->showLogin();
    exit;
}

// ─── Redirect root ───
if ($path === '/') {
    $u = current_user();
    if ($u) redirect($u['role'] === 'admin' ? '/admin' : '/ranking');
    redirect('/login');
    exit;
}

// ─── Logout ───
if ($path === '/logout') {
    $authC->logout();
    exit;
}

// ─── Admin routes ───
if ($path === '/admin') {
    require_login('admin');
    $authC->adminDashboard();
    exit;
}

// ─── Employees ───
if ($path === '/employees') {
    require_login('admin');
    if ($isPost) $empC->store();
    else         $empC->index();
    exit;
}
if (preg_match('#^/employees/([^/]+)/update$#', $path, $m)) {
    require_login('admin');
    $empC->update($m[1]);
    exit;
}
if (preg_match('#^/employees/([^/]+)/delete$#', $path, $m)) {
    require_login('admin');
    $empC->delete($m[1]);
    exit;
}

// ─── Criteria ───
if ($path === '/criteria') {
    require_login('admin');
    if ($isPost) $critC->store();
    else         $critC->index();
    exit;
}
if (preg_match('#^/criteria/([^/]+)/update$#', $path, $m)) {
    require_login('admin');
    $critC->update($m[1]);
    exit;
}
if (preg_match('#^/criteria/([^/]+)/delete$#', $path, $m)) {
    require_login('admin');
    $critC->delete($m[1]);
    exit;
}

// ─── Users ───
if ($path === '/users') {
    require_login('admin');
    if ($isPost) $userC->store();
    else         $userC->index();
    exit;
}
if (preg_match('#^/users/([^/]+)/update$#', $path, $m)) {
    require_login('admin');
    $userC->update($m[1]);
    exit;
}
if (preg_match('#^/users/([^/]+)/delete$#', $path, $m)) {
    require_login('admin');
    $userC->delete($m[1]);
    exit;
}

// ─── AHP ───
if ($path === '/ahp/pairwise') {
    require_login('admin');
    if ($isPost) $ahpC->storePairwise();
    else         $ahpC->pairwise();
    exit;
}
if ($path === '/ahp/calculate') {
    require_login('admin');
    $ahpC->calculate();
    exit;
}
if ($path === '/ahp/scores') {
    require_login('admin');
    if ($isPost) $ahpC->storeScores();
    else         $ahpC->scores();
    exit;
}

// ─── Ranking (admin & pegawai) ───
if ($path === '/ranking') {
    require_login();
    $ahpC->ranking();
    exit;
}

// ─── Nilai Saya (pegawai) ───
if ($path === '/my-scores') {
    require_login('pegawai');
    $ahpC->myScores();
    exit;
}

// ─── Laporan ───
if ($path === '/report') {
    require_login('admin');
    $repC->index();
    exit;
}
if ($path === '/report/export') {
    require_login('admin');
    $repC->export();
    exit;
}

// ─── 404 ───
http_response_code(404);
$pageTitle = '404 - Halaman Tidak Ditemukan';
ob_start();
?>
<div class="text-center py-5">
  <i class="bi bi-map fs-1 text-muted opacity-50 d-block mb-3"></i>
  <h3 class="text-muted">404 - Halaman Tidak Ditemukan</h3>
  <p class="text-muted">Halaman yang Anda cari tidak ada atau telah dipindahkan.</p>
  <a href="/" class="btn btn-telkom"><i class="bi bi-house me-2"></i>Kembali ke Beranda</a>
</div>
<?php
$content = ob_get_clean();
include base_path('views/layouts/app.php');
