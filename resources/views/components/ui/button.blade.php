@props([
    'type' => 'button',
    'variant' => 'primary',
    'block' => false,
])

<button
    type="{{ $type }}"
    {{ $attributes->class([
        'ui-button',
        'ui-button--' . $variant,
        'ui-button--block' => $block,
    ]) }}
>
    {{ $slot }}
</button>