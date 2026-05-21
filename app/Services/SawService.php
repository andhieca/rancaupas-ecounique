<?php

namespace App\Services;

use App\Models\Criterion;
use App\Models\Tourism;

class SawService
{
    /**
     * Hitung Perankingan SAW
     * 
     * @param array $userPreferences Preferensi pengguna [ 'pref_anggaran' => 3, dll ]
     * @return array Data rekomendasi wisata terurut
     */
    public function calculateRanking(array $userPreferences = [])
    {
        $criteria = Criterion::all();
        // Ambil data pariwisata lengkap dengan rata-rata rating
        $tourisms = Tourism::withCount('ratings')->withAvg('ratings', 'rating')->get();

        // Bobot dasar dari sistem/Pakar
        $defaultWeights = [
            'C1' => $criteria->where('code', 'C1')->first()->weight ?? 0.3, // Anggaran (Cost)
            'C2' => $criteria->where('code', 'C2')->first()->weight ?? 0.3, // Fasilitas (Benefit)
            'C3' => $criteria->where('code', 'C3')->first()->weight ?? 0.2, // Jarak (Cost)
            'C4' => $criteria->where('code', 'C4')->first()->weight ?? 0.2, // Keseruan (Benefit)
        ];

        // Jika user memasukkan preferensi
        $pref_anggaran = (float) ($userPreferences['pref_anggaran'] ?? 0);
        $pref_fasilitas = (float) ($userPreferences['pref_fasilitas'] ?? 0);
        $pref_jarak = (float) ($userPreferences['pref_jarak'] ?? 0);
        $pref_keseruan = (float) ($userPreferences['pref_keseruan'] ?? 0);
        
        $total_pref = $pref_anggaran + $pref_fasilitas + $pref_jarak + $pref_keseruan;

        // Tentukan Bobot Akhir
        if ($total_pref > 0) {
            $weights = [
                'C1' => $pref_anggaran / $total_pref,
                'C2' => $pref_fasilitas / $total_pref,
                'C3' => $pref_jarak / $total_pref,
                'C4' => $pref_keseruan / $total_pref,
            ];
        } else {
            $weights = $defaultWeights;
        }

        // 1. Build Decision Matrix
        $decisionMatrix = [];
        foreach ($tourisms as $t) {
            $facilities = array_filter(array_map('trim', explode(',', $t->facilities_list ?? '')));
            $decisionMatrix[$t->id] = [
                'C1' => (float)($t->price_wni ?? 0),
                'C2' => count($facilities),
                'C3' => (float)($t->distance_km ?? 0),
                'C4' => round((float)($t->ratings_avg_rating ?? 0), 2),
            ];
        }

        // 2. Find min/max for normalization
        $minMax = [];
        foreach (['C1', 'C2', 'C3', 'C4'] as $code) {
            $values = collect($decisionMatrix)->pluck($code)->filter(fn($v) => $v > 0);
            $minMax[$code] = [
                'min' => $values->isNotEmpty() ? $values->min() : 0,
                'max' => $values->isNotEmpty() ? $values->max() : 1,
            ];
        }

        $results = [];

        foreach ($tourisms as $t) {
            $row = $decisionMatrix[$t->id];
            
            // Normalize C1 (Cost)
            $n_C1 = $row['C1'] == 0 ? 1 : round($minMax['C1']['min'] / $row['C1'], 4);
            
            // Normalize C2 (Benefit)
            $n_C2 = $minMax['C2']['max'] > 0 ? round($row['C2'] / $minMax['C2']['max'], 4) : 0;
            
            // Normalize C3 (Cost)
            $n_C3 = $row['C3'] == 0 ? 1 : round($minMax['C3']['min'] / $row['C3'], 4);
            
            // Normalize C4 (Benefit)
            $n_C4 = $minMax['C4']['max'] > 0 ? round($row['C4'] / $minMax['C4']['max'], 4) : 0;

            // Final score V_i
            $score = ($weights['C1'] * $n_C1) +
                     ($weights['C2'] * $n_C2) +
                     ($weights['C3'] * $n_C3) +
                     ($weights['C4'] * $n_C4);

            $t->saw_score = round($score * 100, 2); // Persentase
            $results[] = $t;
        }

        // Urutkan nilai V terbesar ke terkecil
        usort($results, function($a, $b) {
            return $b->saw_score <=> $a->saw_score;
        });

        return $results;
    }
}
