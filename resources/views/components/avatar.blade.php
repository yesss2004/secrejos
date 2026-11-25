@props(['user', 'size' => 'md', 'ring' => false])

@php
    $sizes = [
        'xs' => 'h-7 w-7 text-[10px]',
        'sm' => 'h-8 w-8 text-[11px]',
        'md' => 'h-10 w-10 text-xs',
        'lg' => 'h-12 w-12 text-sm',
        'xl' => 'h-16 w-16 text-lg',
    ];
    $tone = $user->avatar_color ?? 'navy';
@endphp

<span {{ $attributes->merge(['class' => 'avatar avatar-'.$tone.' '.($sizes[$size] ?? $sizes['md']).($ring ? ' ring-2 ring-white shadow-sm' : '') ]) }} title="{{ $user->name }}">
    {{ $user->initials }}
</span>
