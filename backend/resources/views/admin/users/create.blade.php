@extends('admin.layout')

@section('title', 'Create User')

@section('content')
    <div class="topbar">
        <h1>Create User</h1>

        <a class="button secondary" href="{{ route('admin.users.index') }}">
            Back
        </a>
    </div>

    <div class="panel">
        <div class="panel-body">

            <form method="POST"
                  action="{{ route('admin.users.store') }}"
                  enctype="multipart/form-data">

                @include('admin.users.partials.form', ['user' => null])

            </form>

        </div>
    </div>
@endsection