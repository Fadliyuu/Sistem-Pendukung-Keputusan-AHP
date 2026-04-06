<?php
namespace App;

use Google\Cloud\Core\Exception\NotFoundException;

class Auth {
    public function __construct(private Firestore $fs) {}

    public function attempt(string $username, string $password): bool {
        $query = $this->fs->col('users')->where('username', '==', $username)->documents();
        $user = null;
        foreach ($query as $doc) { $user = $doc->data(); $user['id'] = $doc->id(); break; }

        // Seed admin jika belum ada
        if (!$user && $username === envv('ADMIN_DEFAULT_USER', 'admin') && $password === envv('ADMIN_DEFAULT_PASS', 'admin123')) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $this->fs->set('users', 'admin', [
                'nama' => 'Administrator',
                'username' => $username,
                'password_hash' => $hash,
                'role' => 'admin'
            ]);
            $user = ['id'=>'admin','nama'=>'Administrator','username'=>$username,'password_hash'=>$hash,'role'=>'admin'];
        }
        // Seed pegawai default
        if (!$user && $username === envv('EMP_DEFAULT_USER', 'pegawai') && $password === envv('EMP_DEFAULT_PASS', 'pegawai123')) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $this->fs->set('users', 'pegawai', [
                'nama' => 'Pegawai',
                'username' => $username,
                'password_hash' => $hash,
                'role' => 'pegawai'
            ]);
            $user = ['id'=>'pegawai','nama'=>'Pegawai','username'=>$username,'password_hash'=>$hash,'role'=>'pegawai'];
        }

        if (!$user || !password_verify($password, $user['password_hash'] ?? '')) {
            return false;
        }
        $_SESSION['user'] = $user;
        return true;
    }

    public function logout() {
        unset($_SESSION['user']);
        session_destroy();
    }
}
