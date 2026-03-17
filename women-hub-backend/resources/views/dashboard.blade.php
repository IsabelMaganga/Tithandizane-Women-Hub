<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') — Tithandizane Women Hub</title>
    <link rel="short icon" href="{{ asset('images/Ellipse 3.png') }}">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary:    #7C3D5E;
            --primary-lt: #A85080;
            --accent:     #E8976A;
            --accent-lt:  #F5C9A8;
            --bg:         #FDF6F0;
            --sidebar-bg: #2B1A27;
            --sidebar-w:  260px;
            --card-r:     16px;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: #2B1A27;
            min-height: 100vh;
        }

        h1, h2, h3, h4, h5, .brand-name {
            font-family: 'Playfair Display', serif;
        }

        /* ── Sidebar ── */
        #sidebar {
            position: fixed;
            top: 0; left: 0;
            width: var(--sidebar-w);
            height: 100vh;
            background: var(--sidebar-bg);
            display: flex;
            flex-direction: column;
            z-index: 1000;
            transition: transform .3s ease;
        }

        .sidebar-brand {
            padding: 28px 24px 20px;
            border-bottom: 1px solid rgba(255,255,255,.08);
        }

        .sidebar-brand .brand-name {
            font-size: 1.1rem;
            font-weight: 700;
            color: #fff;
            line-height: 1.2;
        }

        .sidebar-brand .brand-sub {
            font-size: .72rem;
            color: var(--accent-lt);
            letter-spacing: .06em;
            text-transform: uppercase;
        }

        .sidebar-brand .brand-logo {
            width: 44px; height: 44px;
            background: linear-gradient(135deg, var(--primary-lt), var(--accent));
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.3rem; color: #fff;
            margin-bottom: 12px;
        }

        .sidebar-nav { flex: 1; padding: 16px 0; overflow-y: auto; }

        .nav-section-label {
            font-size: .65rem;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: rgba(255,255,255,.35);
            padding: 12px 24px 6px;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 24px;
            color: rgba(255,255,255,.65);
            text-decoration: none;
            font-size: .9rem;
            font-weight: 500;
            border-radius: 0;
            transition: all .2s;
            border-left: 3px solid transparent;
        }

        .sidebar-link:hover {
            color: #fff;
            background: rgba(255,255,255,.06);
        }

        .sidebar-link.active {
            color: #fff;
            background: rgba(232,151,106,.12);
            border-left-color: var(--accent);
        }

        .sidebar-link i { font-size: 1.1rem; width: 20px; text-align: center; }

        .sidebar-link .badge-count {
            margin-left: auto;
            background: var(--accent);
            color: #fff;
            font-size: .65rem;
            border-radius: 10px;
            padding: 2px 8px;
        }

        .sidebar-footer {
            padding: 16px 24px;
            border-top: 1px solid rgba(255,255,255,.08);
        }

        .admin-chip {
            display: flex; align-items: center; gap: 10px;
        }

        .admin-avatar {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, var(--primary-lt), var(--accent));
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: .85rem; color: #fff; font-weight: 700;
        }

        .admin-info { flex: 1; overflow: hidden; }
        .admin-info .aname { font-size: .85rem; color: #fff; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .admin-info .arole { font-size: .7rem; color: rgba(255,255,255,.45); }

        /* ── Main content ── */
        #main {
            margin-left: var(--sidebar-w);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .topbar {
            background: #fff;
            border-bottom: 1px solid #EDE0D8;
            padding: 14px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky; top: 0; z-index: 900;
        }

        .topbar-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.2rem;
            color: var(--primary);
            margin: 0;
        }

        .content-area { padding: 28px; flex: 1; }

        /* ── Cards ── */
        .stat-card {
            background: #fff;
            border-radius: var(--card-r);
            padding: 22px;
            border: 1px solid #EDE0D8;
            transition: box-shadow .2s;
        }

        .stat-card:hover { box-shadow: 0 6px 24px rgba(124,61,94,.1); }

        .stat-icon {
            width: 48px; height: 48px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.3rem;
        }

        .stat-icon.purple  { background: #F3E8F0; color: var(--primary); }
        .stat-icon.orange  { background: #FDF0E6; color: var(--accent); }
        .stat-icon.red     { background: #FDECEC; color: #D94040; }
        .stat-icon.green   { background: #E8F5EB; color: #2E8B3C; }
        .stat-icon.blue    { background: #E8F0FD; color: #2E5BD9; }

        .stat-value { font-size: 2rem; font-weight: 700; font-family: 'Playfair Display', serif; line-height: 1; margin: 8px 0 2px; }
        .stat-label { font-size: .8rem; color: #9A7A8E; font-weight: 500; }

        /* ── Table ── */
        .data-table { background: #fff; border-radius: var(--card-r); border: 1px solid #EDE0D8; overflow: hidden; }
        .data-table .table { margin: 0; }
        .data-table .table thead th {
            background: #FAF2F7;
            color: var(--primary);
            font-size: .75rem;
            letter-spacing: .06em;
            text-transform: uppercase;
            font-weight: 600;
            border-bottom: 1px solid #EDE0D8;
            padding: 14px 16px;
        }
        .data-table .table tbody td { padding: 14px 16px; vertical-align: middle; font-size: .875rem; border-bottom: 1px solid #F5ECF2; }
        .data-table .table tbody tr:last-child td { border-bottom: none; }
        .data-table .table tbody tr:hover { background: #FDF8FB; }

        /* ── Forms ── */
        .form-card { background: #fff; border-radius: var(--card-r); border: 1px solid #EDE0D8; padding: 28px; }
        .form-label { font-size: .82rem; font-weight: 600; color: #6B3D57; margin-bottom: 6px; }
        .form-control, .form-select {
            border-color: #DDD0D8;
            border-radius: 10px;
            font-size: .875rem;
            padding: 10px 14px;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-lt);
            box-shadow: 0 0 0 3px rgba(124,61,94,.1);
        }

        /* ── Buttons ── */
        .btn-primary-hub {
            background: linear-gradient(135deg, var(--primary), var(--primary-lt));
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 10px 20px;
            font-weight: 600;
            font-size: .875rem;
            transition: opacity .2s, transform .1s;
        }
        .btn-primary-hub:hover { opacity: .9; color: #fff; transform: translateY(-1px); }
        .btn-primary-hub:active { transform: translateY(0); }

        .btn-accent-hub {
            background: linear-gradient(135deg, var(--accent), #D4845A);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 10px 20px;
            font-weight: 600;
            font-size: .875rem;
        }
        .btn-accent-hub:hover { opacity: .9; color: #fff; }

        /* ── Badges ── */
        .badge { font-size: .7rem; padding: 5px 10px; border-radius: 20px; font-weight: 600; }
        .badge-active   { background: #E8F5EB; color: #2E8B3C; }
        .badge-inactive { background: #F0EEF5; color: #7A6B90; }

        /* ── Checkbox days ── */
        .day-check { display: none; }
        .day-label {
            display: inline-block;
            padding: 6px 12px;
            border: 1.5px solid #DDD0D8;
            border-radius: 8px;
            cursor: pointer;
            font-size: .8rem;
            font-weight: 500;
            color: #6B3D57;
            transition: all .15s;
            margin: 3px 2px;
        }
        .day-check:checked + .day-label {
            background: var(--primary);
            color: #fff;
            border-color: var(--primary);
        }

        /* ── Alert ── */
        .alert-hub {
            border-radius: 10px;
            font-size: .875rem;
            border: none;
        }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            #sidebar { transform: translateX(-100%); }
            #sidebar.open { transform: translateX(0); }
            #main { margin-left: 0; }
        }

        .page-header { margin-bottom: 24px; }
        .page-header h2 { margin: 0; }
        .page-header p { color: #9A7A8E; font-size: .875rem; margin: 4px 0 0; }

        .section-card {
            background: #fff;
            border-radius: var(--card-r);
            border: 1px solid #EDE0D8;
            overflow: hidden;
        }

        .section-card-header {
            padding: 16px 22px;
            border-bottom: 1px solid #EDE0D8;
            display: flex; align-items: center; justify-content: space-between;
            background: #FAF2F7;
        }

        .section-card-header h6 {
            margin: 0;
            font-size: .9rem;
            font-weight: 700;
            color: var(--primary);
            font-family: 'Playfair Display', serif;
        }
    </style>

    @stack('styles')
</head>
<body>

<!-- Sidebar -->
<nav id="sidebar">
    <div class="sidebar-brand">
        <div class="brand-logo"><i class="bi bi-heart-fill"></i></div>
        <div class="brand-name">Tithandizane</div>
        <div class="brand-sub">Women Hub · Admin</div>
    </div>

    <div class="sidebar-nav">
        <div class="nav-section-label">Main</div>

        <a href="{{ route('admin.dashboard') }}"
           class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2-fill"></i> Dashboard
        </a>

        <div class="nav-section-label">Management</div>

        <a href="{{ route('admin.mentors.index') }}"
           class="sidebar-link {{ request()->routeIs('admin.mentors.*') ? 'active' : '' }}">
            <i class="bi bi-people-fill"></i> Mentors
        </a>

        <a href="{{ route('admin.reports.index') }}"
           class="sidebar-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
            <i class="bi bi-shield-exclamation"></i> Harassment Reports
            @php $newCount = \App\Models\HarassmentReport::where('status','new')->count(); @endphp
            @if($newCount)
                <span class="badge-count">{{ $newCount }}</span>
            @endif
        </a>
    </div>

    <div class="sidebar-footer">
        <div class="admin-chip">
            <div class="admin-avatar">{{ strtoupper(substr(Auth::guard('admin')->user()->name, 0, 1)) }}</div>
            <div class="admin-info">
                <div class="aname">{{ Auth::guard('admin')->user()->name }}</div>
                <div class="arole">Administrator</div>
            </div>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="btn btn-sm text-white p-0" style="background:none;border:none;" title="Logout">
                    <i class="bi bi-box-arrow-right" style="font-size:1.1rem;color:rgba(255,255,255,.5)"></i>
                </button>
            </form>
        </div>
    </div>
</nav>

<!-- Main -->
<div id="main">
    <div class="topbar">
        <button class="btn btn-sm d-md-none me-3" id="sidebarToggle">
            <i class="bi bi-list" style="font-size:1.4rem;color:var(--primary)"></i>
        </button>
        <h5 class="topbar-title">@yield('page-title', 'Dashboard')</h5>
        <div class="d-flex align-items-center gap-2">
            <span class="badge" style="background:#F3E8F0;color:var(--primary);font-size:.75rem;">
                <i class="bi bi-circle-fill me-1" style="font-size:.5rem;color:#2E8B3C"></i>Online
            </span>
        </div>
    </div>

    <div class="content-area">
        @if(session('success'))
            <div class="alert alert-hub alert-success alert-dismissible fade show mb-3" role="alert"
                 style="background:#E8F5EB;color:#1A6B28;">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-hub alert-danger alert-dismissible fade show mb-3" role="alert">
                <i class="bi bi-exclamation-circle-fill me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('sidebarToggle')?.addEventListener('click', () => {
        document.getElementById('sidebar').classList.toggle('open');
    });
</script>
@stack('scripts')
</body>
</html>
