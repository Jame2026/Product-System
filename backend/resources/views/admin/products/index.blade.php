@extends('admin.layout')

@section('title', 'Products')

@section('content')
<div class="topbar">
    <h1>Products</h1>

    <div>
        <a class="button" href="{{ route('admin.products.create') }}">
            New Product
        </a>
    </div>
</div>

<div class="panel">

    <form method="POST"
        action="{{ route('admin.products.bulkDelete') }}"
        onsubmit="return confirm('Delete selected products?')">

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
                    <th>Image</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($products as $product)
                <tr>

                    <td>
                        <input type="checkbox"
                            class="product-checkbox"
                            name="products[]"
                            value="{{ $product->id }}">
                    </td>

                    <td>
                        @if ($product->image)
                        <img class="thumb"
                            src="{{ asset('storage/'.$product->image) }}"
                            alt="{{ $product->name }}">
                        @else
                        <span class="muted">No image</span>
                        @endif
                    </td>

                    <td>{{ $product->name }}</td>

                    <td>
                        {{ $product->category?->name ?? 'No category' }}
                    </td>

                    <td>
                        ${{ number_format((float)$product->price, 2) }}
                    </td>

                    <td>
                        {{ $product->qty }}
                    </td>

                    <td>
                        {{ $product->desc }}
                    </td>

                    <td>
                        <span @class([ 'badge' , 'on'=> $product->is_active,
                            'off' => ! $product->is_active
                            ])>
                            {{ $product->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>

                    <td>
                        <div class="actions">

                            <a class="button secondary"
                                href="{{ route('admin.products.edit', $product) }}">
                                Edit
                            </a>

                            <form method="POST"
                                action="{{ route('admin.products.destroy', $product) }}"
                                onsubmit="return confirm('Delete this product?')">

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
                    <td colspan="9" class="muted">
                        No products yet.
                    </td>
                </tr>

                @endforelse
            </tbody>
        </table>

    </form>

    <div class="pagination">
        {{ $products->links() }}
    </div>

</div>

<script>
    document.getElementById('select-all').addEventListener('change', function() {

        const checkboxes = document.querySelectorAll('.product-checkbox');

        checkboxes.forEach(function(checkbox) {
            checkbox.checked = document.getElementById('select-all').checked;
        });

    });
</script>

@endsection