@extends('admin.layout')

@section('title', 'Order Details')

@section('content')
<h2>Order #{{ $order->id }}</h2>

<div class="card">
    <div class="card-body">

        <h4>Customer Information</h4>

        <p><strong>Name:</strong> {{ $order->customer_name ?? $order->user->name }}</p>
        <p><strong>Email:</strong> {{ $order->customer_email ?? $order->user->email }}</p>
        <p><strong>Phone:</strong> {{ $order->phone ?? 'N/A' }}</p>
        <p><strong>Shipping Address:</strong> {{ $order->shipping_address }}</p>

        <hr>

        <h4>Products</h4>

        <table class="table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Subtotal</th>
                </tr>
            </thead>

            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->product_name ?? $item->product->name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>${{ number_format($item->price, 2) }}</td>
                    <td>${{ number_format($item->subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <h3>Total: ${{ number_format($order->total, 2) }}</h3>

        <p>
            Status:
            <strong>{{ ucfirst($order->status) }}</strong>
        </p>

    </div>
</div>
@endsection
