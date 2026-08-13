@props(['route', 'icon', 'label'])

@php
$active = request()->routeIs($route.'*');
@endphp

<a href="{{ \Illuminate\Support\Facades\Route::has($route) ? route($route) : '#' }}"
   class="group relative flex items-center gap-3 pl-3 pr-3 py-2 rounded-control text-sm font-medium transition-all
       {{ $active ? 'bg-primary-soft text-primary' : 'text-text-secondary hover:bg-surface hover:text-text-primary' }}">
    @if ($active)
        <span class="absolute left-0 top-1.5 bottom-1.5 w-[3px] rounded-full bg-primary"></span>
    @endif
    <x-dynamic-component :component="'heroicon-o-'.$icon"
        class="w-5 h-5 shrink-0 transition-colors {{ $active ? 'text-primary' : 'text-text-secondary/70 group-hover:text-text-primary' }}" />
    <span x-show="!colapsada" x-transition.opacity>{{ $label }}</span>
</a>
