@extends('admin.layout')

@section('title', 'Users')

@section('content')
<div class="topbar">
    <h1>Users</h1>

    <div>
        <a class="button" href="{{ route('admin.users.create') }}">
            New User
        </a>
    </div>
</div>

<div class="panel">

    @if(auth()->user() && auth()->user()->role === 'admin' && isset($admins) && $admins->count())
        <h2>Admins</h2>

        <table>
            <thead>
                <tr>
                    <th>Avatar</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($admins as $admin)
                <tr>
                    <td>
                        @if ($admin->avatar)
                            <img class="thumb" src="{{ asset('storage/'.$admin->avatar) }}" alt="{{ $admin->name }}">
                        @else
                            <span class="muted">No image</span>
                        @endif
                    </td>

                    <td>{{ $admin->name }}</td>
                    <td>{{ $admin->email }}</td>
                    <td>{{ ucfirst($admin->role) }}</td>
                    <td>
                        <span @class([
                            'badge',
                            'on' => $admin->is_active,
                            'off' => ! $admin->is_active
                        ])>
                            {{ $admin->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td>{{ optional($admin->created_at)->format('Y-m-d') }}</td>
                    <td>
                        <div class="actions">
                            <a class="button secondary" href="{{ route('admin.users.edit', $admin) }}">Edit</a>

                            <form method="POST" action="{{ route('admin.users.destroy', $admin) }}" onsubmit="return confirm('Delete this user?')">
                                @csrf
                                @method('DELETE')
                                <button class="button danger" type="submit">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

    @endif

    <form method="POST"
        action="{{ route('admin.users.bulkDelete') }}"
        onsubmit="return confirm('Delete selected users?')">

        @csrf
        @method('DELETE')

        <div style="margin-bottom:15px;">
            <button type="submit" class="button danger">
                Delete Selected
            </button>
        </div>

        <table>
            <thead>
                <tr>
                    <th>
                        <input type="checkbox" id="select-all">
                    </th>
                    <th>Avatar</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($users as $user)
                <tr>

                    <td>
                        <input type="checkbox"
                            class="user-checkbox"
                            name="users[]"
                            value="{{ $user->id }}">
                    </td>

                    <td>
                        @if ($user->avatar)
                            <img class="thumb"
                                src="{{ asset('storage/'.$user->avatar) }}"
                                alt="{{ $user->name }}">
                        @else
                            <span class="muted">No image</span>
                        @endif
                    </td>

                    <td>{{ $user->name }}</td>

                    <td>{{ $user->email }}</td>

                    <td>
                        {{ ucfirst($user->role) }}
                    </td>

                    <td>
                        <span @class([
                            'badge',
                            'on' => $user->is_active,
                            'off' => ! $user->is_active
                        ])>
                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>

                    <td>
                        {{ optional($user->created_at)->format('Y-m-d') }}
                    </td>

                    <td>
                        <div class="actions">

                            <a class="button secondary"
                                href="{{ route('admin.users.edit', $user) }}">
                                Edit
                            </a>

                            <form method="POST"
                                action="{{ route('admin.users.destroy', $user) }}"
                                onsubmit="return confirm('Delete this user?')">

                                @csrf
                                @method('DELETE')

                                <button class="button danger" type="submit">
                                    Delete
                                </button>

                            </form>

                        </div>
                    </td>

                </tr>

                @empty
                <tr>
                    <td colspan="8" class="muted">
                        No users yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

    </form>

    <div class="pagination">
        {{ $users->links() }}
    </div>

</div>

<script>
    const selectAll = document.getElementById('select-all');

    if (selectAll) {
        selectAll.addEventListener('change', function () {
            document.querySelectorAll('.user-checkbox').forEach(cb => {
                cb.checked = this.checked;
            });
        });
    }
</script>

@endsection