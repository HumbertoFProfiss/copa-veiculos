@props(['route', 'icon', 'label', 'modulo' => null])

@php
$active = request()->routeIs($route.'*');
$bloqueado = $modulo && ! app('tenant')->possuiAcessoModulo($modulo);
$href = $bloqueado
    ? route('admin.upgrade', ['modulo' => $modulo])
    : (\Illuminate\Support\Facades\Route::has($route) ? route($route) : '#');
@endphp

<a href="{{ $href }}"
   class="group relative flex items-center gap-3 pl-3 pr-3 py-2 rounded-control text-sm font-medium transition-all
       {{ $active ? 'bg-primary-soft text-primary' : ($bloqueado ? 'text-text-secondary/50 hover:bg-surface' : 'text-text-secondary hover:bg-surface hover:text-text-primary') }}">
    @if ($active)
        <span class="absolute left-0 top-1.5 bottom-1.5 w-[3px] rounded-full bg-primary"></span>
    @endif
    <x-dynamic-component :component="'heroicon-o-'.$icon"
        class="w-5 h-5 shrink-0 transition-colors {{ $active ? 'text-primary' : ($bloqueado ? 'text-text-secondary/40' : 'text-text-secondary/70 group-hover:text-text-primary') }}" />
    <span x-show="!colapsada" x-transition.opacity class="flex-1">{{ $label }}</span>
    @if ($bloqueado)
        <x-heroicon-o-lock-closed x-show="!colapsada" class="w-3.5 h-3.5 text-text-secondary/40 shrink-0" />
    @endif
</a>
