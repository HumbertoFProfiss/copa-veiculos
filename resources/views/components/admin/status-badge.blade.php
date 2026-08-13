@props(['variant' => 'neutral', 'label'])

@php
$variants = [
    'success' => 'bg-success/10 text-success ring-1 ring-inset ring-success/20',
    'warning' => 'bg-warning/10 text-warning ring-1 ring-inset ring-warning/20',
    'error' => 'bg-error/10 text-error ring-1 ring-inset ring-error/20',
    'info' => 'bg-primary-soft text-primary ring-1 ring-inset ring-primary/20',
    'neutral' => 'bg-surface text-text-secondary ring-1 ring-inset ring-border',
];

$dots = [
    'success' => 'bg-success',
    'warning' => 'bg-warning',
    'error' => 'bg-error',
    'info' => 'bg-primary',
    'neutral' => 'bg-text-secondary',
];

$classes = $variants[$variant] ?? $variants['neutral'];
$dot = $dots[$variant] ?? $dots['neutral'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium $classes"]) }}>
    <span class="w-1.5 h-1.5 rounded-full {{ $dot }}"></span>
    {{ $label ?? $slot }}
</span>
