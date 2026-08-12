@props(['route', 'icon', 'label'])

@php
$active = request()->routeIs($route.'*');
@endphp

<a href="{{ \Illuminate\Support\Facades\Route::has($route) ? route($route) : '#' }}"
   class="flex items-center gap-3 px-3 py-2 rounded-control text-sm font-medium transition-colors
       {{ $active ? 'bg-primary-soft text-primary' : 'text-text-secondary hover:bg-surface hover:text-text-primary' }}">
    <x-dynamic-component :component="'heroicon-o-'.$icon" class="w-5 h-5 shrink-0" />
    <span x-show="!colapsada" x-transition.opacity>{{ $label }}</span>
</a>
