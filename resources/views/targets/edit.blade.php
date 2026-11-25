@extends('layouts.app')

@section('title', 'Edit Target')

@section('content')
    <x-page-header
        eyebrow="Modul Target &amp; Jadwal"
        title="Edit Target"
        subtitle="Perbarui informasi target, prioritas, atau tenggat penyelesaian."
        :back="route('targets.index')"
    />

    @include('targets._form', [
        'target' => $target,
        'action' => route('targets.update', $target),
        'method' => 'PUT',
        'priorities' => $priorities,
        'submitLabel' => 'Simpan Perubahan',
    ])
@endsection
