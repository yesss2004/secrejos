@extends('layouts.app')

@section('title', 'Edit Pengguna')

@section('content')
    <x-page-header
        eyebrow="Administrasi Sistem"
        title="Edit Data Pengguna"
        subtitle="Perbarui profil, peran, dan status akun pengguna."
        :back="route('users.index')"
    />

    @include('users._form', [
        'action' => route('users.update', $user),
        'method' => 'PUT',
        'submitLabel' => 'Simpan Perubahan',
        'user' => $user,
    ])
@endsection
