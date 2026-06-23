@extends('admin.layout')

@section('title', 'Categories')

@section('content')
<div class="topbar">
    <h1>Categories</h1>
    <a class="button" href="{{ route('admin.categories.create') }}">
        New Category
    </a>
</div>

<div class="panel">

    <form method="POST"
          action="{{ route('admin.categories.bulkDelete') }}"
          onsubmit="return confirm('Delete selected categories?')">

        @csrf
        @method('DELETE')

        <div style="margin-bottom: 15px;">
            <button class="button danger" type="submit">
                Delete Selected
            </button>
        </div>

        <table>
            <thead>
                <tr>
                    <th>
                        <input type="checkbox" id="select-all">
                    </th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Products</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($categories as $category)
                    <tr>

                        <td>
                            <input type="checkbox"
                                   name="categories[]"
                                   value="{{ $category->id }}"
                                   class="category-checkbox">
                        </td>

                        <td>{{ $category->name }}</td>

                        <td>{{ $category->dec }}</td>

                        <td>{{ $category->products_count }}</td>

                        <td>
                            <span @class([
                                'badge',
                                'on' => $category->is_active,
                                'off' => ! $category->is_active
                            ])>
                                {{ $category->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>

                        <td>
                            <div class="actions">
                                <a class="button secondary"
                                   href="{{ route('admin.categories.edit', $category) }}">
                                    Edit
                                </a>

                                <form method="POST"
                                      action="{{ route('admin.categories.destroy', $category) }}"
                                      onsubmit="return confirm('Delete this category?')">

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
                        <td colspan="6" class="muted">
                            No categories yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    </form>

    <div class="pagination">
        {{ $categories->links() }}
    </div>

</div>

<script>
document.getElementById('select-all').addEventListener('change', function () {

    document.querySelectorAll('.category-checkbox').forEach(function (checkbox) {
        checkbox.checked = document.getElementById('select-all').checked;
    });

});
</script>
@endsection