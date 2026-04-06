<?php ob_start();
$pageTitle = 'Manajemen User';
?>
<div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-2">
  <div>
    <h2><i class="bi bi-person-badge-fill text-telkom me-2"></i>Manajemen User</h2>
    <p>Kelola akun login untuk admin dan pegawai</p>
  </div>
  <button class="btn btn-telkom" data-bs-toggle="modal" data-bs-target="#modalTambah">
    <i class="bi bi-person-plus-fill me-2"></i>Tambah User
  </button>
</div>

<!-- Alert Info -->
<div class="alert alert-light border mb-4 d-flex gap-3 align-items-start">
  <i class="bi bi-shield-lock-fill text-warning fs-5 flex-shrink-0 mt-1"></i>
  <div class="small">
    <strong>Perhatian:</strong> Manajemen user ini berbeda dengan data pegawai. User adalah akun untuk login ke sistem.
    Pastikan setiap pegawai yang membutuhkan akses memiliki akun dengan role <em>pegawai</em>.
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
            <th>Nama</th>
            <th>Username</th>
            <th>Role</th>
            <th style="width:130px;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($users)): ?>
          <tr>
            <td colspan="5" class="text-center py-5">
              <i class="bi bi-people fs-1 text-muted opacity-50 d-block mb-2"></i>
              <span class="text-muted">Belum ada data user.</span>
            </td>
          </tr>
          <?php else: ?>
          <?php foreach ($users as $idx => $u): ?>
          <tr>
            <td class="text-muted"><?= $idx + 1 ?></td>
            <td>
              <div class="d-flex align-items-center gap-2">
                <div style="width:34px;height:34px;background:<?= ($u['role']??'')==='admin'?'#fff0f0':'#f0f7ff' ?>;color:<?= ($u['role']??'')==='admin'?'#cc0000':'#1565c0' ?>;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.85rem;border-radius:50%;flex-shrink:0;">
                  <?= strtoupper(substr($u['nama'] ?? 'U', 0, 1)) ?>
                </div>
                <div class="fw-semibold"><?= htmlspecialchars($u['nama'] ?? '—') ?></div>
              </div>
            </td>
            <td>
              <code><?= htmlspecialchars($u['username'] ?? '') ?></code>
            </td>
            <td>
              <?php if (($u['role'] ?? '') === 'admin'): ?>
                <span class="badge" style="background:#fff0f0;color:#cc0000;border:1px solid #ffcccc;">
                  <i class="bi bi-shield-fill me-1"></i>Admin
                </span>
              <?php else: ?>
                <span class="badge" style="background:#f0f7ff;color:#1565c0;border:1px solid #c5d8f5;">
                  <i class="bi bi-person-fill me-1"></i>Pegawai
                </span>
              <?php endif; ?>
            </td>
            <td>
              <div class="d-flex gap-1">
                <button class="btn btn-sm btn-outline-primary"
                  data-bs-toggle="modal" data-bs-target="#modalEdit"
                  data-id="<?= htmlspecialchars($u['id'] ?? '') ?>"
                  data-nama="<?= htmlspecialchars($u['nama'] ?? '') ?>"
                  data-username="<?= htmlspecialchars($u['username'] ?? '') ?>"
                  data-role="<?= htmlspecialchars($u['role'] ?? 'pegawai') ?>"
                  onclick="fillEdit(this)"
                  title="Edit">
                  <i class="bi bi-pencil-fill"></i>
                </button>
                <?php if (($u['id']??'') !== 'admin'): ?>
                <button class="btn btn-sm btn-outline-danger"
                  data-bs-toggle="modal" data-bs-target="#modalHapus"
                  data-id="<?= htmlspecialchars($u['id'] ?? '') ?>"
                  data-nama="<?= htmlspecialchars($u['nama'] ?? '') ?>"
                  onclick="fillHapus(this)"
                  title="Hapus">
                  <i class="bi bi-trash-fill"></i>
                </button>
                <?php else: ?>
                <button class="btn btn-sm btn-outline-secondary" disabled title="Admin utama tidak bisa dihapus">
                  <i class="bi bi-lock-fill"></i>
                </button>
                <?php endif; ?>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
    <div class="p-3 border-top text-muted" style="font-size:.78rem;">
      Total: <strong><?= count($users) ?></strong> user terdaftar
    </div>
  </div>
</div>

<!-- MODAL TAMBAH -->
<div class="modal fade" id="modalTambah" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header" style="background:#cc0000;color:#fff;">
        <h5 class="modal-title"><i class="bi bi-person-plus-fill me-2"></i>Tambah User</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" action="/users" novalidate>
        <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
            <input class="form-control" name="nama" placeholder="Masukkan nama lengkap" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Username <span class="text-danger">*</span></label>
            <input class="form-control" name="username" placeholder="Masukkan username unik" required autocomplete="off">
          </div>
          <div class="mb-3">
            <label class="form-label">Password <span class="text-danger">*</span></label>
            <input class="form-control" name="password" type="password" placeholder="Minimal 6 karakter" required autocomplete="new-password">
          </div>
          <div class="mb-3">
            <label class="form-label">Role <span class="text-danger">*</span></label>
            <select class="form-select" name="role">
              <option value="pegawai">Pegawai</option>
              <option value="admin">Admin</option>
            </select>
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
        <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit User</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" id="formEdit" novalidate>
        <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
            <input class="form-control" name="nama" id="editNama" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Username <span class="text-danger">*</span></label>
            <input class="form-control" name="username" id="editUsername" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Password Baru <small class="text-muted">(kosongkan jika tidak ingin mengubah)</small></label>
            <input class="form-control" name="password" type="password" placeholder="Isi untuk mengubah password" autocomplete="new-password">
          </div>
          <div class="mb-3">
            <label class="form-label">Role <span class="text-danger">*</span></label>
            <select class="form-select" name="role" id="editRole">
              <option value="pegawai">Pegawai</option>
              <option value="admin">Admin</option>
            </select>
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
        <h5 class="modal-title"><i class="bi bi-exclamation-triangle-fill me-2"></i>Hapus User</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center py-4">
        <p class="mb-1">Apakah Anda yakin ingin menghapus user?</p>
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
  document.getElementById('editNama').value     = btn.dataset.nama;
  document.getElementById('editUsername').value = btn.dataset.username;
  document.getElementById('editRole').value     = btn.dataset.role;
  document.getElementById('formEdit').action    = '/users/' + btn.dataset.id + '/update';
}
function fillHapus(btn) {
  document.getElementById('hapusNama').textContent = btn.dataset.nama;
  document.getElementById('formHapus').action      = '/users/' + btn.dataset.id + '/delete';
}
</script>
JS; ?>
<?php $content = ob_get_clean(); include base_path('views/layouts/app.php'); ?>
