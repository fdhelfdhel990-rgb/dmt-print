@extends('layouts.customer')
@section('title','Pesanan Berhasil - DMT Print')
@section('content')<section class="page-section"><div class="site-container"><section class="panel"><p class="eyebrow blue">Pesanan diterima</p><h1>{{ $order->order_number }}</h1><p>Admin akan memeriksa detail dan file desain Anda sebelum menetapkan harga final. Pesanan belum dianggap dibayar.</p><p>Simpan kode pesanan ini untuk komunikasi dengan admin.</p><a href="{{ route('home') }}" class="button button-primary">Kembali ke Beranda</a></section></div></section>@endsection
