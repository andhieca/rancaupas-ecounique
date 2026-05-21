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

    public function exportSaw(Request $request)
    {
        $date = $request->input('date', date('Y-m-d'));
        
        $tourisms = Tourism::withCount('ratings')
            ->withAvg('ratings', 'rating')
            ->get();
            
        $criteria = Criterion::orderBy('code')->get();
        $sawData = $this->calculateSAW($tourisms, $criteria);
        
        $html = $this->buildSawPdfHtml($sawData, $date);
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
        $pdf->setPaper('a4', 'landscape');
        
        $fileName = 'Laporan_SAW_' . $date . '.pdf';
        return $pdf->download($fileName);
    }

    private function buildSawPdfHtml(array $sawData, string $date): string
    {
        $criteria = $sawData['criteria'];
        $ranking = $sawData['ranking'];
        $dm = $sawData['decisionMatrix'];
        $nm = $sawData['normalizedMatrix'];
        $wm = $sawData['weightedMatrix'];

        $html = '<!DOCTYPE html><html><head><meta charset="UTF-8">
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body { font-family: "Helvetica Neue", Arial, sans-serif; font-size: 11px; color: #1a1a1a; padding: 30px; }
            .header { text-align: center; margin-bottom: 24px; border-bottom: 3px solid #2d6a4f; padding-bottom: 16px; }
            .header h1 { font-size: 20px; color: #2d6a4f; margin-bottom: 4px; letter-spacing: 1px; }
            .header h2 { font-size: 14px; color: #555; font-weight: normal; }
            .header p { font-size: 11px; color: #888; margin-top: 6px; }
            .info-box { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 6px; padding: 12px 16px; margin-bottom: 20px; }
            .info-box table { width: 100%; }
            .info-box td { padding: 2px 8px; font-size: 11px; }
            .info-box .label { color: #555; width: 160px; }
            .info-box .value { font-weight: bold; color: #1a1a1a; }
            .section-title { font-size: 13px; font-weight: bold; color: #2d6a4f; margin: 20px 0 8px 0; border-left: 4px solid #2d6a4f; padding-left: 8px; }
            table.data { width: 100%; border-collapse: collapse; margin-bottom: 16px; font-size: 10px; }
            table.data th { background: #2d6a4f; color: white; padding: 6px 5px; text-align: center; font-size: 9px; }
            table.data td { padding: 5px 5px; border: 1px solid #e0e0e0; text-align: center; }
            table.data tr:nth-child(even) { background: #f9fafb; }
            table.data tr:hover { background: #ecfdf5; }
            .rank-1 { background: #dcfce7 !important; font-weight: bold; }
            .rank-badge { display: inline-block; width: 22px; height: 22px; line-height: 22px; border-radius: 50%; text-align: center; font-weight: bold; font-size: 10px; }
            .rank-1-badge { background: #2d6a4f; color: white; }
            .rank-2-badge { background: #b45309; color: white; }
            .rank-3-badge { background: #d97706; color: white; }
            .rank-other-badge { background: #d1d5db; color: #374151; }
            .footer { margin-top: 30px; text-align: center; font-size: 9px; color: #aaa; border-top: 1px solid #e5e7eb; padding-top: 10px; }
            .text-left { text-align: left !important; }
            .text-right { text-align: right !important; }
            .font-mono { font-family: "Courier New", monospace; }
            .cost-label { background: #fef2f2; color: #dc2626; padding: 1px 6px; border-radius: 3px; font-size: 8px; font-weight: bold; }
            .benefit-label { background: #f0fdf4; color: #16a34a; padding: 1px 6px; border-radius: 3px; font-size: 8px; font-weight: bold; }
        </style></head><body>';

        // Header
        $html .= '<div class="header">';
        $html .= '<h1>LAPORAN REKOMENDASI DESTINASI WISATA</h1>';
        $html .= '<h2>Perhitungan Metode Simple Additive Weighting (SAW)</h2>';
        $html .= '<p>Kawasan Wisata Ranca Upas &mdash; Tanggal: ' . date('d F Y', strtotime($date)) . '</p>';
        $html .= '</div>';

        // Info Box
        $totalWeight = $criteria->sum('weight');
        $benefitCriteria = $criteria->where('type', 'benefit')->pluck('name')->map(fn($n) => explode(' (', $n)[0])->implode(', ');
        $costCriteria = $criteria->where('type', 'cost')->pluck('name')->map(fn($n) => explode(' (', $n)[0])->implode(', ');

        $html .= '<div class="info-box"><table>';
        $html .= '<tr><td class="label">Metode</td><td class="value">: Simple Additive Weighting (SAW)</td>';
        $html .= '<td class="label">Tanggal Perhitungan</td><td class="value">: ' . $date . '</td></tr>';
        $html .= '<tr><td class="label">Jumlah Alternatif</td><td class="value">: ' . count($ranking) . ' wisata</td>';
        $html .= '<td class="label">Jumlah Kriteria</td><td class="value">: ' . $criteria->count() . ' kriteria</td></tr>';
        $html .= '<tr><td class="label">Total Bobot</td><td class="value">: ' . number_format($totalWeight, 2) . '</td>';
        $html .= '<td class="label">Kriteria Benefit</td><td class="value">: ' . ($benefitCriteria ?: '-') . '</td></tr>';
        $html .= '<tr><td class="label">Kriteria Cost</td><td class="value" colspan="3">: ' . ($costCriteria ?: '-') . '</td></tr>';
        $html .= '</table></div>';

        // === 1. Matriks Keputusan ===
        $html .= '<div class="section-title">1. Matriks Keputusan (X)</div>';
        $html .= '<table class="data"><thead><tr><th>No</th><th style="text-align:left;min-width:120px;">Alternatif</th>';
        foreach ($criteria as $c) {
            $cName = explode(' (', $c->name)[0];
            $typeLabel = $c->type === 'cost' ? '<span class="cost-label">Cost</span>' : '<span class="benefit-label">Benefit</span>';
            $html .= '<th>' . $cName . '<br>' . $typeLabel . '</th>';
        }
        $html .= '</tr></thead><tbody>';
        $idx = 1;
        foreach ($ranking as $r) {
            $t = $r['tourism'];
            $html .= '<tr><td>' . $idx++ . '</td><td class="text-left">' . $t->name . '</td>';
            foreach ($criteria as $c) {
                $val = $dm[$t->id][$c->code] ?? 0;
                $html .= '<td class="font-mono">' . $val . '</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</tbody></table>';

        // === 2. Matriks Normalisasi ===
        $html .= '<div class="section-title">2. Matriks Normalisasi (R)</div>';
        $html .= '<table class="data"><thead><tr><th>No</th><th style="text-align:left;min-width:120px;">Alternatif</th>';
        foreach ($criteria as $c) {
            $cName = explode(' (', $c->name)[0];
            $html .= '<th>' . $cName . '</th>';
        }
        $html .= '</tr></thead><tbody>';
        $idx = 1;
        foreach ($ranking as $r) {
            $t = $r['tourism'];
            $html .= '<tr><td>' . $idx++ . '</td><td class="text-left">' . $t->name . '</td>';
            foreach ($criteria as $c) {
                $val = $nm[$t->id][$c->code] ?? 0;
                $html .= '<td class="font-mono">' . number_format($val, 4) . '</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</tbody></table>';

        // === 3. Matriks Terbobot ===
        $html .= '<div class="section-title">3. Matriks Terbobot (V = W × R)</div>';
        $html .= '<table class="data"><thead><tr><th>No</th><th style="text-align:left;min-width:120px;">Alternatif</th>';
        foreach ($criteria as $c) {
            $cName = explode(' (', $c->name)[0];
            $html .= '<th>' . $cName . ' (' . number_format($c->weight, 2) . ')</th>';
        }
        $html .= '<th style="background:#374151;">Yi</th>';
        $html .= '</tr></thead><tbody>';
        $idx = 1;
        foreach ($ranking as $r) {
            $t = $r['tourism'];
            $html .= '<tr><td>' . $idx++ . '</td><td class="text-left">' . $t->name . '</td>';
            foreach ($criteria as $c) {
                $val = $wm[$t->id][$c->code] ?? 0;
                $html .= '<td class="font-mono">' . number_format($val, 4) . '</td>';
            }
            $html .= '<td class="font-mono" style="font-weight:bold;background:#f3f4f6;">' . number_format($r['score'], 4) . '</td>';
            $html .= '</tr>';
        }
        $html .= '</tbody></table>';

        // === 4. Hasil Perangkingan ===
        $html .= '<div class="section-title">4. Hasil Perangkingan</div>';
        $html .= '<table class="data"><thead><tr>';
        $html .= '<th style="width:60px;">Ranking</th><th style="text-align:left;">Nama Wisata</th><th>Jenis Wisata</th>';
        foreach ($criteria as $c) {
            $cName = explode(' (', $c->name)[0];
            $html .= '<th>' . $cName . '</th>';
        }
        $html .= '<th style="background:#374151;">Nilai Akhir (Yi)</th>';
        $html .= '</tr></thead><tbody>';

        foreach ($ranking as $r) {
            $t = $r['tourism'];
            $rowClass = $r['rank'] === 1 ? ' class="rank-1"' : '';
            $badgeClass = match(true) {
                $r['rank'] === 1 => 'rank-1-badge',
                $r['rank'] === 2 => 'rank-2-badge',
                $r['rank'] === 3 => 'rank-3-badge',
                default => 'rank-other-badge',
            };
            $html .= '<tr' . $rowClass . '>';
            $html .= '<td><span class="rank-badge ' . $badgeClass . '">' . $r['rank'] . '</span></td>';
            $html .= '<td class="text-left" style="font-weight:bold;">' . $t->name . '</td>';
            $html .= '<td>' . ($t->category ?: 'Kunjungan') . '</td>';
            foreach ($criteria as $c) {
                $val = $dm[$t->id][$c->code] ?? 0;
                $html .= '<td class="font-mono">' . $val . '</td>';
            }
            $html .= '<td class="font-mono" style="font-weight:bold;font-size:12px;">' . number_format($r['score'], 4) . '</td>';
            $html .= '</tr>';
        }
        $html .= '</tbody></table>';

        // Keterangan
        $html .= '<div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:6px;padding:10px 14px;margin-top:12px;font-size:10px;color:#1e40af;">';
        $html .= '<strong>Keterangan:</strong> Nilai Yi yang lebih tinggi menunjukkan alternatif wisata yang lebih direkomendasikan. ';
        $html .= 'Perhitungan menggunakan metode SAW dengan normalisasi Benefit (X/Max) dan Cost (Min/X).</div>';

        // Footer
        $html .= '<div class="footer">';
        $html .= 'Dicetak otomatis oleh Sistem Pendukung Keputusan &mdash; Ranca Upas Eco Unique &mdash; ' . date('d/m/Y H:i');
        $html .= '</div>';

        $html .= '</body></html>';
        return $html;
    }
}
