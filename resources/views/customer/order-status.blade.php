@extends('layouts.customer')

@section('title','Status Pesanan - DMT Print')

@section('breadcrumb')
<a href="{{ route('home') }}">Beranda</a><span>›</span><a href="{{ route('orders.track') }}">Cek Pesanan</a><span>›</span><b>{{ $order->order_number }}</b>
@endsection

@section('content')
@php
    $remaining = max(0, (int) $order->final_total - (int) $order->amount_paid);
    $minimumDownPayment = (int) ceil(((int) $order->final_total) / 2);
    $payableAmount = $order->amount_paid > 0 ? $remaining : min($remaining, $minimumDownPayment);
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
                <section class="panel" aria-live="polite">{{ session('status') }}</section>
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
                    <div class="summary-line total"><span>Nominal yang harus dibayar</span><b>Rp {{ number_format($payableAmount,0,',','.') }}</b></div>
                    @forelse(($paymentMethods ?? collect()) as $method)
                        <form method="POST" action="{{ route('orders.payment',$order) }}" enctype="multipart/form-data" class="payment-method-card">
                            @csrf
                            <input type="hidden" name="method" value="{{ $method->type }}">
                            <div class="payment-method-heading">
                                <div><b>{{ $method->name }}</b><small>{{ str($method->type)->replace('_',' ')->title() }}</small></div>
                                <span>Rp {{ number_format($payableAmount,0,',','.') }}</span>
                            </div>
                            @if($method->type === 'qris')
                                <img src="{{ $method->imageUrl() }}" alt="QRIS {{ $method->name }}" class="qris-image">
                            @elseif($method->type === 'bank_transfer')
                                <dl class="payment-detail-list">
                                    <div><dt>Bank</dt><dd>{{ $method->bank_name }}</dd></div>
                                    <div><dt>Nomor rekening</dt><dd><span data-copy-source>{{ $method->account_number }}</span> <button type="button" class="text-button" data-copy-button>Salin</button></dd></div>
                                    <div><dt>Atas nama</dt><dd>{{ $method->account_name }}</dd></div>
                                </dl>
                            @else
                                <p>Pembayaran dilakukan langsung di lokasi DMT Print.</p>
                            @endif
                            @if($method->instructions)<p>{{ $method->instructions }}</p>@endif
                            <div class="form-group">
                                <label>Jenis pembayaran</label>
                                <select name="payment_type">
                                    @if($order->amount_paid > 0)
                                        <option value="settlement">Pelunasan</option>
                                    @else
                                        <option value="down_payment">DP 50%</option>
                                        <option value="full">Pembayaran penuh</option>
                                    @endif
                                </select>
                            </div>
                            @if($method->type !== 'cash')
                                <div class="form-group">
                                    <label>Bukti pembayaran</label>
                                    <input type="file" name="proof">
                                </div>
                            @endif
                            <button class="button button-primary">{{ $method->type === 'cash' ? 'Pilih Bayar di Lokasi' : 'Upload Bukti Pembayaran' }}</button>
                        </form>
                    @empty
                        <section class="empty-state">
                            <p>Belum ada metode pembayaran aktif. Silakan hubungi admin untuk instruksi pembayaran.</p>
                        </section>
                    @endforelse
                    <p class="copy-feedback" data-copy-feedback aria-live="polite"></p>
                </section>
            @endif
        </div>
        <aside>
            <section class="panel">
                <h2>Ringkasan Pesanan</h2>
                @foreach($order->items as $item)
                    <div class="summary-product"><x-product-image :model="$item" :alt="$item->product_name" class="summary-thumb" /><div><b>{{ $item->product_name }}</b><small>@foreach($item->options as $option){{ $option->option_name }}: {{ $option->option_value }}@if(!$loop->last) | @endif @endforeach<br>{{ $item->quantity }} {{ $item->unit }}</small></div></div>
                @endforeach
                <div class="summary-line"><span>Total pesanan</span><b>{{ $order->final_total === null ? 'Menunggu admin' : 'Rp '.number_format($order->final_total,0,',','.') }}</b></div>
                <div class="summary-line"><span>Sudah dibayar</span><b>Rp {{ number_format($order->amount_paid,0,',','.') }}</b></div>
                <div class="summary-line total"><span>Sisa pembayaran</span><b>Rp {{ number_format($remaining,0,',','.') }}</b></div>
            </section>
            <section class="panel help-panel"><h3>Butuh bantuan?</h3><p>Hubungi admin dan sertakan kode pesanan.</p></section>
        </aside>
    </div>
</section>
@endsection
