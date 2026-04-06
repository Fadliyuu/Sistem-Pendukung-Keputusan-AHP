<?php ob_start();
$pageTitle = 'Hasil Ranking Pegawai';
$me = current_user();
$isAdmin = $me && $me['role'] === 'admin';
?>
<div class="page-header">
  <h2><i class="bi bi-trophy-fill text-telkom me-2"></i>Ranking Pegawai Terbaik</h2>
  <p>Hasil perhitungan AHP berdasarkan bobot kriteria yang telah ditentukan</p>
</div>

<?php if (!empty($ahpInfo)): ?>
<div class="row g-3 mb-4">
  <div class="col-md-4">
    <div class="card text-center p-3">
      <div class="text-muted small mb-1">Lambda Max (λ)</div>
      <div class="fw-bold fs-5"><?= number_format($ahpInfo['lambda'] ?? 0, 4) ?></div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card text-center p-3">
      <div class="text-muted small mb-1">Consistency Ratio (CR)</div>
      <div class="fw-bold fs-5"><?= number_format($ahpInfo['cr'] ?? 0, 4) ?></div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card text-center p-3">
      <div class="text-muted small mb-1">Status Konsistensi</div>
      <span class="cr-badge <?= ($ahpInfo['cr'] ?? 1) <= 0.1 ? 'ok' : 'bad' ?> px-3 py-1">
        <i class="bi <?= ($ahpInfo['cr'] ?? 1) <= 0.1 ? 'bi-check-circle-fill' : 'bi-x-circle-fill' ?>"></i>
        <?= ($ahpInfo['cr'] ?? 1) <= 0.1 ? 'Konsisten' : 'Tidak Konsisten' ?>
      </span>
    </div>
  </div>
</div>
<?php endif; ?>

<?php if (empty($ranking)): ?>
  <div class="card">
    <div class="card-body text-center py-5">
      <i class="bi bi-hourglass-split fs-1 text-muted opacity-50"></i>
      <h5 class="mt-3 text-muted">Belum Ada Data Ranking</h5>
      <p class="text-muted">Pastikan admin sudah menyelesaikan seluruh proses AHP.</p>
      <?php if ($isAdmin): ?>
        <a href="/ahp/pairwise" class="btn btn-telkom"><i class="bi bi-arrow-right me-2"></i>Mulai Proses AHP</a>
      <?php endif; ?>
    </div>
  </div>
<?php else: ?>

<!-- Top 3 Podium -->
<?php if (count($ranking) >= 1): ?>
<div class="row g-3 mb-4 justify-content-center">
  <?php
  $order = [0]; // default: hanya 1
  if (count($ranking)===2) $order=[1,0];
  if (count($ranking)>=3)  $order=[1,0,2];
  $medals = ['gold'=>['🥇','#ffc107','#fff3cd'],'silver'=>['🥈','#aaa','#e8e8e8'],'bronze'=>['🥉','#cd7f32','#ffe5d0']];
  $medalKeys = ['gold','silver','bronze'];
  foreach ($order as $idx):
    if (!isset($ranking[$idx])) continue;
    $r = $ranking[$idx];
    $mk = $medalKeys[$idx] ?? 'bronze';
    $m = $medals[$mk];
  ?>
  <div class="col-md-4">
    <div class="card text-center p-4 h-100" style="border: 2px solid <?= $m[1] ?>; background: <?= $m[2] ?>;">
      <div style="font-size:3rem;"><?= $m[0] ?></div>
      <div class="fw-bold fs-5 mt-2"><?= htmlspecialchars($r['nama'] ?? '-') ?></div>
      <div class="text-muted small"><?= htmlspecialchars($r['jabatan'] ?? '') ?></div>
      <div class="text-muted small"><?= htmlspecialchars($r['divisi'] ?? '') ?></div>
      <div class="mt-3">
        <span class="badge" style="background:<?= $m[1] ?>;color:#fff;font-size:.9rem;padding:6px 16px;">
          <?= number_format($r['total'] ?? 0, 4) ?>
        </span>
      </div>
      <div class="mt-2 text-muted" style="font-size:.75rem;">Posisi #<?= $idx+1 ?></div>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Full Ranking Table -->
<div class="card">
  <div class="card-body p-0">
    <div class="p-4 pb-0">
      <h6 class="fw-bold mb-0"><i class="bi bi-list-ol me-2 text-telkom"></i>Tabel Ranking Lengkap</h6>
    </div>
    <div class="table-responsive mt-3">
      <table class="table align-middle mb-0">
        <thead>
          <tr>
            <th style="width:60px;">No.</th>
            <th>Nama Pegawai</th>
            <th>Jabatan / Divisi</th>
            <?php foreach ($criteria ?? [] as $c): ?>
            <th class="text-center" style="min-width:90px;font-size:.72rem;">
              <?= htmlspecialchars($c['nama_kriteria']) ?>
            </th>
            <?php endforeach; ?>
            <th class="text-center">Nilai Akhir</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($ranking as $i => $r): ?>
          <tr <?= !$isAdmin && ($r['pegawai_id'] ?? '') === ($myPegawaiId ?? '') ? 'style="background:#fff8f8;font-weight:600;"' : '' ?>>
            <td>
              <?php if ($i===0): ?>
                <div class="rank-medal gold"><?= $i+1 ?></div>
              <?php elseif ($i===1): ?>
                <div class="rank-medal silver"><?= $i+1 ?></div>
              <?php elseif ($i===2): ?>
                <div class="rank-medal bronze"><?= $i+1 ?></div>
              <?php else: ?>
                <div class="rank-medal normal"><?= $i+1 ?></div>
              <?php endif; ?>
            </td>
            <td>
              <div class="fw-semibold"><?= htmlspecialchars($r['nama'] ?? '-') ?></div>
              <?php if (!$isAdmin && ($r['pegawai_id'] ?? '') === ($myPegawaiId ?? '')): ?>
                <span class="badge bg-danger" style="font-size:.65rem;">Anda</span>
              <?php endif; ?>
            </td>
            <td>
              <div class="small"><?= htmlspecialchars($r['jabatan'] ?? '-') ?></div>
              <div class="text-muted" style="font-size:.75rem;"><?= htmlspecialchars($r['divisi'] ?? '') ?></div>
            </td>
            <?php foreach ($criteria ?? [] as $idx => $c): ?>
            <td class="text-center">
              <span class="badge bg-light text-dark border">
                <?= isset($r['scores'][$idx]) ? number_format($r['scores'][$idx], 0) : '—' ?>
              </span>
            </td>
            <?php endforeach; ?>
            <td class="text-center">
              <div class="d-flex align-items-center gap-2 justify-content-center">
                <div class="progress" style="width:60px;">
                  <div class="progress-bar" style="width:<?= !empty($ranking[0]['total']) ? min(100, ($r['total']/$ranking[0]['total'])*100) : 0 ?>%"></div>
                </div>
                <strong class="text-telkom"><?= number_format($r['total'] ?? 0, 4) ?></strong>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php if ($isAdmin): ?>
<div class="mt-3 d-flex gap-2 no-print">
  <a href="/report" class="btn btn-telkom"><i class="bi bi-file-earmark-bar-graph me-2"></i>Lihat Laporan</a>
  <a href="/report/export" class="btn btn-outline-telkom"><i class="bi bi-file-pdf me-2"></i>Export PDF</a>
</div>
<?php endif; ?>

<?php endif; ?>
<?php $content = ob_get_clean(); include base_path('views/layouts/app.php'); ?>
