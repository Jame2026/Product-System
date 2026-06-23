@extends('admin.layout')

@section('title', 'Edit User')

@section('content')
    <div class="topbar">
        <h1>Edit User</h1>

        <a class="button secondary" href="{{ route('admin.users.index') }}">
            Back
        </a>
    </div>

    <div class="panel">
        <div class="panel-body">

            <form method="POST"
                  action="{{ route('admin.users.update', $user) }}"
                  enctype="multipart/form-data">

                @method('PUT')

                @include('admin.users.partials.form', ['user' => $user])

            </form>

        </div>
    </div>
@endsection