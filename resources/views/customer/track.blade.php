@extends('layouts.customer')
@section('title','Cek Pesanan - DMT Print')
@section('breadcrumb')<a href="{{ route('home') }}">Beranda</a><span>›</span><b>Cek Pesanan</b>@endsection
@section('content')
<section class="track-page"><div class="track-illustration"><span class="track-doc"></span><span class="track-box"></span></div><div class="track-card"><p class="eyebrow blue">Pantau pesanan</p><h1>Cek status pesanan Anda</h1><p>Masukkan kode pesanan dan nomor WhatsApp yang digunakan saat memesan.</p><form action="{{ route('orders.status') }}"><div class="form-group"><label>Kode pesanan</label><input name="order_number" value="{{ old('order_number') }}" placeholder="Contoh: DMT-240901-A12B">@error('order_number')<small>{{ $message }}</small>@enderror</div><div class="form-group"><label>Nomor WhatsApp</label><input name="phone" value="{{ old('phone') }}" placeholder="Contoh: 081234567890">@error('phone')<small>{{ $message }}</small>@enderror</div><button class="button button-primary button-block">Cek Status Pesanan</button></form><small>Kode pesanan tersedia pada halaman berhasil setelah Anda mengirim pesanan.</small></div></section>
@endsection
