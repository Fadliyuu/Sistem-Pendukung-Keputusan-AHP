<?php ob_start();
$pageTitle = 'Laporan Hasil Penilaian';
?>
<div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-2 no-print">
  <div>
    <h2><i class="bi bi-file-earmark-bar-graph-fill text-telkom me-2"></i>Laporan Hasil Penilaian AHP</h2>
    <p>PT Telkom Satelit Indonesia Regional 6 — <?= date('d F Y') ?></p>
  </div>
  <div class="d-flex gap-2">
    <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
      <i class="bi bi-printer me-1"></i>Cetak
    </button>
    <a href="/report/export" class="btn btn-telkom btn-sm">
      <i class="bi bi-file-pdf me-2"></i>Export PDF
    </a>
  </div>
</div>

<!-- Print Header (hanya muncul saat print) -->
<div class="d-none d-print-block mb-4">
  <h4 class="text-center mb-1">LAPORAN HASIL PENILAIAN PEGAWAI TERBAIK</h4>
  <h5 class="text-center text-muted">PT Telkom Satelit Indonesia Regional 6</h5>
  <p class="text-center text-muted">Metode: Analytical Hierarchy Process (AHP) &bull; Tanggal: <?= date('d F Y') ?></p>
  <hr>
</div>

<!-- Bobot Kriteria -->
<?php if (!empty($criteriaSummary)): ?>
<div class="card mb-4">
  <div class="card-body">
    <h6 class="fw-bold mb-3"><i class="bi bi-tags-fill text-telkom me-2"></i>Bobot Kriteria AHP</h6>
    <div class="table-responsive">
      <table class="table table-sm table-bordered mb-0" style="font-size:.84rem;">
        <thead>
          <tr>
            <th>No.</th>
            <th>Kriteria</th>
            <th>Jenis</th>
            <th class="text-center">Bobot Prioritas</th>
            <th class="text-center">Persentase</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($criteriaSummary as $i => $cs): ?>
          <tr>
            <td><?= $i+1 ?></td>
            <td><strong><?= htmlspecialchars($cs['nama_kriteria']) ?></strong></td>
            <td>
              <span class="badge <?= ($cs['jenis']??'benefit')==='benefit'?'badge-benefit':'badge-cost' ?>">
                <?= ucfirst($cs['jenis']??'benefit') ?>
              </span>
            </td>
            <td class="text-center fw-bold"><?= number_format($cs['bobot'], 4) ?></td>
            <td class="text-center"><?= number_format($cs['bobot']*100, 2) ?>%</td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <?php if (!empty($ahpMeta)): ?>
    <div class="row g-2 mt-3">
      <div class="col-auto">
        <span class="badge bg-light text-dark border">λ<sub>max</sub> = <?= number_format($ahpMeta['lambda']??0,4) ?></span>
      </div>
      <div class="col-auto">
        <span class="badge bg-light text-dark border">CI = <?= number_format($ahpMeta['ci']??0,4) ?></span>
      </div>
      <div class="col-auto">
        <span class="badge bg-light text-dark border">CR = <?= number_format($ahpMeta['cr']??0,4) ?></span>
      </div>
      <div class="col-auto">
        <span class="badge <?= ($ahpMeta['cr']??1)<=0.1?'bg-success':'bg-danger' ?>">
          <?= ($ahpMeta['cr']??1)<=0.1?'Konsisten':'Tidak Konsisten' ?>
        </span>
      </div>
    </div>
    <?php endif; ?>
  </div>
</div>
<?php endif; ?>

<!-- Ranking -->
<div class="card">
  <div class="card-body p-0">
    <div class="p-4 pb-0">
      <h6 class="fw-bold"><i class="bi bi-trophy-fill text-warning me-2"></i>Hasil Ranking Pegawai Terbaik</h6>
    </div>
    <div class="table-responsive mt-3">
      <table class="table table-bordered align-middle mb-0" style="font-size:.85rem;">
        <thead>
          <tr>
            <th style="background:#1a1a2e;color:#fff;width:60px;">Posisi</th>
            <th style="background:#1a1a2e;color:#fff;">Nama Pegawai</th>
            <th style="background:#1a1a2e;color:#fff;">Jabatan</th>
            <th style="background:#1a1a2e;color:#fff;">Divisi</th>
            <th style="background:#1a1a2e;color:#fff;">Masa Kerja</th>
            <?php foreach ($criteriaSummary ?? [] as $cs): ?>
            <th style="background:#1a1a2e;color:#fff;text-align:center;font-size:.72rem;"><?= htmlspecialchars($cs['nama_kriteria']) ?></th>
            <?php endforeach; ?>
            <th style="background:#cc0000;color:#fff;text-align:center;">Nilai Akhir</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($ranking)): ?>
          <tr><td colspan="20" class="text-center py-4 text-muted">Belum ada data ranking.</td></tr>
          <?php else: ?>
          <?php foreach ($ranking as $i => $r): ?>
          <tr <?= $i===0?'style="background:#fffde7;"':'' ?>>
            <td class="text-center">
              <?php
              if ($i===0)      echo '<span class="fs-5">🥇</span>';
              elseif ($i===1)  echo '<span class="fs-5">🥈</span>';
              elseif ($i===2)  echo '<span class="fs-5">🥉</span>';
              else             echo '<strong class="text-muted">'.($i+1).'</strong>';
              ?>
            </td>
            <td><strong><?= htmlspecialchars($r['nama'] ?? '-') ?></strong></td>
            <td><?= htmlspecialchars($r['jabatan'] ?? '-') ?></td>
            <td><?= htmlspecialchars($r['divisi'] ?? '-') ?></td>
            <td class="text-center"><?= htmlspecialchars($r['masa_kerja'] ?? '-') ?> th</td>
            <?php foreach ($criteriaSummary ?? [] as $idx => $cs): ?>
            <td class="text-center">
              <span class="badge bg-light text-dark border">
                <?= isset($r['scores'][$idx]) ? number_format($r['scores'][$idx], 0) : '—' ?>
              </span>
            </td>
            <?php endforeach; ?>
            <td class="text-center">
              <strong class="text-telkom fs-6"><?= number_format($r['total']??0,4) ?></strong>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
    <?php if (!empty($ranking)): ?>
    <div class="p-3 text-muted small border-top">
      Dicetak pada: <?= date('d F Y, H:i') ?> WIB &bull;
      Total: <strong><?= count($ranking) ?></strong> pegawai dinilai &bull;
      Sistem Pendukung Keputusan AHP — PT Telkom Satelit Indonesia Regional 6
    </div>
    <?php endif; ?>
  </div>
</div>
<?php $content = ob_get_clean(); include base_path('views/layouts/app.php'); ?>
