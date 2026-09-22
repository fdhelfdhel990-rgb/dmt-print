<article class="product-card">
    <a href="{{ route('product.show', $product) }}" class="product-art tone-{{ $product->tone }}">
        @if($product->tag)<span class="product-tag">{{ $product->tag }}</span>@endif
        <span class="print-sheet"><i></i><b>DMT</b><small>{{ $product->category->name }}</small></span>
    </a>
    <div class="product-card-body"><p class="product-category">{{ $product->category->name }}</p><h3><a href="{{ route('product.show', $product) }}">{{ $product->name }}</a></h3><p class="product-price"><small>Mulai</small> Rp {{ number_format($product->base_price, 0, ',', '.') }}<span>/{{ $product->unit }}</span></p></div>
</article>
