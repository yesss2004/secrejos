@extends('layouts.app')

@section('title', 'Catat Kendala')

@section('content')
    <x-page-header
        eyebrow="03. Kendala"
        title="Catat Kendala"
        subtitle="Isi hambatan, penyebab, prioritas, dan rencana tindak lanjut."
        :back="route('issues.index')"
    />

    @include('issues._form', [
        'issue' => null,
        'action' => route('issues.store'),
        'method' => 'POST',
        'priorities' => $priorities,
        'statuses' => ['terbuka' => 'Belum ditangani', 'ditangani' => 'Sedang ditangani', 'selesai' => 'Selesai'],
        'submitLabel' => 'Simpan Kendala',
    ])
@endsection
