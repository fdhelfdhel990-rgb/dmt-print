@extends('layouts.admin')
@section('title','Tampilan Web')
@section('page-title','Tampilan Web')
@section('page-description','Atur konten yang dilihat customer tanpa mengubah kode.')
@section('content')
@include('partials.admin-page-header')
@if(session('status'))<section class="admin-card">{{ session('status') }}</section>@endif
@if($errors->any())<section class="admin-card">{{ $errors->first() }}</section>@endif
<div class="admin-tabs" data-tabs><button class="active" data-tab="banner">Banner</button><button data-tab="featured">Produk Unggulan</button><button data-tab="profile">Profil Usaha</button><button data-tab="payment">Pembayaran</button><button data-tab="social">Media Sosial</button></div>
<section class="admin-card tab-panel active" data-panel="banner">
    <div class="admin-card-heading">
        <div>
            <h2>Banner Beranda (Artwork Visual)</h2>
            <p>Admin cukup mengupload artwork gambar banner. Gambar akan tampil penuh responsif tanpa overlay teks.</p>
        </div>
    </div>
    <form method="POST" action="{{ route('admin.banners.store') }}" enctype="multipart/form-data" class="banner-add-form">
        @csrf
        <div class="form-grid">
            <div class="form-group full">
                <label>Gambar Banner Artwork (JPG, PNG, WebP maks. 4 MB)</label>
                <input name="image" type="file" accept="image/jpeg,image/png,image/webp" required>
            </div>
            <div class="form-group">
                <label>Urutan Tampil (0 = paling awal)</label>
                <input name="sort_order" type="number" value="{{ old('sort_order', 0) }}" min="0" required>
            </div>
            <div class="form-group">
                <label>Nama / Label Internal (Opsional)</label>
                <input name="title" value="{{ old('title') }}" placeholder="Contoh: Promo Ramadhan / Banner Utama">
            </div>
            <div class="form-group full">
                <label class="check-inline">
                    <input name="is_active" value="1" type="checkbox" checked> Aktifkan Banner
                </label>
            </div>
        </div>
        <button class="button button-primary">Tambah Banner</button>
    </form>

    <div class="banner-admin-grid">
        @forelse($banners as $banner)
            <article class="banner-card-admin">
                <div class="banner-card-preview">
                    @if($banner->imageUrl())
                        <img src="{{ $banner->imageUrl() }}" alt="{{ $banner->title }}" class="banner-preview-img">
                    @else
                        <div class="banner-preview"><b>Belum ada gambar</b></div>
                    @endif
                </div>
                <div class="banner-card-body">
                    <div class="banner-card-header">
                        <b>{{ $banner->title ?: 'Banner #'.$banner->id }}</b>
                        <span class="status-pill {{ $banner->is_active ? 'success' : 'neutral' }}">{{ $banner->is_active ? 'Aktif' : 'Nonaktif' }} (Urutan: {{ $banner->sort_order }})</span>
                    </div>
                    <form method="POST" action="{{ route('admin.banners.update', $banner) }}" enctype="multipart/form-data" class="banner-edit-form">
                        @csrf
                        @method('PATCH')
                        <div class="banner-form-row">
                            <div class="form-group">
                                <label>Ganti Gambar</label>
                                <input name="image" type="file" accept="image/jpeg,image/png,image/webp">
                            </div>
                            <div class="form-group">
                                <label>Urutan</label>
                                <input name="sort_order" type="number" value="{{ $banner->sort_order }}" min="0" style="width: 80px;">
                            </div>
                            <div class="form-group">
                                <label>Nama/Label</label>
                                <input name="title" value="{{ $banner->title }}">
                            </div>
                        </div>
                        <div class="banner-action-bar">
                            <label class="check-inline">
                                <input name="is_active" value="1" type="checkbox" @checked($banner->is_active)> Aktif
                            </label>
                            <div class="admin-row-actions">
                                <button type="submit" class="button button-primary" style="min-height:34px;padding:0 14px;font-size:12px">Simpan</button>
                            </div>
                        </div>
                    </form>
                    <form method="POST" action="{{ route('admin.banners.destroy', $banner) }}" class="banner-delete-form" onsubmit="return confirm('Arsipkan banner ini?')">
                        @csrf
                        @method('DELETE')
                        <button class="table-link danger-link">Arsipkan Banner</button>
                    </form>
                </div>
            </article>
        @empty
            <p class="muted">Belum ada banner yang dibuat.</p>
        @endforelse
    </div>
</section>
<section class="admin-card tab-panel" data-panel="featured"><h2>Produk Unggulan</h2><form method="POST" action="{{ route('admin.appearance.featured') }}">@csrf @method('PATCH')<div class="featured-select">@foreach($products as $product)<label><input name="products[]" value="{{ $product->id }}" type="checkbox" @checked(in_array($product->id, $featuredProductIds, true))><span class="table-thumb tone-{{ $product->tone }}"></span>{{ $product->name }}</label>@endforeach</div><button class="button button-primary">Simpan Produk Unggulan</button></form></section>
<section class="admin-card tab-panel" data-panel="profile"><h2>Profil Usaha</h2><form method="POST" action="{{ route('admin.appearance.business') }}">@csrf @method('PATCH')<div class="form-grid"><div class="form-group"><label>Nama usaha</label><input name="business_name" value="{{ old('business_name',$business['business_name'] ?? '') }}" required></div><div class="form-group"><label>WhatsApp</label><input name="phone" value="{{ old('phone',$business['phone'] ?? '') }}" required></div><div class="form-group full"><label>Alamat</label><textarea name="address" rows="3" required>{{ old('address',$business['address'] ?? '') }}</textarea></div><div class="form-group"><label>Jam operasional</label><input name="opening_hours" value="{{ old('opening_hours',$business['opening_hours'] ?? '') }}" required></div><div class="form-group"><label>Google Maps</label><input name="maps_url" value="{{ old('maps_url',$business['maps_url'] ?? '') }}"></div></div><button class="button button-primary">Simpan Profil</button></form></section>
<section class="admin-card tab-panel" data-panel="payment"><h2>Metode Pembayaran</h2>@foreach(['qris'=>'QRIS','bank_transfer'=>'Transfer Bank','cash'=>'Bayar Langsung'] as $type => $label)@php($method = $paymentMethods->firstWhere('type',$type) ?? new \App\Models\PaymentMethod(['type'=>$type,'name'=>$label,'is_active'=>true,'sort_order'=>0]))<form method="POST" action="{{ route('admin.appearance.payment') }}" enctype="multipart/form-data">@csrf @method('PATCH')<input type="hidden" name="type" value="{{ $type }}"><div class="form-grid"><div class="form-group"><label>Nama metode</label><input name="name" value="{{ $method->name }}" required></div><div class="form-group"><label>Urutan</label><input name="sort_order" type="number" value="{{ $method->sort_order }}"></div><div class="form-group"><label>Bank</label><input name="bank_name" value="{{ $method->bank_name }}"></div><div class="form-group"><label>No rekening</label><input name="account_number" value="{{ $method->account_number }}"></div><div class="form-group"><label>Nama pemilik</label><input name="account_name" value="{{ $method->account_name }}"></div><div class="form-group full"><label>Instruksi</label><textarea name="instructions">{{ $method->instructions }}</textarea></div>@if($type === 'qris')<div class="form-group">@if($method->imageUrl())<img src="{{ $method->imageUrl() }}" alt="QRIS" style="width:160px;border-radius:8px">@else<div class="admin-upload compact"><b>QRIS belum diunggah</b></div>@endif<label>Gambar QRIS</label><input name="image" type="file" accept="image/jpeg,image/png,image/webp"><label><input name="remove_image" value="1" type="checkbox"> Hapus QRIS</label></div>@endif<label><input name="is_active" value="1" type="checkbox" @checked($method->is_active)> Aktif</label></div><button class="button button-primary">Simpan {{ $label }}</button></form>@endforeach</section>
<section class="admin-card tab-panel" data-panel="social"><h2>Media Sosial</h2><form method="POST" action="{{ route('admin.appearance.social') }}">@csrf @method('PATCH')<div class="form-grid"><div class="form-group"><label>Instagram</label><input name="instagram" value="{{ old('instagram',$social['instagram'] ?? '') }}" placeholder="https://instagram.com/..."><label><input name="show_instagram" value="1" type="checkbox" @checked($social['show_instagram'] ?? false)> Tampilkan Instagram</label></div><div class="form-group"><label>TikTok</label><input name="tiktok" value="{{ old('tiktok',$social['tiktok'] ?? '') }}" placeholder="https://tiktok.com/@..."><label><input name="show_tiktok" value="1" type="checkbox" @checked($social['show_tiktok'] ?? false)> Tampilkan TikTok</label></div></div><button class="button button-primary">Simpan Media Sosial</button></form></section>
@endsection
