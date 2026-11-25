@extends('layouts.app')

@section('title', 'Buat Laporan')

@section('content')
    <x-page-header
        eyebrow="Manajemen Laporan"
        title="Buat Laporan Baru"
        subtitle="Lengkapi informasi laporan dengan teliti. Laporan akan masuk ke antrean review administrator."
        :back="route('reports.index')"
    />

    @include('reports._form', [
        'action' => route('reports.store'),
        'method' => 'POST',
        'submitLabel' => 'Submit Laporan',
        'report' => null,
    ])
@endsection
