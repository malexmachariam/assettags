<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetModel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use BaconQrCode\Renderer\GDLibRenderer;
use BaconQrCode\Writer;


class AssetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $assets = Asset::with('assetModel.category')->get();
        return view('assets.index', compact('assets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $assetModels = AssetModel::with('category')->get();
        return view('assets.create', compact('assetModels'));
    }

       /**
     * Return the asset tag HTML for modal (AJAX).
     */
    public function tag(Asset $asset)
    {
        // Return only the tag partial for modal
        return view('assets.partials.tag', compact('asset'))->render();
    }
    public function store(Request $request)
    {
        // First, get the model to know if serial is required
        $assetModel = AssetModel::findOrFail($request->asset_model_id); // use findOrFail for safety

        $requireSerial = $assetModel->require_serial_number;

        $rules = [
            'name'            => 'required|string|max:255',
            'description'     => 'nullable|string',
            'asset_model_id'  => 'required|exists:asset_models,id',
            'serial_number'   => $requireSerial 
                ? ['required', 'string', 'max:255', 'unique:assets,serial_number'] 
                : ['nullable', 'string', 'max:255', 'unique:assets,serial_number'],
        ];

        $validated = $request->validate($rules);

        // Now process serial
            // If serial number is required or provided, use it (uppercased). Otherwise, generate a UUID as the asset tag.
            $serial = $requireSerial ? $validated['serial_number'] : ($validated['serial_number'] ?: null);
            $serial = $serial ? strtoupper(trim($serial)) : null;
            $assetTag = $serial ?: (string) Str::uuid();

        $asset = Asset::create([
                'uuid'            => $assetTag, // Asset tag is always set in backend
            'name'            => $validated['name'],
            'description'     => $validated['description'],
            'asset_model_id'  => $validated['asset_model_id'],
            'serial_number'   => $serial,
        ]);

        Log::info('Asset created successfully', [
            'asset_id' => $asset->id,
            'uuid'     => $asset->uuid,
            'serial'   => $serial,
        ]);

        return redirect()->route('home') 
                        ->with('success', 'Asset added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Asset $asset)
    {
        // Generate QR code with logo overlay
        $qrText = $asset->uuid;
        // $qr = \QrCode::format('png')->size(300)->margin(2)->generate($qrText);
        $renderer = new ImageRenderer(
            new RendererStyle(300, 2),           // size + margin
            new GdImageBackEnd()                 // ← Force GD instead of Imagick
        );
        $qrBinary = $writer->writeString($asset->uuid ?? $asset->id);
        $qrBase64 = base64_encode($qrBinary);

        // Use Intervention Image to merge logo
        // Force GD driver for Intervention Image
        \Intervention\Image\ImageManagerStatic::configure(['driver' => 'gd']);
        $qrImage = \Intervention\Image\ImageManagerStatic::make($qr);
        $logoPath = public_path('img/favicon.png');
        if (file_exists($logoPath)) {
            $logo = \Intervention\Image\ImageManagerStatic::make($logoPath)->resize(60, 60);
            $qrImage->insert($logo, 'center');
        }
        $qrWithLogo = $qrImage->encode('data-url');

        return view('assets.show', [
            'asset' => $asset,
            'qrWithLogo' => $qrWithLogo,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Asset $asset)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Asset $asset)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Asset $asset)
    {
        //
    }
}
