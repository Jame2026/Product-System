@csrf

<div class="grid grid-2">
    <div class="field">
        <label for="category_id">Category</label>
        <select id="category_id" name="category_id" required>
            <option value="">Select category</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected((int) old('category_id', $product?->category_id) === $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="field">
        <label for="name">Name</label>
        <input id="name" name="name" value="{{ old('name', $product?->name) }}" required>
    </div>
</div>

<div class="grid grid-2">
    <div class="field">
        <label for="price">Price</label>
        <input id="price" type="number" step="0.01" min="0" name="price" value="{{ old('price', $product?->price) }}" required>
    </div>

    <div class="field">
        <label for="qty">Quantity</label>
        <input id="qty" type="number" min="0" name="qty" value="{{ old('qty', $product?->qty ?? 0) }}" required>
    </div>
</div>

<div class="field">
    <label for="image">Image</label>
    @if ($product?->image)
        <p><img class="thumb" src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}"></p>
    @endif
    <input id="image" type="file" name="image" accept="image/*">
</div>
<div class="field">
    <label for="desc">Description</label>
    <textarea id="desc" name="desc" required>{{ old('desc', $product?->desc) }}</textarea>
</div>

<div class="field check">
    <input id="is_active" type="checkbox" name="is_active" value="1" @checked(old('is_active', $product?->is_active ?? true))>
    <label for="is_active">Active</label>
</div>

<button class="button" type="submit">Save Product</button>
