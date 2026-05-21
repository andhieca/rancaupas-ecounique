<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CriterionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:criteria,code',
            'name' => 'required|string|max:255',
            'type' => 'required|in:benefit,cost',
            'weight' => 'required|numeric|min:0|max:1',
        ]);

        \App\Models\Criterion::create($validated);

        return back()->with('success', 'Kriteria berhasil ditambahkan')->with('activeTab', 'kriteria');
    }

    public function update(Request $request, string $id)
    {
        $criterion = \App\Models\Criterion::findOrFail($id);
        
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:criteria,code,' . $criterion->id,
            'name' => 'required|string|max:255',
            'type' => 'required|in:benefit,cost',
            'weight' => 'required|numeric|min:0|max:1',
        ]);

        $criterion->update($validated);

        return back()->with('success', 'Kriteria berhasil diperbarui')->with('activeTab', 'kriteria');
    }

    public function destroy(string $id)
    {
        $criterion = \App\Models\Criterion::findOrFail($id);
        $criterion->delete();

        return back()->with('success', 'Kriteria berhasil dihapus')->with('activeTab', 'kriteria');
    }
}
