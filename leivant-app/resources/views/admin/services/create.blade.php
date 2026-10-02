@extends('layouts.admin')

@section('title', 'Create Service | Leivant')

@section('admin')
    <h1 class="text-5xl font-extrabold text-white">Create Service</h1>
    <div class="mt-8">@include('admin.services._form')</div>
@endsection
