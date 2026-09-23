<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $invoiceNumber }} - DMT Print</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="invoice-body">
    <main class="invoice-page">
        <div class="invoice-actions">
            <button class="button button-primary" type="button" onclick="window.print()">Cetak</button>
            <a href="{{ route('admin.orders.show',$order) }}" class="button button-secondary">Kembali</a>
        </div>
        <header class="invoice-header">
            <div class="invoice-brand">
                <img src="{{ asset('images/branding/dmt-print-logo.svg') }}" alt="DMT Print" onerror="this.style.display='none'">
                <div>
                    <h1>{{ $business['business_name'] ?? 'Darul Muttaqien Printing' }}</h1>
                    <p>{{ $business['address'] ?? '-' }}</p>
                    <p>WhatsApp: {{ $business['phone'] ?? '-' }}</p>
                </div>
            </div>
            <div class="invoice-title">
                <h2>INVOICE</h2>
                <p>{{ $invoiceNumber }}</p>
            </div>
        </header>

        <section class="invoice-meta">
            <dl>
                <div><dt>Kode pesanan</dt><dd>{{ $order->order_number }}</dd></div>
                <div><dt>Tanggal pesanan</dt><dd>{{ $order->created_at->format('d M Y, H.i') }}</dd></div>
                <div><dt>Tanggal invoice</dt><dd>{{ now()->format('d M Y, H.i') }}</dd></div>
                <div><dt>Status pesanan</dt><dd>{{ str($order->status)->replace('_',' ')->title() }}</dd></div>
                <div><dt>Status pembayaran</dt><dd>{{ $remaining <= 0 ? 'Lunas' : 'Belum lunas' }}</dd></div>
                <div><dt>Skema pembayaran</dt><dd>{{ $order->payment_scheme ? str($order->payment_scheme)->replace('_',' ')->title() : '-' }}</dd></div>
            </dl>
            <dl>
                <div><dt>Pelanggan</dt><dd>{{ $order->customer->name }}</dd></div>
                <div><dt>WhatsApp</dt><dd>{{ $order->customer->phone }}</dd></div>
                <div><dt>Email</dt><dd>{{ $order->customer->email ?: '-' }}</dd></div>
                <div><dt>Penerimaan</dt><dd>{{ $order->fulfillment_method === 'shipping' ? 'Pengiriman' : 'Ambil sendiri' }}</dd></div>
                <div><dt>Alamat</dt><dd>{{ $order->shipping_address ?: '-' }} {{ $order->shipping_region }}</dd></div>
            </dl>
        </section>

        <section>
            <table class="invoice-table">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Opsi</th>
                        <th>Jumlah</th>
                        <th>Harga/unit</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        @php($unitPrice = $item->unit_final_price ?? $item->unit_estimate)
                        @php($subtotal = $item->final_subtotal ?? $item->subtotal)
                        <tr>
                            <td><div class="invoice-product"><x-product-image :model="$item" :alt="$item->product_name" class="invoice-thumb" /> <span>{{ $item->product_name }}</span></div></td>
                            <td>@forelse($item->options as $option){{ $option->option_name }}: {{ $option->option_value }}@if(!$loop->last)<br>@endif @empty - @endforelse</td>
                            <td>{{ $item->quantity }} {{ $item->unit }}</td>
                            <td>Rp {{ number_format($unitPrice,0,',','.') }}</td>
                            <td>Rp {{ number_format($subtotal,0,',','.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>

        <section class="invoice-summary">
            <div>
                <h3>Catatan</h3>
                <p>{{ $order->customer_note ?: '-' }}</p>
            </div>
            <dl>
                <div><dt>Subtotal</dt><dd>Rp {{ number_format($order->estimated_subtotal,0,',','.') }}</dd></div>
                <div><dt>Ongkir</dt><dd>Rp {{ number_format($order->shipping_cost,0,',','.') }}</dd></div>
                <div><dt>Total</dt><dd>Rp {{ number_format($total,0,',','.') }}</dd></div>
                <div><dt>Pembayaran terverifikasi</dt><dd>Rp {{ number_format($verifiedTotal,0,',','.') }}</dd></div>
                <div class="invoice-total"><dt>Sisa tagihan</dt><dd>Rp {{ number_format($remaining,0,',','.') }}</dd></div>
            </dl>
        </section>
    </main>
</body>
</html>
