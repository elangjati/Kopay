<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk #{{ $order->id }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            background: #f5f5f5;
            display: flex;
            justify-content: center;
            padding: 20px;
        }

        .receipt {
            background: white;
            width: 300px;
            padding: 20px 16px;
        }

        .header { text-align: center; margin-bottom: 12px; }
        .header h1 { font-size: 16px; font-weight: bold; letter-spacing: 1px; }
        .header p { font-size: 11px; color: #555; margin-top: 2px; }

        .divider { border-top: 1px dashed #999; margin: 10px 0; }
        .divider-solid { border-top: 1px solid #333; margin: 10px 0; }

        .info { margin-bottom: 4px; display: flex; justify-content: space-between; }
        .info span:first-child { color: #555; }

        .items { margin: 8px 0; }
        .item { margin-bottom: 6px; }
        .item-name { font-weight: bold; }
        .item-detail { display: flex; justify-content: space-between; color: #444; padding-left: 8px; }

        .total-row { display: flex; justify-content: space-between; font-weight: bold; font-size: 13px; }

        .footer { text-align: center; margin-top: 12px; font-size: 11px; color: #666; }

        .status-badge {
            display: inline-block;
            background: #166534;
            color: white;
            font-size: 10px;
            padding: 2px 8px;
            border-radius: 10px;
            margin-top: 4px;
        }

        .print-btn {
            display: block;
            width: 300px;
            margin: 16px auto 0;
            padding: 10px;
            background: #166534;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 13px;
            font-weight: bold;
            cursor: pointer;
            text-align: center;
        }

        .back-btn {
            display: block;
            width: 300px;
            margin: 8px auto 0;
            padding: 8px;
            background: white;
            color: #555;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 12px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
        }

        @media print {
            body { background: white; padding: 0; }
            .receipt { width: 100%; padding: 0; }
            .print-btn, .back-btn { display: none; }
        }
    </style>
</head>
<body>

<div>
    <div class="receipt">
        <div class="header">
            <img src="/images/logo.png" alt="Kopay" style="width:48px;height:48px;object-fit:cover;border-radius:8px;margin:0 auto 6px;">
            <h1>KOPAY</h1>
            <p>Terima kasih atas kunjungan Anda</p>
            <span class="status-badge">PEMBAYARAN BERHASIL</span>
        </div>

        <div class="divider-solid"></div>

        <div class="info">
            <span>No. Struk</span>
            <span>#{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</span>
        </div>
        <div class="info">
            <span>Tanggal</span>
            <span>{{ $order->created_at->format('d/m/Y H:i') }}</span>
        </div>
        <div class="info">
            <span>Pelanggan</span>
            <span>{{ $order->customer_name }}</span>
        </div>
        <div class="info">
            <span>Pembayaran</span>
            <span>{{ $order->payment_method === 'qris' ? 'QRIS' : 'Tunai' }}</span>
        </div>

        <div class="divider"></div>

        <div class="items">
            @foreach($order->items as $item)
            <div class="item">
                <div class="item-name">{{ $item->menu->name ?? 'Menu dihapus' }}</div>
                <div class="item-detail">
                    <span>{{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                    <span>Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</span>
                </div>
            </div>
            @endforeach
        </div>

        <div class="divider-solid"></div>

        <div class="total-row">
            <span>TOTAL</span>
            <span>Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
        </div>

        @if($order->notes)
        <div class="divider"></div>
        <div style="font-size:11px; color:#555;">
            <span>Catatan: {{ $order->notes }}</span>
        </div>
        @endif

        <div class="divider"></div>

        <div class="footer">
            <p>Pesanan telah dibayar</p>
            <p style="margin-top:6px;">— Sampai jumpa lagi! —</p>
        </div>
    </div>

    <button class="print-btn" onclick="window.print()">Cetak Struk</button>
    <a href="{{ route('admin.kasir.create') }}" class="back-btn">+ Pesanan Baru</a>
</div>

<script>
    // Auto print when page loads
    window.onload = function() {
        window.print();
    }
</script>
</body>
</html>
