<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $batch->name }} Tags</title>
    <style>
        @page { margin: 18pt; }
        body { margin: 0; font-family: Arial, sans-serif; }
        .page { page-break-after: always; }
        .page:last-child { page-break-after: auto; }
        .batch-title { font-size: 16pt; font-weight: bold; margin-bottom: 12pt; }
        .tag-wrap { width: 100%; }
        .tag {
            width: 360pt;
            border-collapse: collapse;
            border: 3pt solid #0a2d5e;
            page-break-inside: avoid;
        }
        .tag-logo,
        .tag-qr { padding: 10pt 12pt; vertical-align: middle; text-align: center; }
        .tag-footer { background: #0a2d5e; padding: 8pt 12pt; }
        .footer-table { width: 100%; border-collapse: collapse; }
        .footer-left { color: #fff; font-size: 9.5pt; font-weight: bold; letter-spacing: 0.5pt; }
        .footer-right-wrap { text-align: right; }
        .footer-right { color: #ffeb3b; font-size: 10.5pt; font-weight: bold; }
        .footer-category { color: #d8e4ff; font-size: 6.5pt; font-weight: 600; letter-spacing: 0.4pt; }
    </style>
</head>
<body>
    @foreach($tagData as $tag)
        <div class="page">
            <div class="batch-title">Batch: {{ $batch->name }}</div>
            <div class="tag-wrap">
                <table class="tag">
                    <tr>
                        <td class="tag-logo" style="width: 42%;">
                            @if($logoBase64)
                                <img src="data:image/png;base64,{{ $logoBase64 }}" alt="ODPP Logo" style="max-height: 80pt; width: auto;">
                            @endif
                        </td>
                        <td class="tag-qr">
                            <img src="data:image/png;base64,{{ $tag['qrBase64'] }}" alt="QR Code" style="width: 90pt; height: 90pt;">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="tag-footer">
                            <table class="footer-table">
                                <tr>
                                    <td class="footer-left">PROPERTY OF ODPP</td>
                                    <td class="footer-right-wrap">
                                        <div class="footer-right">{{ $tag['asset']->asset_tag }}</div>
                                        <div class="footer-category">{{ $tag['categoryText'] }}</div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    @endforeach
</body>
</html>