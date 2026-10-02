@extends('layouts.admin')

@section('title', 'Create User | Leivant Admin')

@section('admin')
    <p class="admin-eyebrow">Access Control</p>
    <h1 class="mt-2">Create User</h1>
    <div class="mt-6">@include('admin.users._form')</div>
@endsection
