<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tourism;

class HomeController extends Controller
{
    /**
     * Display the public landing page
     */
    public function index(Request $request)
    {
        // Get top 3 tourisms with their ratings for the featured section
        $tourisms = Tourism::withCount('ratings')
            ->withAvg('ratings', 'rating')
            ->orderByDesc('ratings_avg_rating')
            ->orderBy('id')
            ->take(3)
            ->get();

        // Rearrange to put the highest rated in the middle (index 1)
        if ($tourisms->count() >= 2) {
            $highest = $tourisms->shift();
            $tourisms->splice(1, 0, [$highest]);
        }
            
        $homepageImages = \App\Models\Setting::group('homepage_images')->active()->orderBy('sort_order')->get();
        
        return view('home', compact('tourisms', 'homepageImages'));
    }

    /**
     * Ambil detail wisata untuk public API
     */
    public function detail(Tourism $tourism)
    {
        $tourism->loadCount('ratings');
        $tourism->loadAvg('ratings', 'rating');

        return response()->json([
            'tourism' => $tourism,
        ]);
    }
}

