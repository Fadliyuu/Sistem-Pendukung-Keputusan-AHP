<?php ob_start();
$pageTitle = 'Data Pegawai';
?>
<!-- Page Header -->
<div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-2">
  <div>
    <h2><i class="bi bi-people-fill text-telkom me-2"></i>Data Pegawai</h2>
    <p>Kelola data seluruh pegawai PT Telkom Satelit Indonesia Regional 6</p>
  </div>
  <button class="btn btn-telkom" data-bs-toggle="modal" data-bs-target="#modalTambah">
    <i class="bi bi-person-plus-fill me-2"></i>Tambah Pegawai
  </button>
</div>

<!-- Table -->
<div class="card">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table align-middle mb-0">
        <thead>
          <tr>
            <th style="width:50px;">No.</th>
            <th>Nama Pegawai</th>
            <th>Jabatan</th>
            <th>Divisi</th>
            <th>Masa Kerja</th>
            <th style="width:130px;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($employees)): ?>
          <tr>
            <td colspan="6" class="text-center py-5">
              <i class="bi bi-person-x fs-1 text-muted opacity-50 d-block mb-2"></i>
              <span class="text-muted">Belum ada data pegawai. Klik "Tambah Pegawai" untuk memulai.</span>
            </td>
          </tr>
          <?php else: ?>
          <?php foreach ($employees as $idx => $e): ?>
          <tr>
            <td class="text-muted"><?= $idx + 1 ?></td>
            <td>
              <div class="d-flex align-items-center gap-2">
                <div style="width:34px;height:34px;borderRadius:50%;background:#fff0f0;color:#cc0000;display:flex;alignItems:center;justifyContent:center;fontWeight:700;fontSize:.85rem;border-radius:50%;flex-shrink:0;">
                  <?= strtoupper(substr($e['nama_pegawai'] ?? 'P', 0, 1)) ?>
                </div>
                <div>
                  <div class="fw-semibold"><?= htmlspecialchars($e['nama_pegawai'] ?? '') ?></div>
                  <div class="text-muted" style="font-size:.72rem;"><?= htmlspecialchars($e['id_pegawai'] ?? '') ?></div>
                </div>
              </div>
            </td>
            <td><?= htmlspecialchars($e['jabatan'] ?? '') ?></td>
            <td><?= htmlspecialchars($e['divisi'] ?? '') ?></td>
            <td>
              <span class="badge bg-light text-dark border">
                <i class="bi bi-calendar me-1"></i><?= htmlspecialchars($e['masa_kerja'] ?? '') ?> tahun
              </span>
            </td>
            <td>
              <div class="d-flex gap-1">
                <button class="btn btn-sm btn-outline-primary"
                  data-bs-toggle="modal" data-bs-target="#modalEdit"
                  data-id="<?= htmlspecialchars($e['id_pegawai']) ?>"
                  data-nama="<?= htmlspecialchars($e['nama_pegawai'] ?? '') ?>"
                  data-jabatan="<?= htmlspecialchars($e['jabatan'] ?? '') ?>"
                  data-divisi="<?= htmlspecialchars($e['divisi'] ?? '') ?>"
                  data-masa="<?= htmlspecialchars($e['masa_kerja'] ?? '') ?>"
                  onclick="fillEdit(this)">
                  <i class="bi bi-pencil-fill"></i>
                </button>
                <button class="btn btn-sm btn-outline-danger"
                  data-bs-toggle="modal" data-bs-target="#modalHapus"
                  data-id="<?= htmlspecialchars($e['id_pegawai']) ?>"
                  data-nama="<?= htmlspecialchars($e['nama_pegawai'] ?? '') ?>"
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
      Total: <strong><?= count($employees) ?></strong> pegawai terdaftar
    </div>
  </div>
</div>

<!-- ══ MODAL TAMBAH ══ -->
<div class="modal fade" id="modalTambah" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header" style="background:#cc0000;color:#fff;">
        <h5 class="modal-title"><i class="bi bi-person-plus-fill me-2"></i>Tambah Pegawai</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" action="/employees" id="formTambah" novalidate>
        <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nama Pegawai <span class="text-danger">*</span></label>
            <input class="form-control" name="nama_pegawai" placeholder="Masukkan nama lengkap" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Jabatan <span class="text-danger">*</span></label>
            <input class="form-control" name="jabatan" placeholder="Contoh: Teknisi, Supervisor" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Divisi <span class="text-danger">*</span></label>
            <input class="form-control" name="divisi" placeholder="Contoh: Operasional, IT" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Masa Kerja (tahun) <span class="text-danger">*</span></label>
            <input class="form-control" name="masa_kerja" type="number" min="0" max="50" placeholder="Contoh: 3" required>
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

<!-- ══ MODAL EDIT ══ -->
<div class="modal fade" id="modalEdit" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header" style="background:#1565c0;color:#fff;">
        <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Pegawai</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" id="formEdit" novalidate>
        <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nama Pegawai <span class="text-danger">*</span></label>
            <input class="form-control" name="nama_pegawai" id="editNama" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Jabatan <span class="text-danger">*</span></label>
            <input class="form-control" name="jabatan" id="editJabatan" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Divisi <span class="text-danger">*</span></label>
            <input class="form-control" name="divisi" id="editDivisi" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Masa Kerja (tahun) <span class="text-danger">*</span></label>
            <input class="form-control" name="masa_kerja" id="editMasa" type="number" min="0" max="50" required>
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

<!-- ══ MODAL HAPUS ══ -->
<div class="modal fade" id="modalHapus" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title"><i class="bi bi-exclamation-triangle-fill me-2"></i>Hapus Pegawai</h5>
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

<?php
$extraJs = <<<JS
<script>
function fillEdit(btn) {
  document.getElementById('editNama').value    = btn.dataset.nama;
  document.getElementById('editJabatan').value = btn.dataset.jabatan;
  document.getElementById('editDivisi').value  = btn.dataset.divisi;
  document.getElementById('editMasa').value    = btn.dataset.masa;
  document.getElementById('formEdit').action   = '/employees/' + btn.dataset.id + '/update';
}
function fillHapus(btn) {
  document.getElementById('hapusNama').textContent = btn.dataset.nama;
  document.getElementById('formHapus').action      = '/employees/' + btn.dataset.id + '/delete';
}
</script>
JS;
?>
<?php $content = ob_get_clean(); include base_path('views/layouts/app.php'); ?>
