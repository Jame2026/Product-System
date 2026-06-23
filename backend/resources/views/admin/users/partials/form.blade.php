@csrf

<div class="grid grid-2">

    <div class="field">
        <label for="name">Name</label>
        <input id="name"
               name="name"
               value="{{ old('name', $user?->name) }}"
               required>
    </div>

    <div class="field">
        <label for="email">Email</label>
        <input id="email"
               type="email"
               name="email"
               value="{{ old('email', $user?->email) }}"
               required>
    </div>

</div>

<div class="grid grid-2">

    <div class="field">
        <label for="password">Password</label>
        <input id="password"
               type="password"
               name="password"
               @if(!$user) required @endif>

        <small class="muted">
            Leave blank when editing to keep current password
        </small>
    </div>

</div>

<div class="field">
    <label for="avatar">Avatar</label>

    @if ($user?->avatar)
        <p>
            <img class="thumb"
                 src="{{ asset('storage/'.$user->avatar) }}"
                 alt="{{ $user->name }}">
        </p>
    @endif

    <input id="avatar"
           type="file"
           name="avatar"
           accept="image/*">
</div>

<div class="field check">
    <input id="is_active"
           type="checkbox"
           name="is_active"
           value="1"
           @checked(old('is_active', $user?->is_active ?? true))>

    <label for="is_active">Active</label>
</div>

<button class="button" type="submit">
    Save User
</button>