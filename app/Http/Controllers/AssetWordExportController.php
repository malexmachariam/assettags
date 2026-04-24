<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Facades\Storage;

class AssetWordExportController extends Controller
{
    public function export(Request $request, Asset $asset)
    {
        // Choose template based on category (LAPTOP or DESKTOP)
        $category = strtoupper(optional(optional($asset->assetModel)->category)->name ?? '');
        if ($category === 'LAPTOP') {
            $templatePath = storage_path('app/ict_template_laptop.docx');
        } elseif ($category === 'DESKTOP') {
            $templatePath = storage_path('app/ict_template_desktop.docx');
        } else {
            $templatePath = storage_path('app/ict_template.docx'); // fallback
        }
        $tempFile = tempnam(sys_get_temp_dir(), 'asset_word_') . '.docx';

        $templateProcessor = new TemplateProcessor($templatePath);
        $templateProcessor->setValue('tag_no', $asset->asset_tag);
        $templateProcessor->setValue('serial_number', $asset->serial_number ?: 'N/A');
        $templateProcessor->setValue('asset_name', $asset->name);
        $templateProcessor->setValue('category', optional(optional($asset->assetModel)->category)->name ?: 'N/A');
        $templateProcessor->setValue('model', optional($asset->assetModel)->name ?: 'N/A');
        $templateProcessor->setValue('allocated_to', $asset->allocated_to ?: 'N/A');
        $templateProcessor->setValue('allocated_name', $asset->allocated_name ?: 'N/A');
        $templateProcessor->setValue('description', $asset->description ?: '');

        $templateProcessor->saveAs($tempFile);

        return response()->download($tempFile, 'asset-'.$asset->serial_number.'.docx')->deleteFileAfterSend(true);
    }
}
