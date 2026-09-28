@props([
    'variant' => 'orange',
    'size' => 'md',
])

@php
$baseClasses = 'inline-flex items-center font-medium rounded-full';

$sizeClasses = [
    'sm' => 'px-2 py-0.5 text-xs',
    'md' => 'px-2.5 py-1 text-xs',
    'lg' => 'px-3 py-1.5 text-sm',
][$size] ?? 'px-2.5 py-1 text-xs';

$variantClasses = [
    'orange' => 'bg-amber-50 text-amber-800 border border-amber-200',
    'green' => 'bg-emerald-50 text-emerald-800 border border-emerald-200',
    'cyan' => 'bg-cyan-50 text-cyan-800 border border-cyan-200',
    'blue' => 'bg-blue-50 text-blue-800 border border-blue-200',
    'red' => 'bg-rose-50 text-rose-800 border border-rose-200',
    'gray' => 'bg-slate-100 text-slate-700 border border-slate-200',
][$variant] ?? 'bg-amber-50 text-amber-800 border border-amber-200';

$classes = "{$baseClasses} {$sizeClasses} {$variantClasses}";
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>
