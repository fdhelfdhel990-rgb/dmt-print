@extends('layouts.customer')

@section('title','Status Pesanan - DMT Print')

@section('breadcrumb')
<a href="{{ route('home') }}">Beranda</a><span>›</span><a href="{{ route('orders.track') }}">Cek Pesanan</a><span>›</span><b>{{ $order->order_number }}</b>
@endsection

@section('content')
@php
    $remaining = max(0, (int) $order->final_total - (int) $order->amount_paid);
@endphp
<section class="page-section">
    <div class="site-container order-status-grid">
        <div>
            <div class="status-header">
                <div>
                    <p class="eyebrow blue">{{ $order->order_number }}</p>
                    <h1>{{ str($order->status)->replace('_',' ')->title() }}</h1>
                    <p>Diperbarui {{ $order->updated_at->format('d M Y, H.i') }}</p>
                </div>
                <span class="status-badge production">{{ str($order->status)->replace('_',' ')->title() }}</span>
            </div>

            @if(session('status'))
                <section class="panel">{{ session('status') }}</section>
            @endif

            <section class="panel">
                <h2>Perjalanan Pesanan</h2>
                <div class="order-timeline">
                    @foreach($order->statusHistories as $history)
                        <div class="{{ $loop->last ? 'current' : 'done' }}">
                            <span>{{ $loop->last ? $loop->iteration : '✓' }}</span>
                            <div>
                                <b>{{ str($history->status)->replace('_',' ')->title() }}</b>
                                <small>{{ $history->created_at->format('d M Y, H.i') }}</small>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            @if($order->status === 'waiting_customer_approval')
                <section class="panel">
                    <h2>Konfirmasi Harga</h2>
                    <form method="POST" action="{{ route('orders.approve',$order) }}">
                        @csrf
                        <div class="form-group">
                            <label>Skema pembayaran</label>
                            <select name="payment_scheme">
                                <option value="down_payment">DP 50%</option>
                                <option value="full">Pembayaran penuh</option>
                            </select>
                        </div>
                        <button class="button button-primary">Setujui Harga</button>
                    </form>
                    <form method="POST" action="{{ route('orders.revise',$order) }}">
                        @csrf
                        <div class="form-group">
                            <label>Catatan revisi</label>
                            <textarea name="revision_note"></textarea>
                        </div>
                        <button class="button button-secondary">Minta Revisi</button>
                    </form>
                </section>
            @endif

            @if($remaining > 0 && in_array($order->status, ['waiting_payment', 'paid'], true))
                <section class="panel">
                    <h2>Pembayaran</h2>
                    @foreach(($paymentMethods ?? collect()) as $method)
                        <div class="summary-line"><span>{{ $method->name }}</span><b>{{ str($method->type)->replace('_',' ')->title() }}</b></div>
                        @if($method->type === 'qris')
                            @if($method->imageUrl())<img src="{{ $method->imageUrl() }}" alt="QRIS {{ $method->name }}" style="width:180px;border-radius:8px">@else<small>QRIS belum diunggah admin.</small>@endif
                        @endif
                        @if($method->instructions)<p>{{ $method->instructions }}</p>@endif
                    @endforeach
                    <form method="POST" action="{{ route('orders.payment',$order) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label>Metode</label>
                            <select name="method">
                                <option value="qris">QRIS</option>
                                <option value="bank_transfer">Transfer bank</option>
                                <option value="cash">Bayar langsung</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Jenis</label>
                            <select name="payment_type">
                                <option value="{{ $order->amount_paid > 0 ? 'settlement' : 'down_payment' }}">{{ $order->amount_paid > 0 ? 'Pelunasan' : 'DP 50%' }}</option>
                                <option value="full">Pembayaran penuh</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Bukti pembayaran</label>
                            <input type="file" name="proof">
                        </div>
                        <button class="button button-primary">Kirim Pembayaran</button>
                    </form>
                </section>
            @endif
        </div>
        <aside>
            <section class="panel">
                <h2>Ringkasan Pesanan</h2>
                @foreach($order->items as $item)
                    <div class="summary-product"><span class="summary-thumb"></span><div><b>{{ $item->product_name }}</b><small>{{ $item->quantity }} {{ $item->unit }}</small></div></div>
                @endforeach
                <div class="summary-line"><span>Total pesanan</span><b>{{ $order->final_total === null ? 'Menunggu admin' : 'Rp '.number_format($order->final_total,0,',','.') }}</b></div>
                <div class="summary-line"><span>Sudah dibayar</span><b>Rp {{ number_format($order->amount_paid,0,',','.') }}</b></div>
                <div class="summary-line total"><span>Sisa pembayaran</span><b>Rp {{ number_format($remaining,0,',','.') }}</b></div>
            </section>
            <section class="panel help-panel"><h3>Butuh bantuan?</h3><p>Hubungi admin dan sertakan kode pesanan.</p><a href="#">Chat WhatsApp</a></section>
        </aside>
    </div>
</section>
@endsection
