@extends('layouts.admin')

@section('title', 'Edit Provider | Leivant')

@section('admin')
    <h1 class="text-5xl font-extrabold text-white">Edit Provider</h1>
    <div class="mt-8">@include('admin.providers._form', ['provider' => $provider])</div>
@endsection
