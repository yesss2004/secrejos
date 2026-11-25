@props(['tone' => 'slate', 'label', 'icon' => null])

<span {{ $attributes->merge(['class' => 'badge badge-'.$tone]) }}>
    @if($icon)
        <x-icon :name="$icon" class="h-3 w-3" />
    @endif
    {{ $label }}
</span>
