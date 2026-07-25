@extends('layouts.admin')

@section('title', 'Create Banner')

@section('content')
    <form method="POST" action="{{ route('admin.banners.store') }}" enctype="multipart/form-data">
        @csrf
        @include('admin.banners._form')
    </form>
@endsection
