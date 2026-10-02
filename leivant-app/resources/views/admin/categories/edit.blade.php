@extends('layouts.admin')
@section('title', 'Edit Category | Leivant Admin')
@section('admin')
    <p class="admin-eyebrow">Marketplace Taxonomy</p><h1 class="mt-2">Edit Category</h1><div class="mt-6">@include('admin.categories._form', ['category' => $category])</div>
@endsection
