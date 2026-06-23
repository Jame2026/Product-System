@csrf

<div class="field">
    <label for="name">Name</label>
    <input id="name" name="name" value="{{ old('name', $category?->name) }}" required>
</div>

<div class="field">
    <label for="dec">Description</label>
    <textarea id="dec" name="dec" required>{{ old('dec', $category?->dec) }}</textarea>
</div>

<div class="field check">
    <input id="is_active" type="checkbox" name="is_active" value="1" @checked(old('is_active', $category?->is_active ?? true))>
    <label for="is_active">Active</label>
</div>

<button class="button" type="submit">Save Category</button>
