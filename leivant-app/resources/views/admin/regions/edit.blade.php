@extends('layouts.admin')
@section('title', 'Edit Region | Leivant Admin')
@section('admin')
    <p class="admin-eyebrow">Discovery Taxonomy</p><h1 class="mt-2">Edit Region</h1><div class="mt-6">@include('admin.regions._form', ['region' => $region])</div>
@endsection
