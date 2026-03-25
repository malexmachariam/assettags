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
        'serial_number',
    ];

    public function assetModel()
    {
        return $this->belongsTo(AssetModel::class);
    }
    public function getAssetTagAttribute()
    {
        $tagNumber = 'ABCD-' . strtoupper(substr($this->uuid, 0, 8));

        // Generate QR Code using bacon v3
        $renderer = new \BaconQrCode\Renderer\GDLibRenderer(260);
        $writer   = new \BaconQrCode\Writer($renderer);
        $qrBinary = $writer->writeString($this->uuid);
        $qrBase64 = base64_encode($qrBinary);

        return '
        <div style="width: 460px; height: 220px; margin: 15px auto; background: white; border: 3px solid #1a1a1a; border-radius: 8px; overflow: hidden; box-shadow: 0 6px 20px rgba(0,0,0,0.15); font-family: Arial, sans-serif;">

            <!-- Top Blue Bar with Asset Tag -->
            <div style="background: #0d6efd; color: white; text-align: center; padding: 8px 0; font-size: 18px; font-weight: bold;">
                Asset Tag: <span style="background: rgba(255,255,255,0.25); padding: 2px 12px; border-radius: 4px;">' . $tagNumber . '</span>
            </div>

            <div style="display: flex; height: 170px; padding: 15px;">

                <!-- Left: Logo + Tag Number -->
                <div style="width: 48%; text-align: center; border-right: 2px solid #ddd; padding-right: 15px;">
                    <img src="' . asset('img/favicon.png') . '" 
                        alt="Logo" 
                        style="height: 85px; width: auto; margin-bottom: 12px;">

                    <div style="font-size: 21px; font-weight: bold; color: #1a1a1a; line-height: 1.2;">
                        ' . htmlspecialchars($tagNumber) . '
                    </div>
                </div>

                <!-- Right: QR Code -->
                <div style="width: 52%; text-align: center; padding-left: 15px; display: flex; align-items: center; justify-content: center;">
                    <img src="data:image/png;base64,' . $qrBase64 . '" 
                        style="width: 165px; height: 165px; border: 1px solid #ccc; padding: 8px; background: #f8f9fa;">
                </div>

            </div>

        </div>';
    }
     
     
}
