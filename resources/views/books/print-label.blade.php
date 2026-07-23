<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Cetak Label - {{ $book->title }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; padding: 10mm; }
        .label {
            border: 2px solid #000;
            padding: 10px;
            width: 80mm;
            text-align: center;
        }
        .label-title {
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 4px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .label-author {
            font-size: 9px;
            color: #555;
            margin-bottom: 6px;
        }
        .barcode {
            margin: 6px auto;
        }
        .barcode img {
            width: 50mm;
            height: 50mm;
        }
        .isbn-text {
            font-size: 8px;
            color: #666;
            margin-top: 2px;
        }
        @media print {
            body { padding: 0; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 10px;">
        <button onclick="window.print()">Cetak Label</button>
    </div>

    <div class="label">
        <div class="label-title">{{ $book->title }}</div>
        <div class="label-author">{{ $book->author }}</div>
        <div class="barcode">
            <img src="data:image/png;base64,{{ $barcode }}" alt="QR Code {{ $book->isbn }}">
        </div>
        <div class="isbn-text">{{ $book->isbn }}</div>
    </div>
</body>
</html>
