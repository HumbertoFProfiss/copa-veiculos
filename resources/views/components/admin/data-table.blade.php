@props(['head'])

<div class="bg-bg border border-border rounded-card shadow-soft overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-surface border-b border-border sticky top-0">
                <tr>
                    {{ $head }}
                </tr>
            </thead>
            <tbody class="divide-y divide-border [&>tr]:transition-colors [&>tr:hover]:bg-surface/60">
                {{ $slot }}
            </tbody>
        </table>
    </div>
</div>
