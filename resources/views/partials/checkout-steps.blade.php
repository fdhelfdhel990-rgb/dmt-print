<div class="checkout-steps site-container">
    @foreach(['Keranjang','Penerima','Pengiriman','Pembayaran','Selesai'] as $i=>$step)<div class="checkout-step {{ $i <= ($activeStep ?? 0) ? 'active' : '' }}"><span>{{ $i+1 }}</span><small>{{ $step }}</small></div>@endforeach
</div>
