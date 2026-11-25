@props(['title', 'subtitle' => null, 'eyebrow' => null, 'back' => null])

<div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
    <div class="min-w-0">
        @if($back)
            <a href="{{ $back }}" class="mb-2 inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 transition hover:text-emerald-600">
                <x-icon name="arrow-left" class="h-3.5 w-3.5" />
                Kembali
            </a>
        @endif
        <h1 class="font-sans text-[26px] font-extrabold leading-tight tracking-[-0.02em] text-ink-900 sm:text-[30px]">{{ $title }}</h1>
        @if($subtitle)
            <p class="mt-1.5 max-w-2xl text-sm leading-relaxed text-slate-500">{{ $subtitle }}</p>
        @endif
    </div>

    @if(! $slot->isEmpty())
        <div class="flex shrink-0 flex-wrap items-center gap-2">{{ $slot }}</div>
    @endif
</div>
