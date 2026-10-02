@extends('layouts.admin')

@section('title', 'Create Product | Leivant')

@section('admin')
    <h1 class="text-5xl font-extrabold text-white">Create Product</h1>
    <div class="mt-8">
        @include('admin.products._form')
    </div>
@endsection
