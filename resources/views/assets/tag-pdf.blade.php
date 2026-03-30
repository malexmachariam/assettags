<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Asset Tag PDF</title>
    <style>
        @page { margin: 0; }
        body { margin: 0; padding: 0; font-family: Arial, sans-serif; background: #ffffff; }

        table.tag {
            width: 365pt;
            border-collapse: collapse;
            border: 3pt solid #0a2d5e;
            page-break-inside: avoid;
        }

        td.logo-cell {
            width: 42%;
            text-align: center;
            vertical-align: middle;
            padding: 10pt 12pt;
            page-break-inside: avoid;
        }

        td.qr-cell {
            text-align: center;
            vertical-align: middle;
            padding: 10pt 12pt;
            page-break-inside: avoid;
        }

        td.footer-cell {
            background: #0a2d5e;
            padding: 8pt 14pt;
            page-break-inside: avoid;
        }

        table.footer-inner {
            width: 100%;
            border-collapse: collapse;
        }

        .footer-left {
            color: #ffffff;
            font-size: 11pt;
            font-weight: bold;
            letter-spacing: 0.5pt;
        }

        .footer-right {
            color: #ffeb3b;
            font-size: 12pt;
            font-weight: bold;
            text-align: right;
        }

        .footer-category {
            color: #d8e4ff;
            font-size: 7pt;
            font-weight: 600;
            letter-spacing: 0.4pt;
            text-align: right;
            margin-top: 1pt;
        }
    </style>
</head>
<body>
    <table class="tag">
        <tr>
            <td class="logo-cell">
                @if ($logoBase64)
                    <img src="data:image/png;base64,{{ $logoBase64 }}" alt="ODPP Logo"
                         style="max-height: 95pt; width: auto;">
                @endif
            </td>
            <td class="qr-cell">
                <img src="data:image/png;base64,{{ $qrBase64 }}" alt="QR Code"
                     style="width: 105pt; height: 105pt;">
            </td>
        </tr>
        <tr>
            <td colspan="2" class="footer-cell">
                <table class="footer-inner">
                    <tr>
                        <td class="footer-left">PROPERTY OF ODPP</td>
                        <td style="text-align: right;">
                            <div class="footer-right">{{ $tagNumber }}</div>
                            <div class="footer-category">{{ $categoryText }}</div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>