@extends('layouts.admin')

@section('title', 'Create Product')

@section('content')
    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
        @csrf
        @include('admin.products._form')
    </form>
@endsection
