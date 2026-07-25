@extends('layouts.admin')

@section('title', 'Edit Category')

@section('content')
    <form method="POST" action="{{ route('admin.categories.update', $category) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.categories._form', ['category' => $category])
    </form>
@endsection
