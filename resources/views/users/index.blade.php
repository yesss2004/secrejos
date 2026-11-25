@extends('layouts.app')

@section('title', 'Manajemen Pengguna')

@section('content')
    <x-page-header
        eyebrow="Administrasi Sistem"
        title="Manajemen Pengguna"
        subtitle="Kelola akses staf sekretariat, peran, dan status keaktifan akun."
    >
        <a href="{{ route('users.create') }}" class="btn-primary">
            <x-icon name="plus" class="h-4 w-4" /> Tambah Pengguna
        </a>
    </x-page-header>

    <div class="stagger grid grid-cols-2 gap-3 sm:gap-4 xl:grid-cols-4">
        <div class="card flex items-center gap-3 p-4">
            <span class="icon-tile tone-slate h-10 w-10 rounded-lg"><x-icon name="users" class="h-[18px] w-[18px]" /></span>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Pengguna</p>
                <p class="text-xl font-extrabold leading-tight text-ink-900">{{ $stats['total'] }}</p>
            </div>
        </div>
        <div class="card flex items-center gap-3 p-4">
            <span class="icon-tile tone-violet h-10 w-10 rounded-lg"><x-icon name="shield" class="h-[18px] w-[18px]" /></span>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Admin</p>
                <p class="text-xl font-extrabold leading-tight text-ink-900">{{ $stats['admin'] }}</p>
            </div>
        </div>
        <div class="card flex items-center gap-3 p-4">
            <span class="icon-tile tone-emerald h-10 w-10 rounded-lg"><x-icon name="check-circle" class="h-[18px] w-[18px]" /></span>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Aktif</p>
                <p class="text-xl font-extrabold leading-tight text-ink-900">{{ $stats['active'] }}</p>
            </div>
        </div>
        <div class="card flex items-center gap-3 p-4">
            <span class="icon-tile tone-rose h-10 w-10 rounded-lg"><x-icon name="x-circle" class="h-[18px] w-[18px]" /></span>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Nonaktif</p>
                <p class="text-xl font-extrabold leading-tight text-ink-900">{{ $stats['inactive'] }}</p>
            </div>
        </div>
    </div>

    <div class="card mt-5 overflow-hidden">
        <form method="GET" action="{{ route('users.index') }}" class="filter-bar">
            <div class="relative min-w-0 flex-1 sm:max-w-xs">
                <x-icon name="search" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                <input type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Cari nama, email, atau jabatan..."
                       class="input !py-2.5 !pl-10 !text-[13px]">
            </div>

            <select name="role" class="select !w-auto !min-w-[140px] !py-2.5 !text-[13px]">
                <option value="">Semua peran</option>
                <option value="admin" @selected(($filters['role'] ?? '') === 'admin')>Admin</option>
                <option value="operator" @selected(in_array(($filters['role'] ?? ''), ['operator', 'staff'], true))>Operator Subbagian</option>
                <option value="verifikator" @selected(($filters['role'] ?? '') === 'verifikator')>Verifikator/PPK</option>
                <option value="pimpinan" @selected(($filters['role'] ?? '') === 'pimpinan')>Pimpinan/Sekretaris</option>
            </select>

            <select name="status" class="select !w-auto !min-w-[130px] !py-2.5 !text-[13px]">
                <option value="">Semua status</option>
                <option value="active" @selected(($filters['status'] ?? '') === 'active')>Aktif</option>
                <option value="inactive" @selected(($filters['status'] ?? '') === 'inactive')>Nonaktif</option>
            </select>

            <button type="submit" class="btn-primary btn-sm !px-4"><x-icon name="filter" class="h-3.5 w-3.5" /> Terapkan</button>

            @if(array_filter($filters))
                <a href="{{ route('users.index') }}" class="btn-ghost btn-sm text-slate-500">Reset</a>
            @endif

            <p class="ml-auto hidden text-[11px] text-slate-400 lg:block">{{ $users->total() }} pengguna terdaftar</p>
        </form>

        @if($users->count())
            <div class="overflow-x-auto">
                <table class="data-table min-w-[900px]">
                    <thead>
                        <tr>
                            <th class="w-[34%]">Pengguna</th>
                            <th>Jabatan</th>
                            <th>Kontak</th>
                            <th>Kontribusi</th>
                            <th>Peran</th>
                            <th>Status</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <x-avatar :user="$user" size="md" />
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-1.5">
                                                <p class="truncate text-[13px] font-bold text-slate-800">{{ $user->name }}</p>
                                                @if($user->id === auth()->id())
                                                    <span class="rounded bg-slate-100 px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-wide text-slate-500">Anda</span>
                                                @endif
                                            </div>
                                            <p class="truncate text-[11px] text-slate-400">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <p class="text-[12px] font-semibold text-slate-700">{{ $user->position ?? '-' }}</p>
                                </td>
                                <td>
                                    <p class="text-[11px] font-semibold text-slate-600">{{ $user->phone ?? '-' }}</p>
                                </td>
                                <td>
                                    <div class="flex items-center gap-3 text-[10px] font-semibold text-slate-500">
                                        <span class="flex items-center gap-1"><x-icon name="document" class="h-3.5 w-3.5 text-slate-400" /> {{ $user->reports_count }}</span>
                                        <span class="flex items-center gap-1"><x-icon name="mail" class="h-3.5 w-3.5 text-slate-400" /> {{ $user->letters_count }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge {{ $user->isAdmin() ? 'badge-violet' : 'badge-slate' }}">{{ $user->role_label }}</span>
                                </td>
                                <td>
                                    @if($user->is_active)
                                        <span class="badge badge-emerald"><span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Aktif</span>
                                    @else
                                        <span class="badge badge-rose"><span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span> Nonaktif</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('users.edit', $user) }}" class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-amber-600" title="Edit pengguna">
                                            <x-icon name="pencil" class="h-4 w-4" />
                                        </a>
                                        @if($user->id !== auth()->id())
                                            <form method="POST" action="{{ route('users.toggle', $user) }}" data-confirm="{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }} akun {{ $user->name }}?">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 {{ $user->is_active ? 'hover:text-rose-600' : 'hover:text-emerald-600' }}" title="{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                    <x-icon :name="$user->is_active ? 'x-circle' : 'check-circle'" class="h-4 w-4" />
                                                </button>
                                            </form>
                                        @endif
                                        @can('delete', $user)
                                            <form method="POST" action="{{ route('users.destroy', $user) }}" data-confirm="Hapus pengguna {{ $user->name }}? Tindakan ini tidak dapat dibatalkan.">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="rounded-lg p-2 text-slate-400 transition hover:bg-rose-50 hover:text-rose-600" title="Hapus pengguna">
                                                    <x-icon name="trash" class="h-4 w-4" />
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-100 px-5 py-4">
                {{ $users->links() }}
            </div>
        @else
            <x-empty-state icon="users" title="Pengguna tidak ditemukan" description="Tidak ada pengguna yang sesuai filter. Tambahkan pengguna baru atau ubah pencarian.">
                <a href="{{ route('users.create') }}" class="btn-primary btn-sm"><x-icon name="plus" class="h-3.5 w-3.5" /> Tambah Pengguna</a>
            </x-empty-state>
        @endif
    </div>
@endsection
