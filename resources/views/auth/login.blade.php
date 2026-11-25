@extends('layouts.guest')

@section('title', 'Masuk')

@section('switch')
    Akses pengguna sekretariat
@endsection

@section('content')
    <div class="mb-8">
        <p class="mb-2 text-[11px] font-bold uppercase tracking-[0.18em] text-emerald-600">Selamat datang</p>
        <h1 class="text-[30px] font-extrabold leading-tight tracking-[-0.03em] text-ink-900">Masuk ke e-Secretariat</h1>
        <p class="mt-2 text-sm leading-relaxed text-slate-500">Gunakan akun sekretariat Anda. Menu dan fitur disesuaikan dengan hak akses peran pengguna.</p>
    </div>

    <form method="POST" action="{{ route('login.store') }}" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="label">Alamat email</label>
            <div class="relative">
                <x-icon name="mail" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                       placeholder="nama@sekretariat.go.id"
                       class="input !py-3 !pl-10 @error('email') !border-rose-400 !ring-rose-500/10 @enderror">
            </div>
            @error('email')
                <p class="field-error"><x-icon name="alert" class="mt-0.5 h-3 w-3 shrink-0" /> {{ $message }}</p>
            @enderror
        </div>

        <div>
            <div class="mb-1.5 flex items-center justify-between">
                <label for="password" class="label !mb-0">Kata sandi</label>
                <span class="text-[11px] font-semibold text-slate-400">Minimal 8 karakter</span>
            </div>
            <div class="relative">
                <x-icon name="lock" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                <input id="password" name="password" type="password" required autocomplete="current-password"
                       placeholder="Masukkan kata sandi"
                       class="input !py-3 !pl-10 !pr-11 @error('password') !border-rose-400 !ring-rose-500/10 @enderror">
                <button type="button" data-toggle-password="password" class="absolute right-2.5 top-1/2 -translate-y-1/2 rounded-lg p-1.5 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600" aria-label="Tampilkan kata sandi">
                    <x-icon name="eye" class="h-4 w-4" />
                </button>
            </div>
            @error('password')
                <p class="field-error"><x-icon name="alert" class="mt-0.5 h-3 w-3 shrink-0" /> {{ $message }}</p>
            @enderror
        </div>

        <label class="flex cursor-pointer items-center gap-2.5 text-[12px] font-medium text-slate-600">
            <input type="checkbox" name="remember" value="1" @checked(old('remember')) class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
            Ingat saya di perangkat ini
        </label>

        <button type="submit" class="btn-primary w-full !py-3">
            Masuk ke dashboard
            <x-icon name="arrow-right" class="h-4 w-4" />
        </button>
    </form>

    <div class="mt-6 rounded-xl border border-slate-200 bg-white/70 px-4 py-3">
        <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-slate-400">Akun admin</p>
        <div class="mt-2 text-[11px] text-slate-600">
            <p><span class="font-semibold text-slate-800">Email:</span> admin@sekretariat.go.id</p>
            <p class="mt-1"><span class="font-semibold text-slate-800">Kata sandi:</span> <code class="rounded bg-slate-100 px-1.5 py-0.5 font-mono text-[10px]">password</code></p>
        </div>
    </div>
@endsection
