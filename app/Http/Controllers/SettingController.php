<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'group' => 'required|string|in:jenis_wisata,fasilitas,homepage_images',
            'label' => 'required|string|max:255',
            'value' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'sort_order' => 'nullable|integer',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/settings'), $filename);
            $validated['image'] = '/uploads/settings/' . $filename;
        }

        $validated['is_active'] = true;
        $validated['sort_order'] = $validated['sort_order'] ?? Setting::where('group', $validated['group'])->max('sort_order') + 1;

        Setting::create($validated);

        return redirect()->route('admin.dashboard')->with('success', 'Data berhasil ditambahkan.')->with('activeTab', 'pengaturan');
    }

    public function update(Request $request, Setting $setting)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'value' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_active' => 'nullable|boolean',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/settings'), $filename);
            $validated['image'] = '/uploads/settings/' . $filename;
        } else {
            unset($validated['image']);
        }

        $validated['is_active'] = $request->has('is_active');

        $setting->update($validated);

        return redirect()->route('admin.dashboard')->with('success', 'Data berhasil diperbarui.')->with('activeTab', 'pengaturan');
    }

    public function destroy(Setting $setting)
    {
        $setting->delete();

        return redirect()->route('admin.dashboard')->with('success', 'Data berhasil dihapus.')->with('activeTab', 'pengaturan');
    }
}
