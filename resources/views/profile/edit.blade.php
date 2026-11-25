@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
    <x-page-header
        eyebrow="Pengaturan Akun"
        title="Profil Saya"
        subtitle="Kelola informasi pribadi dan keamanan akun sekretariat Anda."
    />

    <div class="relative mb-6 overflow-hidden rounded-2xl bg-gradient-to-br from-ink-900 via-ink-800 to-[#0d2a2f] p-6 text-white shadow-[0_20px_50px_-25px_rgba(7,17,29,0.55)] sm:p-7">
        <div class="absolute -right-16 -top-16 h-56 w-56 rounded-full bg-emerald-400/15 blur-3xl"></div>
        <div class="absolute bottom-0 left-1/3 h-40 w-40 rounded-full bg-teal-400/10 blur-2xl"></div>

        <div class="relative flex flex-col gap-5 sm:flex-row sm:items-center">
            <div class="relative">
                <x-avatar :user="$user" size="xl" class="!h-20 !w-20 !text-2xl ring-4 ring-white/10" />
                <span class="absolute -bottom-1 -right-1 flex h-6 w-6 items-center justify-center rounded-full bg-emerald-500 ring-4 ring-ink-900">
                    <x-icon name="check" class="h-3 w-3" />
                </span>
            </div>

            <div class="min-w-0 flex-1">
                <div class="flex flex-wrap items-center gap-2">
                    <h2 class="text-[22px] font-extrabold tracking-tight">{{ $user->name }}</h2>
                    <span class="rounded-full border border-white/15 bg-white/10 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-emerald-300">{{ $user->role_label }}</span>
                </div>
                <p class="mt-1 text-[13px] text-slate-400">{{ $user->position ?? 'Staf Sekretariat' }}</p>
                <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-1.5 text-[11px] text-slate-400">
                    <span class="flex items-center gap-1.5"><x-icon name="mail" class="h-3.5 w-3.5 text-emerald-400" /> {{ $user->email }}</span>
                    <span class="flex items-center gap-1.5"><x-icon name="phone" class="h-3.5 w-3.5 text-emerald-400" /> {{ $user->phone ?? 'Belum diisi' }}</span>
                    <span class="flex items-center gap-1.5"><x-icon name="calendar" class="h-3.5 w-3.5 text-emerald-400" /> Bergabung {{ $user->created_at->locale('id')->translatedFormat('F Y') }}</span>
                </div>
            </div>

            <div class="flex gap-3 sm:flex-col">
                <div class="flex-1 rounded-xl border border-white/10 bg-white/[0.05] px-4 py-2.5 text-center">
                    <p class="text-xl font-extrabold">{{ $user->reports()->count() }}</p>
                    <p class="text-[9px] font-bold uppercase tracking-wider text-slate-400">Laporan</p>
                </div>
                <div class="flex-1 rounded-xl border border-white/10 bg-white/[0.05] px-4 py-2.5 text-center">
                    <p class="text-xl font-extrabold">{{ $user->letters()->count() }}</p>
                    <p class="text-[9px] font-bold uppercase tracking-wider text-slate-400">Surat</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-5 xl:grid-cols-3">
        <div class="space-y-5 xl:col-span-2">
            <div class="card p-5 sm:p-6">
                <div class="mb-5 flex items-start gap-3">
                    <span class="icon-tile tone-emerald h-9 w-9 rounded-lg"><x-icon name="user" class="h-4 w-4" /></span>
                    <div>
                        <h2 class="text-[15px] font-extrabold text-ink-900">Informasi Pribadi</h2>
                        <p class="mt-0.5 text-[11px] text-slate-500">Perbarui data yang tampil pada profil dan dokumen laporan.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label for="name" class="label">Nama lengkap</label>
                            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required maxlength="100"
                                   class="input @error('name') !border-rose-400 @enderror">
                            @error('name')<p class="field-error"><x-icon name="alert" class="mt-0.5 h-3 w-3 shrink-0" /> {{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="email" class="label">Alamat email</label>
                            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required maxlength="150"
                                   class="input @error('email') !border-rose-400 @enderror">
                            @error('email')<p class="field-error"><x-icon name="alert" class="mt-0.5 h-3 w-3 shrink-0" /> {{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label for="position" class="label">Jabatan</label>
                            <input id="position" name="position" type="text" value="{{ old('position', $user->position) }}" maxlength="100"
                                   placeholder="Contoh: Staf Administrasi" class="input @error('position') !border-rose-400 @enderror">
                            @error('position')<p class="field-error"><x-icon name="alert" class="mt-0.5 h-3 w-3 shrink-0" /> {{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="phone" class="label">Nomor telepon</label>
                            <input id="phone" name="phone" type="text" value="{{ old('phone', $user->phone) }}" maxlength="30"
                                   placeholder="08xx-xxxx-xxxx" class="input @error('phone') !border-rose-400 @enderror">
                            @error('phone')<p class="field-error"><x-icon name="alert" class="mt-0.5 h-3 w-3 shrink-0" /> {{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="flex justify-end border-t border-slate-100 pt-4">
                        <button type="submit" class="btn-primary"><x-icon name="save" class="h-4 w-4" /> Simpan Profil</button>
                    </div>
                </form>
            </div>

            <div class="card p-5 sm:p-6">
                <div class="mb-5 flex items-start gap-3">
                    <span class="icon-tile tone-amber h-9 w-9 rounded-lg"><x-icon name="lock" class="h-4 w-4" /></span>
                    <div>
                        <h2 class="text-[15px] font-extrabold text-ink-900">Keamanan Kata Sandi</h2>
                        <p class="mt-0.5 text-[11px] text-slate-500">Gunakan kombinasi yang kuat dan unik untuk melindungi akun.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('profile.password') }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="current_password" class="label">Kata sandi saat ini</label>
                        <input id="current_password" name="current_password" type="password" required autocomplete="current-password"
                               class="input @error('current_password') !border-rose-400 @enderror">
                        @error('current_password')<p class="field-error"><x-icon name="alert" class="mt-0.5 h-3 w-3 shrink-0" /> {{ $message }}</p>@enderror
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label for="password" class="label">Kata sandi baru</label>
                            <input id="password" name="password" type="password" required autocomplete="new-password"
                                   placeholder="Minimal 8 karakter" class="input @error('password') !border-rose-400 @enderror">
                            @error('password')<p class="field-error"><x-icon name="alert" class="mt-0.5 h-3 w-3 shrink-0" /> {{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="password_confirmation" class="label">Konfirmasi kata sandi baru</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                                   class="input">
                        </div>
                    </div>

                    <div class="flex justify-end border-t border-slate-100 pt-4">
                        <button type="submit" class="btn-dark"><x-icon name="key" class="h-4 w-4" /> Perbarui Kata Sandi</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="space-y-5">
            <div class="card p-5">
                <h2 class="text-[13px] font-extrabold text-ink-900">Ringkasan Akun</h2>
                <dl class="mt-3.5 space-y-2.5 text-[11px]">
                    <div class="flex items-center justify-between">
                        <dt class="text-slate-400">Peran</dt>
                        <dd><x-status-badge :tone="$user->isAdmin() ? 'violet' : 'slate'" :label="$user->role_label" /></dd>
                    </div>
                    <div class="flex items-center justify-between border-t border-slate-100 pt-2.5">
                        <dt class="text-slate-400">Status akun</dt>
                        <dd><x-status-badge :tone="$user->is_active ? 'emerald' : 'rose'" :label="$user->is_active ? 'Aktif' : 'Nonaktif'" /></dd>
                    </div>
                    <div class="flex items-center justify-between border-t border-slate-100 pt-2.5">
                        <dt class="text-slate-400">Bergabung</dt>
                        <dd class="font-semibold text-slate-700">{{ $user->created_at->format('d M Y') }}</dd>
                    </div>
                    <div class="flex items-center justify-between border-t border-slate-100 pt-2.5">
                        <dt class="text-slate-400">Login terakhir</dt>
                        <dd class="font-semibold text-slate-700">{{ $user->updated_at->diffForHumans() }}</dd>
                    </div>
                </dl>
            </div>

            <div class="card p-5">
                <h2 class="text-[13px] font-extrabold text-ink-900">Aktivitas Saya</h2>
                <div class="mt-3 space-y-2.5">
                    @forelse($user->activities()->latest()->take(4)->get() as $activity)
                        <div class="flex items-start gap-2.5">
                            <span class="mt-0.5 h-1.5 w-1.5 shrink-0 rounded-full bg-emerald-500"></span>
                            <div>
                                <p class="text-[11px] leading-relaxed text-slate-600">{{ $activity->description }}</p>
                                <p class="mt-0.5 text-[9px] text-slate-400">{{ $activity->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-[11px] text-slate-400">Belum ada aktivitas tercatat.</p>
                    @endforelse
                </div>
                <a href="{{ route('activities.index') }}" class="btn-secondary btn-sm mt-4 w-full">Lihat semua aktivitas</a>
            </div>

            <div class="rounded-2xl border border-emerald-200/60 bg-gradient-to-br from-emerald-50 to-teal-50/40 p-5">
                <div class="flex items-center gap-2">
                    <x-icon name="shield" class="h-4 w-4 text-emerald-600" />
                    <h2 class="text-[12px] font-extrabold text-emerald-900">Akun terlindungi</h2>
                </div>
                <p class="mt-1.5 text-[11px] leading-relaxed text-emerald-800/80">Data akun Anda tersimpan aman. Segera hubungi administrator jika menemukan aktivitas mencurigakan.</p>
            </div>
        </div>
    </div>
@endsection
