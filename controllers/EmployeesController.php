<?php
namespace App\Controllers;

use App\Firestore;

class EmployeesController {
    public function __construct(private Firestore $fs) {}

    public function index() {
        $employees = $this->fs->all('employees');
        view('employees_index', compact('employees'));
    }

    public function store() {
        verify_csrf();
        $data = [
            'nama_pegawai' => $_POST['nama_pegawai'] ?? '',
            'jabatan'      => $_POST['jabatan'] ?? '',
            'divisi'       => $_POST['divisi'] ?? '',
            'masa_kerja'   => $_POST['masa_kerja'] ?? '',
        ];
        $id = uniqid('peg_');
        $this->fs->set('employees', $id, $data + ['id_pegawai'=>$id]);
        flash('success', 'Pegawai ditambahkan.');
        redirect('/employees');
    }

    public function update($id) {
        verify_csrf();
        $data = [
            'nama_pegawai' => $_POST['nama_pegawai'] ?? '',
            'jabatan'      => $_POST['jabatan'] ?? '',
            'divisi'       => $_POST['divisi'] ?? '',
            'masa_kerja'   => $_POST['masa_kerja'] ?? '',
        ];
        $this->fs->set('employees', $id, $data + ['id_pegawai'=>$id]);
        flash('success', 'Pegawai diperbarui.');
        redirect('/employees');
    }

    public function delete($id) {
        verify_csrf();
        $this->fs->delete('employees', $id);
        flash('success', 'Pegawai dihapus.');
        redirect('/employees');
    }
}
