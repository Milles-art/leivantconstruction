@extends('layouts.admin')

@section('title', 'Edit User | Leivant Admin')

@section('admin')
    <p class="admin-eyebrow">Access Control</p>
    <h1 class="mt-2">Edit User</h1>
    <div class="mt-6">@include('admin.users._form', ['user' => $user])</div>
@endsection
