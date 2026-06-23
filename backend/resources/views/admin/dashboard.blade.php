@extends('admin.layout')

@section('title', 'Dashboard')

@section('content')
    <div class="topbar">
        <div>
            <h1>Dashboard</h1>
            <p class="muted">Manage your categories and products from Blade.</p>
        </div>
    </div>

    <div class="grid stats">
        <a class="stat" href="{{ route('admin.categories.index') }}">
            Categories
            <strong>{{ \App\Models\Category::count() }}</strong>
        </a>
        <a class="stat" href="{{ route('admin.products.index') }}">
            Products
            <strong>{{ \App\Models\Product::count() }}</strong>
        </a>
    </div>
@endsection
