<?php

namespace App\Http\Controllers;

use App\Models\Plat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PlatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $plats = Plat::all();
        return response()->json($plats);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = $request->only(['name', 'description', 'price', 'category_id']);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('plats', 'public');
        }

        $plat = Plat::create($data);
        return response()->json($plat, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $plat = Plat::find($id);
        return response()->json($plat);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $plat = Plat::find($id);

        if (!$plat) {
            return response()->json(['message' => 'Plat not found'], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = $request->only(['name', 'description', 'price', 'category_id']);

        if ($request->hasFile('image')) {
            if ($plat->image_path && Storage::disk('public')->exists($plat->image_path)) {
                Storage::disk('public')->delete($plat->image_path);
            }

            $data['image_path'] = $request->file('image')->store('plats', 'public');
        }

        $plat->update($data);
        return response()->json($plat);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $plat = Plat::find($id);

        if (!$plat) {
            return response()->json(['message' => 'Plat not found'], 404);
        }

        if ($plat->image_path && Storage::disk('public')->exists($plat->image_path)) {
            Storage::disk('public')->delete($plat->image_path);
        }

        $plat->delete();
        return response()->json(['message' => 'Plat deleted successfully'], 200);
    }
}
