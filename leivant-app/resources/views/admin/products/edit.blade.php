@extends('layouts.admin')

@section('title', 'Edit Product | Leivant')

@section('admin')
    <h1 class="text-5xl font-extrabold text-white">Edit Product</h1>
    <div class="mt-8">
        @include('admin.products._form', ['product' => $product])
    </div>
@endsection
