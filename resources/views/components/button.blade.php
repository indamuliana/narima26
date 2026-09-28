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
    'primary' => 'bg-orange-600 hover:bg-orange-700 text-white font-semibold focus:ring-orange-500 shadow-sm hover:shadow',
    'cyan' => 'bg-cyan-700 hover:bg-cyan-800 text-white font-semibold focus:ring-cyan-600 shadow-sm hover:shadow',
    'green' => 'bg-emerald-600 hover:bg-emerald-700 text-white font-semibold focus:ring-emerald-500 shadow-sm hover:shadow',
    'secondary' => 'bg-slate-100 hover:bg-slate-200 text-slate-800 font-semibold focus:ring-slate-300 border border-slate-200',
    'outline' => 'border-2 border-slate-300 hover:border-orange-600 hover:text-orange-700 hover:bg-orange-50 bg-white text-slate-800 font-semibold focus:ring-orange-500',
    'danger' => 'bg-rose-600 hover:bg-rose-700 text-white font-semibold focus:ring-rose-500 shadow-sm',
][$variant] ?? 'bg-orange-600 hover:bg-orange-700 text-white font-semibold focus:ring-orange-500';

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
