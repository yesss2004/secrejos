@extends('layouts.app')

@section('title', 'Edit Surat')

@section('content')
    <x-page-header
        eyebrow="Persuratan & Arsip"
        title="Edit Arsip Surat"
        subtitle="Perbarui informasi surat dan status tindak lanjutnya."
        :back="route('letters.show', $letter)"
    />

    @include('letters._form', [
        'action' => route('letters.update', $letter),
        'method' => 'PUT',
        'submitLabel' => 'Simpan Perubahan',
    ])
@endsection
