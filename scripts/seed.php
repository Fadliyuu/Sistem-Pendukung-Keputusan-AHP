<?php
/**
 * ============================================================
 * SEEDER — SPK AHP Pegawai Terbaik
 * PT Telkom Satelit Indonesia Regional 6
 * ============================================================
 * Jalankan: php seed.php
 * ============================================================
 */

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/helpers.php';

use App\Firestore;

echo "\n";
echo "==========================================================\n";
echo "  SEEDER — SPK AHP Telkomsat Regional 6\n";
echo "==========================================================\n\n";

try {
    $fs = new Firestore();
    echo "✅ Terhubung ke Firebase Firestore (project: " . envv('FIRESTORE_PROJECT_ID') . ")\n\n";

    // ── 1. USERS ──────────────────────────────────────────────
    echo "📌 Menyimpan data User...\n";

    $users = [
        [
            'id'       => 'admin_utama',
            'nama'     => 'Administrator',
            'username' => 'admin',
            'password' => 'admin123',
            'role'     => 'admin',
        ],
        [
            'id'       => 'usr_budi',
            'nama'     => 'Budi Santoso',
            'username' => 'budi.santoso',
            'password' => 'pegawai123',
            'role'     => 'pegawai',
            'linked_username' => 'budi.santoso',
        ],
        [
            'id'       => 'usr_siti',
            'nama'     => 'Siti Rahayu',
            'username' => 'siti.rahayu',
            'password' => 'pegawai123',
            'role'     => 'pegawai',
            'linked_username' => 'siti.rahayu',
        ],
        [
            'id'       => 'usr_andi',
            'nama'     => 'Andi Wijaya',
            'username' => 'andi.wijaya',
            'password' => 'pegawai123',
            'role'     => 'pegawai',
            'linked_username' => 'andi.wijaya',
        ],
        [
            'id'       => 'usr_dewi',
            'nama'     => 'Dewi Kusuma',
            'username' => 'dewi.kusuma',
            'password' => 'pegawai123',
            'role'     => 'pegawai',
            'linked_username' => 'dewi.kusuma',
        ],
        [
            'id'       => 'usr_rizal',
            'nama'     => 'Rizal Firmansyah',
            'username' => 'rizal.firmansyah',
            'password' => 'pegawai123',
            'role'     => 'pegawai',
            'linked_username' => 'rizal.firmansyah',
        ],
    ];

    foreach ($users as $u) {
        $id = $u['id'];
        $data = [
            'nama'          => $u['nama'],
            'username'      => $u['username'],
            'password_hash' => password_hash($u['password'], PASSWORD_DEFAULT),
            'role'          => $u['role'],
        ];
        if (isset($u['linked_username'])) {
            $data['linked_username'] = $u['linked_username'];
        }
        $fs->set('users', $id, $data);
        echo "   ✔ User: {$u['nama']} ({$u['role']}) — login: {$u['username']} / {$u['password']}\n";
    }

    // ── 2. PEGAWAI ────────────────────────────────────────────
    echo "\n📌 Menyimpan data Pegawai...\n";

    $pegawai = [
        [
            'id'           => 'peg_budi',
            'nama_pegawai' => 'Budi Santoso',
            'jabatan'      => 'Teknisi Satelit',
            'divisi'       => 'Operasional',
            'masa_kerja'   => '5',
            'linked_username' => 'budi.santoso',
        ],
        [
            'id'           => 'peg_siti',
            'nama_pegawai' => 'Siti Rahayu',
            'jabatan'      => 'Supervisor Lapangan',
            'divisi'       => 'Operasional',
            'masa_kerja'   => '7',
            'linked_username' => 'siti.rahayu',
        ],
        [
            'id'           => 'peg_andi',
            'nama_pegawai' => 'Andi Wijaya',
            'jabatan'      => 'Teknisi Jaringan',
            'divisi'       => 'IT & Infrastruktur',
            'masa_kerja'   => '3',
            'linked_username' => 'andi.wijaya',
        ],
        [
            'id'           => 'peg_dewi',
            'nama_pegawai' => 'Dewi Kusuma',
            'jabatan'      => 'Staf Administrasi',
            'divisi'       => 'Administrasi & Keuangan',
            'masa_kerja'   => '4',
            'linked_username' => 'dewi.kusuma',
        ],
        [
            'id'           => 'peg_rizal',
            'nama_pegawai' => 'Rizal Firmansyah',
            'jabatan'      => 'Teknisi Lapangan',
            'divisi'       => 'Operasional',
            'masa_kerja'   => '6',
            'linked_username' => 'rizal.firmansyah',
        ],
    ];

    foreach ($pegawai as $p) {
        $id = $p['id'];
        unset($p['id']);
        $p['id_pegawai'] = $id;
        $fs->set('employees', $id, $p);
        echo "   ✔ Pegawai: {$p['nama_pegawai']} — {$p['jabatan']} / {$p['divisi']}\n";
    }

    // ── 3. KRITERIA ───────────────────────────────────────────
    echo "\n📌 Menyimpan data Kriteria...\n";

    $kriteria = [
        [
            'id'            => 'kri_01_kehadiran',
            'nama_kriteria' => 'Kehadiran',
            'jenis_kriteria'=> 'benefit',
            'deskripsi'     => 'Tingkat kehadiran dan ketepatan waktu pegawai dalam bekerja',
        ],
        [
            'id'            => 'kri_02_kinerja',
            'nama_kriteria' => 'Kinerja',
            'jenis_kriteria'=> 'benefit',
            'deskripsi'     => 'Hasil dan kualitas pekerjaan yang dicapai pegawai',
        ],
        [
            'id'            => 'kri_03_kedisiplinan',
            'nama_kriteria' => 'Kedisiplinan',
            'jenis_kriteria'=> 'benefit',
            'deskripsi'     => 'Kepatuhan terhadap peraturan dan tata tertib perusahaan',
        ],
        [
            'id'            => 'kri_04_tanggung_jawab',
            'nama_kriteria' => 'Tanggung Jawab',
            'jenis_kriteria'=> 'benefit',
            'deskripsi'     => 'Kemampuan menyelesaikan tugas sesuai target dan tepat waktu',
        ],
        [
            'id'            => 'kri_05_kerja_sama',
            'nama_kriteria' => 'Kerja Sama',
            'jenis_kriteria'=> 'benefit',
            'deskripsi'     => 'Kemampuan bekerja sama dalam tim dan koordinasi antar divisi',
        ],
    ];

    foreach ($kriteria as $k) {
        $id = $k['id'];
        unset($k['id']);
        $k['id_kriteria'] = $id;
        $fs->set('criteria', $id, $k);
        echo "   ✔ Kriteria: {$k['nama_kriteria']} ({$k['jenis_kriteria']})\n";
    }

    // ── 4. MATRIKS PAIRWISE ───────────────────────────────────
    echo "\n📌 Menyimpan Matriks Perbandingan Berpasangan AHP...\n";

    // Matriks 5x5 (Kehadiran, Kinerja, Kedisiplinan, Tanggung Jawab, Kerja Sama)
    // Sumber: penilaian pakar (konsisten, CR < 0.1)
    $matrix = [
        // K.hadir  Kinerja  K.displin T.jawab  K.sama
        [1,       3,       2,        4,        5      ],  // Kehadiran
        [1/3,     1,       1/2,      2,        3      ],  // Kinerja
        [1/2,     2,       1,        3,        4      ],  // Kedisiplinan
        [1/4,     1/2,     1/3,      1,        2      ],  // Tanggung Jawab
        [1/5,     1/3,     1/4,      1/2,      1      ],  // Kerja Sama
    ];

    // Flatten ke format flat key m_i_j (sama seperti AhpController::storePairwise)
    $flatMatrix = [];
    foreach ($matrix as $i => $row) {
        foreach ($row as $j => $val) {
            $flatMatrix['m_' . $i . '_' . $j] = (float)$val;
        }
    }
    $fs->set('pairwise', 'current', $flatMatrix);
    echo "   ✔ Matriks 5x5 berhasil disimpan (format flat key)\n";

    // ── 5. HITUNG DAN SIMPAN HASIL AHP ───────────────────────
    echo "\n📌 Menghitung & Menyimpan Hasil AHP...\n";

    $n = 5;
    // Normalisasi
    $colSums = array_fill(0, $n, 0);
    for ($j = 0; $j < $n; $j++)
        for ($i = 0; $i < $n; $i++)
            $colSums[$j] += $matrix[$i][$j];

    $weights = [];
    $norm = [];
    for ($i = 0; $i < $n; $i++) {
        $rowSum = 0;
        for ($j = 0; $j < $n; $j++) {
            $val = $colSums[$j] == 0 ? 0 : $matrix[$i][$j] / $colSums[$j];
            $norm[$i][$j] = $val;
            $rowSum += $val;
        }
        $weights[$i] = $rowSum / $n;
    }

    // Konsistensi
    $lamSum = 0;
    for ($i = 0; $i < $n; $i++) {
        $rowSum = 0;
        for ($j = 0; $j < $n; $j++) $rowSum += $matrix[$i][$j] * $weights[$j];
        $lamSum += $weights[$i] == 0 ? 0 : $rowSum / $weights[$i];
    }
    $lambda = $lamSum / $n;
    $ci = ($lambda - $n) / ($n - 1);
    $ri = 1.12; // n=5
    $cr = $ci / $ri;

    $result = [
        'lambda'  => $lambda,
        'ci'      => $ci,
        'ri'      => $ri,
        'cr'      => $cr,
        'status'  => $cr <= 0.1 ? 'Konsisten' : 'Tidak konsisten',
    ];
    // Simpan weights sebagai flat keys (format sama dengan AhpController::calculate)
    foreach ($weights as $i => $w) {
        $result['w_' . $i] = (float)$w;
    }
    $fs->set('results', 'current', $result);

    echo "   ✔ λ max   = " . number_format($lambda, 4) . "\n";
    echo "   ✔ CI      = " . number_format($ci, 4) . "\n";
    echo "   ✔ CR      = " . number_format($cr, 4) . " → " . ($cr <= 0.1 ? "✅ KONSISTEN" : "❌ TIDAK KONSISTEN") . "\n";
    echo "   ✔ Bobot:\n";
    $namaKriteria = ['Kehadiran','Kinerja','Kedisiplinan','Tanggung Jawab','Kerja Sama'];
    foreach ($weights as $i => $w) {
        echo "      [{$namaKriteria[$i]}] = " . number_format($w, 4) . " (" . number_format($w*100, 1) . "%)\n";
    }

    // ── 6. NILAI PEGAWAI (SCORES) ─────────────────────────────
    echo "\n📌 Menyimpan Nilai Pegawai per Kriteria...\n";

    // idx: 0=Kehadiran, 1=Kinerja, 2=Kedisiplinan, 3=TanggungJawab, 4=KerjaSama
    $scores = [
        'peg_budi'  => [0=>88, 1=>82, 2=>90, 3=>85, 4=>80],
        'peg_siti'  => [0=>92, 1=>91, 2=>88, 3=>93, 4=>90],
        'peg_andi'  => [0=>78, 1=>85, 2=>80, 3=>79, 4=>83],
        'peg_dewi'  => [0=>85, 1=>76, 2=>82, 3=>80, 4=>88],
        'peg_rizal' => [0=>80, 1=>88, 2=>85, 3=>87, 4=>82],
    ];

    $namaPeg = [
        'peg_budi'  => 'Budi Santoso',
        'peg_siti'  => 'Siti Rahayu',
        'peg_andi'  => 'Andi Wijaya',
        'peg_dewi'  => 'Dewi Kusuma',
        'peg_rizal' => 'Rizal Firmansyah',
    ];

    foreach ($scores as $pegId => $vals) {
        $fs->set('scores', $pegId, $vals);
        $total = 0;
        foreach ($weights as $i => $w) $total += $w * ($vals[$i] ?? 0);
        echo "   ✔ {$namaPeg[$pegId]}: nilai akhir = " . number_format($total, 4) . "\n";
    }

    // ── RANGKUMAN ─────────────────────────────────────────────
    echo "\n==========================================================\n";
    echo "  ✅ SEEDER SELESAI — Semua data berhasil dikirim\n";
    echo "==========================================================\n";
    echo "\nData yang telah disimpan ke Firebase:\n";
    echo "  • " . count($users)   . " akun user\n";
    echo "  • " . count($pegawai) . " data pegawai\n";
    echo "  • " . count($kriteria). " kriteria penilaian\n";
    echo "  • 1 matriks perbandingan berpasangan\n";
    echo "  • " . count($scores)  . " set nilai pegawai\n";
    echo "\nAkun login yang tersedia:\n";
    echo "  Admin    : admin / admin123\n";
    foreach ($pegawai as $p) {
        $username = $p['linked_username'];
        echo "  Pegawai  : {$username} / pegawai123 ({$p['nama_pegawai']})\n";
    }
    echo "\nBuka: http://localhost:8080\n\n";

} catch (\Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "\nPastikan:\n";
    echo "  1. FIRESTORE_PROJECT_ID di .env sudah benar\n";
    echo "  2. FIRESTORE_KEY_FILE di .env menunjuk ke file JSON yang valid\n";
    echo "  3. Cloud Firestore API sudah diaktifkan di Firebase Console\n\n";
    exit(1);
}
