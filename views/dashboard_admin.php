<?php ob_start();
$pageTitle = 'Dashboard Admin';
?>
<!-- Page Header -->
<div class="page-header d-flex align-items-center justify-content-between">
  <div>
    <h2><i class="bi bi-grid-1x2-fill text-telkom me-2"></i>Dashboard</h2>
    <p>Selamat datang, <strong><?= htmlspecialchars(current_user()['nama'] ?? 'Admin') ?></strong> — <?= date('l, d F Y') ?></p>
  </div>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
  <div class="col-6 col-xl-3">
    <div class="stat-card">
      <div class="stat-icon" style="background:#fff0f0; color:#cc0000;">
        <i class="bi bi-people-fill"></i>
      </div>
      <div>
        <div class="stat-value"><?= $counts['pegawai'] ?? 0 ?></div>
        <div class="stat-label">Total Pegawai</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-xl-3">
    <div class="stat-card">
      <div class="stat-icon" style="background:#f0f7ff; color:#1565c0;">
        <i class="bi bi-tags-fill"></i>
      </div>
      <div>
        <div class="stat-value"><?= $counts['kriteria'] ?? 0 ?></div>
        <div class="stat-label">Kriteria Penilaian</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-xl-3">
    <div class="stat-card">
      <div class="stat-icon" style="background:<?= isset($ahpResult['cr']) && $ahpResult['cr'] <= 0.1 ? '#f0fdf4;color:#15803d' : '#fff7ed;color:#c2410c' ?>;">
        <i class="bi bi-calculator-fill"></i>
      </div>
      <div>
        <div class="stat-value" style="font-size:1.3rem;">
          <?php if (isset($ahpResult['cr'])): ?>
            <span class="badge <?= $ahpResult['cr'] <= 0.1 ? 'bg-success' : 'bg-warning text-dark' ?>" style="font-size:.9rem;">
              CR <?= number_format($ahpResult['cr'], 3) ?>
            </span>
          <?php else: ?>
            <span class="text-muted" style="font-size:1rem;">—</span>
          <?php endif; ?>
        </div>
        <div class="stat-label">Status Konsistensi AHP</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-xl-3">
    <div class="stat-card">
      <div class="stat-icon" style="background:#fdf4ff; color:#7e22ce;">
        <i class="bi bi-trophy-fill"></i>
      </div>
      <div>
        <div class="stat-value"><?= $counts['ranking'] ?? 0 ?></div>
        <div class="stat-label">Pegawai Dinilai</div>
      </div>
    </div>
  </div>
</div>

<div class="row g-4">
  <!-- Alur Proses AHP -->
  <div class="col-lg-5">
    <div class="card h-100">
      <div class="card-body">
        <h6 class="fw-bold mb-4"><i class="bi bi-signpost-2-fill text-telkom me-2"></i>Alur Proses AHP</h6>
        <div class="d-flex flex-column gap-2">
          <a href="/employees" class="step-card">
            <div class="step-num">1</div>
            <div>
              <div class="step-label">Kelola Data Pegawai</div>
              <div class="step-sub">Tambah / edit / hapus pegawai</div>
            </div>
            <i class="bi bi-chevron-right ms-auto text-muted"></i>
          </a>
          <a href="/criteria" class="step-card">
            <div class="step-num">2</div>
            <div>
              <div class="step-label">Kelola Kriteria</div>
              <div class="step-sub">Tambah kriteria penilaian</div>
            </div>
            <i class="bi bi-chevron-right ms-auto text-muted"></i>
          </a>
          <a href="/ahp/pairwise" class="step-card">
            <div class="step-num">3</div>
            <div>
              <div class="step-label">Input Perbandingan Berpasangan</div>
              <div class="step-sub">Matriks AHP (skala Saaty 1–9)</div>
            </div>
            <i class="bi bi-chevron-right ms-auto text-muted"></i>
          </a>
          <a href="/ahp/calculate" class="step-card">
            <div class="step-num">4</div>
            <div>
              <div class="step-label">Hitung Bobot & Konsistensi</div>
              <div class="step-sub">Eigen vector, CI, CR</div>
            </div>
            <i class="bi bi-chevron-right ms-auto text-muted"></i>
          </a>
          <a href="/ahp/scores" class="step-card">
            <div class="step-num">5</div>
            <div>
              <div class="step-label">Input Nilai Pegawai</div>
              <div class="step-sub">Nilai per kriteria (0–100)</div>
            </div>
            <i class="bi bi-chevron-right ms-auto text-muted"></i>
          </a>
          <a href="/ranking" class="step-card">
            <div class="step-num">6</div>
            <div>
              <div class="step-label">Lihat Hasil Ranking</div>
              <div class="step-sub">Pegawai terbaik berdasarkan AHP</div>
            </div>
            <i class="bi bi-chevron-right ms-auto text-muted"></i>
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Top Ranking -->
  <div class="col-lg-7">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-4">
          <h6 class="fw-bold mb-0"><i class="bi bi-trophy-fill text-warning me-2"></i>Top Ranking Pegawai</h6>
          <a href="/ranking" class="btn btn-sm btn-outline-telkom">Lihat Semua</a>
        </div>
        <?php if (empty($topRanking)): ?>
          <div class="text-center py-5">
            <i class="bi bi-hourglass-split fs-1 text-muted opacity-50"></i>
            <p class="text-muted mt-3 mb-1">Belum ada data ranking</p>
            <small class="text-muted">Selesaikan semua langkah AHP terlebih dahulu</small>
          </div>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table align-middle">
              <thead>
                <tr>
                  <th style="width:60px;">Posisi</th>
                  <th>Nama Pegawai</th>
                  <th>Jabatan</th>
                  <th>Nilai Akhir</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($topRanking as $i => $r): ?>
                <tr>
                  <td>
                    <?php if ($i === 0): ?>
                      <div class="rank-medal gold"><?= $i+1 ?></div>
                    <?php elseif ($i === 1): ?>
                      <div class="rank-medal silver"><?= $i+1 ?></div>
                    <?php elseif ($i === 2): ?>
                      <div class="rank-medal bronze"><?= $i+1 ?></div>
                    <?php else: ?>
                      <div class="rank-medal normal"><?= $i+1 ?></div>
                    <?php endif; ?>
                  </td>
                  <td><strong><?= htmlspecialchars($r['nama'] ?? '-') ?></strong></td>
                  <td><span class="text-muted"><?= htmlspecialchars($r['jabatan'] ?? '-') ?></span></td>
                  <td>
                    <div class="d-flex align-items-center gap-2">
                      <div class="progress flex-grow-1" style="max-width:80px;">
                        <div class="progress-bar" style="width:<?= min(100, ($r['total'] ?? 0) * 100) ?>%"></div>
                      </div>
                      <span class="fw-bold text-telkom"><?= number_format($r['total'] ?? 0, 4) ?></span>
                    </div>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- Quick Actions -->
<div class="row g-3 mt-2">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <h6 class="fw-bold mb-3"><i class="bi bi-lightning-fill text-warning me-2"></i>Aksi Cepat</h6>
        <div class="d-flex flex-wrap gap-2">
          <a href="/employees" class="btn btn-telkom btn-sm"><i class="bi bi-person-plus-fill me-2"></i>Tambah Pegawai</a>
          <a href="/criteria" class="btn btn-telkom btn-sm"><i class="bi bi-plus-circle-fill me-2"></i>Tambah Kriteria</a>
          <a href="/ahp/pairwise" class="btn btn-outline-telkom btn-sm"><i class="bi bi-table me-2"></i>Input Matriks</a>
          <a href="/ahp/calculate" class="btn btn-outline-telkom btn-sm"><i class="bi bi-calculator me-2"></i>Hitung AHP</a>
          <a href="/ahp/scores" class="btn btn-outline-telkom btn-sm"><i class="bi bi-pencil me-2"></i>Input Nilai</a>
          <a href="/report" class="btn btn-outline-secondary btn-sm"><i class="bi bi-file-pdf me-2"></i>Cetak Laporan</a>
        </div>
      </div>
    </div>
  </div>
</div>
<?php $content = ob_get_clean(); include base_path('views/layouts/app.php'); ?>
