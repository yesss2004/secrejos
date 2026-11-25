@extends('layouts.app')

@section('title', 'Edit Kendala')

@section('content')
    <x-page-header
        eyebrow="03. Kendala"
        title="Edit Kendala"
        subtitle="Perbarui penyebab, tindak lanjut, prioritas, atau status kendala."
        :back="route('issues.index')"
    />

    @include('issues._form', [
        'issue' => $issue,
        'action' => route('issues.update', $issue),
        'method' => 'PUT',
        'priorities' => $priorities,
        'statuses' => $statuses,
        'submitLabel' => 'Simpan Perubahan',
    ])
@endsection
