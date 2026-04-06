<?php
namespace App\Controllers;

use App\Firestore;
use App\Ahp;

class AhpController {
    public function __construct(private Firestore $fs, private Ahp $ahp) {}

    // ─── Helper: normalisasi raw matrix dari Firestore flat format ke int 2D ───
    private function normalizeMatrix(array $raw, int $n): array {
        $matrix = [];
        // Cek apakah format flat 'm_i_j' atau nested
        $hasFlatKey = isset($raw['m_0_0']) || isset($raw['m_0_1']);
        if ($hasFlatKey) {
            // Format baru: flat key 'm_i_j'
            for ($i = 0; $i < $n; $i++) {
                for ($j = 0; $j < $n; $j++) {
                    $matrix[$i][$j] = (float)($raw['m_' . $i . '_' . $j] ?? ($i === $j ? 1 : 0));
                }
            }
        } else {
            // Format lama: nested array atau legacy
            for ($i = 0; $i < $n; $i++) {
                $row = $raw[$i] ?? $raw[(string)$i] ?? [];
                for ($j = 0; $j < $n; $j++) {
                    $matrix[$i][$j] = (float)($row[$j] ?? $row[(string)$j] ?? ($i === $j ? 1 : 0));
                }
            }
        }
        return $matrix;
    }

    // ─── Tampilkan form pairwise dengan data yang sudah tersimpan ───
    public function pairwise() {
        $criteria = $this->loadCriteria();
        $n = count($criteria);
        $savedMatrix = array_fill(0, $n, array_fill(0, $n, ''));
        if ($n > 0) {
            // Data disimpan flat di document root (format: m_i_j)
            $pairDoc = $this->fs->get('pairwise', 'current') ?? [];
            unset($pairDoc['id']);
            $savedMatrix = $this->normalizeMatrix($pairDoc, $n);
        }
        view('pairwise', compact('criteria', 'savedMatrix'));
    }

    // ─── Simpan matriks pairwise ───
    public function storePairwise() {
        verify_csrf();
        $rawMatrix = $_POST['matrix'] ?? [];

        // Firestore tidak support nested array — flatten ke format 'i_j' => float
        $flat = [];
        foreach ($rawMatrix as $i => $row) {
            foreach ($row as $j => $val) {
                $flat['m_' . (int)$i . '_' . (int)$j] = (float)$val;
            }
        }
        $this->fs->set('pairwise', 'current', $flat);
        flash('success', 'Matriks perbandingan berhasil disimpan.');
        redirect('/ahp/pairwise');
    }

    // ─── Hitung AHP ───
    public function calculate() {
        $criteria = $this->loadCriteria();
        $n = count($criteria);
        // Data pairwise disimpan flat di document root (format: m_i_j)
        $pairDoc = $this->fs->get('pairwise', 'current') ?? [];
        unset($pairDoc['id']); // hapus meta field dari Firestore

        // Normalisasi ke int 2D array
        $pair = $this->normalizeMatrix($pairDoc, $n);

        [$weights, $norm] = $this->ahp->weights($pair);
        [$lambda, $ci, $cr] = $this->ahp->consistency($pair, $weights);

        // Hitung RI untuk ditampilkan
        $riTable = [1=>0,2=>0,3=>0.58,4=>0.90,5=>1.12,6=>1.24,7=>1.32,8=>1.41,9=>1.45];
        $ri = $riTable[$n] ?? 1.45;
        // Override CR RI dengan yang benar (consistency() pakai tabel internal juga)
        $cr_display = $ri == 0 ? 0 : $ci / $ri;

        $status = $cr_display <= 0.1 ? 'Konsisten' : 'Tidak konsisten, perbaiki input';

        // Flatten weights ke format Firestore-safe: w_0, w_1, ...
        $flatResult = [
            'lambda' => $lambda,
            'ci'     => $ci,
            'ri'     => $ri,
            'cr'     => $cr_display,
            'status' => $status,
        ];
        foreach ($weights as $i => $w) {
            $flatResult['w_' . $i] = (float)$w;
        }
        $this->fs->set('results', 'current', $flatResult);
        $cr = $cr_display;
        view('ahp_result', compact('criteria','weights','norm','lambda','ci','ri','cr','status'));
    }

    // ─── Tampilkan form input nilai dengan data tersimpan ───
    public function scores() {
        $criteria  = $this->loadCriteria();
        $employees = $this->fs->all('employees');

        // Load saved scores — Firestore returns string keys
        $savedScores = [];
        $scoresDocs = $this->fs->all('scores');
        foreach ($scoresDocs as $doc) {
            $pid = $doc['id'];
            foreach ($criteria as $idx => $c) {
                $savedScores[$pid][$idx] = $doc[$idx] ?? $doc[(string)$idx] ?? '';
            }
        }

        view('scores', compact('criteria','employees','savedScores'));
    }

    // ─── Simpan nilai pegawai ───
    public function storeScores() {
        verify_csrf();
        $rawScores = $_POST['scores'] ?? [];
        foreach ($rawScores as $pegId => $vals) {
            $clean = [];
            foreach ($vals as $idx => $v) {
                $clean[(int)$idx] = (float)$v;
            }
            $this->fs->set('scores', $pegId, $clean);
        }
        flash('success','Nilai pegawai berhasil disimpan.');
        redirect('/ahp/scores');
    }

    // ─── Helper: normalisasi weights dari Firestore flat-key ke int-indexed array ───
    private function normalizeWeights(array $resultDoc, int $n): array {
        $out = [];
        for ($i = 0; $i < $n; $i++) {
            // Format baru flat: w_0, w_1, ...
            $out[$i] = (float)($resultDoc['w_' . $i] ?? 0);
        }
        return $out;
    }

    private function normalizeScores(array $docs, int $n): array {
        $scores = [];
        foreach ($docs as $doc) {
            $pid = $doc['id'];
            $vals = [];
            for ($i = 0; $i < $n; $i++) {
                $vals[$i] = (float)($doc[$i] ?? $doc[(string)$i] ?? 0);
            }
            $scores[$pid] = $vals;
        }
        return $scores;
    }

    // ─── Ranking (dipakai admin & pegawai) ───
    public function ranking() {
        $criteria  = $this->loadCriteria();
        $n = count($criteria);
        $resultDoc = $this->fs->get('results','current') ?? [];
        $weights   = $this->normalizeWeights($resultDoc, $n);
        $ahpInfo   = $resultDoc;

        // Load scores — Firestore mungkin simpan key sebagai string
        $scoresDocs = $this->fs->all('scores');
        $scores = $this->normalizeScores($scoresDocs, $n);

        // Hitung ranking
        $rawRanking = $this->ahp->finalScores($weights, $scores);

        // Load employee details
        $employees = $this->fs->all('employees');
        $mapEmp = [];
        foreach ($employees as $e) {
            $mapEmp[$e['id_pegawai']] = $e;
        }

        $ranking = [];
        foreach ($rawRanking as $r) {
            $pid  = $r['pegawai_id'];
            $emp  = $mapEmp[$pid] ?? [];
            $ranking[] = [
                'pegawai_id' => $pid,
                'nama'       => $emp['nama_pegawai'] ?? $pid,
                'jabatan'    => $emp['jabatan'] ?? '-',
                'divisi'     => $emp['divisi'] ?? '-',
                'masa_kerja' => $emp['masa_kerja'] ?? '-',
                'total'      => $r['total'],
                'scores'     => $scores[$pid] ?? [],
            ];
        }

        // Cek apakah user pegawai — cari ID nya
        $me = current_user();
        $myPegawaiId = null;
        if ($me && $me['role'] === 'pegawai') {
            // Coba temukan pegawai yang username-nya cocok
            foreach ($employees as $e) {
                if (($e['user_id'] ?? '') === $me['id'] || ($e['linked_username'] ?? '') === $me['username']) {
                    $myPegawaiId = $e['id_pegawai'];
                    break;
                }
            }
        }

        view('dashboard_employee', compact('ranking','criteria','ahpInfo','myPegawaiId'));
    }

    // ─── Nilai saya (pegawai) ───
    public function myScores() {
        $me = current_user();
        $criteria  = $this->loadCriteria();
        $n = count($criteria);
        $resultDoc = $this->fs->get('results','current') ?? [];
        $weights   = $this->normalizeWeights($resultDoc, $n);

        // Load all scores — Firestore mungkin simpan key sebagai string
        $scoresDocs = $this->fs->all('scores');
        $scores = $this->normalizeScores($scoresDocs, $n);

        $rawRanking = $this->ahp->finalScores($weights, $scores);

        // Cari data pegawai saya
        $employees = $this->fs->all('employees');
        $myEmp = null;
        $myPegawaiId = null;
        foreach ($employees as $e) {
            if (($e['user_id'] ?? '') === $me['id'] || ($e['linked_username'] ?? '') === $me['username']) {
                $myEmp = $e;
                $myPegawaiId = $e['id_pegawai'];
                break;
            }
        }

        $myRankInfo = null;
        if ($myPegawaiId && isset($scores[$myPegawaiId])) {
            // Cari posisi ranking saya
            $rank = 1;
            foreach ($rawRanking as $idx => $r) {
                if ($r['pegawai_id'] === $myPegawaiId) {
                    $rank = $idx + 1;
                    break;
                }
            }
            $detail = [];
            foreach ($criteria as $i => $c) {
                $nilai = $scores[$myPegawaiId][$i] ?? 0;
                $bobot = $weights[$i] ?? 0;
                $detail[] = [
                    'nama_kriteria' => $c['nama_kriteria'],
                    'bobot'  => $bobot,
                    'nilai'  => $nilai,
                    'kontribusi' => $bobot * $nilai,
                ];
            }
            // Hitung total
            $total = 0;
            foreach ($detail as $d) $total += $d['kontribusi'];

            $myRankInfo = [
                'rank'    => $rank,
                'total'   => $total,
                'total_pegawai' => count($rawRanking),
                'jabatan' => $myEmp['jabatan'] ?? '',
                'detail'  => $detail,
            ];
        }

        view('my_scores', compact('myRankInfo','criteria'));
    }

    // ─── Helper: load criteria sorted ───
    private function loadCriteria(): array {
        $criteria = $this->fs->all('criteria');
        usort($criteria, fn($a,$b) => strcmp($a['id_kriteria'], $b['id_kriteria']));
        return array_values($criteria);
    }
}
