<?php
namespace App;

class Ahp {
    /**
     * @param array $pairwise matrix NxN
     * @return array [weights, norm]
     */
    public function weights(array $pairwise): array {
        $n = count($pairwise);
        if ($n === 0) return [[], []];
        $colSums = array_fill(0, $n, 0);
        for ($j = 0; $j < $n; $j++) {
            for ($i = 0; $i < $n; $i++) {
                $colSums[$j] += $pairwise[$i][$j] ?? 0;
            }
        }
        $norm = $weights = [];
        for ($i = 0; $i < $n; $i++) {
            $rowSum = 0;
            for ($j = 0; $j < $n; $j++) {
                $val = $colSums[$j] == 0 ? 0 : $pairwise[$i][$j] / $colSums[$j];
                $norm[$i][$j] = $val;
                $rowSum += $val;
            }
            $weights[$i] = $rowSum / $n;
        }
        return [$weights, $norm];
    }

    public function consistency(array $pairwise, array $weights): array {
        $n = count($pairwise);
        if ($n === 0) return [0, 0, 0];
        $lamSum = 0;
        for ($i = 0; $i < $n; $i++) {
            $rowSum = 0;
            for ($j = 0; $j < $n; $j++) $rowSum += ($pairwise[$i][$j] ?? 0) * ($weights[$j] ?? 0);
            $lamSum += $weights[$i] == 0 ? 0 : $rowSum / $weights[$i];
        }
        $lambdaMax = $lamSum / $n;
        $ci = $n <= 1 ? 0 : ($lambdaMax - $n) / ($n - 1);
        $riTable = [1=>0,2=>0,3=>0.58,4=>0.90,5=>1.12,6=>1.24,7=>1.32,8=>1.41,9=>1.45];
        $ri = $riTable[$n] ?? 1.45;
        $cr = $ri == 0 ? 0 : $ci / $ri;
        return [$lambdaMax, $ci, $cr];
    }

    /**
     * @param array $weights indexed by criteria order
     * @param array $scores [pegawaiId => [criteriaIndex => value]]
     * @return array ranking sorted desc by total
     */
    public function finalScores(array $weights, array $scores): array {
        $results = [];
        foreach ($scores as $pegId => $vals) {
            $total = 0;
            foreach ($weights as $i => $w) {
                $total += $w * ($vals[$i] ?? 0);
            }
            $results[] = ['pegawai_id' => $pegId, 'total' => $total];
        }
        usort($results, fn($a, $b) => $b['total'] <=> $a['total']);
        return $results;
    }
}
