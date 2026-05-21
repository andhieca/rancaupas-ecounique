<?php

namespace App\Http\Controllers;

use App\Models\Tourism;
use App\Models\Rating;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Services\SawService;

class VisitorController extends Controller
{
    /**
     * Halaman utama pengunjung: Katalog & Rekomendasi
     */
    public function index(Request $request)
    {
        $tourisms = Tourism::withCount('ratings')
            ->withAvg('ratings', 'rating')
            ->get();

        // Handle SAW calculation
        $results = [];
        $isCalculated = false;

        if ($request->isMethod('post') || $request->has('calculate')) {
            $isCalculated = true;
            
            ActivityLog::create([
                'user_id' => auth()->id(),
                'activity' => 'Mencari rekomendasi wisata'
            ]);

            $query = Tourism::query()
                ->withCount('ratings')
                ->withAvg('ratings', 'rating');

            // 2. Terapkan logika OR untuk filter ("walaupun terpilih salah satu")
            $hasFilters = $request->filled('jenis_wisata') || 
                          ($request->filled('budget') && $request->budget !== 'semua') || 
                          ($request->filled('jarak') && $request->jarak !== 'semua') || 
                          $request->filled('fasilitas');

            if ($hasFilters) {
                $query->where(function($q) use ($request) {
                    if ($request->filled('jenis_wisata')) {
                        $q->orWhere('category', 'like', '%' . $request->jenis_wisata . '%');
                    }
                    if ($request->filled('budget') && $request->budget !== 'semua') {
                        if ($request->budget === 'under_50') $q->orWhere('price_wni', '<', 50000);
                        elseif ($request->budget === '50_100') $q->orWhereBetween('price_wni', [50000, 100000]);
                        elseif ($request->budget === 'over_100') $q->orWhere('price_wni', '>', 100000);
                    }
                    if ($request->filled('jarak') && $request->jarak !== 'semua') {
                        if ($request->jarak === 'dekat') $q->orWhere('distance_km', '<', 5);
                        elseif ($request->jarak === 'menengah') $q->orWhereBetween('distance_km', [5, 15]);
                        elseif ($request->jarak === 'jauh') $q->orWhere('distance_km', '>', 15);
                    }
                    if ($request->filled('fasilitas')) {
                        foreach ($request->fasilitas as $fas) {
                            $q->orWhere('facilities_list', 'like', '%' . $fas . '%');
                        }
                    }
                });
            }

            $rawResults = $query->get();

            // 3. Filter rating_min
            if ($request->filled('rating_min') && $request->rating_min > 0) {
                $minRating = (float) $request->rating_min;
                $rawResults = $rawResults->filter(function($t) use ($minRating) {
                    return ($t->ratings_avg_rating ?? 0) >= $minRating;
                });
            }

            // 4. Hitung dan Urutkan berdasarkan SAW
            $sawService = new SawService();
            $preferences = [
                'pref_anggaran' => ($request->filled('budget') && $request->budget !== 'semua') ? 5 : 0,
                'pref_fasilitas' => $request->filled('fasilitas') ? 5 : 0,
                'pref_jarak' => ($request->filled('jarak') && $request->jarak !== 'semua') ? 5 : 0,
                'pref_keseruan' => $request->filled('jenis_wisata') ? 5 : 3,
            ];
            
            $sawData = $sawService->calculateRanking($preferences);
            $sawScores = collect($sawData)->pluck('saw_score', 'id');

            $results = $rawResults->map(function($t) use ($sawScores) {
                $t->saw_score = $sawScores[$t->id] ?? 0;
                return $t;
            })->sortByDesc('saw_score')->values();
        } else {
            // Logs when accessing the dashboard via GET without calculation
            ActivityLog::create([
                'user_id' => auth()->id(),
                'activity' => 'Lihat pengunjung'
            ]);
        }

        $jenisWisata = \App\Models\Setting::group('jenis_wisata')->active()->orderBy('sort_order')->get();

        return view('visitor', compact('tourisms', 'results', 'isCalculated', 'jenisWisata'));
    }

    /**
     * Simpan rating pengunjung
     */
    public function rate(Request $request)
    {
        $validated = $request->validate([
            'tourism_id' => 'required|exists:tourisms,id',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        Rating::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'tourism_id' => $validated['tourism_id'],
            ],
            [
                'rating' => $validated['rating'],
            ]
        );

        return response()->json(['success' => true, 'message' => 'Rating berhasil disimpan!']);
    }

    /**
     * Ambil detail wisata (API JSON)
     */
    public function detail(Tourism $tourism)
    {
        $tourism->loadCount('ratings');
        $tourism->loadAvg('ratings', 'rating');
        
        // Get current user's rating if exists
        $userRating = null;
        if (auth()->check()) {
            $userRating = Rating::where('user_id', auth()->id())
                ->where('tourism_id', $tourism->id)
                ->value('rating');
        }

        return response()->json([
            'tourism' => $tourism,
            'user_rating' => $userRating,
        ]);
    }
}
