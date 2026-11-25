@props(['class' => 'h-10 w-10'])

<div {{ $attributes->merge(['class' => 'relative flex items-center justify-center rounded-xl bg-gradient-to-br from-emerald-400 via-emerald-500 to-teal-600 text-white shadow-[0_10px_24px_-10px_rgba(5,150,105,0.9)] '.$class]) }}>
    <svg viewBox="0 0 32 32" class="h-[62%] w-[62%]" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <path d="M16 3 27 7v8c0 7-4.8 11.8-11 14C9.8 26.8 5 22 5 15V7l11-4Z" fill="rgba(255,255,255,0.12)"/>
        <path d="M11 15.5 14.5 19 21 12.5"/>
    </svg>
    <span class="absolute right-1.5 top-1.5 h-1.5 w-1.5 rounded-full bg-amber-300 ring-2 ring-emerald-500/60"></span>
</div>
