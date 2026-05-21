<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tourism;
use App\Models\Criterion;
use App\Models\ActivityLog;
use App\Models\Setting;

class AdminController extends Controller
{
    public function index()
    {
        $tourisms = Tourism::withCount('ratings')->withAvg('ratings', 'rating')->get();
        $criteria = Criterion::all();
        $activity_logs = ActivityLog::with('user')->orderBy('created_at', 'desc')->take(10)->get();
        
        // Origin Stats
        $totalUsers = \App\Models\User::where('role', 'pengunjung')->count();
        $domestikUsers = \App\Models\User::where('role', 'pengunjung')->where('origin', 'domestik')->count();
        
        $domestikPct = $totalUsers > 0 ? round(($domestikUsers / $totalUsers) * 100, 1) : 0;
        $mancanegaraPct = $totalUsers > 0 ? round(100 - $domestikPct, 1) : 0;

        $originStats = [
            'domestik' => $domestikPct,
            'mancanegara' => $mancanegaraPct,
        ];

        // Weekly Visitor Stats
        $recentLogs = \App\Models\ActivityLog::where('created_at', '>=', now()->subDays(6)->startOfDay())->get();
        $dailyVisitors = [];
        $days = [1, 2, 3, 4, 5, 6, 0]; // Sen, Sel, Rab, Kam, Jum, Sab, Min
        foreach ($days as $day) {
            $dailyVisitors[] = $recentLogs->filter(fn($log) => $log->created_at->dayOfWeek === $day)->unique('user_id')->count();
        }

        // Settings
        $jenisWisata = Setting::group('jenis_wisata')->orderBy('sort_order')->get();
        $fasilitas = Setting::group('fasilitas')->orderBy('sort_order')->get();
        $homepageImages = Setting::group('homepage_images')->orderBy('sort_order')->get();

        // ========== SAW Calculation ==========
        $sawData = $this->calculateSAW($tourisms, $criteria);

        return view('admin', compact(
            'tourisms', 'criteria', 'originStats', 'dailyVisitors', 'activity_logs',
            'jenisWisata', 'fasilitas', 'homepageImages', 'sawData'
        ));
    }

    /**
     * Calculate SAW (Simple Additive Weighting)
     */
    private function calculateSAW($tourisms, $criteria)
    {
        if ($tourisms->isEmpty() || $criteria->isEmpty()) {
            return [
                'alternatives' => [],
                'criteria' => $criteria,
                'decisionMatrix' => [],
                'normalizedMatrix' => [],
                'weightedMatrix' => [],
                'finalScores' => [],
                'ranking' => [],
                'totalWeight' => $criteria->sum('weight'),
            ];
        }

        // 1. Build Decision Matrix (X)
        // C1: Anggaran (price_wni) - Cost
        // C2: Fasilitas (count of facilities) - Benefit
        // C3: Jarak (distance_km) - Cost
        // C4: Keseruan (ratings_avg_rating) - Benefit

        $decisionMatrix = [];
        foreach ($tourisms as $t) {
            $row = [];
            foreach ($criteria as $c) {
                if ($c->code === 'C1') {
                    $row['C1'] = (float)($t->price_wni ?? 0);
                } elseif ($c->code === 'C2') {
                    $facilities = array_filter(array_map('trim', explode(',', $t->facilities_list ?? '')));
                    $row['C2'] = count($facilities);
                } elseif ($c->code === 'C3') {
                    $row['C3'] = (float)($t->distance_km ?? 0);
                } elseif ($c->code === 'C4') {
                    $row['C4'] = round((float)($t->ratings_avg_rating ?? 0), 2);
                } else {
                    $row[$c->code] = 0;
                }
            }
            // Add average rating as a virtual criterion if needed
            $row['rating'] = round($t->ratings_avg_rating ?? 0, 2);
            $decisionMatrix[$t->id] = $row;
        }

        // 2. Calculate min/max for each criterion (for normalization)
        $minMax = [];
        foreach ($criteria as $c) {
            $values = collect($decisionMatrix)->pluck($c->code)->filter(fn($v) => $v > 0);
            $minMax[$c->code] = [
                'min' => $values->isNotEmpty() ? $values->min() : 0,
                'max' => $values->isNotEmpty() ? $values->max() : 1,
            ];
        }

        // 3. Normalize: Benefit => X/max, Cost => min/X  
        $normalizedMatrix = [];
        foreach ($decisionMatrix as $tid => $row) {
            $nRow = [];
            foreach ($criteria as $c) {
                $val = $row[$c->code];
                if ($c->type === 'benefit') {
                    if ($val == 0) {
                        $nRow[$c->code] = 0;
                    } else {
                        $nRow[$c->code] = $minMax[$c->code]['max'] > 0 ? round($val / $minMax[$c->code]['max'], 4) : 0;
                    }
                } else { // cost
                    if ($val == 0) {
                        $nRow[$c->code] = 1; // 0 cost is the best possible, so normalized value is 1
                    } else {
                        $nRow[$c->code] = round($minMax[$c->code]['min'] / $val, 4);
                    }
                }
            }
            $normalizedMatrix[$tid] = $nRow;
        }

        // 4. Weighted Normalized Matrix (V = W * R)
        $weightedMatrix = [];
        foreach ($normalizedMatrix as $tid => $nRow) {
            $wRow = [];
            foreach ($criteria as $c) {
                $wRow[$c->code] = round($nRow[$c->code] * $c->weight, 4);
            }
            $weightedMatrix[$tid] = $wRow;
        }

        // 5. Final Score (Yi = sum of weighted values)
        $finalScores = [];
        foreach ($weightedMatrix as $tid => $wRow) {
            $finalScores[$tid] = round(array_sum($wRow), 4);
        }

        // 6. Ranking (sort by final score desc)
        arsort($finalScores);
        $rank = 1;
        $ranking = [];
        foreach ($finalScores as $tid => $score) {
            $ranking[] = [
                'rank' => $rank++,
                'tourism_id' => $tid,
                'tourism' => $tourisms->firstWhere('id', $tid),
                'score' => $score,
            ];
        }

        return [
            'alternatives' => $tourisms,
            'criteria' => $criteria,
            'decisionMatrix' => $decisionMatrix,
            'normalizedMatrix' => $normalizedMatrix,
            'weightedMatrix' => $weightedMatrix,
            'finalScores' => $finalScores,
            'ranking' => $ranking,
            'totalWeight' => $criteria->sum('weight'),
            'minMax' => $minMax,
        ];
    }
}
