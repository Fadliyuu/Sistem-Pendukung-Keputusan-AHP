<?php ob_start();
$pageTitle = 'Input Perbandingan Berpasangan';
$n = count($criteria);
?>
<div class="page-header">
  <h2><i class="bi bi-table text-telkom me-2"></i>Perbandingan Berpasangan (Pairwise)</h2>
  <p>Isi nilai perbandingan antar kriteria menggunakan skala Saaty 1–9</p>
</div>

<?php if (empty($criteria)): ?>
<div class="card">
  <div class="card-body text-center py-5">
    <i class="bi bi-tags fs-1 text-muted opacity-50 d-block mb-3"></i>
    <h5 class="text-muted">Belum Ada Kriteria</h5>
    <p class="text-muted">Tambahkan kriteria penilaian terlebih dahulu sebelum mengisi matriks perbandingan.</p>
    <a href="/criteria" class="btn btn-telkom"><i class="bi bi-plus-circle me-2"></i>Kelola Kriteria</a>
  </div>
</div>
<?php else: ?>

<!-- Panduan Skala Saaty -->
<div class="card mb-4">
  <div class="card-body py-3">
    <button class="btn btn-sm btn-light w-100 d-flex align-items-center justify-content-between" data-bs-toggle="collapse" data-bs-target="#saaty">
      <span><i class="bi bi-book-fill text-primary me-2"></i><strong>Panduan Skala Saaty</strong> — Klik untuk lihat/sembunyikan</span>
      <i class="bi bi-chevron-down"></i>
    </button>
    <div class="collapse mt-3" id="saaty">
      <div class="table-responsive">
        <table class="table table-sm table-bordered text-center" style="font-size:.82rem;">
          <thead class="table-dark"><tr><th>Nilai</th><th>Definisi</th><th>Penjelasan</th></tr></thead>
          <tbody>
            <tr><td><strong>1</strong></td><td>Sama penting</td><td>Kedua elemen sama pentingnya</td></tr>
            <tr><td><strong>3</strong></td><td>Sedikit lebih penting</td><td>Satu elemen sedikit lebih penting dari yang lain</td></tr>
            <tr><td><strong>5</strong></td><td>Lebih penting</td><td>Satu elemen cukup lebih penting</td></tr>
            <tr><td><strong>7</strong></td><td>Sangat lebih penting</td><td>Satu elemen sangat lebih penting</td></tr>
            <tr><td><strong>9</strong></td><td>Mutlak lebih penting</td><td>Satu elemen mutlak lebih penting</td></tr>
            <tr><td><strong>2,4,6,8</strong></td><td>Nilai antara</td><td>Kompromi antara dua penilaian di atas</td></tr>
            <tr><td><strong>1/n</strong></td><td>Kebalikan</td><td>Jika A lebih penting dari B dengan nilai n, maka B vs A = 1/n</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<form method="POST" action="/ahp/pairwise" id="pairwiseForm" novalidate>
  <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">
  <div class="card mb-4">
    <div class="card-body p-0">
      <div class="p-4 pb-0">
        <h6 class="fw-bold mb-0">Matriks Perbandingan Berpasangan (<?= $n ?>×<?= $n ?>)</h6>
        <p class="text-muted small mb-3">Isi nilai baris terhadap kolom. Diagonal otomatis = 1. Nilai kebalikan otomatis dihitung.</p>
      </div>
      <div class="table-responsive p-4">
        <table class="table table-bordered pairwise-table" style="font-size:.82rem;">
          <thead>
            <tr>
              <th style="background:#1a1a2e;color:#fff;min-width:130px;">Kriteria</th>
              <?php foreach ($criteria as $c): ?>
              <th style="background:#1a1a2e;color:#fff;text-align:center;min-width:90px;"><?= htmlspecialchars($c['nama_kriteria']) ?></th>
              <?php endforeach; ?>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($criteria as $i => $ci): ?>
            <tr>
              <th style="background:#f3f4f6;font-size:.8rem;"><?= htmlspecialchars($ci['nama_kriteria']) ?></th>
              <?php foreach ($criteria as $j => $cj): ?>
              <td class="<?= $i === $j ? 'diagonal-cell' : '' ?>">
                <?php if ($i === $j): ?>
                  <strong>1</strong>
                  <input type="hidden" name="matrix[<?= $i ?>][<?= $j ?>]" value="1">
                <?php elseif ($i < $j): ?>
                  <input
                    type="number"
                    step="0.01"
                    min="0.11"
                    max="9"
                    class="form-control form-control-sm pairwise-input text-center"
                    name="matrix[<?= $i ?>][<?= $j ?>]"
                    id="m_<?= $i ?>_<?= $j ?>"
                    value="<?= htmlspecialchars($savedMatrix[$i][$j] ?? '') ?>"
                    placeholder="1–9"
                    data-row="<?= $i ?>"
                    data-col="<?= $j ?>"
                    required
                  >
                <?php else: ?>
                  <input
                    type="text"
                    class="form-control form-control-sm text-center text-muted"
                    id="m_<?= $i ?>_<?= $j ?>"
                    name="matrix[<?= $i ?>][<?= $j ?>]"
                    value="<?= isset($savedMatrix[$i][$j]) ? number_format($savedMatrix[$i][$j], 3) : '' ?>"
                    readonly
                    style="background:#f8f8f8;"
                    tabindex="-1"
                  >
                <?php endif; ?>
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
      <i class="bi bi-floppy-fill me-2"></i>Simpan Matriks
    </button>
    <a href="/ahp/calculate" class="btn btn-outline-telkom">
      <i class="bi bi-calculator-fill me-2"></i>Hitung Bobot AHP
    </a>
  </div>
</form>
<?php endif; ?>

<?php $extraJs = <<<'JS'
<script>
// Auto-fill reciprocal values
document.querySelectorAll('.pairwise-input').forEach(function(input) {
  input.addEventListener('input', function() {
    const r = parseInt(this.dataset.row);
    const c = parseInt(this.dataset.col);
    const val = parseFloat(this.value);
    const reciprocal = document.getElementById('m_' + c + '_' + r);
    if (reciprocal && !isNaN(val) && val > 0) {
      reciprocal.value = (1 / val).toFixed(3);
    } else if (reciprocal) {
      reciprocal.value = '';
    }
  });
  // Trigger on load if value exists
  if (this.value && parseFloat(this.value) > 0) {
    this.dispatchEvent(new Event('input'));
  }
});
</script>
JS; ?>
<?php $content = ob_get_clean(); include base_path('views/layouts/app.php'); ?>
