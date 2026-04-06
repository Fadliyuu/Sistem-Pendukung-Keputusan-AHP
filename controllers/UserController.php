<?php
namespace App\Controllers;

use App\Firestore;

class UserController {
    public function __construct(private Firestore $fs) {}

    public function index() {
        $users = $this->fs->all('users');
        // Sort: admin first
        usort($users, fn($a,$b) => strcmp($b['role']??'', $a['role']??''));
        view('users_index', compact('users'));
    }

    public function store() {
        verify_csrf();
        $nama     = trim($_POST['nama'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $role     = in_array($_POST['role']??'', ['admin','pegawai']) ? $_POST['role'] : 'pegawai';

        // Validasi
        if (empty($nama) || empty($username) || empty($password)) {
            flash('error', 'Semua field wajib diisi.');
            redirect('/users');
        }
        if (strlen($password) < 6) {
            flash('error', 'Password minimal 6 karakter.');
            redirect('/users');
        }

        // Cek username sudah ada
        $existing = $this->fs->col('users')->where('username','==',$username)->documents();
        foreach ($existing as $doc) {
            flash('error', 'Username sudah digunakan.');
            redirect('/users');
        }

        $id   = uniqid('usr_');
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $this->fs->set('users', $id, [
            'nama'          => $nama,
            'username'      => $username,
            'password_hash' => $hash,
            'role'          => $role,
        ]);
        flash('success', "User '{$nama}' berhasil ditambahkan.");
        redirect('/users');
    }

    public function update(string $id) {
        verify_csrf();
        $existing = $this->fs->get('users', $id);
        if (!$existing) {
            flash('error', 'User tidak ditemukan.');
            redirect('/users');
        }

        $nama     = trim($_POST['nama'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $role     = in_array($_POST['role']??'', ['admin','pegawai']) ? $_POST['role'] : ($existing['role']??'pegawai');

        if (empty($nama) || empty($username)) {
            flash('error', 'Nama dan username tidak boleh kosong.');
            redirect('/users');
        }

        $data = [
            'nama'          => $nama,
            'username'      => $username,
            'role'          => $role,
            'password_hash' => $existing['password_hash'],
        ];

        if (!empty($password)) {
            if (strlen($password) < 6) {
                flash('error', 'Password minimal 6 karakter.');
                redirect('/users');
            }
            $data['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $this->fs->set('users', $id, $data);
        flash('success', "User '{$nama}' berhasil diperbarui.");
        redirect('/users');
    }

    public function delete(string $id) {
        verify_csrf();
        // Lindungi akun admin utama
        if ($id === 'admin') {
            flash('error', 'Akun admin utama tidak dapat dihapus.');
            redirect('/users');
        }
        $user = $this->fs->get('users', $id);
        $nama = $user['nama'] ?? $id;
        $this->fs->delete('users', $id);
        flash('success', "User '{$nama}' berhasil dihapus.");
        redirect('/users');
    }
}
