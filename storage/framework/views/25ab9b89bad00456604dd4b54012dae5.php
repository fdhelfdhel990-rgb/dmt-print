<article class="product-card">
    <a href="<?php echo e(route('product.show', $product)); ?>" class="product-art tone-<?php echo e($product->tone); ?>">
        <?php if($product->tag): ?><span class="product-tag"><?php echo e($product->tag); ?></span><?php endif; ?>
        <?php if (isset($component)) { $__componentOriginala58dde406db9207f2e2c58e1c4a3d690 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala58dde406db9207f2e2c58e1c4a3d690 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.product-image','data' => ['model' => $product,'alt' => $product->name]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('product-image'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['model' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product),'alt' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product->name)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala58dde406db9207f2e2c58e1c4a3d690)): ?>
<?php $attributes = $__attributesOriginala58dde406db9207f2e2c58e1c4a3d690; ?>
<?php unset($__attributesOriginala58dde406db9207f2e2c58e1c4a3d690); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala58dde406db9207f2e2c58e1c4a3d690)): ?>
<?php $component = $__componentOriginala58dde406db9207f2e2c58e1c4a3d690; ?>
<?php unset($__componentOriginala58dde406db9207f2e2c58e1c4a3d690); ?>
<?php endif; ?>
    </a>
    <div class="product-card-body"><p class="product-category"><?php echo e($product->category->name); ?></p><h3><a href="<?php echo e(route('product.show', $product)); ?>"><?php echo e($product->name); ?></a></h3><p class="product-price"><small>Mulai</small> Rp <?php echo e(number_format($product->base_price, 0, ',', '.')); ?><span>/<?php echo e($product->unit); ?></span></p><a href="<?php echo e(route('product.show', $product)); ?>" class="product-card-cta">Lihat detail</a></div>
</article>
<?php /**PATH D:\WEB\Laravel\print\dmt\resources\views/partials/product-card.blade.php ENDPATH**/ ?>