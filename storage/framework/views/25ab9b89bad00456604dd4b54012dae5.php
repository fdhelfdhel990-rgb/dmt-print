<article class="product-card">
    <a href="<?php echo e(route('product.show', $product)); ?>" class="product-art tone-<?php echo e($product->tone); ?>">
        <?php if($product->tag): ?><span class="product-tag"><?php echo e($product->tag); ?></span><?php endif; ?>
        <span class="print-sheet"><i></i><b>DMT</b><small><?php echo e($product->category->name); ?></small></span>
    </a>
    <div class="product-card-body"><p class="product-category"><?php echo e($product->category->name); ?></p><h3><a href="<?php echo e(route('product.show', $product)); ?>"><?php echo e($product->name); ?></a></h3><p class="product-price"><small>Mulai</small> Rp <?php echo e(number_format($product->base_price, 0, ',', '.')); ?><span>/<?php echo e($product->unit); ?></span></p></div>
</article>
<?php /**PATH D:\WEB\Laravel\print\dmt\resources\views/partials/product-card.blade.php ENDPATH**/ ?>