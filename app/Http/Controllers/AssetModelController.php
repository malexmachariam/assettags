<?php

namespace App\Http\Controllers;

use App\Models\AssetModel;
use App\Models\Category;
use Illuminate\Http\Request;

class AssetModelController extends Controller
{
    public function index()
    {
        $assetModels = AssetModel::with('category')->get();
        $categories = Category::all();
        return view('asset_models.index', compact('assetModels', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'category_id' => 'required|exists:categories,id',
            'model_no' => 'nullable|string',
            'require_serial_number' => 'boolean',
        ]);
        $validated['require_serial_number'] = $request->has('require_serial_number');
        AssetModel::create($validated);
        return redirect()->route('asset-models.index')->with('success', 'Asset model created successfully.');
    }

    public function update(Request $request, AssetModel $assetModel)
    {
        $validated = $request->validate([
            'name' => 'required',
            'category_id' => 'required|exists:categories,id',
            'model_no' => 'nullable|string',
            'require_serial_number' => 'boolean',
        ]);
        $validated['require_serial_number'] = $request->has('require_serial_number');
        $assetModel->update($validated);
        return redirect()->route('asset-models.index')->with('success', 'Asset model updated successfully.');
    }

    public function destroy(AssetModel $assetModel)
    {
        $assetModel->delete();
        return redirect()->route('asset-models.index')->with('success', 'Asset model deleted successfully.');
    }
}
