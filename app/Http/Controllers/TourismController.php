<?php

namespace App\Http\Controllers;

use App\Models\Tourism;
use Illuminate\Http\Request;

class TourismController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price_wni' => 'nullable|numeric',
            'price_wna' => 'nullable|numeric',
            'distance_km' => 'nullable|numeric|min:0',
            'facilities_list' => 'nullable|string',
            'category' => 'nullable|string|max:255',
            'map_url' => 'nullable|string|max:1000',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
            'status' => 'nullable|in:aktif,nonaktif',
        ]);

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $filename = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads'), $filename);
                $imagePaths[] = '/uploads/' . $filename;
            }
        }
        
        unset($validated['images']);

        if (!empty($imagePaths)) {
            $validated['image'] = $imagePaths[0];
            $validated['gallery'] = $imagePaths;
        }

        Tourism::create($validated);

        return redirect()->back()->with('success', 'Data Pariwisata berhasil ditambahkan.')->with('activeTab', 'wisata');
    }

    public function update(Request $request, Tourism $tourism)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price_wni' => 'nullable|numeric',
            'price_wna' => 'nullable|numeric',
            'distance_km' => 'nullable|numeric|min:0',
            'facilities_list' => 'nullable|string',
            'category' => 'nullable|string|max:255',
            'map_url' => 'nullable|string|max:1000',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
            'status' => 'nullable|in:aktif,nonaktif',
        ]);

        $existingGallery = json_decode($request->input('existing_gallery', '[]'), true) ?: [];
        $imagePaths = $existingGallery;

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $filename = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads'), $filename);
                $imagePaths[] = '/uploads/' . $filename;
            }
        }

        unset($validated['images']);

        if (!empty($imagePaths)) {
            $validated['image'] = $imagePaths[0];
            $validated['gallery'] = $imagePaths;
        } else {
            $validated['image'] = null;
            $validated['gallery'] = null;
        }

        $tourism->update($validated);

        return redirect()->back()->with('success', 'Data Pariwisata berhasil diperbarui.')->with('activeTab', 'wisata');
    }

    public function destroy(Tourism $tourism)
    {
        $tourism->delete();

        return redirect()->back()->with('success', 'Data Pariwisata berhasil dihapus.')->with('activeTab', 'wisata');
    }
}
