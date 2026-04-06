<?php ob_start();
$pageTitle = 'Input Nilai Pegawai';
?>
<div class="page-header">
  <h2><i class="bi bi-pencil-square text-telkom me-2"></i>Input Nilai Pegawai per Kriteria</h2>
  <p>Berikan penilaian setiap pegawai pada masing-masing kriteria (skala 0–100)</p>
</div>

<?php if (empty($criteria) || empty($employees)): ?>
<div class="card">
  <div class="card-body text-center py-5">
    <i class="bi bi-exclamation-triangle-fill fs-1 text-warning opacity-75 d-block mb-3"></i>
    <h5 class="text-muted">Data Tidak Lengkap</h5>
    <p class="text-muted">Pastikan data kriteria dan pegawai sudah diisi terlebih dahulu.</p>
    <div class="d-flex gap-2 justify-content-center">
      <a href="/employees" class="btn btn-telkom btn-sm">Kelola Pegawai</a>
      <a href="/criteria" class="btn btn-outline-telkom btn-sm">Kelola Kriteria</a>
    </div>
  </div>
</div>
<?php else: ?>

<!-- Keterangan Skala -->
<div class="card mb-4">
  <div class="card-body py-3">
    <div class="d-flex gap-3 align-items-center flex-wrap">
      <strong class="small"><i class="bi bi-info-circle text-primary me-1"></i>Skala Nilai:</strong>
      <span class="badge bg-danger">0–20: Sangat Kurang</span>
      <span class="badge bg-warning text-dark">21–40: Kurang</span>
      <span class="badge bg-secondary">41–60: Cukup</span>
      <span class="badge bg-info text-dark">61–80: Baik</span>
      <span class="badge bg-success">81–100: Sangat Baik</span>
    </div>
  </div>
</div>

<form method="POST" action="/ahp/scores" id="scoresForm" novalidate>
  <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">
  <div class="card mb-4">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-bordered align-middle mb-0" style="font-size:.85rem;">
          <thead>
            <tr>
              <th style="background:#1a1a2e;color:#fff;min-width:140px;">Pegawai</th>
              <?php foreach ($criteria as $idx => $c): ?>
              <th style="background:#1a1a2e;color:#fff;text-align:center;min-width:110px;">
                <?= htmlspecialchars($c['nama_kriteria']) ?>
                <div class="badge <?= ($c['jenis_kriteria']??'benefit')==='benefit'?'badge-benefit':'badge-cost' ?> mt-1" style="font-size:.65rem;">
                  <?= ($c['jenis_kriteria']??'benefit')==='benefit'?'Benefit':'Cost' ?>
                </div>
              </th>
              <?php endforeach; ?>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($employees as $p): ?>
            <tr>
              <th style="background:#f8f9fb;">
                <div class="fw-semibold"><?= htmlspecialchars($p['nama_pegawai'] ?? '') ?></div>
                <div class="text-muted" style="font-size:.72rem;"><?= htmlspecialchars($p['jabatan'] ?? '') ?></div>
              </th>
              <?php foreach ($criteria as $idx => $c): ?>
              <td class="text-center">
                <input
                  type="number"
                  min="0"
                  max="100"
                  step="1"
                  class="form-control form-control-sm text-center score-input"
                  name="scores[<?= htmlspecialchars($p['id_pegawai']) ?>][<?= $idx ?>]"
                  value="<?= htmlspecialchars($savedScores[$p['id_pegawai']][$idx] ?? '') ?>"
                  placeholder="0–100"
                  required
                >
              </td>
              <?php endforeach; ?>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="d-flex gap-2 flex-wrap">
    <button type="submit" class="btn btn-telkom">
      <i class="bi bi-floppy-fill me-2"></i>Simpan Nilai
    </button>
    <a href="/ranking" class="btn btn-outline-telkom">
      <i class="bi bi-trophy me-2"></i>Lihat Ranking
    </a>
    <a href="/ahp/calculate" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left me-1"></i>Kembali ke Hasil AHP
    </a>
  </div>
</form>

<?php endif; ?>

<?php $extraJs = <<<'JS'
<script>
// Color coding for score inputs
document.querySelectorAll('.score-input').forEach(function(inp) {
  function updateColor() {
    const v = parseInt(inp.value);
    inp.classList.remove('border-danger','border-warning','border-secondary','border-info','border-success');
    if      (v <= 20)  inp.classList.add('border-danger');
    else if (v <= 40)  inp.classList.add('border-warning');
    else if (v <= 60)  inp.classList.add('border-secondary');
    else if (v <= 80)  inp.classList.add('border-info');
    else               inp.classList.add('border-success');
  }
  inp.addEventListener('input', updateColor);
  if (inp.value) updateColor();
});
</script>
JS; ?>
<?php $content = ob_get_clean(); include base_path('views/layouts/app.php'); ?>
