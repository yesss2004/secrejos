@extends('layouts.app')

@section('title', 'Catat Surat')

@section('content')
    <x-page-header
        eyebrow="Persuratan & Arsip"
        title="Catat Surat Baru"
        subtitle="Registrasikan surat masuk atau keluar ke dalam arsip digital sekretariat."
        :back="route('letters.index')"
    />

    @include('letters._form', [
        'action' => route('letters.store'),
        'method' => 'POST',
        'submitLabel' => 'Simpan ke Arsip',
    ])
@endsection
