@extends('layouts.admin')

@section('title', 'Create Provider | Leivant')

@section('admin')
    <h1 class="text-5xl font-extrabold text-white">Create Provider</h1>
    <div class="mt-8">@include('admin.providers._form')</div>
@endsection
