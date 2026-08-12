@props(['variant' => 'neutral', 'label'])

@php
$variants = [
    'success' => 'bg-success/10 text-success',
    'warning' => 'bg-warning/10 text-warning',
    'error' => 'bg-error/10 text-error',
    'info' => 'bg-primary-soft text-primary',
    'neutral' => 'bg-surface text-text-secondary border border-border',
];

$classes = $variants[$variant] ?? $variants['neutral'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1 px-2.5 py-0.5 rounded-control text-xs font-medium $classes"]) }}>
    {{ $label ?? $slot }}
</span>
