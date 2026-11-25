@extends('layouts.app')

@section('title', 'Tambah Pengguna')

@section('content')
    <x-page-header
        eyebrow="Administrasi Sistem"
        title="Tambah Pengguna Baru"
        subtitle="Buat akun staf sekretariat dan tentukan hak aksesnya."
        :back="route('users.index')"
    />

    @include('users._form', [
        'action' => route('users.store'),
        'method' => 'POST',
        'submitLabel' => 'Simpan Pengguna',
        'user' => null,
    ])
@endsection
