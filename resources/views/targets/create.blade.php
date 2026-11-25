@extends('layouts.app')

@section('title', 'Tambah Target')

@section('content')
    <x-page-header
        eyebrow="Modul Target &amp; Jadwal"
        title="Tambah Target"
        subtitle="Catat pekerjaan, tentukan prioritas, dan atur tenggat penyelesaian."
        :back="route('targets.index')"
    />

    @include('targets._form', [
        'target' => null,
        'action' => route('targets.store'),
        'method' => 'POST',
        'priorities' => $priorities,
        'submitLabel' => 'Simpan Target',
    ])
@endsection
