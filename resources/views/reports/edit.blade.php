@extends('layouts.app')

@section('title', 'Edit Laporan')

@section('content')
    <x-page-header
        eyebrow="Manajemen Laporan"
        title="Edit Laporan"
        subtitle="Perbarui informasi laporan selama statusnya masih menunggu review."
        :back="route('reports.show', $report)"
    />

    @include('reports._form', [
        'action' => route('reports.update', $report),
        'method' => 'PUT',
        'submitLabel' => 'Simpan Perubahan',
        'report' => $report,
    ])
@endsection
