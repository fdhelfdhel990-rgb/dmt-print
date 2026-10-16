<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['slug' => '']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['slug' => '']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php ($icon = [
    'merchandise-souvenir' => 'gift',
    'merchandise' => 'gift',
    'print-warna' => 'printer',
    'stiker' => 'sticker',
    'packaging-umkm' => 'package',
    'poster' => 'image',
    'kalender' => 'calendar',
    'banner' => 'presentation',
    'kartu-nama' => 'id-card',
    'stempel' => 'stamp',
][$slug] ?? 'category'); ?>
<svg <?php echo e($attributes->merge(['class' => 'category-icon category-icon-svg', 'viewBox' => '0 0 48 48', 'fill' => 'none', 'xmlns' => 'http://www.w3.org/2000/svg', 'aria-hidden' => 'true'])); ?>>
    <?php switch($icon):
        case ('gift'): ?>
            <path d="M10 21h28v19H10V21Z" />
            <path d="M8 14h32v7H8v-7ZM24 14v26M16 14c-5-6 5-10 8 0M32 14c5-6-5-10-8 0" />
            <?php break; ?>
        <?php case ('printer'): ?>
            <path d="M14 18V8h20v10M14 34H9V20h30v14h-5" />
            <path d="M15 29h18v11H15V29ZM14 26h3M19 13h10" />
            <?php break; ?>
        <?php case ('sticker'): ?>
            <path d="M11 10h22l5 5v23H11V10Z" />
            <path d="M32 10v7h6M18 22h12M18 29h8" />
            <?php break; ?>
        <?php case ('package'): ?>
            <path d="M9 17 24 9l15 8-15 8-15-8Z" />
            <path d="M9 17v16l15 8 15-8V17M24 25v16M16 13l15 8" />
            <?php break; ?>
        <?php case ('image'): ?>
            <path d="M9 11h30v26H9V11Z" />
            <path d="m14 32 8-9 6 6 4-5 4 8M17 18h.1" />
            <?php break; ?>
        <?php case ('calendar'): ?>
            <path d="M10 13h28v27H10V13ZM10 21h28M17 8v8M31 8v8" />
            <path d="M17 27h3M23 27h3M29 27h3M17 33h3M23 33h3" />
            <?php break; ?>
        <?php case ('presentation'): ?>
            <path d="M9 10h30v22H9V10ZM24 32v8M17 40h14" />
            <path d="M16 25h5l4-6 3 4h4" />
            <?php break; ?>
        <?php case ('id-card'): ?>
            <path d="M8 13h32v25H8V13Z" />
            <path d="M15 28c1-5 9-5 10 0M20 23a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM29 20h6M29 27h6M14 9h20" />
            <?php break; ?>
        <?php case ('stamp'): ?>
            <path d="M18 25h12l2 9H16l2-9Z" />
            <path d="M20 25v-7a4 4 0 0 1 8 0v7M13 34h22v6H13v-6ZM16 40h16" />
            <?php break; ?>
        <?php default: ?>
            <path d="M11 11h11v11H11V11ZM26 11h11v11H26V11ZM11 26h11v11H11V26ZM26 26h11v11H26V26Z" />
    <?php endswitch; ?>
</svg>
<?php /**PATH D:\WEB\Laravel\print\dmt\resources\views\components\category-icon.blade.php ENDPATH**/ ?>