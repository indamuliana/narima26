@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'href' => null,
])

@php
$baseClasses = 'inline-flex items-center justify-center font-medium rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer';

$sizeClasses = [
    'sm' => 'px-3 py-1.5 text-xs gap-1.5',
    'md' => 'px-4 py-2.5 text-sm gap-2',
    'lg' => 'px-6 py-3 text-base gap-2.5 font-semibold',
][$size] ?? 'px-4 py-2.5 text-sm gap-2';

$variantClasses = [
    'primary' => 'bg-nampi-orange hover:bg-nampi-orange-hover text-white focus:ring-nampi-orange shadow-sm hover:shadow',
    'cyan' => 'bg-nampi-cyan hover:bg-nampi-cyan-hover text-white focus:ring-nampi-cyan shadow-sm hover:shadow',
    'green' => 'bg-nampi-green hover:bg-nampi-green-hover text-white focus:ring-nampi-green shadow-sm hover:shadow',
    'secondary' => 'bg-slate-100 hover:bg-slate-200 text-slate-700 focus:ring-slate-300',
    'outline' => 'border border-slate-300 hover:border-nampi-orange hover:text-nampi-orange bg-transparent text-slate-700 focus:ring-nampi-orange',
    'danger' => 'bg-rose-500 hover:bg-rose-600 text-white focus:ring-rose-400 shadow-sm',
][$variant] ?? 'bg-nampi-orange hover:bg-nampi-orange-hover text-white focus:ring-nampi-orange';

$classes = "{$baseClasses} {$sizeClasses} {$variantClasses}";
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
