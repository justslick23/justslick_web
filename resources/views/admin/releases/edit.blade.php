@extends('layouts.admin')

@section('title', 'Edit release')

@section('content')
    <h1 class="h3 mb-4">Edit release</h1>

    <form method="POST" action="{{ route('admin.releases.update', $release) }}" enctype="multipart/form-data" novalidate>
        @csrf
        @method('PUT')
        @include('admin.releases._form')
    </form>
@endsection