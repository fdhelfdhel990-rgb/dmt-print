@extends('layouts.customer')
@section('title', 'Detail Penerima - DMT Print')
@section('breadcrumb')
    <a href="{{ route('home') }}">Beranda</a><span>›</span><b>Detail Penerima</b>
@endsection
@section('content')
    @include('partials.checkout-steps', ['activeStep' => 2])

    <section class="page-section compact-top">
        <form class="site-container checkout-grid" method="POST" action="{{ route('checkout.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="checkout-main">
                <section class="panel accent-panel">
                    <div class="panel-heading">
                        <div>
                            <h2>Detail Penerima</h2>
                            <p>Admin akan menghubungi nomor ini untuk konfirmasi.</p>
                        </div>
                    </div>
                    @if($errors->any())
                        <p role="alert">Periksa kembali data yang ditandai sebelum melanjutkan.</p>
                    @endif
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="recipient-name">Nama lengkap</label>
                            <input id="recipient-name" name="name" value="{{ old('name') }}" autocomplete="name" required @error('name') aria-invalid="true" @enderror>
                            @error('name')<small role="alert">{{ $message }}</small>@enderror
                        </div>
                        <div class="form-group">
                            <label for="recipient-phone">Nomor WhatsApp</label>
                            <input id="recipient-phone" type="tel" inputmode="tel" name="phone" value="{{ old('phone') }}" autocomplete="tel" required @error('phone') aria-invalid="true" @enderror>
                            @error('phone')<small role="alert">{{ $message }}</small>@enderror
                        </div>
                        <div class="form-group full">
                            <label for="recipient-email">Email <small>(opsional)</small></label>
                            <input id="recipient-email" type="email" name="email" value="{{ old('email') }}" autocomplete="email">
                            @error('email')<small role="alert">{{ $message }}</small>@enderror
                        </div>
                        <div class="form-group full">
                            <label for="customer-note">Catatan <small>(opsional)</small></label>
                            <textarea id="customer-note" name="customer_note">{{ old('customer_note') }}</textarea>
                            @error('customer_note')<small role="alert">{{ $message }}</small>@enderror
                        </div>
                    </div>
                </section>

                <section class="panel accent-panel">
                    <h2>Cara menerima pesanan</h2>
                    <div class="choice-cards">
                        <label class="choice-card"><input type="radio" name="fulfillment_method" value="shipping" data-fulfillment-option @checked(old('fulfillment_method', 'shipping') === 'shipping') required> Pengiriman</label>
                        <label class="choice-card"><input type="radio" name="fulfillment_method" value="pickup" data-fulfillment-option @checked(old('fulfillment_method') === 'pickup')> Ambil sendiri</label>
                    </div>
                    <div class="form-grid shipping-fields" data-shipping-fields>
                        <div class="form-group full">
                            <label for="shipping-address">Alamat lengkap</label>
                            <textarea id="shipping-address" name="shipping_address" autocomplete="street-address">{{ old('shipping_address') }}</textarea>
                            @error('shipping_address')<small role="alert">{{ $message }}</small>@enderror
                        </div>
                        <div class="form-group">
                            <label for="shipping-region">Kota/kabupaten</label>
                            <input id="shipping-region" name="shipping_region" value="{{ old('shipping_region') }}" autocomplete="address-level2">
                            @error('shipping_region')<small role="alert">{{ $message }}</small>@enderror
                        </div>
                        <div class="form-group">
                            <label for="postal-code">Kode pos <small>(opsional)</small></label>
                            <input id="postal-code" name="postal_code" value="{{ old('postal_code') }}" autocomplete="postal-code" inputmode="numeric">
                            @error('postal_code')<small role="alert">{{ $message }}</small>@enderror
                        </div>
                        <div class="form-group full">
                            <label for="address-note">Catatan alamat <small>(opsional)</small></label>
                            <input id="address-note" name="address_note" value="{{ old('address_note') }}" placeholder="Patokan rumah atau instruksi kurir">
                            @error('address_note')<small role="alert">{{ $message }}</small>@enderror
                        </div>
                    </div>
                </section>

                <section class="panel">
                    <h2>File desain <small>(opsional)</small></h2>
                    <p>Unggah desain jika sudah tersedia. Anda juga dapat mengirimkannya kemudian.</p>
                    @foreach($items as $item)
                        <div class="form-group">
                            <label for="design-file-{{ $item['key'] }}">{{ $item['product']->name }}</label>
                            <input id="design-file-{{ $item['key'] }}" type="file" name="design_files[{{ $item['key'] }}]" accept=".jpg,.jpeg,.png,.pdf,.zip">
                            <small>JPG, PNG, PDF, atau ZIP; ukuran maksimal 10 MB.</small>
                            @error('design_files.'.$item['key'])<small role="alert">{{ $message }}</small>@enderror
                        </div>
                    @endforeach
                </section>

                <div>
                    <label class="check-row"><input type="checkbox" name="approved" value="1" @checked(old('approved')) required> Saya memahami harga ini masih estimasi dan harga final ditentukan admin setelah pemeriksaan.</label>
                    @error('approved')<small role="alert">{{ $message }}</small>@enderror
                </div>
                <button type="submit" class="button button-primary button-block">Buat Pesanan</button>
            </div>

            <aside class="checkout-side">
                <section class="panel sticky-panel">
                    <h2>Ringkasan</h2>
                    @foreach($items as $item)
                        <div class="summary-product">
                            <x-product-image :model="$item['product']" :alt="$item['product']->name" class="summary-thumb" />
                            <div>
                                <b>{{ $item['product']->name }}</b>
                                <small>@foreach($item['price']['values'] as $value){{ $value['option_name'] }}: {{ $value['option_value'] }}@if(!$loop->last) | @endif @endforeach<br>{{ $item['quantity'] }} {{ $item['product']->unit }} | Rp {{ number_format($item['price']['subtotal'], 0, ',', '.') }}</small>
                            </div>
                        </div>
                    @endforeach
                    <div class="summary-line total"><span>Total estimasi</span><b>Rp {{ number_format($subtotal, 0, ',', '.') }}</b></div>
                    <p>Pembayaran dilakukan setelah admin menetapkan harga final.</p>
                </section>
            </aside>
        </form>
    </section>
@endsection
