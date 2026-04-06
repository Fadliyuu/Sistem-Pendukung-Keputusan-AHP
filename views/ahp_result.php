<?php ob_start();
$pageTitle = 'Hasil Perhitungan AHP';
$n = count($criteria ?? []);
?>
<div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-2">
  <div>
    <h2><i class="bi bi-calculator-fill text-telkom me-2"></i>Hasil Perhitungan AHP</h2>
    <p>Bobot prioritas, matriks normalisasi, dan uji konsistensi</p>
  </div>
  <a href="/ahp/pairwise" class="btn btn-outline-telkom btn-sm">
    <i class="bi bi-arrow-left me-1"></i>Ubah Matriks
  </a>
</div>

<?php if (empty($criteria)): ?>
<div class="card">
  <div class="card-body text-center py-5">
    <i class="bi bi-exclamation-triangle-fill fs-1 text-warning opacity-75 d-block mb-3"></i>
    <h5 class="text-muted">Belum Ada Kriteria atau Matriks</h5>
    <p class="text-muted">Silakan tambahkan kriteria dan isi matriks perbandingan terlebih dahulu.</p>
    <a href="/ahp/pairwise" class="btn btn-telkom">Isi Matriks Pairwise</a>
  </div>
</div>
<?php else: ?>

<!-- Status Konsistensi Banner -->
<div class="card mb-4 border-0" style="background:<?= ($cr??1)<=0.1?'linear-gradient(135deg,#d1fae5,#a7f3d0)':'linear-gradient(135deg,#fee2e2,#fca5a5)' ?>;">
  <div class="card-body d-flex align-items-center gap-4 flex-wrap py-4">
    <div class="text-center">
      <div class="cr-badge <?= ($cr??1)<=0.1?'ok':'bad' ?>">
        <i class="bi <?= ($cr??1)<=0.1?'bi-check-circle-fill':'bi-x-circle-fill' ?> fs-5"></i>
        <?= ($cr??1)<=0.1 ? 'KONSISTEN' : 'TIDAK KONSISTEN' ?>
      </div>
      <div class="mt-2 small text-muted">
        <?= ($cr??1)<=0.1 ? 'Penilaian dapat diterima (CR ≤ 0,1)' : 'Perbaiki matriks perbandingan (CR > 0,1)' ?>
      </div>
    </div>
    <div class="d-flex gap-4 flex-wrap">
      <div class="text-center">
        <div class="fw-bold fs-4"><?= number_format($lambda??0, 4) ?></div>
        <div class="text-muted small">Lambda Max (λ<sub>max</sub>)</div>
      </div>
      <div class="text-center">
        <div class="fw-bold fs-4"><?= number_format($ci??0, 4) ?></div>
        <div class="text-muted small">Consistency Index (CI)</div>
      </div>
      <div class="text-center">
        <div class="fw-bold fs-4"><?= number_format($ri??0, 3) ?></div>
        <div class="text-muted small">Random Index (RI)</div>
      </div>
      <div class="text-center">
        <div class="fw-bold fs-4 <?= ($cr??1)<=0.1?'text-success':'text-danger' ?>">
          <?= number_format($cr??0, 4) ?>
        </div>
        <div class="text-muted small">Consistency Ratio (CR)</div>
      </div>
    </div>
    <?php if (($cr??1) > 0.1): ?>
    <a href="/ahp/pairwise" class="btn btn-danger btn-sm ms-auto">
      <i class="bi bi-pencil-square me-1"></i>Perbaiki Matriks
    </a>
    <?php endif; ?>
  </div>
</div>

<div class="row g-4">
  <!-- Bobot Prioritas -->
  <div class="col-lg-5">
    <div class="card h-100">
      <div class="card-body">
        <h6 class="fw-bold mb-4"><i class="bi bi-bar-chart-fill text-telkom me-2"></i>Bobot Prioritas (Eigen Vector)</h6>
        <div class="d-flex flex-column gap-3">
          <?php
          // Cast semua weights ke float untuk keamanan
          $floatWeights = [];
          foreach ($criteria as $idx => $c) {
            $v = $weights[$idx] ?? $weights[(string)$idx] ?? 0;
            $floatWeights[$idx] = is_array($v) ? 0.0 : (float)$v;
          }
          $maxW = max($floatWeights ?: [1]);
          // Urutkan descending untuk tampilan
          $sortedWeights = $floatWeights;
          arsort($sortedWeights);
          foreach ($sortedWeights as $idx => $w):
            $c = $criteria[$idx] ?? null;
            if (!$c) continue;
            $pct = $maxW > 0 ? ($w / $maxW) * 100 : 0;
          ?>
          <div>
            <div class="d-flex justify-content-between mb-1">
              <span class="small fw-semibold"><?= htmlspecialchars($c['nama_kriteria']) ?></span>
              <span class="small fw-bold text-telkom"><?= number_format($w, 4) ?></span>
            </div>
            <div class="progress" style="height:10px;">
              <div class="progress-bar" style="width:<?= $pct ?>%;"></div>
            </div>
            <div class="text-muted" style="font-size:.7rem;"><?= number_format($w * 100, 2) ?>% dari total bobot</div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>

  <!-- Ringkasan Konsistensi -->
  <div class="col-lg-7">
    <div class="card mb-3">
      <div class="card-body">
        <h6 class="fw-bold mb-3"><i class="bi bi-table text-telkom me-2"></i>Matriks Normalisasi</h6>
        <div class="table-responsive">
          <table class="table table-bordered table-sm" style="font-size:.78rem;">
            <thead>
              <tr>
                <th style="background:#1a1a2e;color:#fff;">Kriteria</th>
                <?php foreach ($criteria as $c): ?>
                <th style="background:#1a1a2e;color:#fff;text-align:center;"><?= htmlspecialchars($c['nama_kriteria']) ?></th>
                <?php endforeach; ?>
                <th style="background:#cc0000;color:#fff;text-align:center;">Bobot</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($criteria as $i => $rowCriteria): ?>
              <tr>
                <th class="table-light" style="font-size:.78rem;"><?= htmlspecialchars($rowCriteria['nama_kriteria']) ?></th>
                <?php foreach ($criteria as $j => $colCriteria): ?>
                <?php
                  $normVal = $norm[$i][$j] ?? $norm[$i][(string)$j] ?? 0;
                  $normVal = is_array($normVal) ? 0.0 : (float)$normVal;
                ?>
                <td class="text-center"><?= number_format($normVal, 4) ?></td>
                <?php endforeach; ?>
                <td class="text-center fw-bold text-telkom"><?= number_format($floatWeights[$i] ?? 0.0, 4) ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Rumus -->
    <div class="card">
      <div class="card-body">
        <h6 class="fw-bold mb-3"><i class="bi bi-formula text-primary me-2"></i>Rumus Perhitungan</h6>
        <div class="row g-3" style="font-size:.82rem;">
          <div class="col-6">
            <div class="p-3 rounded-3" style="background:#f8f9fb;">
              <div class="fw-bold text-muted small mb-1">Lambda Max (λ<sub>max</sub>)</div>
              <div>= Σ(λ<sub>i</sub>) / n = <strong><?= number_format(is_array($lambda??0) ? 0.0 : (float)($lambda??0), 4) ?></strong></div>
            </div>
          </div>
          <div class="col-6">
            <div class="p-3 rounded-3" style="background:#f8f9fb;">
              <div class="fw-bold text-muted small mb-1">Consistency Index (CI)</div>
              <div>= (λmax - n) / (n-1) = <strong><?= number_format(is_array($ci??0) ? 0.0 : (float)($ci??0), 4) ?></strong></div>
            </div>
          </div>
          <div class="col-6">
            <div class="p-3 rounded-3" style="background:#f8f9fb;">
              <div class="fw-bold text-muted small mb-1">Random Index (RI)</div>
              <div>n = <?= $n ?> → RI = <strong><?= number_format($ri??0,2) ?></strong></div>
            </div>
          </div>
          <div class="col-6">
            <div class="p-3 rounded-3" style="background:#f8f9fb;">
              <div class="fw-bold text-muted small mb-1">Consistency Ratio (CR)</div>
              <div>= CI / RI = <strong class="<?= ($cr??1)<=0.1?'text-success':'text-danger' ?>"><?= number_format($cr??0,4) ?></strong></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php if (($cr??1) <= 0.1): ?>
<div class="mt-3 d-flex gap-2">
  <a href="/ahp/scores" class="btn btn-telkom">
    <i class="bi bi-pencil-square me-2"></i>Lanjut: Input Nilai Pegawai
  </a>
  <a href="/ranking" class="btn btn-outline-telkom">
    <i class="bi bi-trophy me-2"></i>Lihat Ranking
  </a>
</div>
<?php endif; ?>

<?php endif; ?>
<?php $content = ob_get_clean(); include base_path('views/layouts/app.php'); ?>
