<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Massal QR Code Aset</title>
    <style>
        .qr-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            padding: 10px;
        }
        .qr-card {
            border: 2px solid #2e7d32;
            border-radius: 6px;
            padding: 10px;
            text-align: center;
            background: #fff;
            box-sizing: border-box;
            page-break-inside: avoid;
        }
        .title-app {
            font-size: 11px;
            font-weight: bold;
            color: #2e7d32;
            margin-bottom: 8px;
            text-transform: uppercase;
            border-bottom: 2px solid #2e7d32;
            padding-bottom: 3px;
        }
        .qr-image-zone img {
            width: 110px;
            height: 110px;
            object-fit: contain;
        }
        .asset-code {
            font-size: 11px;
            font-weight: bold;
            font-family: 'Courier New', Courier, monospace;
            background: #e8f5e9;
            color: #1b5e20;
            padding: 3px 5px;
            border: 1px solid #c8e6c9;
            border-radius: 3px;
            margin-top: 5px;
        }
        .asset-name {
            font-size: 10px;
            color: #333;
            margin-top: 5px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .print-actions {
            background: #f5f5f5;
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid #ddd;
            font-family: sans-serif;
        }
        .btn-print {
            background: #2e7d32;
            color: white;
            border: none;
            padding: 10px 25px;
            font-size: 14px;
            font-weight: bold;
            border-radius: 4px;
            cursor: pointer;
        }
        @media print {
            .print-actions { display: none; }
            body { background: #fff; margin: 0; padding: 0; }
            .qr-card { border: 2px solid #000; }
            .title-app { color: #000; border-bottom: 2px solid #000; }
            .asset-code { background: #fff; color: #000; border: 1px solid #000; }
        }
    </style>
</head>
<body>

    <div class="print-actions">
        <button class="btn-print" onclick="window.print()">🔥 MULAI CETAK STIKER MASSAL</button>
        <p style="font-size: 12px; color: #666; margin-top: 5px;">Total data siap cetak: {{ $assets->count() }} label aset.</p>
    </div>

    <div class="qr-grid">
        @foreach($assets as $asset)
            <div class="qr-card">
                <div class="title-app">SIMASET BAZNAS</div>
                
                <div class="qr-image-zone">
                    @if($asset->qr_code && file_exists(public_path($asset->qr_code)))
                        <img src="{{ asset($asset->qr_code) }}" alt="QR">
                    @else
                        {!! QrCode::size(110)->generate($asset->kode_aset) !!}
                    @endif
                </div>
                
                <div class="asset-code">
                    {{ $asset->kode_aset ?? '-' }}
                </div>
                
                <div class="asset-name">
                    {{ $asset->barang->nama_barang ?? 'Aset BAZNAS' }}
                </div>
            </div>
        @endforeach
    </div>

</body>
</html>