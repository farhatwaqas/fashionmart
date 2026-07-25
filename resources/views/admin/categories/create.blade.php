@extends('layouts.admin')

@section('title', 'Create Category')

@section('content')
    <form method="POST" action="{{ route('admin.categories.store') }}" enctype="multipart/form-data">
        @csrf
        @include('admin.categories._form')
    </form>
@endsection
