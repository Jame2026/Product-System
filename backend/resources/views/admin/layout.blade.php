<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') - Product System</title>
    <style>
        :root { color-scheme: light; font-family: Arial, Helvetica, sans-serif; }
        * { box-sizing: border-box; }
        body { margin: 0; background: #f6f7f9; color: #1f2937; }
        a { color: inherit; text-decoration: none; }
        .shell { min-height: 100vh; display: grid; grid-template-columns: 240px 1fr; }
        .sidebar { background: #111827; color: #fff; padding: 24px; }
        .brand { font-size: 20px; font-weight: 700; margin-bottom: 28px; }
        .nav { display: grid; gap: 8px; }
        .nav a { padding: 10px 12px; border-radius: 6px; color: #d1d5db; }
        .nav a:hover, .nav a.active { background: #243042; color: #fff; }
        .logout { width: 100%; border: 0; border-radius: 6px; padding: 10px 12px; color: #d1d5db; background: transparent; cursor: pointer; font: inherit; font-weight: 600; text-align: left; }
        .logout:hover { background: #243042; color: #fff; }
        .main { padding: 28px; }
        .topbar { display: flex; justify-content: space-between; align-items: center; gap: 16px; margin-bottom: 22px; }
        h1 { margin: 0; font-size: 28px; line-height: 1.2; }
        .panel { background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; }
        .panel-body { padding: 20px; }
        .grid { display: grid; gap: 16px; }
        .grid-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .stats { grid-template-columns: repeat(2, minmax(0, 280px)); }
        .stat { background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 18px; }
        .stat strong { display: block; font-size: 30px; margin-top: 6px; }
        .button { display: inline-flex; align-items: center; justify-content: center; min-height: 38px; padding: 8px 14px; border-radius: 6px; border: 1px solid #111827; background: #111827; color: #fff; cursor: pointer; font-weight: 600; }
        .button.secondary { background: #fff; color: #111827; }
        .button.danger { background: #b91c1c; border-color: #b91c1c; }
        table { width: 100%; border-collapse: collapse; background: #fff; }
        th, td { padding: 12px 14px; border-bottom: 1px solid #e5e7eb; text-align: left; vertical-align: middle; }
        th { background: #f9fafb; font-size: 13px; color: #4b5563; }
        .actions { display: flex; gap: 8px; align-items: center; }
        .badge { display: inline-flex; padding: 4px 8px; border-radius: 999px; font-size: 12px; font-weight: 700; }
        .badge.on { background: #dcfce7; color: #166534; }
        .badge.off { background: #fee2e2; color: #991b1b; }
        label { display: block; font-weight: 700; margin-bottom: 6px; }
        input, select, textarea { width: 100%; border: 1px solid #d1d5db; border-radius: 6px; min-height: 40px; padding: 9px 10px; background: #fff; }
        input[type="checkbox"] { width: auto; min-height: auto; }
        textarea { min-height: 110px; resize: vertical; }
        .field { margin-bottom: 16px; }
        .check { display: flex; gap: 8px; align-items: center; }
        .check label { margin: 0; }
        .alert { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; border-radius: 6px; padding: 12px 14px; margin-bottom: 18px; }
        .errors { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; border-radius: 6px; padding: 12px 14px; margin-bottom: 18px; }
        .thumb { width: 58px; height: 58px; object-fit: cover; border-radius: 6px; border: 1px solid #e5e7eb; }
        .muted { color: #6b7280; }
        .pagination { padding: 16px; }
        @media (max-width: 760px) {
            .shell { grid-template-columns: 1fr; }
            .sidebar { position: static; }
            .topbar, .actions { align-items: stretch; flex-direction: column; }
            .grid-2, .stats { grid-template-columns: 1fr; }
            .main { padding: 18px; }
            table { display: block; overflow-x: auto; }
        }
    </style>
</head>
<body>
    <div class="shell">
        <aside class="sidebar">
            <div class="brand">Product System</div>
            <nav class="nav">
                <a href="{{ route('admin.dashboard') }}" @class(['active' => request()->routeIs('admin.dashboard')])>Dashboard</a>
                <a href="{{ route('admin.categories.index') }}" @class(['active' => request()->routeIs('admin.categories.*')])>Categories</a>
                <a href="{{ route('admin.products.index') }}" @class(['active' => request()->routeIs('admin.products.*')])>Products</a>
                <a href="{{ route('admin.users.index') }}" @class(['active' => request()->routeIs('admin.users.*')])>Users</a>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button class="logout" type="submit">Logout</button>
                </form>
            </nav>
        </aside>

        <main class="main">
            @if (session('status'))
                <div class="alert">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="errors">
                    <strong>Please fix these fields:</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</body>
</html>
