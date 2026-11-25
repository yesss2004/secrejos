<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <meta name="theme-color" content="#08111f">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Sistem rekap laporan pribadi — dashboard untuk mencatat, menilai, dan menampilkan laporan yang saya input sendiri.">

    <title>@yield('title', 'Dashboard') &middot; Rekap Laporan Saya</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="page-bg min-h-full antialiased">
    <div id="sidebar-overlay" class="fixed inset-0 z-40 hidden bg-ink-950/60 backdrop-blur-sm lg:hidden"></div>

    <aside id="sidebar" class="sidebar-shell sidebar-grid fixed inset-y-0 left-0 z-50 flex w-[280px] -translate-x-full flex-col overflow-hidden transition-transform duration-300 ease-out lg:translate-x-0">
        <div class="relative px-5 pb-5 pt-6">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                <x-brand-mark class="h-11 w-11" />
                <div class="min-w-0">
                    <p class="truncate text-[17px] font-extrabold tracking-[-0.02em] text-white">Sekretariat</p>
                    <p class="text-[9px] font-bold uppercase tracking-[0.2em] text-emerald-300/80">Dashboard Utama</p>
                </div>
            </a>
        </div>

        <div class="mx-4 h-px bg-white/[0.07]"></div>

        <nav class="hide-scrollbar relative flex-1 overflow-y-auto px-3 py-2">
            <p class="nav-label">Menu Utama</p>

            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <x-icon name="dashboard" />
                <span>Dashboard Utama</span>
            </a>

            @if(auth()->user()->canManageData())
                <a href="{{ route('monitoring.index') }}" class="nav-link {{ request()->routeIs('monitoring.*') ? 'active' : '' }}">
                    <x-icon name="list" />
                    <span>Monitoring</span>
                </a>
            @endif

            @if(auth()->user()->canManageData() || auth()->user()->canReview())
                <a href="{{ route('issues.index') }}" class="nav-link {{ request()->routeIs('issues.*') ? 'active' : '' }}">
                    <x-icon name="alert" />
                    <span>Kendala</span>
                </a>
            @endif

            @if(auth()->user()->canManageData())
                <a href="{{ route('targets.index') }}" class="nav-link {{ request()->routeIs('targets.*') ? 'active' : '' }}">
                    <x-icon name="calendar" />
                    <span>Target &amp; Jadwal</span>
                </a>
                <a href="{{ route('letters.index') }}" class="nav-link {{ request()->routeIs('letters.*') ? 'active' : '' }}">
                    <x-icon name="mail" />
                    <span>Persuratan &amp; Arsip</span>
                </a>
            @endif

            <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <x-icon name="chart" />
                <span>Laporan &amp; Rekapitulasi</span>
            </a>

            <a href="{{ route('plans.index') }}" class="nav-link {{ request()->routeIs('plans.*') ? 'active' : '' }}">
                <x-icon name="document" />
                <span>Perencanaan &amp; Anggaran</span>
            </a>

            @if(auth()->user()->isAdmin())
                <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <x-icon name="users" />
                    <span>Manajemen Pengguna</span>
                </a>
            @endif

            <a href="{{ route('profile.edit') }}" class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                <x-icon name="user" />
                <span>Profil Saya</span>
            </a>

        </nav>

        <div class="relative border-t border-white/[0.07] p-3">
            <div class="flex items-center gap-2.5 rounded-xl px-2 py-2">
                <x-avatar :user="auth()->user()" size="sm" />
                <div class="min-w-0 flex-1">
                    <p class="truncate text-[12px] font-bold text-white">{{ auth()->user()->name }}</p>
                    <p class="truncate text-[10px] text-slate-400">{{ auth()->user()->role_label }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="rounded-lg p-2 text-slate-400 transition hover:bg-white/10 hover:text-rose-300" title="Keluar">
                        <x-icon name="logout" class="h-4 w-4" />
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <div class="lg:pl-[280px]">
        <header class="{{ request()->routeIs('dashboard') ? 'hidden' : 'sticky top-0 z-30 border-b border-slate-200/70 bg-white/80 backdrop-blur-xl' }}">
            <div class="mx-auto flex h-[72px] max-w-[1680px] items-center gap-3 px-4 sm:px-6 lg:px-8">
                <button type="button" data-sidebar-toggle class="btn-ghost -ml-2 !p-2.5 lg:hidden" aria-label="Buka menu">
                    <x-icon name="menu" class="h-5 w-5" />
                </button>

                <div class="hidden min-w-0 xl:block">
                    <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-slate-400">{{ now()->locale('id')->translatedFormat('l, d F Y') }}</p>
                    <p class="truncate text-[15px] font-extrabold tracking-tight text-ink-900">@yield('title', 'Dashboard')</p>
                </div>

                @if(auth()->user()->canManageData())
                <form action="{{ route('search') }}" method="GET" class="mx-auto min-w-0 flex-1 sm:max-w-md xl:ml-8 xl:mr-0">
                    <label for="global-search" class="sr-only">Cari laporan atau surat</label>
                    <div class="relative">
                        <x-icon name="search" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                        <input id="global-search" type="search" name="q" value="{{ request()->routeIs('search') ? request('q') : '' }}"
                               placeholder="Cari laporan, surat, atau nomor arsip..."
                               class="input !rounded-xl !bg-slate-50/80 !py-2.5 !pl-10 !pr-10 !text-[13px] focus:!bg-white">
                        <kbd class="pointer-events-none absolute right-3 top-1/2 hidden -translate-y-1/2 rounded border border-slate-200 bg-white px-1.5 py-0.5 text-[10px] font-semibold text-slate-400 sm:block">/</kbd>
                    </div>
                </form>
                @endif

                <div class="flex items-center gap-1.5">
                    @if(auth()->user()->canReview() || auth()->user()->canManageData())
                    <div class="relative">
                        <button type="button" data-dropdown-toggle="notification-menu" class="relative rounded-xl p-2.5 text-slate-500 transition hover:bg-slate-100 hover:text-slate-800" aria-label="Notifikasi">
                            <x-icon name="bell" class="h-5 w-5" />
                            @if(($pendingCount ?? 0) > 0)
                                <span class="absolute right-1.5 top-1.5 flex h-2 w-2">
                                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-rose-400 opacity-75"></span>
                                    <span class="relative inline-flex h-2 w-2 rounded-full bg-rose-500"></span>
                                </span>
                            @endif
                        </button>

                        <div id="notification-menu" data-dropdown class="absolute right-0 mt-2 hidden w-[340px] origin-top-right overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-[0_24px_60px_-24px_rgba(15,23,42,0.35)]">
                            <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3">
                                <div>
                                    <p class="text-sm font-extrabold text-ink-900">Notifikasi</p>
                                    <p class="text-[11px] text-slate-400">{{ $pendingCount ?? 0 }} laporan menunggu review</p>
                                </div>
                                <span class="badge badge-amber">{{ $pendingCount ?? 0 }} baru</span>
                            </div>
                            <div class="max-h-[320px] overflow-y-auto">
                                @forelse(($pendingPreview ?? collect()) as $pending)
                                    <a href="{{ route('reports.show', $pending) }}" class="flex gap-3 border-b border-slate-50 px-4 py-3 transition last:border-0 hover:bg-slate-50">
                                        <span class="icon-tile tone-amber h-9 w-9 rounded-lg">
                                            <x-icon name="clock" class="h-4 w-4" />
                                        </span>
                                        <div class="min-w-0 flex-1">
                                            <p class="line-clamp-1 text-xs font-bold text-slate-800">{{ $pending->title }}</p>
                                            <p class="mt-0.5 text-[11px] text-slate-400">{{ $pending->user?->name }} &middot; {{ $pending->created_at->diffForHumans() }}</p>
                                        </div>
                                    </a>
                                @empty
                                    <div class="px-4 py-8 text-center">
                                        <x-icon name="check-circle" class="mx-auto mb-2 h-7 w-7 text-emerald-400" />
                                        <p class="text-xs font-semibold text-slate-600">Tidak ada laporan tertunda</p>
                                        <p class="mt-0.5 text-[11px] text-slate-400">Semua laporan sudah ditinjau.</p>
                                    </div>
                                @endforelse
                            </div>
                            <a href="{{ route('reports.index', ['status' => 'pending']) }}" class="block border-t border-slate-100 bg-slate-50/60 px-4 py-2.5 text-center text-[11px] font-bold text-emerald-700 transition hover:bg-emerald-50">
                                Lihat semua laporan menunggu
                            </a>
                        </div>
                    </div>
                    @endif

                    <div class="relative">
                        <button type="button" data-dropdown-toggle="user-menu" class="flex items-center gap-2 rounded-xl p-1 pr-2 transition hover:bg-slate-100">
                            <x-avatar :user="auth()->user()" size="sm" />
                            <div class="hidden text-left sm:block">
                                <p class="max-w-[110px] truncate text-[12px] font-bold text-ink-900">{{ auth()->user()->name }}</p>
                                <p class="max-w-[110px] truncate text-[10px] text-slate-500">{{ auth()->user()->role_label }}</p>
                            </div>
                            <x-icon name="chevron-down" class="hidden h-3.5 w-3.5 text-slate-400 sm:block" />
                        </button>

                        <div id="user-menu" data-dropdown class="absolute right-0 mt-2 hidden w-60 origin-top-right overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-[0_24px_60px_-24px_rgba(15,23,42,0.35)]">
                            <div class="border-b border-slate-100 px-4 py-3.5">
                                <p class="truncate text-sm font-extrabold text-ink-900">{{ auth()->user()->name }}</p>
                                <p class="truncate text-[11px] text-slate-500">{{ auth()->user()->email }}</p>
                            </div>
                            <div class="p-1.5">
                                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-[13px] font-semibold text-slate-600 transition hover:bg-slate-100 hover:text-slate-900">
                                    <x-icon name="user" class="h-4 w-4 text-slate-400" /> Profil Saya
                                </a>
                                @if(auth()->user()->canManageData())
                                    <a href="{{ route('activities.index') }}" class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-[13px] font-semibold text-slate-600 transition hover:bg-slate-100 hover:text-slate-900">
                                        <x-icon name="activity" class="h-4 w-4 text-slate-400" /> Riwayat Aktivitas
                                    </a>
                                @endif
                            </div>
                            <div class="border-t border-slate-100 p-1.5">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-[13px] font-semibold text-rose-600 transition hover:bg-rose-50">
                                        <x-icon name="logout" class="h-4 w-4" /> Keluar dari akun
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main class="{{ request()->routeIs('dashboard') ? 'mx-auto max-w-[1680px] px-4 py-4 sm:px-6 lg:px-8 lg:py-8' : 'mx-auto max-w-[1680px] px-4 py-6 sm:px-6 lg:px-8 lg:py-8' }}">
            @yield('content')

            <footer class="mt-10 flex flex-col items-center justify-between gap-2 border-t border-slate-200/70 py-5 text-[11px] text-slate-400 sm:flex-row">
                <p>&copy; {{ now()->year }} <span class="font-semibold text-slate-500">e-Secretariat</span>. Sekretariat Utama.</p>
                <p class="flex items-center gap-1.5"><x-icon name="shield" class="h-3.5 w-3.5 text-emerald-500" /> Data tersimpan aman &middot; v1.0</p>
            </footer>
        </main>
    </div>

    <div class="pointer-events-none fixed right-4 top-[86px] z-[70] flex w-[calc(100%-2rem)] max-w-sm flex-col gap-2">
        @if(session('success'))
            <div data-toast class="toast pointer-events-auto flex items-start gap-3 rounded-xl border border-emerald-200 bg-white p-4 shadow-[0_18px_40px_-20px_rgba(15,23,42,0.3)]">
                <span class="icon-tile tone-emerald h-8 w-8 rounded-lg"><x-icon name="check-circle" class="h-4 w-4" /></span>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-bold text-slate-800">Berhasil</p>
                    <p class="mt-0.5 text-xs leading-relaxed text-slate-500">{{ session('success') }}</p>
                </div>
                <button type="button" onclick="this.closest('[data-toast]').remove()" class="rounded p-1 text-slate-400 hover:bg-slate-100">
                    <x-icon name="x" class="h-3.5 w-3.5" />
                </button>
            </div>
        @endif

        @if(session('error'))
            <div data-toast class="toast pointer-events-auto flex items-start gap-3 rounded-xl border border-rose-200 bg-white p-4 shadow-[0_18px_40px_-20px_rgba(15,23,42,0.3)]">
                <span class="icon-tile tone-rose h-8 w-8 rounded-lg"><x-icon name="alert" class="h-4 w-4" /></span>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-bold text-slate-800">Perhatian</p>
                    <p class="mt-0.5 text-xs leading-relaxed text-slate-500">{{ session('error') }}</p>
                </div>
                <button type="button" onclick="this.closest('[data-toast]').remove()" class="rounded p-1 text-slate-400 hover:bg-slate-100">
                    <x-icon name="x" class="h-3.5 w-3.5" />
                </button>
            </div>
        @endif
    </div>

    @stack('scripts')
</body>
</html>
