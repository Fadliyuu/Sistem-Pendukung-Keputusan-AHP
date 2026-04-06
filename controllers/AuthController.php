<?php
namespace App\Controllers;

use App\Auth as AuthService;
use App\Firestore;
use App\Ahp;

class AuthController {
    public function __construct(private AuthService $auth, private Firestore $fs, private Ahp $ahp) {}

    public function showLogin() {
        view('login');
    }

    public function login() {
        verify_csrf();
        remember_old();
        $u = trim($_POST['username'] ?? '');
        $p = $_POST['password'] ?? '';

        if (empty($u) || empty($p)) {
            flash('error', 'Username dan password tidak boleh kosong.');
            redirect('/login');
        }

        if (!$this->auth->attempt($u, $p)) {
            flash('error', 'Username atau password salah. Silakan coba lagi.');
            redirect('/login');
        }
        clear_old();
        $role = current_user()['role'];
        redirect($role === 'admin' ? '/admin' : '/ranking');
    }

    public function logout() {
        $this->auth->logout();
        redirect('/login');
    }

    public function adminDashboard() {
        $criteria  = array_values(array_filter($this->fs->all('criteria'), fn($c) => !empty($c['id_kriteria'])));
        $employees = $this->fs->all('employees');
        $n = count($criteria);
        usort($criteria, fn($a,$b) => strcmp($a['id_kriteria'],$b['id_kriteria']));

        // Ambil hasil AHP
        $ahpResult = $this->fs->get('results','current') ?? [];

        // Hitung ranking untuk top 5
        $topRanking = [];
        if (!empty($ahpResult['w_0'])) {
            // Weights stored flat: w_0, w_1, ...
            $weights = [];
            for ($i = 0; $i < $n; $i++) {
                $weights[$i] = (float)($ahpResult['w_' . $i] ?? 0);
            }
            $scoresDocs = $this->fs->all('scores');
            $scores = [];
            foreach ($scoresDocs as $doc) {
                $pid = $doc['id'];
                $vals = [];
                for ($i = 0; $i < $n; $i++) {
                    $vals[$i] = (float)($doc[$i] ?? $doc[(string)$i] ?? 0);
                }
                $scores[$pid] = $vals;
            }
            $rawRanking = $this->ahp->finalScores($weights, $scores);
            $mapEmp = [];
            foreach ($employees as $e) $mapEmp[$e['id_pegawai']] = $e;
            foreach (array_slice($rawRanking, 0, 5) as $r) {
                $emp = $mapEmp[$r['pegawai_id']] ?? [];
                $topRanking[] = [
                    'nama'    => $emp['nama_pegawai'] ?? $r['pegawai_id'],
                    'jabatan' => $emp['jabatan'] ?? '-',
                    'total'   => $r['total'],
                ];
            }
        }

        $counts = [
            'pegawai'  => count($employees),
            'kriteria' => count($criteria),
            'ranking'  => count($topRanking),
        ];

        view('dashboard_admin', compact('counts', 'ahpResult', 'topRanking'));
    }

    public function employeeDashboard() {
        redirect('/ranking');
    }
}
