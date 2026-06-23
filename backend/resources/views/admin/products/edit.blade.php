@extends('admin.layout')

@section('title', 'Edit Product')

@section('content')
    <div class="topbar">
        <h1>Edit Product</h1>
        <a class="button secondary" href="{{ route('admin.products.index') }}">Back</a>
    </div>

    <div class="panel">
        <div class="panel-body">
            <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
                @method('PUT')
                @include('admin.products.partials.form', ['product' => $product])
            </form>
        </div>
    </div>
@endsection
