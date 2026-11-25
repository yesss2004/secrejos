@extends('layouts.guest')

@section('title', 'Daftar')

@section('switch')
    Sudah punya akun?
    <a href="{{ route('login') }}" class="font-bold text-emerald-700 hover:text-emerald-800">Masuk di sini</a>
@endsection

@section('content')
    <div class="mb-7">
        <p class="mb-2 text-[11px] font-bold uppercase tracking-[0.18em] text-emerald-600">Buat akun baru</p>
        <h1 class="text-[30px] font-extrabold leading-tight tracking-[-0.03em] text-ink-900">Bergabung ke e-Secretariat</h1>
        <p class="mt-2 text-sm leading-relaxed text-slate-500">Daftarkan diri Anda sebagai staf sekretariat untuk mulai mengelola laporan dan arsip.</p>
    </div>

    <form method="POST" action="{{ route('register.store') }}" class="space-y-4">
        @csrf

        <div>
            <label for="name" class="label">Nama lengkap</label>
            <div class="relative">
                <x-icon name="user" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus autocomplete="name"
                       placeholder="Nama lengkap Anda"
                       class="input !py-3 !pl-10 @error('name') !border-rose-400 @enderror">
            </div>
            @error('name')
                <p class="field-error"><x-icon name="alert" class="mt-0.5 h-3 w-3 shrink-0" /> {{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" class="label">Alamat email</label>
            <div class="relative">
                <x-icon name="mail" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="username"
                       placeholder="nama@sekretariat.go.id"
                       class="input !py-3 !pl-10 @error('email') !border-rose-400 @enderror">
            </div>
            @error('email')
                <p class="field-error"><x-icon name="alert" class="mt-0.5 h-3 w-3 shrink-0" /> {{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="position" class="label">Jabatan <span class="font-normal text-slate-400">(opsional)</span></label>
            <div class="relative">
                <x-icon name="briefcase" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                <input id="position" name="position" type="text" value="{{ old('position') }}"
                       placeholder="Contoh: Staf Administrasi"
                       class="input !py-3 !pl-10 @error('position') !border-rose-400 @enderror">
            </div>
            @error('position')
                <p class="field-error"><x-icon name="alert" class="mt-0.5 h-3 w-3 shrink-0" /> {{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label for="password" class="label">Kata sandi</label>
                <div class="relative">
                    <x-icon name="lock" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                    <input id="password" name="password" type="password" required autocomplete="new-password"
                           placeholder="Min. 8 karakter"
                           class="input !py-3 !pl-10 @error('password') !border-rose-400 @enderror">
                </div>
                @error('password')
                    <p class="field-error"><x-icon name="alert" class="mt-0.5 h-3 w-3 shrink-0" /> {{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="label">Ulangi kata sandi</label>
                <div class="relative">
                    <x-icon name="lock" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                    <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                           placeholder="Ulangi kata sandi"
                           class="input !py-3 !pl-10">
                </div>
            </div>
        </div>

        <button type="submit" class="btn-primary w-full !py-3">
            Buat akun saya
            <x-icon name="arrow-right" class="h-4 w-4" />
        </button>

        <p class="text-center text-[11px] leading-relaxed text-slate-400">
            Dengan mendaftar, Anda menyetujui kebijakan penggunaan data internal sekretariat.
        </p>
    </form>
@endsection
