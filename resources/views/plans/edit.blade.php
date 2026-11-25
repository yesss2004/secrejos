@extends('layouts.app')

@section('title', 'Edit Perencanaan Subkegiatan')

@section('content')
    <x-page-header eyebrow="Perencanaan &amp; Anggaran" title="Edit Perencanaan" subtitle="{{ $plan->sub_activity_code }} &middot; {{ $plan->sub_activity_name }}" />
    @include('plans._form', ['action' => route('plans.update', $plan), 'method' => 'PUT', 'submitLabel' => 'Simpan Perubahan'])
@endsection
