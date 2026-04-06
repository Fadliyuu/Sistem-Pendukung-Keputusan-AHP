<?php ob_start();
$pageTitle = 'Nilai Saya';
$me = current_user();
?>
<div class="page-header">
  <h2><i class="bi bi-bar-chart-fill text-telkom me-2"></i>Nilai Saya</h2>
  <p>Hasil penilaian pribadi Anda berdasarkan kriteria yang telah ditetapkan</p>
</div>

<?php if (empty($myRankInfo)): ?>
<div class="card">
  <div class="card-body text-center py-5">
    <i class="bi bi-clipboard-x fs-1 text-muted opacity-50 d-block mb-3"></i>
    <h5 class="text-muted">Nilai Belum Tersedia</h5>
    <p class="text-muted">Data penilaian Anda belum diinput oleh admin. Silakan hubungi administrator.</p>
  </div>
</div>
<?php else: ?>

<!-- Posisi Ranking -->
<div class="row g-3 mb-4">
  <div class="col-md-4">
    <div class="card text-center p-4" style="border:2px solid <?= $myRankInfo['rank']===1?'#ffc107':($myRankInfo['rank']===2?'#aaa':($myRankInfo['rank']===3?'#cd7f32':'#e8eaed')) ?>;">
      <div style="font-size:3rem;">
        <?= $myRankInfo['rank']===1?'🥇':($myRankInfo['rank']===2?'🥈':($myRankInfo['rank']===3?'🥉':'🎖️')) ?>
      </div>
      <div class="fw-bold fs-3 text-telkom">Posisi #<?= $myRankInfo['rank'] ?></div>
      <div class="text-muted small">dari <?= $myRankInfo['total'] ?> pegawai</div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card text-center p-4">
      <div class="text-muted small mb-2">Nilai Akhir</div>
      <div class="fw-bold fs-2 text-telkom"><?= number_format($myRankInfo['total'], 4) ?></div>
      <div class="progress mt-2" style="height:8px;">
        <div class="progress-bar" style="width:<?= $myRankInfo['total']*100 ?>%;"></div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card text-center p-4">
      <div class="text-muted small mb-2">Nama Pegawai</div>
      <div class="fw-bold fs-5"><?= htmlspecialchars($me['nama']??'') ?></div>
      <div class="text-muted small"><?= htmlspecialchars($myRankInfo['jabatan']??'') ?></div>
    </div>
  </div>
</div>

<!-- Nilai Per Kriteria -->
<div class="card">
  <div class="card-body">
    <h6 class="fw-bold mb-4"><i class="bi bi-list-check text-telkom me-2"></i>Detail Nilai per Kriteria</h6>
    <div class="row g-3">
      <?php foreach ($myRankInfo['detail'] ?? [] as $d): ?>
      <div class="col-md-6">
        <div class="p-3 rounded-3 border" style="background:#f8f9fb;">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <div>
              <div class="fw-semibold small"><?= htmlspecialchars($d['nama_kriteria']) ?></div>
              <div class="text-muted" style="font-size:.72rem;">Bobot: <?= number_format($d['bobot'],4) ?> (<?= number_format($d['bobot']*100,1) ?>%)</div>
            </div>
            <span class="badge bg-telkom fs-6 px-3"><?= number_format($d['nilai'],0) ?></span>
          </div>
          <div class="progress" style="height:8px;">
            <div class="progress-bar" style="width:<?= min(100,$d['nilai']) ?>%;"></div>
          </div>
          <div class="text-muted mt-1" style="font-size:.72rem;">
            Kontribusi nilai akhir: <?= number_format($d['kontribusi'],4) ?>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<?php endif; ?>
<?php $content = ob_get_clean(); include base_path('views/layouts/app.php'); ?>
