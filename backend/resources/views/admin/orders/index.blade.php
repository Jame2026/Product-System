@extends('admin.layout')

@section('title', 'Orders')

@section('content')
<div class="container">
    <h2>Orders Management</h2>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Customer</th>
                <th>Products</th>
                <th>Total Amount</th>
                <th>Status</th>
                <th>Ordered At</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>
            @forelse($orders as $order)
                <tr>
                    <td>{{ $order->id }}</td>

                    <td>
                        {{ $order->customer_name ?? $order->user->name }}
                        <br>
                        <small>{{ $order->customer_email ?? $order->user->email }}</small>
                    </td>

                    <td>
                        <ul>
                            @foreach($order->items as $item)
                                <li>
                                    {{ $item->product_name ?? $item->product->name }}
                                    x {{ $item->quantity }}
                                </li>
                            @endforeach
                        </ul>
                    </td>

                    <td>${{ number_format($order->total, 2) }}</td>

                    <td>
                        @if($order->status == 'completed')
                            <span class="badge bg-success">
                                Completed
                            </span>
                        @else
                            <span class="badge bg-warning">
                                {{ ucfirst($order->status) }}
                            </span>
                        @endif
                    </td>

                    <td>
                        {{ $order->created_at->format('d M Y') }}
                    </td>

                    <td>
                        <a href="{{ route('admin.orders.show', $order) }}"
                           class="btn btn-primary btn-sm">
                            View
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">
                        No orders found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $orders->links() }}
</div>
@endsection
