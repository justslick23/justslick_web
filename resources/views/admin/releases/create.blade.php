@extends('layouts.admin')

@section('title', 'Add release')

@section('content')
    <h1 class="h3 mb-4">Add release</h1>

    <form method="POST" action="{{ route('admin.releases.store') }}" enctype="multipart/form-data" novalidate>
        @csrf
        @include('admin.releases._form')
    </form>
@endsection