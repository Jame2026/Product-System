@extends('admin.layout')

@section('title', 'Create Category')

@section('content')
    <div class="topbar">
        <h1>Create Category</h1>
        <a class="button secondary" href="{{ route('admin.categories.index') }}">Back</a>
    </div>

    <div class="panel">
        <div class="panel-body">
            <form method="POST" action="{{ route('admin.categories.store') }}">
                @include('admin.categories.partials.form', ['category' => null])
            </form>
        </div>
    </div>
@endsection
