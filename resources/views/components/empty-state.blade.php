@props(['icon' => 'folder', 'title', 'description' => null])

<div {{ $attributes->merge(['class' => 'empty-state']) }}>
    <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
        <x-icon :name="$icon" class="h-6 w-6" />
    </div>
    <h3 class="text-sm font-bold text-slate-800">{{ $title }}</h3>
    @if($description)
        <p class="mt-1 max-w-sm text-xs leading-relaxed text-slate-500">{{ $description }}</p>
    @endif
    @if(! $slot->isEmpty())
        <div class="mt-4">{{ $slot }}</div>
    @endif
</div>
