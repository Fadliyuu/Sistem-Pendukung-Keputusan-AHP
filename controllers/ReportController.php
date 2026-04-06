<?php
namespace App\Controllers;

use App\Firestore;
use App\Ahp;
use Dompdf\Dompdf;
use Dompdf\Options;

class ReportController {
    public function __construct(private Firestore $fs, private Ahp $ahp) {}

    public function index() {
        [$ranking, $criteriaSummary, $ahpMeta, $criteria] = $this->buildReportData();
        view('report', compact('ranking','criteriaSummary','ahpMeta','criteria'));
    }

    public function export() {
        [$ranking, $criteriaSummary, $ahpMeta, $criteria] = $this->buildReportData();

        // Build HTML for PDF
        $tanggal = date('d F Y, H:i') . ' WIB';
        $html  = '<!DOCTYPE html><html lang="id"><head><meta charset="utf-8">';
        $html .= '<style>
          body { font-family: Arial, sans-serif; font-size: 11px; color: #1e1e2d; }
          h2 { text-align:center; color:#cc0000; margin-bottom:2px; }
          h4 { text-align:center; margin:2px 0; }
          p.sub { text-align:center; color:#666; font-size:10px; margin:2px 0 12px; }
          hr { border: 1px solid #cc0000; margin-bottom:16px; }
          table { width:100%; border-collapse:collapse; margin-bottom:16px; font-size:10px; }
          th { background:#cc0000; color:#fff; padding:7px 8px; text-align:center; }
          td { padding:6px 8px; border:1px solid #e0e0e0; }
          tr:nth-child(even) { background:#f9f9f9; }
          .rank1 { background:#fff9c4 !important; }
          .section-title { font-weight:bold; color:#cc0000; margin:14px 0 4px; font-size:11px; }
          .footer { text-align:center; color:#999; font-size:9px; margin-top:20px; }
          .konsisten { color:#065f46; font-weight:bold; }
          .tidakkonsisten { color:#991b1b; font-weight:bold; }
        </style></head><body>';

        $html .= '<h2>LAPORAN HASIL PENILAIAN PEGAWAI TERBAIK</h2>';
        $html .= '<h4>PT Telkom Satelit Indonesia Regional 6</h4>';
        $html .= '<p class="sub">Metode: Analytical Hierarchy Process (AHP) &bull; Tanggal: ' . $tanggal . '</p>';
        $html .= '<hr>';

        // Bobot kriteria
        $html .= '<p class="section-title">A. Bobot Kriteria AHP</p>';
        $html .= '<table><thead><tr><th>No.</th><th>Kriteria</th><th>Jenis</th><th>Bobot Prioritas</th><th>Persentase</th></tr></thead><tbody>';
        foreach ($criteriaSummary as $i => $cs) {
            $html .= '<tr><td>' . ($i+1) . '</td>';
            $html .= '<td>' . htmlspecialchars($cs['nama_kriteria']) . '</td>';
            $html .= '<td>' . ucfirst($cs['jenis']??'benefit') . '</td>';
            $html .= '<td style="text-align:center;">' . number_format($cs['bobot'],4) . '</td>';
            $html .= '<td style="text-align:center;">' . number_format($cs['bobot']*100,2) . '%</td></tr>';
        }
        $html .= '</tbody></table>';

        // Uji Konsistensi
        if ($ahpMeta) {
            $html .= '<p class="section-title">B. Uji Konsistensi</p>';
            $konsisten = ($ahpMeta['cr']??1) <= 0.1;
            $html .= '<table style="width:auto;"><tbody>';
            $html .= '<tr><th>λ Max</th><td>' . number_format($ahpMeta['lambda']??0,4) . '</td></tr>';
            $html .= '<tr><th>CI</th><td>' . number_format($ahpMeta['ci']??0,4) . '</td></tr>';
            $html .= '<tr><th>RI</th><td>' . number_format($ahpMeta['ri']??0,2) . '</td></tr>';
            $html .= '<tr><th>CR</th><td>' . number_format($ahpMeta['cr']??0,4) . '</td></tr>';
            $kls = $konsisten ? 'konsisten' : 'tidakkonsisten';
            $html .= '<tr><th>Status</th><td class="'.$kls.'">' . ($konsisten?'Konsisten (CR ≤ 0,1)':'Tidak Konsisten (CR > 0,1)') . '</td></tr>';
            $html .= '</tbody></table>';
        }

        // Ranking
        $html .= '<p class="section-title">C. Hasil Ranking Pegawai Terbaik</p>';
        $html .= '<table><thead><tr><th>Posisi</th><th>Nama Pegawai</th><th>Jabatan</th><th>Divisi</th><th>Masa Kerja</th>';
        foreach ($criteriaSummary as $cs) {
            $html .= '<th>' . htmlspecialchars($cs['nama_kriteria']) . '</th>';
        }
        $html .= '<th>Nilai Akhir</th></tr></thead><tbody>';

        foreach ($ranking as $i => $r) {
            $cls = $i === 0 ? ' class="rank1"' : '';
            $html .= '<tr' . $cls . '>';
            $html .= '<td style="text-align:center;">' . ($i+1) . '</td>';
            $html .= '<td><strong>' . htmlspecialchars($r['nama']??'-') . '</strong></td>';
            $html .= '<td>' . htmlspecialchars($r['jabatan']??'-') . '</td>';
            $html .= '<td>' . htmlspecialchars($r['divisi']??'-') . '</td>';
            $html .= '<td style="text-align:center;">' . htmlspecialchars($r['masa_kerja']??'-') . ' th</td>';
            foreach ($criteriaSummary as $idx => $cs) {
                $html .= '<td style="text-align:center;">' . (isset($r['scores'][$idx]) ? number_format($r['scores'][$idx],0) : '—') . '</td>';
            }
            $html .= '<td style="text-align:center;font-weight:bold;color:#cc0000;">' . number_format($r['total']??0,4) . '</td>';
            $html .= '</tr>';
        }
        $html .= '</tbody></table>';

        $html .= '<p class="footer">Sistem Pendukung Keputusan AHP &mdash; PT Telkom Satelit Indonesia Regional 6 &mdash; ' . $tanggal . '</p>';
        $html .= '</body></html>';

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream('laporan-ahp-telkomsat-' . date('Ymd') . '.pdf', ['Attachment' => true]);
    }

    private function buildReportData(): array {
        $criteria = $this->fs->all('criteria');
        usort($criteria, fn($a,$b) => strcmp($a['id_kriteria'],$b['id_kriteria']));
        $criteria = array_values($criteria);
        $n = count($criteria);

        $resultDoc = $this->fs->get('results','current') ?? [];
        // Weights are stored flat: w_0, w_1, ... (by AhpController::calculate)
        $weights   = [];
        for ($i = 0; $i < $n; $i++) {
            $weights[$i] = (float)($resultDoc['w_' . $i] ?? 0);
        }
        $ahpMeta   = !empty($resultDoc['cr']) ? $resultDoc : null;

        $scoresDocs = $this->fs->all('scores');
        $scores = [];
        foreach ($scoresDocs as $doc) {
            $pid = $doc['id'];
            $vals = [];
            for ($i = 0; $i < $n; $i++) {
                $vals[$i] = isset($doc[$i]) ? (float)$doc[$i] : 0;
            }
            $scores[$pid] = $vals;
        }

        $rawRanking = $this->ahp->finalScores($weights, $scores);
        $employees = $this->fs->all('employees');
        $mapEmp = [];
        foreach ($employees as $e) $mapEmp[$e['id_pegawai']] = $e;

        $ranking = [];
        foreach ($rawRanking as $r) {
            $pid = $r['pegawai_id'];
            $emp = $mapEmp[$pid] ?? [];
            $ranking[] = [
                'nama'       => $emp['nama_pegawai'] ?? $pid,
                'jabatan'    => $emp['jabatan'] ?? '-',
                'divisi'     => $emp['divisi'] ?? '-',
                'masa_kerja' => $emp['masa_kerja'] ?? '-',
                'total'      => $r['total'],
                'scores'     => $scores[$pid] ?? [],
            ];
        }

        $criteriaSummary = [];
        foreach ($criteria as $idx => $c) {
            $criteriaSummary[$idx] = [
                'nama_kriteria' => $c['nama_kriteria'],
                'jenis'         => $c['jenis_kriteria'] ?? 'benefit',
                'bobot'         => $weights[$idx] ?? 0,
            ];
        }

        return [$ranking, $criteriaSummary, $ahpMeta, $criteria];
    }
}
