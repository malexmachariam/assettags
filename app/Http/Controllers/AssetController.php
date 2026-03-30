<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Batch;
use App\Models\AssetModel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $bulkAssetModels = $assetModels->where('require_serial_number', false)->values();
        $serialAssetModels = $assetModels->where('require_serial_number', true)->values();
        $activeBatch = Batch::active()->first();

        return view('assets.create', compact('assetModels', 'bulkAssetModels', 'serialAssetModels', 'activeBatch'));
    }

    public function upload()
    {
        $assetModels = AssetModel::with('category')->get();
        $bulkAssetModels = $assetModels->where('require_serial_number', false)->values();
        $serialAssetModels = $assetModels->where('require_serial_number', true)->values();
        $activeBatch = Batch::active()->first();

        return view('assets.upload', compact('assetModels', 'bulkAssetModels', 'serialAssetModels', 'activeBatch'));
    }


       /**
     * Return the asset tag HTML for modal (AJAX).
     */
    public function tag(Asset $asset)
    {
        // Return only the tag partial for modal
        return view('assets.partials.tag', compact('asset'))->render();
    }

    /**
     * Stream the asset tag as a PDF in the browser.
     */
    public function tagPdf(Asset $asset)
    {
        $tagNumber = $asset->asset_tag;
        $categoryText = strtoupper((string) optional(optional($asset->assetModel)->category)->name ?: 'UNCATEGORIZED');
        $assetUrl = route('assets.show', $asset);

        $renderer = new GDLibRenderer(220, 4);
        $writer = new Writer($renderer);
        $qrBinary = $writer->writeString($assetUrl);
        $qrBase64 = base64_encode($qrBinary);

        $logoBase64 = null;
        $logoPath = public_path('img/favicon.png');

        if (file_exists($logoPath)) {
            $logoBase64 = base64_encode(file_get_contents($logoPath));
        }

        return Pdf::loadView('assets.tag-pdf', [
            'asset' => $asset,
            'tagNumber' => $tagNumber,
            'categoryText' => $categoryText,
            'qrBase64' => $qrBase64,
            'logoBase64' => $logoBase64,
        ])
            ->setPaper([0, 0, 365, 185], 'portrait')
            ->stream('asset-tag-' . $asset->id . '.pdf');
    }

    public function store(Request $request)
    {
        $activeBatch = Batch::active()->first();

        if (! $activeBatch) {
            return back()
                ->withErrors(['batch' => 'Create and activate a batch before recording assets.'])
                ->withInput();
        }

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

        $serialInput = $validated['serial_number'] ?? null;
        $serial = $requireSerial ? $serialInput : ($serialInput ?: null);
        $serial = $serial ? strtoupper(trim($serial)) : null;
        $uuid = (string) Str::uuid();
        $assetTagValue = str_pad(Asset::nextSequentialAssetTag(), 6, '0', STR_PAD_LEFT);
        // $assetTagValue = $serial ?: str_pad(Asset::nextSequentialAssetTag(), 6, '0', STR_PAD_LEFT);
        $assetTag = str_starts_with($assetTagValue, 'ODPP/') ? $assetTagValue : 'ODPP/' . $assetTagValue;
        

        if (Asset::where('asset_tag', $assetTag)->exists()) {
            return back()
                ->withErrors(['serial_number' => 'The serial number is already in use as an asset tag.'])
                ->withInput();
        }

        $asset = Asset::create([
            'uuid'            => $uuid,
            'asset_tag'       => $assetTag,
            'name'            => $validated['name'],
            'description'     => $validated['description'],
            'asset_model_id'  => $validated['asset_model_id'],
            'batch_id'        => $activeBatch->id,
            'serial_number'   => $serial,
        ]);

        // Log::info('Asset created successfully', [
        //     'asset_id' => $asset->id,
        //     'uuid'     => $asset->uuid,
        //     'serial'   => $serial,
        // ]);

        return redirect()->route('home') 
                        ->with('success', 'Asset added successfully.');
    }

    public function bulkStore(Request $request)
    {
        $activeBatch = Batch::active()->first();

        if (! $activeBatch) {
            return back()
                ->withErrors(['batch' => 'Create and activate a batch before generating assets.'])
                ->withInput();
        }

        $validated = $request->validate([
            'bulk_asset_model_id' => ['required', 'exists:asset_models,id'],
            'bulk_quantity' => ['required', 'integer', 'min:1', 'max:200'],
            'bulk_description' => ['nullable', 'string'],
        ]);

        $assetModel = AssetModel::with('category')->findOrFail($validated['bulk_asset_model_id']);

        if ($assetModel->require_serial_number) {
            return back()
                ->withErrors(['bulk_asset_model_id' => 'Please select an asset model that does not require serial numbers.'])
                ->withInput();
        }

        $quantity = (int) $validated['bulk_quantity'];
        $description = $validated['bulk_description'] ?? null;

        DB::transaction(function () use ($assetModel, $quantity, $description, $activeBatch) {
            $numbers = Asset::nextSequentialAssetNumbers($quantity);

            foreach ($numbers as $number) {
                Asset::create([
                    'uuid' => (string) Str::uuid(),
                    'asset_tag' => 'ODPP/' . str_pad((string) $number, 6, '0', STR_PAD_LEFT),
                    'name' => $assetModel->name,
                    'description' => $description,
                    'asset_model_id' => $assetModel->id,
                    'batch_id' => $activeBatch->id,
                    'serial_number' => null,
                ]);
            }
        });

        return redirect()->route('home')
            ->with('success', $quantity . ' assets generated successfully.');
    }

    public function importSerialCsv(Request $request)
    {
        $activeBatch = Batch::active()->first();

        if (! $activeBatch) {
            return back()
                ->withErrors(['batch' => 'Create and activate a batch before importing assets.'])
                ->withInput();
        }

        $validated = $request->validate([
            'serial_asset_model_id' => ['required', 'exists:asset_models,id'],
            'serial_csv' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
            'serial_description' => ['nullable', 'string'],
        ]);

        $assetModel = AssetModel::with('category')->findOrFail($validated['serial_asset_model_id']);

        if (! $assetModel->require_serial_number) {
            return back()
                ->withErrors(['serial_asset_model_id' => 'Please select an asset model that requires serial numbers.'])
                ->withInput();
        }

        $file = $request->file('serial_csv');
        $handle = fopen($file->getRealPath(), 'r');

        if ($handle === false) {
            return back()
                ->withErrors(['serial_csv' => 'Unable to read the uploaded CSV file.'])
                ->withInput();
        }

        $serials = [];
        $isFirstDataRow = true;

        while (($row = fgetcsv($handle)) !== false) {
            if (! is_array($row)) {
                continue;
            }

            $firstCell = trim((string) ($row[0] ?? ''));

            if ($firstCell === '') {
                continue;
            }

            if ($isFirstDataRow) {
                $normalizedHeader = strtolower(preg_replace('/\s+/', '', $firstCell));
                $headerCandidates = ['serial', 'serialnumber', 'serialno', 'serial_number', 'sn'];

                if (in_array($normalizedHeader, $headerCandidates, true)) {
                    $isFirstDataRow = false;
                    continue;
                }
            }

            $isFirstDataRow = false;
            $serials[] = strtoupper($firstCell);
        }

        fclose($handle);

        if (empty($serials)) {
            return back()
                ->withErrors(['serial_csv' => 'The CSV has no serial numbers to import.'])
                ->withInput();
        }

        $serialCollection = collect($serials);
        $duplicateInFile = $serialCollection->duplicates()->unique()->values();

        if ($duplicateInFile->isNotEmpty()) {
            return back()
                ->withErrors([
                    'serial_csv' => 'Duplicate serial number(s) found in CSV: ' . $duplicateInFile->take(10)->implode(', '),
                ])
                ->withInput();
        }

        $existingSerials = Asset::whereNotNull('serial_number')
            ->pluck('serial_number')
            ->map(fn ($value) => strtoupper(trim((string) $value)))
            ->filter()
            ->intersect($serialCollection)
            ->values();

        if ($existingSerials->isNotEmpty()) {
            return back()
                ->withErrors([
                    'serial_csv' => 'Serial number(s) already exist: ' . $existingSerials->take(10)->implode(', '),
                ])
                ->withInput();
        }

        $description = $validated['serial_description'] ?? null;

        DB::transaction(function () use ($assetModel, $activeBatch, $description, $serialCollection) {
            $numbers = Asset::nextSequentialAssetNumbers($serialCollection->count());

            foreach ($serialCollection->values() as $index => $serialNumber) {
                Asset::create([
                    'uuid' => (string) Str::uuid(),
                    'asset_tag' => 'ODPP/' . str_pad((string) $numbers[$index], 6, '0', STR_PAD_LEFT),
                    'name' => $assetModel->name,
                    'description' => $description,
                    'asset_model_id' => $assetModel->id,
                    'batch_id' => $activeBatch->id,
                    'serial_number' => $serialNumber,
                ]);
            }
        });

        return redirect()->route('home')
            ->with('success', $serialCollection->count() . ' serial-number assets imported successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Asset $asset)
    {
        $asset->loadMissing(['assetModel.category', 'batch']);

        return view('assets.show', compact('asset'));
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
