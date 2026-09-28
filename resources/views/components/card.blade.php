@props([
    'title' => null,
    'subtitle' => null,
    'headerClass' => '',
    'bodyClass' => '',
    'footerClass' => '',
])

<div {{ $attributes->merge(['class' => 'bg-white rounded-xl border border-slate-200/80 shadow-xs overflow-hidden']) }}>
    @if ($title || isset($header))
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between {{ $headerClass }}">
            <div>
                @if ($title)
                    <h3 class="text-base font-semibold text-slate-800">{{ $title }}</h3>
                @endif
                @if ($subtitle)
                    <p class="text-xs text-slate-500 mt-0.5">{{ $subtitle }}</p>
                @endif
            </div>
            @if (isset($headerActions))
                <div>
                    {{ $headerActions }}
                </div>
            @endif
        </div>
    @endif

    <div class="p-5 {{ $bodyClass }}">
        {{ $slot }}
    </div>

    @if (isset($footer))
        <div class="px-5 py-3.5 bg-slate-50 border-t border-slate-100 {{ $footerClass }}">
            {{ $footer }}
        </div>
    @endif
</div>
