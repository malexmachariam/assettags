<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Batch;
use BaconQrCode\Renderer\GDLibRenderer;
use BaconQrCode\Writer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class BatchController extends Controller
{
    public function index()
    {
        $batches = Batch::withCount('assets')->orderByDesc('active')->orderByDesc('created_at')->get();
        $unassignedCount = Asset::whereNull('batch_id')->count();

        return view('batches.index', [
            'batches' => $batches,
            'activeBatch' => $batches->firstWhere('active', true),
            'unassignedCount' => $unassignedCount,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:batches,name'],
            'active' => ['nullable', 'boolean'],
        ]);

        Batch::create([
            'name' => $validated['name'],
            'active' => $request->boolean('active'),
        ]);

        return redirect()->route('batches.index')
            ->with('success', 'Batch created successfully.');
    }

    public function activate(Batch $batch)
    {
        $batch->update(['active' => true]);

        return redirect()->route('batches.index')
            ->with('success', 'Active batch updated successfully.');
    }

    public function tagsPdf(Batch $batch)
    {
        $batch->load(['assets.assetModel.category']);

        if ($batch->assets->isEmpty()) {
            return redirect()->route('batches.index')
                ->with('error', 'This batch has no assets to export.');
        }

        $logoBase64 = null;
        $logoPath = public_path('img/favicon.png');

        if (file_exists($logoPath)) {
            $logoBase64 = base64_encode(file_get_contents($logoPath));
        }

        $tagData = $batch->assets
            ->sortBy('asset_tag', SORT_NATURAL)
            ->values()
            ->map(function ($asset) {
                $renderer = new GDLibRenderer(220, 4);
                $writer = new Writer($renderer);
                $qrBinary = $writer->writeString(route('assets.show', $asset));

                return [
                    'asset' => $asset,
                    'qrBase64' => base64_encode($qrBinary),
                    'categoryText' => strtoupper((string) optional(optional($asset->assetModel)->category)->name ?: 'UNCATEGORIZED'),
                ];
            });

        return Pdf::loadView('batches.tags-pdf', [
            'batch' => $batch,
            'tagData' => $tagData,
            'logoBase64' => $logoBase64,
        ])
            ->setPaper('a4', 'portrait')
            ->stream('batch-' . $batch->id . '-tags.pdf');
    }

    public function assignUnassigned(Request $request, Batch $batch)
    {
        $count = Asset::whereNull('batch_id')->count();

        if ($count === 0) {
            return redirect()->route('batches.index')
                ->with('error', 'There are no unassigned assets to reassign.');
        }

        Asset::whereNull('batch_id')->update(['batch_id' => $batch->id]);

        return redirect()->route('batches.index')
            ->with('success', "{$count} asset(s) assigned to batch \"{$batch->name}\" successfully.");
    }
}