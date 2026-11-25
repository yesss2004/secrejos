@php
    $editing = isset($user) && $user;
    $selectedRole = old('role', $editing && $user->role === 'staff' ? 'operator' : ($editing ? $user->role : 'operator'));
    $isActive = old('is_active', $editing ? $user->is_active : true);
@endphp

<form method="POST" action="{{ $action }}" class="grid grid-cols-1 gap-5 xl:grid-cols-3">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    <div class="space-y-5 xl:col-span-2">
        <div class="card p-5 sm:p-6">
            <div class="mb-5 flex items-start gap-3">
                <span class="icon-tile tone-violet h-9 w-9 rounded-lg"><x-icon name="user" class="h-4 w-4" /></span>
                <div>
                    <h2 class="text-[15px] font-extrabold text-ink-900">Identitas Pengguna</h2>
                    <p class="mt-0.5 text-[11px] text-slate-500">Informasi dasar akun staf sekretariat.</p>
                </div>
            </div>

            <div class="space-y-5">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="name" class="label">Nama lengkap <span class="text-rose-500">*</span></label>
                        <input id="name" name="name" type="text" value="{{ old('name', $editing ? $user->name : '') }}" required maxlength="100"
                               placeholder="Nama lengkap pengguna" class="input @error('name') !border-rose-400 @enderror">
                        @error('name')<p class="field-error"><x-icon name="alert" class="mt-0.5 h-3 w-3 shrink-0" /> {{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="email" class="label">Alamat email <span class="text-rose-500">*</span></label>
                        <input id="email" name="email" type="email" value="{{ old('email', $editing ? $user->email : '') }}" required maxlength="150"
                               placeholder="nama@sekretariat.go.id" class="input @error('email') !border-rose-400 @enderror">
                        @error('email')<p class="field-error"><x-icon name="alert" class="mt-0.5 h-3 w-3 shrink-0" /> {{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="position" class="label">Jabatan</label>
                        <input id="position" name="position" type="text" value="{{ old('position', $editing ? $user->position : '') }}" maxlength="100"
                               placeholder="Contoh: Staf Administrasi" class="input @error('position') !border-rose-400 @enderror">
                        @error('position')<p class="field-error"><x-icon name="alert" class="mt-0.5 h-3 w-3 shrink-0" /> {{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="phone" class="label">Nomor telepon</label>
                        <input id="phone" name="phone" type="text" value="{{ old('phone', $editing ? $user->phone : '') }}" maxlength="30"
                               placeholder="08xx-xxxx-xxxx" class="input @error('phone') !border-rose-400 @enderror">
                        @error('phone')<p class="field-error"><x-icon name="alert" class="mt-0.5 h-3 w-3 shrink-0" /> {{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <span class="label">Peran pengguna <span class="text-rose-500">*</span></span>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        @foreach([
                            ['value' => 'operator', 'label' => 'Admin/Operator Subbagian', 'desc' => 'Input data dan unggah SPJ', 'icon' => 'user', 'tone' => 'tone-sky'],
                            ['value' => 'verifikator', 'label' => 'Verifikator/PPK', 'desc' => 'Validasi SPJ dan kendala', 'icon' => 'check-circle', 'tone' => 'tone-amber'],
                            ['value' => 'pimpinan', 'label' => 'Pimpinan/Sekretaris', 'desc' => 'Melihat dashboard dan laporan', 'icon' => 'chart', 'tone' => 'tone-emerald'],
                            ['value' => 'admin', 'label' => 'Administrator', 'desc' => 'Mengelola pengguna dan seluruh aplikasi', 'icon' => 'shield', 'tone' => 'tone-violet'],
                        ] as $option)
                            <label class="relative cursor-pointer">
                                <input type="radio" name="role" value="{{ $option['value'] }}" class="peer sr-only" @checked($selectedRole === $option['value'])>
                                <div class="radio-card flex items-center gap-3 rounded-xl border border-slate-200 bg-white p-3.5 transition hover:border-slate-300 peer-checked:border-emerald-500 peer-checked:bg-emerald-50/50 peer-checked:ring-1 peer-checked:ring-emerald-500">
                                    <span class="icon-tile h-9 w-9 rounded-lg {{ $option['tone'] }}"><x-icon :name="$option['icon']" class="h-4 w-4" /></span>
                                    <div>
                                        <p class="text-[12px] font-bold text-slate-800">{{ $option['label'] }}</p>
                                        <p class="text-[10px] text-slate-400">{{ $option['desc'] }}</p>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('role')<p class="field-error"><x-icon name="alert" class="mt-0.5 h-3 w-3 shrink-0" /> {{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <div class="card p-5 sm:p-6">
            <div class="mb-5 flex items-start gap-3">
                <span class="icon-tile tone-amber h-9 w-9 rounded-lg"><x-icon name="lock" class="h-4 w-4" /></span>
                <div>
                    <h2 class="text-[15px] font-extrabold text-ink-900">{{ $editing ? 'Keamanan Akun' : 'Kata Sandi Awal' }}</h2>
                    <p class="mt-0.5 text-[11px] text-slate-500">{{ $editing ? 'Biarkan kosong bila tidak ingin mengganti kata sandi.' : 'Tentukan kata sandi untuk akses pertama.' }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="password" class="label">Kata sandi {{ $editing ? '' : '*' }}</label>
                    <input id="password" name="password" type="password" {{ $editing ? '' : 'required' }} autocomplete="new-password"
                           placeholder="{{ $editing ? 'Kosongkan bila tidak diubah' : 'Minimal 8 karakter' }}"
                           class="input @error('password') !border-rose-400 @enderror">
                    @error('password')<p class="field-error"><x-icon name="alert" class="mt-0.5 h-3 w-3 shrink-0" /> {{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="password_confirmation" class="label">Konfirmasi kata sandi</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                           placeholder="Ulangi kata sandi" class="input">
                </div>
            </div>

            @if($editing)
                <label class="mt-4 flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 bg-slate-50/60 p-3.5">
                    <input type="checkbox" name="is_active" value="1" @checked($isActive) class="mt-0.5 h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                    <span>
                        <span class="block text-[12px] font-bold text-slate-800">Akun aktif</span>
                        <span class="mt-0.5 block text-[10px] leading-relaxed text-slate-500">Pengguna dapat masuk dan mengakses aplikasi. Nonaktifkan untuk menangguhkan akses tanpa menghapus data.</span>
                    </span>
                </label>
            @else
                <label class="mt-4 flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 bg-slate-50/60 p-3.5">
                    <input type="checkbox" name="is_active" value="1" @checked($isActive) class="mt-0.5 h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                    <span>
                        <span class="block text-[12px] font-bold text-slate-800">Langsung aktifkan akun</span>
                        <span class="mt-0.5 block text-[10px] leading-relaxed text-slate-500">Pengguna dapat segera masuk setelah akun dibuat.</span>
                    </span>
                </label>
            @endif
        </div>

        <div class="flex flex-col-reverse items-stretch gap-2 sm:flex-row sm:items-center sm:justify-between">
            <a href="{{ $editing ? route('users.index') : route('users.index') }}" class="btn-secondary">Batal</a>
            <button type="submit" class="btn-primary">
                <x-icon name="save" class="h-4 w-4" /> {{ $submitLabel }}
            </button>
        </div>
    </div>

    <div class="space-y-5">
        <div class="card p-5">
            <h3 class="text-[13px] font-extrabold text-ink-900">Hak Akses</h3>
            <div class="mt-3 space-y-2.5">
                @foreach([
                    ['label' => 'Melihat dashboard & laporan', 'roles' => ['admin', 'operator', 'verifikator', 'pimpinan']],
                    ['label' => 'Input data dan unggah SPJ', 'roles' => ['admin', 'operator']],
                    ['label' => 'Mengelola surat, target, dan kendala', 'roles' => ['admin', 'operator']],
                    ['label' => 'Verifikasi SPJ dan kendala', 'roles' => ['admin', 'verifikator']],
                    ['label' => 'Mengelola pengguna dan peran', 'roles' => ['admin']],
                ] as $permission)
                    @php($hasPermission = in_array($selectedRole, $permission['roles'], true) || ($selectedRole === 'operator' && in_array('staff', $permission['roles'], true)))
                    <div class="flex items-center gap-2">
                        <span class="flex h-4 w-4 items-center justify-center rounded-full {{ $hasPermission ? 'bg-emerald-100 text-emerald-600' : 'bg-slate-100 text-slate-300' }}">
                            <x-icon name="check" class="h-2.5 w-2.5" />
                        </span>
                        <p class="text-[11px] {{ $hasPermission ? 'text-slate-600' : 'text-slate-300' }}">{{ $permission['label'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        @if($editing)
            <div class="card p-5">
                <div class="flex items-center gap-3">
                    <x-avatar :user="$user" size="lg" />
                    <div class="min-w-0">
                        <p class="truncate text-[13px] font-extrabold text-ink-900">{{ $user->name }}</p>
                        <p class="truncate text-[10px] text-slate-400">{{ $user->email }}</p>
                        <div class="mt-1"><x-status-badge :tone="$user->isAdmin() ? 'violet' : 'slate'" :label="$user->role_label" /></div>
                    </div>
                </div>
                <div class="mt-4 grid grid-cols-2 gap-2 border-t border-slate-100 pt-3">
                    <div class="rounded-lg bg-slate-50 p-2.5 text-center">
                        <p class="text-lg font-extrabold text-ink-900">{{ $user->reports()->count() }}</p>
                        <p class="text-[9px] font-bold uppercase tracking-wider text-slate-400">Laporan</p>
                    </div>
                    <div class="rounded-lg bg-slate-50 p-2.5 text-center">
                        <p class="text-lg font-extrabold text-ink-900">{{ $user->letters()->count() }}</p>
                        <p class="text-[9px] font-bold uppercase tracking-wider text-slate-400">Surat</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="card bg-gradient-to-br from-amber-50/80 to-orange-50/30 p-5">
            <div class="flex items-center gap-2">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-amber-500 text-white"><x-icon name="info" class="h-3.5 w-3.5" /></span>
                <h3 class="text-[13px] font-extrabold text-amber-900">Catatan keamanan</h3>
            </div>
            <p class="mt-2 text-[11px] leading-relaxed text-amber-800/90">Gunakan kata sandi kuat minimal 8 karakter. Nonaktifkan akun yang tidak lagi bertugas daripada menghapusnya agar riwayat data tetap utuh.</p>
        </div>
    </div>
</form>
