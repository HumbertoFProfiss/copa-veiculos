<div>
    <label class="block">
        <div class="border-2 border-dashed border-border rounded-card p-6 text-center cursor-pointer hover:border-primary transition-colors">
            <x-heroicon-o-cloud-arrow-up class="w-8 h-8 mx-auto text-text-secondary mb-2" />
            <p class="text-sm text-text-secondary">Arraste fotos aqui ou clique pra selecionar</p>
            <input type="file" wire:model="novasFotos" multiple accept="image/*" class="hidden">
        </div>
    </label>

    <div wire:loading wire:target="novasFotos" class="mt-2 text-sm text-text-secondary">
        Enviando fotos...
    </div>

    @error('novasFotos.*')
        <p class="mt-2 text-sm text-error">{{ $message }}</p>
    @enderror

    @if ($fotos->isNotEmpty())
        <div x-data="fotoReorder(@js($fotos->pluck('id')))"
             x-init="$watch('ordem', v => $wire.reordenar(v))"
             class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 mt-4">
            @foreach ($fotos as $foto)
                <div draggable="true"
                     wire:key="foto-{{ $foto->id }}"
                     data-id="{{ $foto->id }}"
                     @dragstart="arrastar($event, {{ $foto->id }})"
                     @dragover.prevent
                     @drop.prevent="soltar($event, {{ $foto->id }})"
                     class="relative group rounded-card overflow-hidden border border-border cursor-move">
                    <img src="{{ $foto->url() }}" class="w-full h-28 object-cover">

                    @if ($foto->principal)
                        <span class="absolute top-1 left-1">
                            <x-admin.status-badge variant="info" label="Capa" />
                        </span>
                    @endif

                    <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                        @if (! $foto->principal)
                            <button wire:click="definirPrincipal({{ $foto->id }})" type="button"
                                    title="Definir como capa" class="text-white hover:text-primary-light">
                                <x-heroicon-o-star class="w-5 h-5" />
                            </button>
                        @endif
                        <button wire:click="remover({{ $foto->id }})" wire:confirm="Remover esta foto?" type="button"
                                title="Remover" class="text-white hover:text-error">
                            <x-heroicon-o-trash class="w-5 h-5" />
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

@script
<script>
    Alpine.data('fotoReorder', (idsIniciais) => ({
        ordem: idsIniciais,
        arrastando: null,
        arrastar(event, id) {
            this.arrastando = id;
        },
        soltar(event, idAlvo) {
            if (this.arrastando === null || this.arrastando === idAlvo) return;
            const de = this.ordem.indexOf(this.arrastando);
            const para = this.ordem.indexOf(idAlvo);
            this.ordem.splice(de, 1);
            this.ordem.splice(para, 0, this.arrastando);
            this.arrastando = null;
        },
    }));
</script>
@endscript
