<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use BaconQrCode\Renderer\GDLibRenderer;
use BaconQrCode\Writer;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'name',
        'description',
        'asset_model_id',
        'batch_id',
        'serial_number',
        'asset_tag', // Include asset_tag in fillable fields
    ];

    public function assetModel()
    {
        return $this->belongsTo(AssetModel::class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public static function nextSequentialAssetTag(): string
    {
        return (string) static::nextSequentialAssetNumbers(1)[0];
    }

    public static function nextSequentialAssetNumbers(int $count): array
    {
        $usedNumbers = static::query()
            ->pluck('asset_tag')
            ->map(function ($tag) {
                $value = trim((string) $tag);

                if (preg_match('/^(?:ODPP\/)?(\d+)$/', $value, $matches) !== 1) {
                    return null;
                }

                return (int) $matches[1];
            })
            ->filter(fn ($number) => $number !== null && $number > 0)
            ->unique()
            ->sort()
            ->values()
            ->all();

        $usedLookup = array_fill_keys($usedNumbers, true);
        $nextNumbers = [];
        $candidate = 1;

        while (count($nextNumbers) < $count) {
            if (! isset($usedLookup[$candidate])) {
                $nextNumbers[] = $candidate;
            }

            $candidate++;
        }

        return $nextNumbers;
    }

    public function getAssetTagHtmlAttribute()
    {
        $tagNumber = (string) $this->asset_tag;
        $categoryText = strtoupper((string) optional(optional($this->assetModel)->category)->name ?: 'UNCATEGORIZED');

        // Generate QR Code - URL so scanning opens the asset page
        $assetUrl = route('assets.show', $this);

        $renderer = new \BaconQrCode\Renderer\GDLibRenderer(220, 4);
        $writer   = new \BaconQrCode\Writer($renderer);
        $qrBinary = $writer->writeString($assetUrl);
        $qrBase64 = base64_encode($qrBinary);

        return '
        <div style="width: 460px; height: 190px; background: white; border: 4px solid #0a2d5e; border-radius: 12px; overflow: hidden; font-family: Arial, sans-serif; box-shadow: 0 6px 20px rgba(0,0,0,0.15);">

            <!-- Main Content Area -->
            <div style="display: flex; height: 145px; padding: 12px;">

                <!-- Left: Logo -->
                <div style="width: 160px; display: flex; align-items: center; justify-content: center; padding-right: 15px;">
                    <img src="' . asset('img/favicon.png') . '" 
                        alt="ODPP Logo" 
                        style="max-height: 125px; width: auto; object-fit: contain;">
                </div>

                <!-- Right: QR Code -->
                <div style="flex: 1; display: flex; align-items: center; justify-content: center; padding-left: 10px;">
                    <div style="background: white; padding: 8px; border: 2px solid #ddd; border-radius: 6px;">
                        <img src="data:image/png;base64,' . $qrBase64 . '" 
                            style="width: 135px; height: 135px;">
                    </div>
                </div>

            </div>

            <!-- Bottom Blue Bar -->
            <div style="background: #0a2d5e; color: white; height: 45px; display: flex; align-items: center; padding: 0 18px; font-weight: bold;">

                <!-- Left Text -->
                <div style="font-size: 15px; letter-spacing: 1px;">
                    PROPERTY OF ODPP
                </div>

                <!-- Right Asset Tag -->
                <div style="flex: 1; text-align: right; color: #ffeb3b; display: flex; flex-direction: column; align-items: flex-end; justify-content: center; line-height: 1.05;">
                    <div style="font-size: 17px;">' . htmlspecialchars($tagNumber) . '</div>
                    <div style="font-size: 9px; color: #d8e4ff; letter-spacing: 0.7px;">' . htmlspecialchars($categoryText) . '</div>
                </div>

            </div>

        </div>';
    }
    public function getAssetTagAttribute1()
    {
        //$tagNumber = 'ABCD-' . strtoupper(substr($this->uuid, 0, 8));
        //$tagNumber = 'ODPP-' . strtoupper(substr($this->uuid, 0, 12));

        $category = $this->assetModel && $this->assetModel->category ? strtoupper($this->assetModel->category->name) : 'ASSET';
        // $tagNumber = 'ODPP/' . $category . '/' . strtoupper(substr($this->uuid, 0, 12));
        $tagNumber = (string) $this->asset_tag;

        // Prepare QR code data as JSON
        $qrData = [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'category' => $category,
            //'model' => $this->assetModel ? $this->assetModel->name : null,
            //'date_of_purchase' => $this->date_of_purchase ?? null,
        ];
        //dd($qrData);
        $qrString = json_encode($qrData);

            // Generate QR Code at high resolution for print (200px)
            $renderer = new \BaconQrCode\Renderer\GDLibRenderer(200);
            $writer   = new \BaconQrCode\Writer($renderer);
            $qrBinary = $writer->writeString($qrString);
            $qrBase64 = base64_encode($qrBinary);

        // 39mm x 13mm = 147px x 49px at 96dpi
        return '
       <div style="width:147px;height:49px;background:#fff;border:1px solid #222;border-radius:3px;overflow:hidden;display:flex;flex-direction:column;justify-content:space-between;font-family:Arial,sans-serif;">
    
            <div style="display:flex;flex-direction:row;align-items:center;width:100%;height:36px;padding:2px 4px 0 4px;">

                <!-- Logo (left) -->
                <div style="width:36px;height:36px;display:flex;align-items:center;justify-content:center;">
                    <img src="' . asset('img/favicon.png') . '" 
                        style="width:30px;height:30px;object-fit:contain;">
                </div>

                <!-- Small spacer -->
                <div style="flex:1;"></div>

                <!-- QR Code (center-right, NOT edge) -->
                 <div style="width:55px;height:50px;display:flex;align-items:center;justify-content:center; ">
                        <img src="data:image/png;base64,' . $qrBase64 . '" style="height:24px;width:24px;border:0;display:block;image-rendering:crisp-edges;image-rendering:-webkit-optimize-contrast;">
                </div>

                <!-- Large blank area (right side like your orange box) -->
                <div style="flex:1;"></div>
              

            </div>

            <!-- Tag text -->
            <div style="font-size:6px;font-weight:bold;color:#001854;white-space:nowrap;text-align:left;width:100%;margin-bottom:1px;">
                ' . htmlspecialchars($tagNumber) . '
            </div>

        </div>';
    }
     
     
}
