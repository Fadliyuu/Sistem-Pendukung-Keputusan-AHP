<?php ob_start();
$pageTitle = 'Data Kriteria';
?>
<div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-2">
  <div>
    <h2><i class="bi bi-tags-fill text-telkom me-2"></i>Data Kriteria Penilaian</h2>
    <p>Kelola kriteria yang digunakan dalam perhitungan AHP</p>
  </div>
  <button class="btn btn-telkom" data-bs-toggle="modal" data-bs-target="#modalTambah">
    <i class="bi bi-plus-circle-fill me-2"></i>Tambah Kriteria
  </button>
</div>

<!-- Info -->
<div class="alert alert-light border mb-4 d-flex gap-3 align-items-start">
  <i class="bi bi-info-circle-fill text-primary fs-5 flex-shrink-0 mt-1"></i>
  <div class="small">
    <strong>Penting:</strong> Urutan kriteria dalam tabel ini akan menjadi urutan kolom/baris pada matriks perbandingan berpasangan AHP.
    Pastikan kriteria sudah benar sebelum memulai perhitungan.
  </div>
</div>

<!-- Table -->
<div class="card">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table align-middle mb-0">
        <thead>
          <tr>
            <th style="width:50px;">No.</th>
            <th>Nama Kriteria</th>
            <th>Jenis</th>
            <th>Deskripsi</th>
            <th style="width:130px;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($criteria)): ?>
          <tr>
            <td colspan="5" class="text-center py-5">
              <i class="bi bi-tags fs-1 text-muted opacity-50 d-block mb-2"></i>
              <span class="text-muted">Belum ada kriteria. Klik "Tambah Kriteria" untuk memulai.</span>
            </td>
          </tr>
          <?php else: ?>
          <?php foreach ($criteria as $idx => $c): ?>
          <tr>
            <td class="text-muted"><?= $idx + 1 ?></td>
            <td>
              <div class="fw-semibold"><?= htmlspecialchars($c['nama_kriteria'] ?? '') ?></div>
              <div class="text-muted" style="font-size:.72rem;"><?= htmlspecialchars($c['id_kriteria'] ?? '') ?></div>
            </td>
            <td>
              <?php if (($c['jenis_kriteria'] ?? '') === 'benefit'): ?>
                <span class="badge badge-benefit px-2 py-1"><i class="bi bi-arrow-up me-1"></i>Benefit</span>
              <?php else: ?>
                <span class="badge badge-cost px-2 py-1"><i class="bi bi-arrow-down me-1"></i>Cost</span>
              <?php endif; ?>
            </td>
            <td class="text-muted small"><?= htmlspecialchars($c['deskripsi'] ?? '—') ?></td>
            <td>
              <div class="d-flex gap-1">
                <button class="btn btn-sm btn-outline-primary"
                  data-bs-toggle="modal" data-bs-target="#modalEdit"
                  data-id="<?= htmlspecialchars($c['id_kriteria']) ?>"
                  data-nama="<?= htmlspecialchars($c['nama_kriteria'] ?? '') ?>"
                  data-jenis="<?= htmlspecialchars($c['jenis_kriteria'] ?? 'benefit') ?>"
                  data-desc="<?= htmlspecialchars($c['deskripsi'] ?? '') ?>"
                  onclick="fillEdit(this)">
                  <i class="bi bi-pencil-fill"></i>
                </button>
                <button class="btn btn-sm btn-outline-danger"
                  data-bs-toggle="modal" data-bs-target="#modalHapus"
                  data-id="<?= htmlspecialchars($c['id_kriteria']) ?>"
                  data-nama="<?= htmlspecialchars($c['nama_kriteria'] ?? '') ?>"
                  onclick="fillHapus(this)">
                  <i class="bi bi-trash-fill"></i>
                </button>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
    <div class="p-3 border-top text-muted" style="font-size:.78rem;">
      Total: <strong><?= count($criteria) ?></strong> kriteria &bull;
      <span class="badge badge-benefit">Benefit</span> = nilai lebih tinggi lebih baik &bull;
      <span class="badge badge-cost">Cost</span> = nilai lebih rendah lebih baik
    </div>
  </div>
</div>



<!-- MODAL TAMBAH -->
<div class="modal fade" id="modalTambah" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header" style="background:#cc0000;color:#fff;">
        <h5 class="modal-title"><i class="bi bi-plus-circle-fill me-2"></i>Tambah Kriteria</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" action="/criteria" novalidate>
        <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nama Kriteria <span class="text-danger">*</span></label>
            <input class="form-control" name="nama_kriteria" placeholder="Contoh: Kedisiplinan" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Jenis Kriteria <span class="text-danger">*</span></label>
            <select class="form-select" name="jenis_kriteria">
              <option value="benefit">Benefit (nilai tinggi = lebih baik)</option>
              <option value="cost">Cost (nilai rendah = lebih baik)</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <textarea class="form-control" name="deskripsi" rows="2" placeholder="Penjelasan singkat kriteria ini..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-telkom"><i class="bi bi-check-lg me-1"></i>Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- MODAL EDIT -->
<div class="modal fade" id="modalEdit" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header" style="background:#1565c0;color:#fff;">
        <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Kriteria</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" id="formEdit" novalidate>
        <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nama Kriteria <span class="text-danger">*</span></label>
            <input class="form-control" name="nama_kriteria" id="editNama" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Jenis Kriteria <span class="text-danger">*</span></label>
            <select class="form-select" name="jenis_kriteria" id="editJenis">
              <option value="benefit">Benefit</option>
              <option value="cost">Cost</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <textarea class="form-control" name="deskripsi" id="editDesc" rows="2"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Update</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- MODAL HAPUS -->
<div class="modal fade" id="modalHapus" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title"><i class="bi bi-exclamation-triangle-fill me-2"></i>Hapus Kriteria</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center py-4">
        <p class="mb-1">Apakah Anda yakin ingin menghapus?</p>
        <strong id="hapusNama" class="text-danger fs-6"></strong>
        <p class="text-muted small mt-2 mb-0">Data yang dihapus tidak dapat dikembalikan.</p>
      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
        <form method="POST" id="formHapus">
          <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">
          <button type="submit" class="btn btn-danger"><i class="bi bi-trash-fill me-1"></i>Hapus</button>
        </form>
      </div>
    </div>
  </div>
</div>

<?php $extraJs = <<<JS
<script>
function fillEdit(btn) {
  document.getElementById('editNama').value  = btn.dataset.nama;
  document.getElementById('editDesc').value  = btn.dataset.desc;
  document.getElementById('editJenis').value = btn.dataset.jenis;
  document.getElementById('formEdit').action = '/criteria/' + btn.dataset.id + '/update';
}
function fillHapus(btn) {
  document.getElementById('hapusNama').textContent = btn.dataset.nama;
  document.getElementById('formHapus').action      = '/criteria/' + btn.dataset.id + '/delete';
}
</script>
JS; ?>
<?php $content = ob_get_clean(); include base_path('views/layouts/app.php'); ?>
