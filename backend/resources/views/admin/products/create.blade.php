@extends('admin.layout')

@section('title', 'Create Product')

@section('content')
    <div class="topbar">
        <h1>Create Product</h1>
        <a class="button secondary" href="{{ route('admin.products.index') }}">Back</a>
    </div>

    <div class="panel">
        <div class="panel-body">
            <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
                @include('admin.products.partials.form', ['product' => null])
            </form>
        </div>
    </div>
@endsection
