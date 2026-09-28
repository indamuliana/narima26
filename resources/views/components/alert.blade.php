@props([
    'type' => 'info',
    'title' => null,
    'dismissible' => false,
])

@php
$config = [
    'success' => [
        'bg' => 'bg-emerald-50',
        'border' => 'border-emerald-200',
        'text' => 'text-emerald-800',
        'titleText' => 'text-emerald-900',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />',
    ],
    'error' => [
        'bg' => 'bg-rose-50',
        'border' => 'border-rose-200',
        'text' => 'text-rose-800',
        'titleText' => 'text-rose-900',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />',
    ],
    'warning' => [
        'bg' => 'bg-amber-50',
        'border' => 'border-amber-200',
        'text' => 'text-amber-800',
        'titleText' => 'text-amber-900',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />',
    ],
    'info' => [
        'bg' => 'bg-sky-50',
        'border' => 'border-sky-200',
        'text' => 'text-sky-800',
        'titleText' => 'text-sky-900',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',
    ],
][$type] ?? [
    'bg' => 'bg-sky-50',
    'border' => 'border-sky-200',
    'text' => 'text-sky-800',
    'titleText' => 'text-sky-900',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',
];
@endphp

<div {{ $attributes->merge(['class' => "p-4 rounded-xl border {$config['bg']} {$config['border']} {$config['text']} relative flex gap-3"]) }} role="alert">
    <div class="shrink-0 mt-0.5">
        <svg class="w-5 h-5 {{ $config['text'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            {!! $config['icon'] !!}
        </svg>
    </div>
    <div class="flex-1 text-sm">
        @if ($title)
            <h4 class="font-semibold {{ $config['titleText'] }} mb-0.5">{{ $title }}</h4>
        @endif
        <div>{{ $slot }}</div>
    </div>
</div>
