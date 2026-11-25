@extends('layouts.app')

@section('title', 'Buat Perencanaan Subkegiatan')

@section('content')
    <x-page-header eyebrow="Perencanaan &amp; Anggaran" title="Buat Perencanaan" subtitle="Isi data subkegiatan, indikator, dan rincian anggaran sebelum dan sesudah perubahan." />
    @include('plans._form', ['action' => route('plans.store'), 'method' => 'POST', 'submitLabel' => 'Simpan Perencanaan'])
@endsection
