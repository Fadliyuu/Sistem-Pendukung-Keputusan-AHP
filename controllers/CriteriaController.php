<?php
namespace App\Controllers;

use App\Firestore;

class CriteriaController {
    public function __construct(private Firestore $fs) {}

    public function index() {
        $criteria = $this->fs->all('criteria');
        view('criteria_index', compact('criteria'));
    }

    public function store() {
        verify_csrf();
        $data = [
            'nama_kriteria' => $_POST['nama_kriteria'] ?? '',
            'jenis_kriteria'=> $_POST['jenis_kriteria'] ?? 'benefit',
            'deskripsi'     => $_POST['deskripsi'] ?? '',
        ];
        $id = uniqid('kri_');
        $this->fs->set('criteria', $id, $data + ['id_kriteria'=>$id]);
        flash('success', 'Kriteria ditambahkan.');
        redirect('/criteria');
    }

    public function update($id) {
        verify_csrf();
        $data = [
            'nama_kriteria' => $_POST['nama_kriteria'] ?? '',
            'jenis_kriteria'=> $_POST['jenis_kriteria'] ?? 'benefit',
            'deskripsi'     => $_POST['deskripsi'] ?? '',
        ];
        $this->fs->set('criteria', $id, $data + ['id_kriteria'=>$id]);
        flash('success', 'Kriteria diperbarui.');
        redirect('/criteria');
    }

    public function delete($id) {
        verify_csrf();
        $this->fs->delete('criteria', $id);
        flash('success', 'Kriteria dihapus.');
        redirect('/criteria');
    }
}
