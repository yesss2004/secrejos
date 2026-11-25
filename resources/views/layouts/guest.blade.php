<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <meta name="theme-color" content="#08111f">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Masuk') &middot; e-Secretariat</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-[#f4f6fa] antialiased">
    <div class="flex min-h-screen">
        <aside class="sidebar-shell sidebar-grid relative hidden w-[54%] flex-col overflow-hidden p-10 lg:flex xl:p-14">
            <div class="absolute -left-24 -top-24 h-72 w-72 rounded-full bg-emerald-500/10 blur-3xl"></div>
            <div class="absolute bottom-0 right-0 h-80 w-80 rounded-full bg-teal-400/10 blur-3xl"></div>

            <a href="{{ route('login') }}" class="relative flex items-center gap-3">
                <x-brand-mark class="h-12 w-12" />
                <div>
                    <p class="text-[21px] font-extrabold tracking-[-0.03em] text-white">e-Secretariat</p>
                    <p class="text-[9px] font-bold uppercase tracking-[0.22em] text-emerald-300/80">Digital Office Suite</p>
                </div>
            </a>

            <div class="relative my-auto max-w-xl py-12">
                <span class="inline-flex items-center gap-2 rounded-full border border-emerald-400/20 bg-emerald-400/10 px-3 py-1.5 text-[10px] font-bold uppercase tracking-[0.16em] text-emerald-300">
                    <x-icon name="sparkles" class="h-3.5 w-3.5" />
                    Sekretariat modern
                </span>

                <h1 class="mt-6 text-[42px] font-extrabold leading-[1.08] tracking-[-0.035em] text-white xl:text-[50px]">
                    Satu ruang kerja untuk<br>
                    <span class="bg-gradient-to-r from-emerald-300 to-teal-200 bg-clip-text text-transparent">seluruh urusan sekretariat.</span>
                </h1>

                <p class="mt-5 max-w-lg text-[15px] leading-relaxed text-slate-400">
                    Kelola laporan, surat masuk dan keluar, arsip digital, hingga persetujuan pimpinan dalam satu platform yang rapi, aman, dan mudah ditelusuri.
                </p>

                <div class="mt-8 grid max-w-lg grid-cols-1 gap-3 sm:grid-cols-3">
                    <div class="rounded-2xl border border-white/[0.08] bg-white/[0.04] p-4 backdrop-blur-sm">
                        <p class="text-[22px] font-extrabold text-white">12<span class="text-emerald-400">+</span></p>
                        <p class="mt-0.5 text-[10px] font-semibold uppercase tracking-wider text-slate-400">Laporan terkelola</p>
                    </div>
                    <div class="rounded-2xl border border-white/[0.08] bg-white/[0.04] p-4 backdrop-blur-sm">
                        <p class="text-[22px] font-extrabold text-white">12<span class="text-emerald-400">+</span></p>
                        <p class="mt-0.5 text-[10px] font-semibold uppercase tracking-wider text-slate-400">Surat terarsip</p>
                    </div>
                    <div class="rounded-2xl border border-white/[0.08] bg-white/[0.04] p-4 backdrop-blur-sm">
                        <p class="text-[22px] font-extrabold text-white">87<span class="text-emerald-400">%</span></p>
                        <p class="mt-0.5 text-[10px] font-semibold uppercase tracking-wider text-slate-400">Arsip digital</p>
                    </div>
                </div>

                <div class="mt-9 space-y-3">
                    @foreach([
                        ['icon' => 'document', 'text' => 'Laporan bulanan & tahunan dengan alur persetujuan'],
                        ['icon' => 'mail', 'text' => 'Pencatatan surat masuk dan keluar yang tertata'],
                        ['icon' => 'chart', 'text' => 'Statistik kinerja sekretariat secara real-time'],
                    ] as $feature)
                        <div class="flex items-center gap-3">
                            <span class="flex h-8 w-8 items-center justify-center rounded-lg border border-emerald-400/15 bg-emerald-400/10 text-emerald-300">
                                <x-icon :name="$feature['icon']" class="h-4 w-4" />
                            </span>
                            <p class="text-[13px] font-medium text-slate-300">{{ $feature['text'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="relative flex items-center justify-between border-t border-white/[0.07] pt-5 text-[10px] text-slate-500">
                <p>&copy; {{ now()->year }} e-Secretariat</p>
                <p class="flex items-center gap-1.5"><x-icon name="shield" class="h-3.5 w-3.5 text-emerald-400" /> Akses terenkripsi &amp; terverifikasi</p>
            </div>
        </aside>

        <main class="flex w-full flex-1 flex-col lg:w-[46%]">
            <div class="flex items-center justify-between px-6 py-6 sm:px-10">
                <a href="{{ route('login') }}" class="flex items-center gap-2.5 lg:hidden">
                    <x-brand-mark class="h-9 w-9" />
                    <span class="text-[16px] font-extrabold tracking-tight text-ink-900">e-Secretariat</span>
                </a>
                <div class="ml-auto text-[12px] text-slate-500">
                    @hasSection('switch')
                        @yield('switch')
                    @endif
                </div>
            </div>

            <div class="flex flex-1 items-center justify-center px-5 pb-10 sm:px-10">
                <div class="w-full max-w-[420px] fade-up">
                    @yield('content')
                </div>
            </div>

            <div class="px-6 pb-6 text-center text-[10px] text-slate-400 sm:px-10">
                <p>Dibangun untuk sekretariat yang lebih cepat, rapi, dan akuntabel.</p>
            </div>
        </main>
    </div>
</body>
</html>
