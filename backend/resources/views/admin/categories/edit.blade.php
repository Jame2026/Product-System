@extends('admin.layout')

@section('title', 'Edit Category')

@section('content')
    <div class="topbar">
        <h1>Edit Category</h1>
        <a class="button secondary" href="{{ route('admin.categories.index') }}">Back</a>
    </div>

    <div class="panel">
        <div class="panel-body">
            <form method="POST" action="{{ route('admin.categories.update', $category) }}">
                @method('PUT')
                @include('admin.categories.partials.form', ['category' => $category])
            </form>
        </div>
    </div>
@endsection
